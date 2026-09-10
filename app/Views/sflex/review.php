<div class="d-flex justify-content-between mb-4">
  <h1 class="lh-page-title">Review SFlex posts</h1>
  <a href="/sflex" class="btn btn-outline-primary">Back to SFlex</a>
</div>
<div class="row gap-3"> 
    <?php foreach($posts as$p):?> 
    <article class="col-lg-4 col-md-6 col-sm-12 lh-card">
        <strong> <?=e($p['author'])?> </strong>
        <p> <?=e($p['caption'])?> </p>
        <?php if($p['media_path']):?> 
            <div class="sflex-media mb-3"> 
                <?php if($p['media_type']==='video'):?> 
                    <video controls preload="metadata" src="/sflex-media/<?=e(substr($p['media_path'],6))?>">
                    </video> <?php else:?> 
                    <img src="/sflex-media/<?=e(substr($p['media_path'],6))?>" alt="Post media" loading="lazy"> 
                <?php endif;?> 
            </div> 
        <?php endif;?> 
        <form method="post" action="/sflex/<?=$p['id']?>/review" class="d-flex gap-2">
            <input type="hidden" name="_token" value="<?=e($_SESSION['csrf'])?>">
        <button class="btn btn-success" name="status" value="approved">Approve</button>
        <button class="btn btn-outline-danger" name="status" value="rejected">Reject</button>
        </form>
    </article> 
    <?php endforeach;if(!$posts):?> 
    <div class="lh-card text-secondary">No posts awaiting review.</div> 
    <?php endif;?> 
</div>