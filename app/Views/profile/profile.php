<section class="container-md justify-content-center gap-4 py-2">
    <div>
        <div class="lh-card">
            <p>
                <strong>Name:</strong> <?= e($u['name']?:'—') ?>
            </p>
            <p>
                <strong>Email:</strong> <?= e($u['email']) ?>
            </p>
            <p>
                <strong>Role:</strong> <?= e(str_replace('_',' ',ucwords($u['role'],'_'))) ?>
            </p>
        </div>

        <h3 class="lh-page-title mt-3 mb-3 px-2">Account Settings</h3>

        <form class="lh-card" method="post" action="/profile/password">
            
            <div class="form-floating mb-3 py-2">
                <label for="current_password">Current password </label>
                <input id="current_password" name="current_password" type="password" class="form-control" required>
            </div>
            
            <div class="form-floating mb-3 py-2">
                <label for="password">New password </label>
                <input id="password" name="password" type="password" class="form-control" required>
            </div>

            <div class="form-floating mb-3 py-2">
                <label for="confirm_password">Confirm new password </label>
                <input id="confirm_password" name="confirm_password" type="password" class="form-control" required>
            </div>

            <?= form_token() ?> <button class="btn btn-lh-primary">Change password</button>
        </form>
    </div>
</section>