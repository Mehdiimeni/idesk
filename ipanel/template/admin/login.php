<?php
///template/admin/login.php

$defultAdminDir = !empty($_COOKIE['adminLanguageDir']) ? $_COOKIE['adminLanguageDir'] : $adminLanguageDir;
?>
<!DOCTYPE html>
<html lang="<?php echo ($adminLanguage); ?>" data-layout="topnav" dir="<?php echo ($defultAdminDir); ?>">

<head>
    <meta charset="utf-8" />
    <title><?php echo (_lang['login_admin']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="../itheme/panel/icon/favicon.ico">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#263859">
    <meta name="author" content="Mehdi Imeni: Imeni1982@gmail.com" />

    <script src="../itheme/panel/js/hyper-config.js"></script>

    <link href="../itheme/panel/css/app-creative<?php echo ($defultAdminDir == 'rtl') ? '-rtl' : ''; ?>.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../itheme/panel/css/icons.min.css" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --dashboard-primary: #263859;
            --dashboard-secondary: #4b6cb7;
            --dashboard-bg: #f4f6f9;
            --dashboard-border: #e8edf3;
            --dashboard-muted: #8a97a6;
            --dashboard-text: #1f2d3d;
            --dashboard-soft: #eef3ff;
            --admin-accent: #dc3545;
        }

        body.authentication-bg {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(220, 53, 69, 0.16), transparent 34%),
                radial-gradient(circle at bottom right, rgba(75, 108, 183, 0.22), transparent 38%),
                var(--dashboard-bg);
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .admin-login-page {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 40px 0;
            position: relative;
            z-index: 2;
        }

        .admin-login-shell {
            background: #fff;
            border: 1px solid var(--dashboard-border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 22px 55px rgba(31, 45, 61, 0.14);
        }

        .admin-login-brand {
            height: 100%;
            min-height: 560px;
            background: linear-gradient(135deg, #1f2d3d, #263859 55%, #4b6cb7);
            color: #fff;
            padding: 38px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .admin-login-brand:before {
            content: "";
            position: absolute;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            top: -130px;
            right: -120px;
        }

        .admin-login-brand:after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(220, 53, 69, 0.18);
            bottom: -90px;
            left: -80px;
        }

        .admin-login-brand-content {
            position: relative;
            z-index: 2;
        }

        .admin-logo-box {
            background: rgba(255, 255, 255, 0.96);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 18px;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        }

        .admin-logo-box img {
            height: 48px;
            max-width: 230px;
            object-fit: contain;
        }

        .admin-login-brand h2 {
            margin-top: 42px;
            margin-bottom: 14px;
            font-weight: 800;
            line-height: 1.5;
        }

        .admin-login-brand p {
            max-width: 390px;
            line-height: 1.9;
            opacity: 0.82;
            margin: 0;
        }

        .admin-security-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 26px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
        }

        .admin-login-brand-footer {
            position: relative;
            z-index: 2;
            font-size: 13px;
            opacity: 0.75;
        }

        .admin-login-form-side {
            padding: 42px;
        }

        .admin-login-title {
            color: var(--dashboard-text);
            font-weight: 800;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .admin-login-title i {
            color: var(--admin-accent);
        }

        .admin-login-subtitle {
            color: var(--dashboard-muted);
            font-size: 13px;
            margin-bottom: 26px;
            line-height: 1.8;
        }

        .form-label {
            font-weight: 700;
            color: #334155;
            font-size: 13px;
        }

        .form-control,
        .form-select {
            border-radius: 14px;
            border: 1px solid var(--dashboard-border);
            padding: 12px 14px;
            min-height: 48px;
            color: var(--dashboard-text);
            background-color: #fbfcfe;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--dashboard-secondary);
            box-shadow: 0 0 0 4px rgba(75, 108, 183, 0.12);
            background-color: #fff;
        }

        .input-group-text {
            border-radius: 14px;
            border: 1px solid var(--dashboard-border);
            background: var(--dashboard-soft);
            color: var(--dashboard-secondary);
        }

        .input-group .form-control {
            border-left: 0;
            border-right: 0;
        }

        html[dir="rtl"] .input-group .form-control {
            border-right: 0;
            border-left: 0;
        }

        .toggle-password {
            border-radius: 14px;
            border: 1px solid var(--dashboard-border);
            background: #fff;
            color: #64748b;
        }

        .btn-login {
            min-height: 48px;
            border-radius: 14px;
            border: 0;
            background: linear-gradient(135deg, var(--dashboard-primary), var(--dashboard-secondary));
            font-weight: 800;
            box-shadow: 0 12px 24px rgba(75, 108, 183, 0.22);
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(75, 108, 183, 0.32);
        }

        .admin-alert {
            border: 0;
            border-radius: 14px;
            background: #fff4db;
            color: #946200;
            font-weight: 600;
        }

        .admin-login-footer {
            text-align: center;
            color: var(--dashboard-muted);
            font-size: 13px;
            padding: 18px;
        }

        .floating-pattern {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 1;
            opacity: 0.35;
        }

        .floating-pattern span {
            position: absolute;
            border-radius: 999px;
            background: rgba(75, 108, 183, 0.12);
        }

        .floating-pattern span:nth-child(1) {
            width: 90px;
            height: 90px;
            top: 9%;
            left: 8%;
        }

        .floating-pattern span:nth-child(2) {
            width: 140px;
            height: 140px;
            bottom: 12%;
            right: 8%;
        }

        .floating-pattern span:nth-child(3) {
            width: 54px;
            height: 54px;
            top: 48%;
            right: 18%;
        }

        @media (max-width: 991.98px) {
            .admin-login-brand {
                min-height: auto;
                padding: 28px;
            }

            .admin-login-brand h2 {
                margin-top: 28px;
            }

            .admin-login-form-side {
                padding: 30px;
            }
        }

        @media (max-width: 575.98px) {
            .admin-login-page {
                padding: 16px 0;
            }

            .admin-login-shell {
                border-radius: 18px;
            }

            .admin-login-brand {
                padding: 24px;
            }

            .admin-login-form-side {
                padding: 24px;
            }
        }
    </style>
</head>

<body class="authentication-bg pb-0" dir="<?php echo ($defultAdminDir); ?>">

    <div class="floating-pattern">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <main class="admin-login-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-9 col-xl-10 col-lg-11">
                    <div class="admin-login-shell">
                        <div class="row g-0">

                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="admin-login-brand">
                                    <div class="admin-login-brand-content">
                                        <a href="#" class="admin-logo-box">
                                            <img src="../itheme/panel/images/logo-intek.png" alt="dark logo">
                                        </a>
<<<<<<< HEAD

                                        <h2>
                                            <?php echo (_lang['login_admin']); ?>
                                        </h2>

                                        <p>
                                            <?php echo (_lang['login_adver2']); ?>
                                        </p>

=======

                                        <h2>
                                            <?php echo (_lang['login_admin']); ?>
                                        </h2>

                                        <p>
                                            <?php echo (_lang['login_adver2']); ?>
                                        </p>

>>>>>>> 5591029... some change
                                        <div class="admin-security-badge">
                                            <i class="ri-shield-user-line"></i>
                                            <?php echo (_lang['company_owner']); ?>
                                        </div>
                                    </div>

                                    <div class="admin-login-brand-footer">
                                        <script>document.write(new Date().getFullYear())</script>
                                        © <?php echo (_lang['company_owner']); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="admin-login-form-side">

                                    <div class="text-center d-lg-none mb-4">
                                        <img src="../itheme/panel/images/logo-intek.png" alt="dark logo" style="height:52px; max-width:230px;">
                                    </div>

                                    <h3 class="admin-login-title">
                                        <i class="ri-admin-line"></i>
                                        <?php echo (_lang['login']); ?>
                                    </h3>

                                    <div class="admin-login-subtitle">
                                        <?php echo (_lang['login_tip1']); ?>
                                    </div>

                                    <?php if (isset($loginMessage) && $loginMessage != '') { ?>
<<<<<<< HEAD
                                        <div class="alert admin-alert alert-dismissible fade show mb-4" role="alert">
                                            <i class="ri-alert-line me-1"></i>
                                            <?php echo ($loginMessage); ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
=======
                                            <div class="alert admin-alert alert-dismissible fade show mb-4" role="alert">
                                                <i class="ri-alert-line me-1"></i>
                                                <?php echo ($loginMessage); ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
>>>>>>> 5591029... some change
                                    <?php } ?>

                                    <div class="mb-3">
                                        <label for="language" class="form-label">
                                            <?php echo (_lang['language_selection']); ?>
                                        </label>

                                        <select class="form-select" id="language" name="language">
                                            <option value="">
                                                <?php echo (_lang['language_selection']); ?>
                                            </option>

                                            <?php foreach ($allLanguages as $key => $value) { ?>
<<<<<<< HEAD
                                                <option value="<?php echo $key; ?>">
                                                    <?php echo $value; ?>
                                                </option>
=======
                                                    <option value="<?php echo $key; ?>">
                                                        <?php echo $value; ?>
                                                    </option>
>>>>>>> 5591029... some change
                                            <?php } ?>
                                        </select>
                                    </div>

<<<<<<< HEAD
                                    <form method="POST" action="" autocomplete="on">
=======
                                    <form method="POST" action="">
>>>>>>> 5591029... some change
                                        <div class="mb-3">
                                            <label for="emailaddress" class="form-label">
                                                <?php echo (_lang['email']); ?>
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri-mail-line"></i>
                                                </span>

                                                <input
                                                    class="form-control"
                                                    placeholder="<?php echo (_lang['email']); ?>"
                                                    name="email"
                                                    type="email"
                                                    id="emailaddress"
<<<<<<< HEAD
                                                    autocomplete="username"
=======
>>>>>>> 5591029... some change
                                                    required>
                                            </div>
                                        </div>

<<<<<<< HEAD
                                        <div class="mb-3">
=======
                                        <div class="mb-4">
>>>>>>> 5591029... some change
                                            <label for="password" class="form-label">
                                                <?php echo (_lang['password']); ?>
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri-lock-line"></i>
                                                </span>

                                                <input
                                                    class="form-control"
                                                    required
                                                    id="password"
                                                    placeholder="<?php echo (_lang['password']); ?>"
                                                    name="password"
<<<<<<< HEAD
                                                    type="password"
                                                    autocomplete="current-password">
=======
                                                    type="password">
>>>>>>> 5591029... some change

                                                <button class="btn toggle-password" type="button">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                            </div>
                                        </div>

<<<<<<< HEAD
                                        <div class="mb-4">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="remember_login"
                                                    name="remember_login"
                                                    value="1"
                                                    <?php echo !empty($_COOKIE['remember_admin_token']) ? 'checked' : ''; ?>>

                                                <label class="form-check-label" for="remember_login">
                                                    <?php echo _lang['remember_login']; ?>
                                                </label>
                                            </div>
                                        </div>

=======
>>>>>>> 5591029... some change
                                        <button
                                            class="btn btn-primary btn-login w-100"
                                            type="submit"
                                            name="login"
                                            value="<?php echo (_lang['login']); ?>">
                                            <i class="ri-login-circle-line me-1"></i>
                                            <?php echo (_lang['login']); ?>
                                        </button>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="admin-login-footer d-lg-none">
                        <script>document.write(new Date().getFullYear())</script>
                        © <?php echo (_lang['company_owner']); ?>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <script src="../itheme/panel/js/vendor.min.js"></script>
    <script src="../itheme/panel/js/app.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const language = document.getElementById("language");

            if (language) {
                language.addEventListener("change", function () {
                    const selectedLanguage = this.value;
                    document.cookie = `admin_language=${encodeURIComponent(selectedLanguage)}; expires=${new Date(Date.now() + 150 * 24 * 60 * 60 * 1000).toUTCString()}; path=/`;
                    location.reload();
                });
            }

            document.querySelectorAll(".toggle-password").forEach(button => {
                button.addEventListener("click", function () {
                    const passwordInput = this.closest(".input-group").querySelector("input");
                    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";

                    passwordInput.setAttribute("type", type);
                    this.querySelector("i").classList.toggle("ri-eye-line");
                    this.querySelector("i").classList.toggle("ri-eye-off-line");
                });
            });
        });
    </script>

</body>
</html>