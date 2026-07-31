<form method="post">
  <label>Email <input name="email" type="email" autocomplete="email" required>
  </label>
  <label>Password <input name="password" type="password" autocomplete="current-password" required>
  </label> <?= form_token() ?> <button>Sign in</button>
</form>
<p>
  <a href="/forgot-password">Forgot password?</a>
</p>