<!doctype html>
<html lang="en" data-bs-theme="light">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="<?= e($_SESSION['csrf'] ?? '') ?>">
    <title> <?= e($title) ?> · LIGHTHOUSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css?v=<?= config('VERSION') ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css?v=<?= config('VERSION') ?>" rel="stylesheet">
    <link href="/assets/css/app.css?v=<?= config('VERSION') ?>" rel="stylesheet">
  </head>
  <body class="<?= (in_array(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ['/login', '/forgot-password', '/reset-password'])) ? 'lh-login-page' : 'auth-class' ?>"> 
  <?php if ($auth && $user): require __DIR__.'/navigation.php'; endif; ?>
  <div id="page-loader" class="page-loader">
      <div class="spinner-border" role="status" aria-label="Loading"></div>
  </div>
    <main class="<?= (in_array(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ['/sflex'])) ? 'container-fluid' : 'container lh-shell' ?>  py-4 py-lg-5 page-content" id="page-content"> 
      <?php if ($success): ?>
        <div class="alert alert-success d-flex gap-2" data-auto-dismiss>
          <i class="bi bi-check-circle-fill"></i>
          <div> <?= e($success) ?></div>
        </div> <?php endif; ?> <?php if ($error): ?>
          <div class="alert alert-danger d-flex gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div> <?= e($error) ?></div>
          </div>
        <?php endif; ?> 
        <?= $content ?>
    </main>
    <?php if ($auth && $user): ?>
      <div id="lh-upload-manager" class="lh-upload-manager shadow-sm" hidden aria-live="polite">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <strong class="small"><i class="bi bi-cloud-arrow-up me-1"></i>Upload activity</strong>
          <button type="button" class="btn-close btn-sm" aria-label="Hide upload activity" data-upload-manager-close></button>
        </div>
        <div data-upload-manager-items></div>
        <span class="mt-2 small text-muted d-flex p-1 fw-light" data-upload-manager-count>Do not close the browser tab or navigate away while uploading.</span>
      </div>
      <div id="lh-upload-toasts" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
    <?php endif; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const loader = document.getElementById('page-loader');

        if (loader) {
            requestAnimationFrame(() => {
                loader.classList.add('is-hidden');
            });
        }

        const content = document.getElementById('page-content');

        if (content) {
            requestAnimationFrame(() => {
                content.classList.add('is-loaded');
            });
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js?v=<?= config('VERSION') ?>"></script>
    <script src="/assets/js/app.js?v=<?= config('VERSION') ?>"></script>
    <?php require __DIR__.'/pr-modal-delete.php'; ?>
  </body>
</html>
