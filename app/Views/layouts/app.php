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
  <body> <?php if ($auth && $user): require __DIR__.'/navigation.php'; endif; ?>
    <main class="container lh-shell py-4 py-lg-5"> <?php if ($success): ?>
        <div class="alert alert-success d-flex gap-2" data-auto-dismiss>
          <i class="bi bi-check-circle-fill"></i>
          <div> <?= e($success) ?></div>
        </div> <?php endif; ?> <?php if ($error): ?>
          <div class="alert alert-danger d-flex gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div> <?= e($error) ?></div>
          </div>
        <?php endif; ?> <?= $content ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/app.js"></script>
  </body>
</html>