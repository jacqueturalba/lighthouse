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
      <?php if ($post['media_path']): ?>

        <div class="sflex-media sflex-media-large mb-3">
          <?php
          $url = '/sflex-media/' . e(substr($post['media_path'], 6));

          if ($post['media_type'] === 'video'):
          ?>

            <video
              controls
              preload="metadata"
              src="<?=$url?>">
            </video>

          <?php else: ?>

            <img
              src="<?=$url?>"
              alt="Post media"
              loading="lazy" class="img-fluid">

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
              (<?=count($post['comments'] ?? [])?>)
            </span>
          </h5>

        </div>

        <?php if (!empty($post['comments'])): ?>

          <div class="sflex-comment-list">

            <?php foreach ($post['comments'] as $comment): ?>

              <div class="sflex-comment" data-comment-id="<?= (int)$comment['id'] ?>">

                <div class="sflex-comment-header">

                  <strong>
                    <?=e($comment['author'])?>
                  </strong>

                  <span class="text-secondary">
                    ·
                    <?=e(date('M j, Y g:i A', strtotime($comment['created_at'])))?>
                  </span>

                </div>

                <div class="sflex-comment-body">
                  <?=nl2br(e($comment['body']))?>
                </div>
                <div class="small"><button class="btn btn-link btn-sm px-0" data-inline-reply="<?= (int)$comment['id'] ?>">Reply</button><?php if((int)$comment['user_id']===(int)$user['id']): ?><button class="btn btn-link btn-sm" data-inline-edit="<?= (int)$comment['id'] ?>">Edit</button><button class="btn btn-link btn-sm text-danger" data-inline-delete="<?= (int)$comment['id'] ?>">Delete</button><?php endif; ?><?php if($user['role']==='super_admin'): ?><button class="btn btn-link btn-sm" data-inline-hide="<?= (int)$comment['id'] ?>">Hide</button><?php endif; ?></div>

              </div>

            <?php endforeach; ?>

          </div>

        <?php else: ?>

          <div class="text-secondary py-2">
            No comments yet. Be the first to comment.
          </div>

        <?php endif; ?>

        <!-- Add comment -->
        <form
          method="post"
          action="/sflex/<?=$post['id']?>/comment"
          class="sflex-comment-form mt-4">

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
document.querySelector('.sflex-comment-form')?.addEventListener('submit',async function(event){event.preventDefault();const form=event.currentTarget,button=form.querySelector('button'),fd=new FormData(form);fd.append('ajax','1');button.disabled=true;try{const response=await fetch(form.action,{method:'POST',body:fd});if(!response.ok)throw new Error();const body=form.querySelector('textarea');const list=document.querySelector('.sflex-comment-list');if(list){const entry=document.createElement('div');entry.className='sflex-comment';entry.textContent=body.value;list.append(entry);}body.value='';}catch(_){alert('Your comment could not be added.');}finally{button.disabled=false;}});
document.addEventListener('click',async event=>{const reply=event.target.closest('[data-inline-reply]'),edit=event.target.closest('[data-inline-edit]'),del=event.target.closest('[data-inline-delete]'),hide=event.target.closest('[data-inline-hide]');const action=reply||edit||del||hide;if(!action)return;const host=action.closest('[data-comment-id]');if(reply){host.insertAdjacentHTML('beforeend','<form data-inline-comment data-parent="'+reply.dataset.inlineReply+'"><textarea name="body" class="form-control form-control-sm" required></textarea><button class="btn btn-sm btn-primary mt-1">Send</button></form>');}if(edit){const body=host.querySelector('.sflex-comment-body');body.innerHTML='<form data-inline-edit-form data-id="'+edit.dataset.inlineEdit+'"><textarea name="body" class="form-control form-control-sm">'+body.textContent.trim()+'</textarea><button class="btn btn-sm btn-primary mt-1">Save</button></form>';}if(del||hide){const fd=new FormData();fd.append('_token','<?=e($_SESSION['csrf'])?>');let url;if(hide){fd.append('action','hide');url='/sflex/comments/'+hide.dataset.inlineHide+'/moderate';}else url='/sflex/comments/'+del.dataset.inlineDelete+'/delete';const r=await fetch(url,{method:'POST',body:fd});if(r.ok)host.remove();}});document.addEventListener('submit',async event=>{const form=event.target.closest('[data-inline-comment],[data-inline-edit-form]');if(!form)return;event.preventDefault();const fd=new FormData(form);fd.append('_token','<?=e($_SESSION['csrf'])?>');fd.append('ajax','1');let url=form.matches('[data-inline-edit-form]')?'/sflex/comments/'+form.dataset.id+'/edit':'/sflex/<?= (int)$post['id']?>/comment';if(form.dataset.parent)fd.append('parent_id',form.dataset.parent);const r=await fetch(url,{method:'POST',body:fd});const d=await r.json();if(!r.ok){alert(d.error||'Could not save comment.');return;}if(form.matches('[data-inline-edit-form]'))form.closest('.sflex-comment-body').textContent=d.body;else{form.reset();form.remove();}});
</script>
