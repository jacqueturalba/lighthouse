<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title> <?= e($title) ?> · LIGHTHOUSE</title>
    <style>
      body {
        font: 16px system-ui, sans-serif;
        max-width: 1000px;
        margin: auto;
        padding: 2rem;
        color: #14213d
      }

      header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ddd
      }

      nav a {
        margin-right: 1rem
      }

      .card,
      form {
        max-width: 540px;
        margin: 2rem 0;
        padding: 1.4rem;
        border: 1px solid #ddd;
        border-radius: 8px
      }

      input,
      select {
        box-sizing: border-box;
        width: 100%;
        padding: .65rem;
        margin: .3rem 0 1rem
      }

      button,
      .button {
        background: #145da0;
        color: #fff;
        padding: .65rem 1rem;
        border: 0;
        border-radius: 4px;
        text-decoration: none;
        cursor: pointer
      }

      .alert {
        padding: .8rem;
        background: #eef7ee
      }

      .error {
        background: #fff0f0
      }

      table {
        border-collapse: collapse;
        width: 100%
      }

      td,
      th {
        padding: .6rem;
        text-align: left;
        border-bottom: 1px solid #ddd
      }
    </style>
  </head>
  <body> <?php if ($auth && $user): require __DIR__.'/navigation.php'; endif; ?> <?php if ($success): ?><p class="alert"> <?= e($success) ?></p> <?php endif; ?> <?php if ($error): ?><p class="alert error"> <?= e($error) ?></p> <?php endif; ?><main>
      <h2> <?= e($title) ?></h2> <?= $content ?>
    </main>
  </body>
</html>