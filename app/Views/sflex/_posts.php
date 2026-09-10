<?php foreach ($posts as $p): ?>
  <article class="col-lg-3 col-md-4 col-sm-10 lh-card sflex-post">
    <div class="small text-secondary mb-2">
      <strong><?= e($p['author']) ?></strong> · <?= e(date('M j, Y', strtotime($p['created_at']))) ?>
    </div>
    <p class="mb-3"><?= nl2br(e($p['caption'])) ?></p>
    
    <?php if ($p['media_path']): ?>
      <div class="sflex-media mb-3">
        <a href="/sflex/post/<?= (int)$p['id'] ?>">
          <?php if ($p['media_type'] === 'video'): ?>
            <video controls preload="metadata" src="/sflex-media/<?= e(substr($p['media_path'], 6)) ?>"></video>
          <?php else: ?>
            <img src="/sflex-media/<?= e(substr($p['media_path'], 6)) ?>" alt="Post media" loading="lazy" class="img-fluid">
          <?php endif; ?>
        </a>
      </div>
    <?php endif; ?>
    
    <div class="d-flex flex-wrap gap-2" data-post="<?= (int)$p['id'] ?>">
      <?php foreach (['like' => '👍 Like', 'heart' => '❤️ Heart', 'smile' => '😁 Smile', 'laugh' => '😂 Laugh', 'cry' => '😭 Cry'] as $reaction => $label): ?>
        <button class="btn btn-sm <?= $p['mine'] === $reaction ? 'btn-primary' : 'btn-outline-secondary' ?>" data-reaction="<?= $reaction ?>"><?= $label ?></button>
      <?php endforeach; ?>
    </div>
    <button class="btn btn-link px-0 mt-2" data-bs-toggle="collapse" data-bs-target="#comments-<?= (int)$p['id'] ?>">💬 Comments (<?= (int)$p['comments'] ?>)</button>
    <div class="collapse" id="comments-<?= (int)$p['id'] ?>">
      <form method="post" action="/sflex/<?= (int)$p['id'] ?>/comment" class="d-flex gap-2">
        <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
        <input class="form-control" name="body" maxlength="1000" placeholder="Write a comment">
        <button class="btn btn-primary">Send</button>
      </form>
    </div>
  </article>
<?php endforeach; ?>
