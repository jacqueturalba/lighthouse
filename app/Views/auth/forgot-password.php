<form method="post">
  <label>Email <input name="email" type="email" autocomplete="email" required>
  </label> <?= form_token() ?> <button>Send reset link</button>
</form>