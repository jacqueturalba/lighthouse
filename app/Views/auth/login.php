<section class="container-md justify-content-center gap-4 py-2">
  <div class="row align-items-left gap-4 auth-container">
    <div class="text-center align-items-center py-2 col-sm-12 col-md-12 col-lg-12 col-xl-12">
      <h1><span style="color: #2563eb;">LIGHT</span><span style="color: #efb70c;">HOUSE</span></h1>
      <p style="color: #ffffff;">Welcome back! Please enter your details.</p>
    </div>
    <div class="align-items-center py-2 col-xl-6 col-lg-6 col-md-6 col-sm-12 glassmorphism">
      <form method="post">
        <div class="form-floating mb-3 py-2 col-sm-12 col-md-12 col-lg-12 col-xl-12">
          <label for="email">Email</label>
          <input name="email" type="email" autocomplete="email" id="email" class="form-control" required>
        </div>
        <div class="form-floating mb-3 py-2 col-sm-12 col-md-12 col-lg-12 col-xl-12">
          <label for="password">Password</label>
          <input name="password" type="password" autocomplete="current-password" id="password" class="form-control" required>
        </div>
        <?= form_token() ?> <button type="submit" class="btn btn-primary">Sign in</button>
      </form>
      <div class="py-2 col-md-6 col-lg-6 col-xl-4">
          <p>
            <a style="color: #ffffff;" href="/forgot-password">Forgot password?</a>
          </p>
      </div>

    </div>

    <div class="align-self-end py-0 col-xl-5 col-lg-5 col-md-5 col-sm-12">
      <!-- <img src="<?=render_asset('/images/auth/login-img.png') ?>" class="rounded img-fluid" alt="lighthouse" > -->
       &nbsp;
    </div>
  </div>
</section>