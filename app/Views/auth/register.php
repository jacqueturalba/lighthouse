<section class="container-md justify-content-center gap-4 py-2">

    <h3 class="lh-page-title mt-3 mb-3 px-2">Create Account</h3>

    <form class="lh-card" method="post">

        <div class="form-floating mb-3 py-2">
            <label>Name </label>
            <input name="name" maxlength="120" class="form-control" required>
        </div>

        <div class="form-floating mb-3 py-2">
            <label>Email </label>
            <input name="email" type="email" class="form-control" required>
        </div>

        <p>New accounts are created with the Admin role. Super Admin access can only be granted from User Management.</p>

        <div class="form-floating mb-3 py-2">
            <label>Temporary password </label>
            <input name="password" type="password" class="form-control" required>
        </div>

        <div class="form-floating mb-3 py-2">
            <label>Confirm password </label>
            <input name="confirm_password" type="password" class="form-control" required>
        </div>

        <?= form_token() ?> <button type="submit" class="btn btn-primary">Create account</button>
    </form>
</section>