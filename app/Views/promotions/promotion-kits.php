<section class="d-flex flex-column gap-2 mb-4">
  <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
      <div class="d-flex flex-row mb-0 d-flex align-items-center">
        <i class="bi bi-cast fs-2 text-primary me-2"></i>
        <h1 class="lh-page-greeting mb-1">Promotion Kits</h1>
      </div>
      <p class="text-uppercase small fw-semibold text-success mb-1">Resource library</p>
      <p class="text-secondary mb-0">Approved campaign materials, ready for your next promotion and evangelism activities.</p>
    </div>
    <?php if ($user['role'] === 'super_admin'): ?>
      <div class="d-flex gap-2">
        <a class="btn btn-lh-primary" href="/promotion-kit-upload">
          <i class="bi bi-cloud-arrow-up me-2"></i>Upload kit</a>
        <a class="btn btn-outline-secondary" href="/promotion-kit-requests">Review requests</a>
      </div>
    <?php endif; ?>
  </div>
</section>
<section class="row g-4" aria-label="Available promotion kits">
<?php if (!$kits): ?>
  <div class="col-12">
    <div class="card lh-card p-5 text-center">
      <i class="bi bi-folder2-open display-5 text-primary"></i>
      <h2 class="h4 mt-3">No active kits yet</h2>
      <p class="text-secondary mb-0">New campaign resources will appear here once they are published.</p>
    </div>
  </div>
<?php else: foreach ($kits as $kit): ?>
  <div class="col-md-6 col-xl-4">
    <article class="card lh-card h-100">
      <div class="card-body d-flex flex-column gap-3">
        <div class="d-flex justify-content-between gap-3">
          <span class="badge text-bg-light text-primary text-uppercase">
            <?= e(strtoupper($kit['file_extension'])) ?>
          </span>
          <small class="text-secondary">
            <?= e(date('M j, Y', strtotime($kit['created_at']))) ?>
          </small>
        </div>
        <div>
          <h2 class="h5 mb-2">
            <a class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover" href="/promotion-kits/<?= (int)$kit['id'] ?>"><?= e($kit['title']) ?></a>
          </h2>
          <p class="text-secondary mb-0">
            <?= e($kit['description'] ?: 'Campaign resources for Lighthouse branches.') ?>
          </p>
        </div>
        <div class="mt-auto d-flex justify-content-between align-items-center">
          <small class="text-secondary">
            <i class="bi bi-person me-1"></i>
            <?= e($kit['uploader_name']) ?>
          </small>
          <?php if ($kit['access_type'] === 'all'): ?>
            <span class="badge text-bg-success">Available to All</span>
          <?php elseif ($kit['request_status'] === 'pending'): ?>
            <span class="badge text-bg-warning">Pending</span>
          <?php elseif ($kit['request_status'] === 'disapproved'): ?>
            <span class="badge text-bg-danger">Needs review</span>
          <?php elseif ($kit['request_status'] === 'approved'): ?>
            <span class="badge text-bg-success">Approved</span>
          <?php else: ?>
            <span class="badge text-bg-light">Request access</span>
          <?php endif; ?>
        </div>
        <?php if ($user['role'] === 'super_admin'): ?>
          <form method="post" action="/promotion-kits/<?= (int)$kit['id'] ?>/archive" class="mt-3">
            <input type="hidden" name="_token" value="<?= e($_SESSION['csrf']) ?>">
            <button class="btn btn-sm btn-outline-danger" type="submit">Archive kit</button>
          </form>
          <?php endif; ?>
        </div>
      </div>
    </article>
  </div>
  <?php endforeach; 
    endif; ?>
</section>
