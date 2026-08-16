<section class="lh-loginp-page">
    <div class="lh-login-wrapper">

        <!-- LEFT SIDE -->
        <div class="lh-login-branding">

            <div class="lh-login-brand">
                <div class="lh-login-brand-icon">
                    <i class="bi bi-lighthouse"></i>
                </div>

                <div>
                    <div class="lh-login-brand-name">
                        LIGHTHOUSE
                    </div>
                    <div class="lh-login-brand-subtitle">
                        Admin Portal
                    </div>
                </div>
            </div>

            <div class="lh-login-message">
                <h1>
                    Grow in Faith.<br>
                    Serve with<br>
                    <span>Purpose.</span>
                </h1>

                <div class="lh-login-accent"></div>

                <p>
                    A system built to manage,<br>
                    organize, and share the light<br>
                    of God's truth.
                </p>
            </div>

            <div class="lh-login-verse">
                <div class="lh-login-verse-icon">
                    <i class="bi bi-book"></i>
                </div>

                <div>
                    <div class="lh-login-verse-text">
                        Your word is a lamp to my feet<br>
                        and a light to my path.
                    </div>

                    <div class="lh-login-verse-reference">
                        Psalm 119:105
                    </div>
                </div>
            </div>

        </div>


        <!-- RIGHT SIDE -->
        <div class="lh-login-form-area">

            <div class="lh-login-card">

                <div class="lh-login-card-header">
                    <h2>
                        Welcome <span>Back!</span>
                    </h2>

                    <p>
                        Sign in to continue to Lighthouse.
                    </p>
                </div>

                <form method="post">

                    <?= form_token() ?>

                    <!-- EMAIL -->
                    <div class="lh-login-field">
                        <label for="email">
                            Email Address
                        </label>

                        <div class="lh-login-input-wrapper">
                            <i class="bi bi-envelope"></i>

                            <input
                                name="email"
                                type="email"
                                autocomplete="email"
                                id="email"
                                placeholder="Enter your email"
                                required
                            >
                        </div>
                    </div>


                    <!-- PASSWORD -->
                    <div class="lh-login-field">
                        <label for="password">
                            Password
                        </label>

                        <div class="lh-login-input-wrapper">
                            <i class="bi bi-lock"></i>

                            <input
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                id="password"
                                placeholder="Enter your password"
                                required
                            >

                            <button
                                type="button"
                                class="lh-password-toggle"
                                id="togglePassword"
                                aria-label="Show password"
                            >
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>


                    <!-- OPTIONS -->
                    <div class="lh-login-options">

                        <label class="lh-remember">
                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                            >

                            <span>
                                Remember me
                            </span>
                        </label>

                        <a href="/forgot-password">
                            Forgot password?
                        </a>

                    </div>


                    <!-- SIGN IN -->
                    <button
                        type="submit"
                        class="lh-login-submit"
                    >
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Sign In</span>
                    </button>

                </form>


                <!-- DIVIDER -->
                <div class="lh-login-divider">
                    <span></span>
                    <small>or</small>
                    <span></span>
                </div>


                <!-- SSO -->
                <button
                    type="button"
                    class="lh-login-sso"
                >
                    <i class="bi bi-shield-lock"></i>
                    <span>Sign in with SSO</span>
                </button>


                <div class="lh-login-footer">
                    © 2025 Lighthouse. All rights reserved.
                </div>

            </div>

        </div>

    </div>
</section>