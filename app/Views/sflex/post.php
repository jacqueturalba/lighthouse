<div class="row justify-content-center">
  <div class="col-xl-9">

    <a href="/sflex" class="text-decoration-none">
      <i class="bi bi-arrow-left"></i> Back to SFlex
    </a>

    <article class="lh-card mt-3" data-sflex-post="<?= (int)$post['id'] ?>">

      <!-- Post header -->
      <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
        <div>
          <div class="fw-semibold">
            <?=e($post['author'])?>
          </div>

          <div class="small text-secondary">
            <?=e(date('M j, Y g:i A', strtotime($post['created_at'])))?>
          </div>
        </div>
        <?php if((int)$post['user_id']===(int)$user['id']||$user['role']==='super_admin'):?><div class="dropdown"><button class="btn btn-sm btn-link text-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button><ul class="dropdown-menu dropdown-menu-end"><?php if((int)$post['user_id']===(int)$user['id']):?><li><button class="dropdown-item" data-sflex-edit data-post-id="<?= (int)$post['id']?>" data-caption="<?=e($post['caption'])?>">Edit</button></li><?php endif;?><li><button class="dropdown-item text-danger" data-sflex-delete data-post-id="<?= (int)$post['id']?>">Delete</button></li></ul></div><?php endif;?>
      </div>
      <!-- Caption -->
      <?php if (trim((string)$post['caption']) !== ''): ?>

        <div class="sflex-post-caption mb-3" data-sflex-caption>
          <?=nl2br(e($post['caption']))?>
        </div>

      <?php endif; ?>
      <!-- Media -->
      <?php $postMedia = $post['media'] ?? []; if (!$postMedia && $post['media_path']) $postMedia = [['media_path' => $post['media_path'], 'media_type' => $post['media_type']]]; ?>
      <?php if ($postMedia): ?>
        <?php $mediaCarouselId = 'post-media-' . (int)$post['id']; ?>
        <div id="<?= $mediaCarouselId ?>" class="carousel slide lh-media-carousel sflex-carousel sflex-media sflex-media-large mb-3" aria-label="Post images">
          <div class="carousel-inner">
            <?php foreach ($postMedia as $mediaIndex => $mediaItem): ?>
              <div class="carousel-item <?= $mediaIndex === 0 ? 'active' : '' ?>">
                <?php $mediaUrl = '/sflex-media/' . e(substr($mediaItem['media_path'], 6)); if ($mediaItem['media_type'] === 'video'): ?>
                  <video controls preload="metadata" src="<?= $mediaUrl ?>"></video>
                <?php else: ?>
                  <img src="<?= $mediaUrl ?>" alt="Post image <?= $mediaIndex + 1 ?>" loading="lazy">
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
          <?php if (count($postMedia) > 1): ?>
            <button type="button" class="carousel-control-prev" data-bs-target="#<?= $mediaCarouselId ?>" data-bs-slide="prev" aria-label="Previous image"><span class="carousel-control-prev-icon" aria-hidden="true"></span></button>
            <button type="button" class="carousel-control-next" data-bs-target="#<?= $mediaCarouselId ?>" data-bs-slide="next" aria-label="Next image"><span class="carousel-control-next-icon" aria-hidden="true"></span></button>
            <span class="lh-carousel-indicator" data-carousel-indicator data-total="<?= count($postMedia) ?>" aria-live="polite">1 / <?= count($postMedia) ?></span>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- Reactions -->
      <?php
      $reactionCounts = [];

      foreach (($post['counts'] ?? []) as $count) {
          $reactionCounts[$count['reaction']] = (int)$count['total'];
      }

      $reactions = [
          'like'  => ['icon' => 'hand-thumbs-up', 'label' => 'Like'],
          'heart' => ['icon' => 'heart', 'label' => 'Heart'],
          'smile' => ['icon' => 'emoji-smile', 'label' => 'Smile'],
          'laugh' => ['icon' => 'emoji-laughing', 'label' => 'Laugh'],
          'cry'   => ['icon' => 'emoji-tear', 'label' => 'Cry'],
      ];
      ?>

    <div class="sflex-actions border-top border-bottom py-3 mb-4">

        <?php foreach ($reactions as $reaction => $data): ?>

            <?php $count = $reactionCounts[$reaction] ?? 0; ?>

            <button
                type="button"
                data-reaction="<?= e($reaction) ?>"
                class="sflex-reaction-btn <?=($post['mine'] === $reaction ? 'is-reacted' : '')?>"
                aria-label="<?=e($data['label'])?>">

                <i class="bi bi-<?=e($data['icon'])?>"></i>

                <span class="sflex-reaction-label">
                    <?=e($data['label'])?>
                </span>

                <?php if ($count > 0): ?>
                    <span class="sflex-reaction-count">
                        <?=e((string)$count)?>
                    </span>
                <?php endif; ?>

            </button>

        <?php endforeach; ?>

    </div>

      <!-- Comments -->
      <section class="sflex-comments">

        <div class="d-flex align-items-center justify-content-between mb-3">

          <h5 class="mb-0">
            Comments
            <span class="text-secondary">
              (<span data-inline-comment-count><?=count($post['comments'] ?? [])?></span>)
            </span>
          </h5>

        </div>

        <div class="sflex-comment-list">
        <?php if (!empty($post['comments'])): ?>

            <?php foreach ($post['comments'] as $comment): ?>

              <div class="sflex-comment" data-comment-id="<?= (int)$comment['id'] ?>">

                <div class="sflex-comment-header">

                  <strong>
                    <?=e($comment['author'])?>
                  </strong><span data-inline-hidden><?php if($comment['hidden_at']): ?><span class="badge text-bg-warning ms-2">Hidden</span><?php endif; ?></span>

                  <span class="text-secondary">
                    ·
                    <?=e(date('M j, Y g:i A', strtotime($comment['created_at'])))?>
                  </span>

                </div>

                <div class="sflex-comment-body">
                  <?=nl2br(e($comment['body']))?>
                </div>
                <div class="small"><button class="btn btn-link btn-sm px-0" data-inline-reply="<?= (int)$comment['id'] ?>">Reply</button><?php if((int)$comment['user_id']===(int)$user['id']): ?><button class="btn btn-link btn-sm" data-inline-edit="<?= (int)$comment['id'] ?>">Edit</button><?php endif; ?><?php if($user['role'] !== 'super_admin' && (int)$comment['user_id']===(int)$user['id']): ?><button class="btn btn-link btn-sm text-danger" data-inline-delete="<?= (int)$comment['id'] ?>">Delete</button><?php endif; ?><?php if($user['role']==='super_admin'): ?><button class="btn btn-link btn-sm text-danger" data-inline-delete="<?= (int)$comment['id'] ?>">Delete</button><button class="btn btn-link btn-sm" data-inline-hide="<?= (int)$comment['id'] ?>" data-action="<?= $comment['hidden_at']?'unhide':'hide' ?>"><?= $comment['hidden_at']?'Unhide':'Hide' ?></button><?php endif; ?></div>

              </div>

            <?php endforeach; ?>

        <?php else: ?>
          <div class="text-secondary py-2" data-inline-empty>No comments yet. Be the first to comment.</div>
        <?php endif; ?>
        </div>

        <!-- Add comment -->
        <form
          method="post"
          action="/sflex/<?=$post['id']?>/comment"
          class="sflex-comment-form mt-4" data-inline-add-comment>

          <input
            type="hidden"
            name="_token"
            value="<?=e($_SESSION['csrf'])?>">

          <label for="comment" class="form-label fw-semibold">
            Add a comment
          </label>

          <div class="d-flex gap-2">

            <textarea
              id="comment"
              name="body"
              class="form-control"
              rows="2"
              maxlength="1000"
              placeholder="Write a comment..."
              required></textarea>

            <button
              type="submit"
              class="btn btn-primary align-self-end">

              <i class="bi bi-send"></i>
              Comment

            </button>

          </div>

          <div class="form-text">
            Maximum 1000 characters.
          </div>

        </form>

      </section>

    </article>

  </div>
</div>
<script>
document.addEventListener('click', async function (event) {
    const button = event.target.closest('[data-reaction]');
    if (!button) return;

    const form = new FormData();
    form.append('_token', '<?= e($_SESSION['csrf']) ?>');
    form.append('reaction', button.dataset.reaction);

    const response = await fetch(
        '/sflex/<?= (int)$post['id'] ?>/react',
        {
            method: 'POST',
            body: form,
            credentials: 'same-origin'
        }
    );

    if (!response.ok || button.dataset.loading) return;
    button.dataset.loading='1';
    try { const data=await response.json(); document.querySelectorAll('[data-reaction]').forEach(item=>item.classList.toggle('is-reacted',item.dataset.reaction===data.mine)); } finally { delete button.dataset.loading; }
});
// Delegated handlers also apply to comments and replies created after page load.
(()=>{const token='<?=e($_SESSION['csrf'])?>',postId=<?= (int)$post['id']?>,userId=<?= (int)$user['id']?>,isAdmin=<?= $user['role']==='super_admin'?'true':'false'?>;
const esc=value=>{const node=document.createElement('span');node.textContent=value??'';return node.innerHTML;};const count=value=>document.querySelectorAll('[data-inline-comment-count]').forEach(el=>el.textContent=value);
const html=c=>{const own=Number(c.user_id)===userId,hidden=!!c.hidden_at;return '<div class="sflex-comment '+(c.parent_id?'ms-3':'')+'" data-comment-id="'+c.id+'"><div class="sflex-comment-header"><strong>'+esc(c.author)+'</strong><span data-inline-hidden>'+ (hidden?'<span class="badge text-bg-warning ms-2">Hidden</span>':'')+'</span><span class="text-secondary"> · '+esc(c.created_at)+'</span></div><div class="sflex-comment-body">'+esc(c.body).replace(/\n/g,'<br>')+'</div><div class="small"><button class="btn btn-link btn-sm px-0" data-inline-reply="'+c.id+'">Reply</button>'+(own?'<button class="btn btn-link btn-sm" data-inline-edit="'+c.id+'">Edit</button>':'')+(!isAdmin&&own?'<button class="btn btn-link btn-sm text-danger" data-inline-delete="'+c.id+'">Delete</button>':'')+(isAdmin?'<button class="btn btn-link btn-sm text-danger" data-inline-delete="'+c.id+'">Delete</button><button class="btn btn-link btn-sm" data-inline-hide="'+c.id+'" data-action="'+(hidden?'unhide':'hide')+'">'+(hidden?'Unhide':'Hide')+'</button>':'')+'</div></div>';};
document.addEventListener('submit',async event=>{const form=event.target.closest('[data-inline-add-comment],[data-inline-comment],[data-inline-edit-form]');if(!form)return;event.preventDefault();const button=form.querySelector('[type="submit"],button'),fd=new FormData(form),editing=form.matches('[data-inline-edit-form]');fd.append('_token',token);fd.append('ajax','1');if(form.dataset.parent)fd.append('parent_id',form.dataset.parent);button.disabled=true;try{const url=editing?'/sflex/comments/'+form.dataset.id+'/edit':'/sflex/'+postId+'/comment',r=await fetch(url,{method:'POST',body:fd}),d=await r.json();if(!r.ok)throw new Error(d.error||'Could not save comment.');if(editing){form.closest('.sflex-comment-body').innerHTML=esc(d.body).replace(/\n/g,'<br>');return;}form.reset();document.querySelector('[data-inline-empty]')?.remove();if(form.dataset.parent){const parent=form.closest('[data-comment-id]');parent.insertAdjacentHTML('afterend',html(d.comment));form.remove();}else document.querySelector('.sflex-comment-list').insertAdjacentHTML('beforeend',html(d.comment));count(d.comment_count);}catch(error){alert(error.message);}finally{button.disabled=false;}},true);
document.addEventListener('click',async event=>{const reply=event.target.closest('[data-inline-reply]'),edit=event.target.closest('[data-inline-edit]'),del=event.target.closest('[data-inline-delete]'),hide=event.target.closest('[data-inline-hide]');if(!(reply||edit||del||hide))return;event.preventDefault();const action=reply||edit||del||hide,host=action.closest('[data-comment-id]');if(reply){if(!host.querySelector('[data-inline-comment]'))host.insertAdjacentHTML('beforeend','<form class="mt-2" data-inline-comment data-parent="'+reply.dataset.inlineReply+'"><textarea name="body" class="form-control form-control-sm" maxlength="1000" required></textarea><button type="submit" class="btn btn-sm btn-primary mt-1">Send</button></form>');return;}if(edit){const body=host.querySelector('.sflex-comment-body'),old=body.innerText;body.innerHTML='<form data-inline-edit-form data-id="'+edit.dataset.inlineEdit+'"><textarea name="body" class="form-control form-control-sm" maxlength="1000" required>'+esc(old)+'</textarea><button type="submit" class="btn btn-sm btn-primary mt-1">Save</button></form>';return;}const fd=new FormData();fd.append('_token',token);let url=del?'/sflex/comments/'+del.dataset.inlineDelete+'/delete':'/sflex/comments/'+hide.dataset.inlineHide+'/moderate';if(hide)fd.append('action',hide.dataset.action);action.disabled=true;try{const r=await fetch(url,{method:'POST',body:fd}),d=await r.json();if(!r.ok)throw new Error(d.error||'Could not update comment.');if(del)host.remove();else{const hidden=!!d.comment.hidden_at;host.querySelector('[data-inline-hidden]').innerHTML=hidden?'<span class="badge text-bg-warning ms-2">Hidden</span>':'';hide.dataset.action=hidden?'unhide':'hide';hide.textContent=hidden?'Unhide':'Hide';}count(d.comment_count);}catch(error){alert(error.message);}finally{action.disabled=false;}},true);})();
</script>
