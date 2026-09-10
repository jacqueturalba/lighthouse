<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h1 class="lh-page-title mb-1">My SFlex posts</h1>
        <p class="text-secondary mb-0">Track posts you have submitted for review.</p>
    </div>
    <a class="btn btn-outline-primary" href="/sflex">Back to SFlex</a>
</div>
<div class="row g-3">
    <?php foreach($posts as $post): ?>
        <article class="col-md-6 col-xl-4">
            <div class="lh-card h-100">
                <span class="badge text-bg-<?= $post['status']==='approved'?'success':($post['status']==='rejected'?'danger':'warning') ?>">
                    <?= e(ucfirst($post['status'])) ?>
                </span>
                <?php if(!empty($post['media'])): $media=$post['media'][0]; ?>
                <div class="sflex-media my-3">
                    <?php if($media['media_type']==='video'): ?>
                        <video controls preload="metadata" src="/sflex-media/<?=e(substr($media['media_path'],6))?>">

                        </video>
                    <?php else:?>
                        <img loading="lazy" src="/sflex-media/<?=e(substr($media['media_path'],6))?>" alt="Post media">
                    <?php endif;?>
                </div>
                <?php endif;?>
                <p class="mb-2">
                    <?= e(mb_strimwidth($post['caption'],0,160,'…')) ?>
                </p>
                <small class="text-secondary">Submitted <?=e(date('M j, Y',strtotime($post['created_at'])))?></small>
            </div>
        </article>
    <?php endforeach; 
    if(!$posts):?>
    <div class="lh-card text-secondary">You have not submitted any posts yet.</div>
    <?php endif;?>
</div>
