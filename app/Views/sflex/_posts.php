<?php 

$labels=['like'=>'👍','heart'=>'❤️','smile'=>'😁','laugh'=>'😂','cry'=>'😭']; 

foreach($posts as $p): ?> 
  
  <article class="col-lg-3 col-md-4 col-sm-10 lh-card sflex-post" data-sflex-post="<?= (int)$p['id'] ?>">
  <div class="small text-secondary mb-2">
    <strong> <?=e($p['author'])?> </strong> · <?=e(date('M j, Y',strtotime($p['created_at'])))?>
  </div> 
  <?php if(!empty($p['media'])): ?> 
  <div id="media-<?= (int)$p['id'] ?>" class="carousel slide sflex-carousel mb-3"  data-bs-ride="carousel">
      <div class="carousel-inner"> 
        <?php foreach($p['media'] as $i=>$m):  ?> 
          <div class="carousel-item <?= $i===0?'active':''?>">
            <a href="/sflex/post/<?= (int)$p['id'] ?>"> 
            <?php if($m['media_type']==='video'):?> 
            <video controls preload="metadata" src="/sflex-media/<?=e(substr($m['media_path'],6))?>">
            </video> 
            <?php else:?> 
            <img loading="lazy" src="/sflex-media/<?=e(substr($m['media_path'],6))?>" alt="Post image <?= $i+1?>"> 
            <?php endif;?> 
            </a>
          </div> 
        <?php endforeach;?> 
      </div> 
      <?php if(count($p['media'])>1):?> 
      <button class="carousel-control-prev" data-bs-target="#media-<?= (int)$p['id']?>" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" data-bs-target="#media-<?= (int)$p['id']?>" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button> 
    <?php endif;?>
  </div> 
  <?php endif;?> 
  <p class="mb-2"> <?=nl2br(e($p['caption']))?> </p> 
  <?php if($p['counts']):?> 
    <button class="btn btn-sm px-0 text-secondary" data-sflex-reaction-summary data-post-id="<?= (int)$p['id']?>"> 
      <?php foreach($p['counts'] as$c):?> 
        <?= $labels[$c['reaction']]??''?> 
        <?= (int)$c['total']?> 
      <?php endforeach;?> 
    </button> 
  <?php endif;?> 
  <div class="d-flex align-items-center gap-2 border-top pt-2">
    <div class="sflex-reaction-picker" data-post="<?= (int)$p['id']?>">
      <button class="btn btn-sm 
					<?= $p['mine']?'btn-primary':'btn-outline-secondary'?>" data-reaction="<?=e($p['mine']?:'like')?>"> 
          <?= $labels[$p['mine']?:'like']?> 
      </button>
      <div class="sflex-reaction-options"> 
        <?php foreach($labels as$r=>$icon):?> 
          <button class="btn btn-light rounded-circle" data-reaction="<?=$r?>" aria-label="<?=$r?>"> 
            <?=$icon?> 
          </button> 
        <?php endforeach;?> 
      </div>
    </div>
    <button class="btn btn-sm btn-outline-secondary" data-bs-target="#comments-<?= (int)$p['id']?>">
      <i class="bi bi-chat"></i> <?= (int)$p['comments']?> </button>
  </div>
</article> 
<?php endforeach;?>
