<!doctype html>
<html lang="en" data-bs-theme="light">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title> <?= e($title) ?> · LIGHTHOUSE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
  </head>
  <body class="<?= $_SERVER['REQUEST_URI'] === '/login' ? 'bg-auth' : 'auth-class' ?>"> <?php if ($auth && $user): require __DIR__.'/navigation.php'; endif; ?>
  <div id="page-loader" class="page-loader">
      <div class="spinner-border" role="status" aria-label="Loading"></div>
  </div>
    <main class="container lh-shell py-4 py-lg-5 page-content" id="page-content"> <?php if ($success): ?>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/app.js"></script>
    <?php require __DIR__.'/pr-modal-delete.php'; ?>
  </body>
</html>