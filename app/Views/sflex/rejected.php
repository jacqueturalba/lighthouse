<div class="d-flex justify-content-between mb-4">
  <h1 class="lh-page-title">Rejected posts</h1>
  <a href="/sflex" class="btn btn-outline-primary">Back to SFlex</a>
</div>
<div class="row d-grid gap-3 justify-content-center"> 
    <?php foreach($posts as$p):?> 
    <article class="col-lg-4 col-md-6 col-sm-12 lh-card">
        <span class="badge text-bg-danger">Rejected</span>
        <h2 class="h5 mt-2"> <?=e($p['author'])?> </h2>
        <p class="mb-0"> <?=nl2br(e($p['caption']))?> </p>
        <?php if($p['media_path']):?> 
            <div class="sflex-media mb-3"> 
                <?php if($p['media_type']==='video'):?> 
                    <video controls preload="metadata" src="/sflex-media/<?=e(substr($p['media_path'],6))?>">
                    </video> <?php else:?> 
                    <img src="/sflex-media/<?=e(substr($p['media_path'],6))?>" alt="Post media" loading="lazy"> 
                <?php endif;?> 
            </div> 
        <?php endif;?> 
    </article> 
    <?php endforeach;
    if(!$posts):?> 
    <div class="lh-card text-secondary">No rejected posts.</div> 
    <?php endif;?> 
</div>