<div class="row">
  <aside class="col-lg-2">
    <div class="lh-card d-grid gap-2">
      <!-- <a class="btn btn-lh-primary" href="/sflex/create">
        <i class="bi bi-plus-lg"></i> Create new post </a> -->
      <a class="btn btn-outline-primary" href="/sflex">View all posts</a> 
      <a class="btn btn-outline-secondary" href="/sflex/submissions">My submissions</a>
      <?php if($user['role']==='super_admin'):?> 
        <a class="btn btn-outline-secondary" href="/sflex/review">Review submissions</a> 
      <?php endif;?>
      <a class="btn btn-outline-secondary" href="/sflex/rejected">Rejected posts</a> 
    </div>
  </aside>
  <main class="col-lg-10 d-grid">

  <div class="row gap-3 m-3 justify-content-center">
    <div class="col-lg-10">
      <section class="lh-card mt-3">
        <form method="post" action="/sflex" enctype="multipart/form-data" class="d-grid gap-3" data-async-upload="sflex">
          <input type="hidden" name="_token" value="<?=e($_SESSION['csrf'])?>">
          <textarea class="form-control" name="caption" rows="1" placeholder="What spark do you want to share?"></textarea>
          <div class="lh-file-drop" data-multi-upload="sflex"><input class="form-control" type="file" name="media[]" accept="image/*,video/*" multiple><div class="small text-secondary mt-2" data-upload-summary>Drop up to 10 images here, or click to browse. A video must be uploaded alone.</div><div class="d-flex flex-wrap gap-2 mt-2" data-upload-previews></div></div>
          <small class="text-secondary">Up to 10 images, or one video, up to 4 GB each. Posts are reviewed before publishing.</small>
          <button class="btn btn-outline-primary">Submit post</button>
        </form>
      </section>
    </div>
  </div>

    <div id="sflex-feed" class="row gap-3 d-flex justify-content-center" data-next-page="<?= e((string)($nextPage ?? '')) ?>" data-has-more="<?= !empty($hasMore) ? 'true' : 'false' ?>">
      <?php require __DIR__ . '/_posts.php'; ?>
      <?php if(!$posts):?>
        <div class="lh-card p-4 text-secondary">No approved posts yet.</div> 
      <?php endif;?> 
    </div>
    <div id="sflex-load-error" class="text-center py-3" hidden></div>
    <div id="sflex-load-sentinel" aria-hidden="true"></div>
    <div id="sflex-end" class="text-center text-secondary py-3" <?= !empty($hasMore) ? 'hidden' : '' ?>>You've reached the end.</div>

    <template id="sflex-post-skeleton">
      <article class="col-lg-3 col-md-4 col-sm-10 lh-card sflex-post sflex-post-skeleton" aria-hidden="true">
        <div class="sflex-skeleton sflex-skeleton-meta"></div>
        <div class="sflex-skeleton sflex-skeleton-media"></div>
        <div class="sflex-skeleton sflex-skeleton-line"></div>
        <div class="sflex-skeleton sflex-skeleton-line sflex-skeleton-line-short"></div>
        <div class="d-flex gap-2 mt-3"><span class="sflex-skeleton sflex-skeleton-action"></span><span class="sflex-skeleton sflex-skeleton-action"></span></div>
      </article>
    </template>
  </main>
</div>

<script>
const sflexCurrentUserId = <?= (int)$user['id'] ?>;
const sflexIsAdmin = <?= $user['role']==='super_admin' ? 'true' : 'false' ?>;
document.addEventListener('click', async function (event) {
  const button = event.target.closest('[data-reaction]');
  if (!button) return;
  const post = button.closest('[data-post]');
  if (!post || post.dataset.reacting === '1') return;
  post.dataset.reacting = '1';
  const form = new FormData();
  form.append('_token', '<?= e($_SESSION['csrf']) ?>');
  form.append('reaction', button.dataset.reaction);
  try {
    const response = await fetch('/sflex/' + post.dataset.post + '/react', {method: 'POST', body: form});
    const data = await response.json();
    if (!response.ok) throw new Error(data.error || 'Could not update reaction.');
    const card = button.closest('[data-sflex-post]');
    const icons = {like:'👍',heart:'❤️',smile:'😁',laugh:'😂',cry:'😭'};
    const picker = card.querySelector('.sflex-reaction-picker > button[data-reaction]');
    picker.dataset.reaction = data.mine || 'like'; picker.textContent = icons[data.mine || 'like'];
    picker.classList.toggle('btn-primary', !!data.mine); picker.classList.toggle('btn-outline-secondary', !data.mine);
    card.querySelector('[data-sflex-reaction-counts]').innerHTML = data.counts.map(count => `${icons[count.reaction]} ${count.total}`).join(' ');
  } catch (error) { alert(error.message); } finally { delete post.dataset.reacting; }
});
document.addEventListener('click',async function(event){
  const button=event.target.closest('[data-bs-target^="#comments-"]');
  if(!button)return;
  event.preventDefault();
  const id=button.dataset.bsTarget.replace('#comments-','');
  const response=await fetch('/sflex/'+id+'/comments');
  if(!response.ok)return;const data=await response.json();
  document.getElementById('sflexComments').dataset.postId=id;
  document.getElementById('sflex-comments-body').innerHTML='<div class="small text-secondary mb-3">Comments</div>'+data.comments.map(c=>window.sflexCommentHtml(c)).join('')||'<p class="text-secondary">No comments yet.</p>';
  bootstrap.Modal.getOrCreateInstance(document.getElementById('sflexComments')).show();
});
function escapeHtml(value){
  const e=document.createElement('div');
  e.textContent=value;return e.innerHTML;
}
(() => {
  const token='<?=e($_SESSION['csrf'])?>',modal=()=>document.getElementById('sflexComments');
  const count=value=>{const current=modal();if(current)document.querySelectorAll('[data-sflex-post="'+current.dataset.postId+'"] [data-sflex-comment-count]').forEach(el=>el.textContent=value);};
  window.sflexCommentHtml=c=>{const owner=Number(c.user_id)===sflexCurrentUserId,hidden=!!c.hidden_at;return '<div class="border-bottom py-2 '+(c.parent_id?'ms-3':'')+'" data-comment-id="'+c.id+'"><strong>'+escapeHtml(c.author)+'</strong><span data-hidden-badge>'+ (hidden?'<span class="badge text-bg-warning ms-2">Hidden</span>':'')+'</span><div data-comment-body>'+escapeHtml(c.body)+'</div><small class="text-secondary">'+escapeHtml(c.created_at)+'</small><div><button class="btn btn-link btn-sm px-0" data-reply-comment="'+c.id+'">Reply</button>'+(!sflexIsAdmin&&owner?'<button class="btn btn-link btn-sm" data-edit-comment="'+c.id+'">Edit</button><button class="btn btn-link btn-sm text-danger" data-delete-comment="'+c.id+'">Delete</button>':'')+(sflexIsAdmin?'<button class="btn btn-link btn-sm text-danger" data-moderate-comment="'+c.id+'" data-action="remove">Delete</button><button class="btn btn-link btn-sm" data-moderate-comment="'+c.id+'" data-action="'+(hidden?'unhide':'hide')+'">'+(hidden?'Unhide':'Hide')+'</button>':'')+'</div></div>';};
  document.addEventListener('submit',async e=>{const form=e.target.closest('#sflex-modal-comment-form,[data-modal-comment-form]');if(!form)return;e.preventDefault();const current=modal();if(!current)return;const button=form.querySelector('[type="submit"]'),fd=new FormData(form);fd.append('_token',token);fd.append('ajax','1');if(form.dataset.parent)fd.append('parent_id',form.dataset.parent);button.disabled=true;try{const r=await fetch('/sflex/'+current.dataset.postId+'/comment',{method:'POST',body:fd}),d=await r.json();if(!r.ok)throw new Error(d.error||'Could not save comment.');form.reset();const parent=form.dataset.parent&&current.querySelector('[data-comment-id="'+form.dataset.parent+'"]');if(parent)parent.insertAdjacentHTML('afterend',window.sflexCommentHtml(d.comment));else document.getElementById('sflex-comments-body').insertAdjacentHTML('beforeend',window.sflexCommentHtml(d.comment));if(form.dataset.parent)form.remove();count(d.comment_count);}catch(error){let notice=form.querySelector('[data-comment-error]');if(notice)notice.textContent=error.message;else alert(error.message);}finally{button.disabled=false;}},true);
  document.addEventListener('click',async e=>{const cancel=e.target.closest('[data-cancel-reply]');if(cancel){e.preventDefault();cancel.closest('form')?.remove();return;}const reply=e.target.closest('[data-reply-comment]'),edit=e.target.closest('[data-edit-comment]'),del=e.target.closest('[data-delete-comment]'),mod=e.target.closest('[data-moderate-comment]');if(!(reply||edit||del||mod))return;e.preventDefault();const action=reply||edit||del||mod,host=action.closest('[data-comment-id]');if(reply){if(!host.querySelector('[data-modal-comment-form]'))host.insertAdjacentHTML('beforeend','<form class="mt-2" data-modal-comment-form data-parent="'+reply.dataset.replyComment+'"><textarea class="form-control form-control-sm mb-1" name="body" maxlength="1000" required></textarea><button type="submit" class="btn btn-sm btn-primary">Send</button><button type="button" class="btn btn-sm btn-link" data-cancel-reply>Cancel</button></form>');return;}if(edit){const body=host.querySelector('[data-comment-body]'),old=body.textContent;body.innerHTML='<form data-modal-edit-form><textarea class="form-control form-control-sm" name="body" maxlength="1000" required>'+escapeHtml(old)+'</textarea><button type="submit" class="btn btn-sm btn-primary mt-1">Save</button></form>';body.querySelector('form').onsubmit=async event=>{event.preventDefault();const fd=new FormData(event.currentTarget);fd.append('_token',token);const r=await fetch('/sflex/comments/'+edit.dataset.editComment+'/edit',{method:'POST',body:fd}),d=await r.json();if(r.ok)body.textContent=d.body;else alert(d.error||'Could not edit comment.');};return;}const fd=new FormData();fd.append('_token',token);let url=del?'/sflex/comments/'+del.dataset.deleteComment+'/delete':'/sflex/comments/'+mod.dataset.moderateComment+'/moderate';if(mod)fd.append('action',mod.dataset.action);action.disabled=true;try{const r=await fetch(url,{method:'POST',body:fd}),d=await r.json();if(!r.ok)throw new Error(d.error||'Could not update comment.');if(del||mod.dataset.action==='remove')host.remove();else{const hidden=!!d.comment.hidden_at;host.querySelector('[data-hidden-badge]').innerHTML=hidden?'<span class="badge text-bg-warning ms-2">Hidden</span>':'';mod.dataset.action=hidden?'unhide':'hide';mod.textContent=hidden?'Unhide':'Hide';}count(d.comment_count);}catch(error){alert(error.message);}finally{action.disabled=false;}},true);
})();
document.addEventListener('click',async function(event){const button=event.target.closest('[data-sflex-reaction-summary]');if(!button)return;const response=await fetch('/sflex/'+button.dataset.postId+'/reactions');if(!response.ok)return;const data=await response.json();const groups={};data.reactions.forEach(r=>(groups[r.reaction]??=[]).push(r.name));document.getElementById('sflex-comments-body').innerHTML=Object.entries(groups).map(([reaction,names])=>'<h3 class="h6 mt-3">'+escapeHtml(reaction)+' ('+names.length+')</h3><ul class="mb-0">'+names.map(name=>'<li>'+escapeHtml(name)+'</li>').join('')+'</ul>').join('')||'<p class="text-secondary">No reactions yet.</p>';bootstrap.Modal.getOrCreateInstance(document.getElementById('sflexComments')).show();});

(() => {
  const feed = document.getElementById('sflex-feed');
  const sentinel = document.getElementById('sflex-load-sentinel');
  const end = document.getElementById('sflex-end');
  const error = document.getElementById('sflex-load-error');
  const skeleton = document.getElementById('sflex-post-skeleton');
  if (!feed || !sentinel || !skeleton) return;

  let nextPage = Number(feed.dataset.nextPage) || null;
  let hasMore = feed.dataset.hasMore === 'true';
  let isLoading = false;
  let observer;

  const removeSkeletons = () => feed.querySelectorAll('.sflex-post-skeleton').forEach((item) => item.remove());
  const showSkeletons = () => {
    const fragment = document.createDocumentFragment();
    for (let index = 0; index < 2; index += 1) fragment.append(skeleton.content.cloneNode(true));
    feed.append(fragment);
  };
  const stop = () => {
    hasMore = false;
    removeSkeletons();
    end.hidden = false;
    if (observer) observer.disconnect();
  };
  const showError = () => {
    error.hidden = false;
    error.innerHTML = '<span class="text-danger small me-2">Couldn\'t load more posts.</span><button type="button" class="btn btn-sm btn-outline-secondary" data-sflex-retry>Try again</button>';
  };

  async function loadNextPage() {
    if (isLoading || !hasMore || !nextPage) return;
    isLoading = true;
    error.hidden = true;
    showSkeletons();

    try {
      const response = await fetch(`/sflex?ajax=1&page=${encodeURIComponent(nextPage)}`, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin'
      });
      if (!response.ok) throw new Error('Feed request failed');
      const data = await response.json();
      if (typeof data.html !== 'string' || typeof data.has_more !== 'boolean') throw new Error('Invalid feed response');

      removeSkeletons();
      feed.insertAdjacentHTML('beforeend', data.html);
      hasMore = data.has_more;
      nextPage = data.next_page ? Number(data.next_page) : null;
      feed.dataset.hasMore = String(hasMore);
      feed.dataset.nextPage = nextPage || '';
      if (!hasMore) stop();
    } catch (requestError) {
      removeSkeletons();
      showError();
    } finally {
      isLoading = false;
    }
  }

  error.addEventListener('click', (event) => {
    if (event.target.closest('[data-sflex-retry]')) loadNextPage();
  });

  if (!hasMore) {
    stop();
    return;
  }

  observer = new IntersectionObserver((entries) => {
    if (entries.some((entry) => entry.isIntersecting)) loadNextPage();
  }, { rootMargin: '550px 0px', threshold: 0 });
  observer.observe(sentinel);
})();
</script>
