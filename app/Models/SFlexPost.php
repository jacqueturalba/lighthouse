<?php
declare(strict_types=1);
final class SFlexPost
{
private static function select():string{return "SELECT p.*,u.name author,(SELECT COUNT(*) FROM sflex_comments c WHERE c.post_id=p.id AND c.hidden_at IS NULL) comments,(SELECT reaction FROM sflex_reactions r WHERE r.post_id=p.id AND r.user_id=?) mine FROM sflex_posts p JOIN users u ON u.id=p.user_id";}
private static function hydrate(array &$posts):void{if(!$posts)return;$ids=array_column($posts,'id');$m=implode(',',array_fill(0,count($ids),'?'));$q=db()->prepare("SELECT post_id,reaction,COUNT(*) total FROM sflex_reactions WHERE post_id IN ($m) GROUP BY post_id,reaction");$q->execute($ids);$counts=[];foreach($q->fetchAll()as$r)$counts[$r['post_id']][]=$r;$q=db()->prepare("SELECT post_id,media_path,media_type,sort_order FROM sflex_post_media WHERE post_id IN ($m) ORDER BY post_id,sort_order");$q->execute($ids);$media=[];foreach($q->fetchAll()as$r)$media[$r['post_id']][]=$r;foreach($posts as&$p){$p['counts']=$counts[$p['id']]??[];$p['media']=$media[$p['id']]??[];if(!$p['media']&&$p['media_path'])$p['media'][]=['media_path'=>$p['media_path'],'media_type'=>$p['media_type'],'sort_order'=>0];}unset($p);}
public static function feed(int $u,int $o):array{return self::feedPage($u,intdiv($o,5)+1)['posts'];}
public static function feedPage(int $u,int $page,int $per=5):array{$page=max(1,$page);$q=db()->prepare(self::select()." WHERE p.status='approved' ORDER BY p.created_at DESC LIMIT ".($per+1).' OFFSET ?');$q->bindValue(1,$u,PDO::PARAM_INT);$q->bindValue(2,($page-1)*$per,PDO::PARAM_INT);$q->execute();$posts=$q->fetchAll();$more=count($posts)>$per;if($more)array_pop($posts);self::hydrate($posts);return['posts'=>$posts,'has_more'=>$more,'next_page'=>$more?$page+1:null];}
public static function create(int $u,string $caption,?string $path,?string $type,string $status):int{db()->prepare('INSERT INTO sflex_posts(user_id,caption,media_path,media_type,status) VALUES(?,?,?,?,?)')->execute([$u,$caption,$path,$type,$status]);return(int)db()->lastInsertId();}
public static function addMedia(int $post,array $media):void{$q=db()->prepare('INSERT INTO sflex_post_media(post_id,media_path,media_type,sort_order) VALUES(?,?,?,?)');foreach($media as$i=>$v)$q->execute([$post,$v['path'],$v['type'],$i]);}
public static function pending(int $u):array{$q=db()->prepare(self::select()." WHERE p.status='pending' ORDER BY p.created_at ASC");$q->execute([$u]);$p=$q->fetchAll();self::hydrate($p);return$p;}
public static function rejected(int $u,bool $admin):array{$sql=self::select()." WHERE p.status='rejected'";$args=[$u];if(!$admin){$sql.=' AND p.user_id=?';$args[]=$u;}$q=db()->prepare($sql.' ORDER BY p.created_at DESC');$q->execute($args);$p=$q->fetchAll();self::hydrate($p);return$p;}
public static function submissions(int $u):array{$q=db()->prepare('SELECT p.* FROM sflex_posts p WHERE p.user_id=? ORDER BY p.created_at DESC');$q->execute([$u]);$p=$q->fetchAll();self::hydrate($p);return$p;}
public static function review(int $id,string $s):void{if(!in_array($s,['approved','rejected'],true))throw new InvalidArgumentException('Invalid review status.');db()->prepare("UPDATE sflex_posts SET status=? WHERE id=? AND status='pending'")->execute([$s,$id]);}
public static function updateCaption(int $id,int $user,string $caption):bool{$q=db()->prepare('UPDATE sflex_posts SET caption=? WHERE id=? AND user_id=?');$q->execute([$caption,$id,$user]);return$q->rowCount()>0;}

public static function deletePost(int $id, int $user, bool $admin): bool
{
    $db = db();

    // Get the post and verify permission
    $postQuery = $db->prepare(
        'SELECT id, media_path
         FROM sflex_posts
         WHERE id = ?' .
        ($admin ? '' : ' AND user_id = ?')
    );

    $postQuery->execute(
        $admin ? [$id] : [$id, $user]
    );

    $post = $postQuery->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        return false;
    }

    // Collect all media files associated with this post.
    $mediaPaths = [];

    // Legacy/single media stored directly on sflex_posts
    if (!empty($post['media_path'])) {
        $mediaPaths[] = $post['media_path'];
    }

    // Current media stored in sflex_post_media
    $mediaQuery = $db->prepare(
        'SELECT media_path
         FROM sflex_post_media
         WHERE post_id = ?'
    );

    $mediaQuery->execute([$id]);

    foreach ($mediaQuery->fetchAll(PDO::FETCH_COLUMN) as $mediaPath) {
        if (!empty($mediaPath)) {
            $mediaPaths[] = $mediaPath;
        }
    }

    try {
        // Delete the post.
        //
        // Because sflex_post_media, sflex_reactions,
        // and sflex_comments all use ON DELETE CASCADE,
        // their database records will be removed automatically.
        $delete = $db->prepare(
            'DELETE FROM sflex_posts WHERE id = ?'
        );

        $delete->execute([$id]);

        if ($delete->rowCount() !== 1) {
            return false;
        }

    } catch (Throwable $e) {
        return false;
    }

    // Delete physical media files AFTER the post was successfully deleted.
    $projectRoot = dirname(__DIR__, 2);
    $storageRoot = realpath($projectRoot . '/storage/sflex');

    if ($storageRoot !== false) {
        foreach (array_unique($mediaPaths) as $mediaPath) {
            $mediaPath = ltrim($mediaPath, '/\\');

            $file = $projectRoot . '/' . $mediaPath;
            $realFile = realpath($file);

            // Only delete actual files inside storage/sflex
            if (
                $realFile !== false &&
                is_file($realFile) &&
                str_starts_with(
                    $realFile,
                    $storageRoot . DIRECTORY_SEPARATOR
                )
            ) {
                @unlink($realFile);
            }
        }
    }

    return true;
}


public static function reactionState(int $post,int $user):array{$q=db()->prepare('SELECT reaction,COUNT(*) total FROM sflex_reactions WHERE post_id=? GROUP BY reaction');$q->execute([$post]);$counts=$q->fetchAll();$q=db()->prepare('SELECT reaction FROM sflex_reactions WHERE post_id=? AND user_id=?');$q->execute([$post,$user]);return['mine'=>$q->fetchColumn()?:null,'counts'=>$counts];}
public static function react(int $p,int $u,string $r):void{$q=db()->prepare('SELECT reaction FROM sflex_reactions WHERE post_id=? AND user_id=?');$q->execute([$p,$u]);if($q->fetchColumn()===$r){db()->prepare('DELETE FROM sflex_reactions WHERE post_id=? AND user_id=?')->execute([$p,$u]);return;}db()->prepare('INSERT INTO sflex_reactions(post_id,user_id,reaction) VALUES(?,?,?) ON DUPLICATE KEY UPDATE reaction=VALUES(reaction),created_at=NOW()')->execute([$p,$u,$r]);}
public static function reactionUsers(int $p):array{$q=db()->prepare('SELECT r.reaction,u.name FROM sflex_reactions r JOIN users u ON u.id=r.user_id WHERE r.post_id=? ORDER BY r.reaction,u.name');$q->execute([$p]);return$q->fetchAll();}
public static function comments(int $p,bool $admin):array{$q=db()->prepare('SELECT c.*,u.name author FROM sflex_comments c JOIN users u ON u.id=c.user_id WHERE c.post_id=?'.($admin?'':' AND c.hidden_at IS NULL').' ORDER BY COALESCE(c.parent_id,c.id),c.parent_id IS NOT NULL,c.created_at');$q->execute([$p]);return$q->fetchAll();}
public static function comment(int $p,int $u,string $b):int{db()->prepare('INSERT INTO sflex_comments(post_id,user_id,body) VALUES(?,?,?)')->execute([$p,$u,$b]);return(int)db()->lastInsertId();}
public static function commentPayload(int $id):array{$q=db()->prepare('SELECT c.*,u.name author FROM sflex_comments c JOIN users u ON u.id=c.user_id WHERE c.id=?');$q->execute([$id]);return$q->fetch()?:[];}
public static function commentCount(int $post):int{$q=db()->prepare('SELECT COUNT(*) FROM sflex_comments WHERE post_id=? AND hidden_at IS NULL');$q->execute([$post]);return(int)$q->fetchColumn();}
public static function reply(int $p,int $u,int $parent,string $b):int{$q=db()->prepare('SELECT id FROM sflex_comments WHERE id=? AND post_id=? AND parent_id IS NULL');$q->execute([$parent,$p]);if(!$q->fetchColumn())return 0;db()->prepare('INSERT INTO sflex_comments(post_id,parent_id,user_id,body) VALUES(?,?,?,?)')->execute([$p,$parent,$u,$b]);return(int)db()->lastInsertId();}
public static function editComment(int $id,int $user,string $body):bool{$q=db()->prepare('UPDATE sflex_comments SET body=? WHERE id=? AND user_id=?');$q->execute([$body,$id,$user]);return$q->rowCount()>0;}
public static function moderateComment(int $id,string $a):void{if($a==='hide')db()->prepare('UPDATE sflex_comments SET hidden_at=NOW() WHERE id=?')->execute([$id]);if($a==='unhide')db()->prepare('UPDATE sflex_comments SET hidden_at=NULL WHERE id=?')->execute([$id]);if($a==='remove')db()->prepare('DELETE FROM sflex_comments WHERE id=?')->execute([$id]);}
public static function deleteComment(int $id,int $u,bool $admin):bool{$q=db()->prepare('DELETE FROM sflex_comments WHERE id=?'.($admin?'':' AND user_id=?'));$q->execute($admin?[$id]:[$id,$u]);return$q->rowCount()>0;}
public static function find(int $id,int $u):?array{$q=db()->prepare(self::select().' WHERE p.id=?');$q->execute([$u,$id]);$p=$q->fetch();if(!$p)return null;$posts=[$p];self::hydrate($posts);$p=$posts[0];$p['comments']=self::comments($id,false);return$p;}
}
