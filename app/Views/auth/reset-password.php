<form method="post">
  <input type="hidden" name="email" value="
		<?= e($email) ?>">
  <input type="hidden" name="token" value="
			<?= e($token) ?>">
  <label>New password <input name="password" type="password" autocomplete="new-password" required>
  </label>
  <label>Confirm new password <input name="confirm_password" type="password" autocomplete="new-password" required>
  </label> <?= form_token() ?> <button>Reset password</button>
</form>