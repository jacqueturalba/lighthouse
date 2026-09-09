<h1 class="lh-page-title mb-4">Review SFlex posts</h1>
<div class="row gap-3"> 
    <?php foreach($posts as$p):?> 
    <article class="col-3 lh-card">
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
    <div class="lh-card">No posts awaiting review.</div> 
    <?php endif;?> 
</div>