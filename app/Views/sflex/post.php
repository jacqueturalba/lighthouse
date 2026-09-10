<div class="row justify-content-center">
  <div class="col-xl-9">
    <a href="/sflex">← Back to SFlex</a>
    <article class="lh-card mt-3">
      <div class="small text-secondary">
        <strong> <?=e($post['author'])?> </strong> · <?=e(date('M j, Y',strtotime($post['created_at'])))?>
      </div> 
      <?php if($post['media_path']):?> 
            <div class="sflex-media sflex-media-large my-3"> 
                <?php $url='/sflex-media/'.e(substr($post['media_path'],6));
                if($post['media_type']==='video'):?> 
                <video controls preload="metadata" src="<?=$url?>">
                </video> 
        <?php else:?> 
                <img src="<?=$url?>" alt="Post media"> 
        <?php endif;?> 
        </div> 
        <?php endif;?> 
        <p> <?=nl2br(e($post['caption']))?> </p>
    </article>
  </div>
</div>