<div class="row">
  <aside class="col-lg-2">
    <div class="lh-card d-grid gap-2">
      <!-- <a class="btn btn-lh-primary" href="/sflex/create">
        <i class="bi bi-plus-lg"></i> Create new post </a> -->
      <a class="btn btn-outline-primary" href="/sflex">View all posts</a> 
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
        <form method="post" action="/sflex" enctype="multipart/form-data" class="d-grid gap-3">
          <input type="hidden" name="_token" value="<?=e($_SESSION['csrf'])?>">
          <textarea class="form-control" name="caption" rows="1" placeholder="What spark do you want to share?"></textarea>
          <input class="form-control" type="file" name="media" accept="image/*,video/*">
          <small class="text-secondary">Images or videos up to 4 GB. Posts are reviewed before publishing.</small>
          <button class="btn btn-outline-primary">Submit post</button>
        </form>
      </section>
    </div>
  </div>

    <div id="sflex-feed" class="row gap-3 d-flex justify-content-center"> 
        <?php foreach($posts as$p):?> 
            <article class="col-lg-3 col-md-4 col-sm-10 lh-card sflex-post">
                <div class="small text-secondary mb-2">
                    <strong> <?=e($p['author'])?> </strong> · <?=e(date('M j, Y',strtotime($p['created_at'])))?>
                </div> 
            <?php if($p['media_path']):?> 
                <div class="sflex-media mb-3"> 
                    <a href="/sflex/post/<?=$p['id']?>"><?php if($p['media_type']==='video'):?> 
                        <video controls preload="metadata" src="/sflex-media/<?=e(substr($p['media_path'],6))?>">
                        </video> <?php else:?> 
                        <img src="/sflex-media/<?=e(substr($p['media_path'],6))?>" alt="Post media" loading="lazy"> 
                    <?php endif;?></a> 
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
document.addEventListener('click', async function (event) {
  const button = event.target.closest('[data-reaction]');
  if (!button) return;
  const post = button.closest('[data-post]');
  const form = new FormData();
  form.append('_token', '<?= e($_SESSION['csrf']) ?>');
  form.append('reaction', button.dataset.reaction);
  const response = await fetch('/sflex/' + post.dataset.post + '/react', {method: 'POST', body: form});
  if (response.ok) window.location.reload();
});
document.addEventListener('click',async function(event){
  const button=event.target.closest('[data-bs-target^="#comments-"]');
  if(!button)return;
  event.preventDefault();
  const id=button.dataset.bsTarget.replace('#comments-','');
  const response=await fetch('/sflex/'+id+'/comments');
  if(!response.ok)return;const data=await response.json();
  document.getElementById('sflex-comments-body').innerHTML=data.comments.map(c=>'<div class="border-bottom py-2"><strong>'+escapeHtml(c.author)+'</strong><div>'+escapeHtml(c.body)+'</div><small class="text-secondary">'+escapeHtml(c.created_at)+'</small></div>').join('')||'<p class="text-secondary">No comments yet.</p>';
  bootstrap.Modal.getOrCreateInstance(document.getElementById('sflexComments')).show();
});
function escapeHtml(value){
  const e=document.createElement('div');
  e.textContent=value;return e.innerHTML;
}
</script>
