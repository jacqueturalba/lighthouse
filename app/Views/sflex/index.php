<div class="row g-4">
  <aside class="col-lg-3">
    <div class="lh-card d-grid gap-2">
      <a class="btn btn-lh-primary" href="/sflex/create">
        <i class="bi bi-plus-lg"></i> Create new post </a>
      <a class="btn btn-outline-primary" href="/sflex">View all posts</a> 
      <?php if($user['role']==='super_admin'):?> 
        <a class="btn btn-outline-secondary" href="/sflex/review">Review submissions</a> 
      <?php endif;?>
    </div>
  </aside>
  <main class="col-lg-7">
    <div class="d-flex justify-content-between mb-3">
      <div>
        <span class="lh-kicker">Community</span>
        <h1 class="lh-page-title">SFlex</h1>
      </div>
    </div>
    <div id="sflex-feed" class="d-grid gap-3"> 
        <?php foreach($posts as$p):?> 
            <article class="lh-card sflex-post">
                <div class="small text-secondary mb-2">
                    <strong> <?=e($p['author'])?> </strong> · <?=e(date('M j, Y',strtotime($p['created_at'])))?>
                </div> 
            <?php if($p['media_path']):?> 
                <div class="sflex-media mb-3"> 
                    <?php if($p['media_type']==='video'):?> 
                        <video controls preload="metadata" src="/sflex-media/<?=e(substr($p['media_path'],6))?>">
                        </video> <?php else:?> 
                        <img src="/sflex-media/<?=e(substr($p['media_path'],6))?>" alt="Post media" loading="lazy"> 
                    <?php endif;?> 
                </div> 
            <?php endif;?> 
                <p class="mb-3"> <?=nl2br(e($p['caption']))?> </p>
                <div class="d-flex flex-wrap gap-2" data-post="<?=$p['id']?>"> 
                    <?php foreach(['like'=>'👍 Like','heart'=>'❤️ Heart','smile'=>'😁 Smile','laugh'=>'😂 Laugh','cry'=>'😭 Cry']as$r=>$label):?> 
                        <button class="btn btn-sm 
                                    <?=$p['mine']===$r?'btn-primary':'btn-outline-secondary'?>" data-reaction="<?=$r?>"> <?=$label?> 
                        </button> 
                    <?php endforeach;?> 
                </div>
                <button class="btn btn-link px-0 mt-2" data-bs-toggle="collapse" data-bs-target="#comments-<?=$p['id']?>">💬 Comments ( <?=$p['comments']?>) </button>
                <div class="collapse" id="comments-<?=$p['id']?>">
                <form method="post" action="/sflex/<?=$p['id']?>/comment" class="d-flex gap-2">
                    <input type="hidden" name="_token" value="<?=e($_SESSION['csrf'])?>">
                    <input class="form-control" name="body" maxlength="1000" placeholder="Write a comment">
                    <button class="btn btn-primary">Send</button>
                </form>
                </div>
            </article> 
      <?php endforeach;if(!$posts):?> 
        <div class="lh-card p-4 text-secondary">No approved posts yet.</div> 
      <?php endif;?> 
    </div>
    <div id="sflex-end" class="text-center text-secondary py-3">You've reached the end.</div>
  </main>
</div>
<script>
  document.querySelectorAll('[data-reaction]').forEach(b => b.onclick = async () => {
          let box = b.closest('[data-post]');
          let fd = new FormData();
          fd.append('_token', ' < ? = e($_SESSION['csrf']) ? > ');fd.append('
              reaction ',b.dataset.reaction);let r=await fetch(' / sflex / '+box.dataset.post+' / react ',{method:'
              POST ',body:fd});if(r.ok)location.reload();});
</script>