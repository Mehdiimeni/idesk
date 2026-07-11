<?php
///template/user/login.php
$defaultUserDir = !empty($_COOKIE['userLanguageDir']) ? $_COOKIE['userLanguageDir'] : $userLanguageDir;
<<<<<<< HEAD
$rememberLogin = !empty($_COOKIE['remember_user_token']);
=======
>>>>>>> 5591029... some change
?>
<!DOCTYPE html>
<html lang="<?php echo ($userLanguage); ?>" data-layout="topnav" dir="<?php echo ($defaultUserDir); ?>">

<head>
	<meta charset="utf-8" />
	<title><?php echo (_lang['login_user']); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="icon" href="./itheme/panel/icon/favicon.ico">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="theme-color" content="#263859">
	<meta name="author" content="Mehdi Imeni: Imeni1982@gmail.com" />

	<script src="./itheme/panel/js/hyper-config.js"></script>

	<link href="./itheme/panel/css/app-creative<?php echo ($defaultUserDir == 'rtl') ? '-rtl' : ''; ?>.min.css"
		rel="stylesheet" type="text/css" id="app-style" />

	<link href="./itheme/panel/css/icons.min.css" rel="stylesheet" type="text/css" />

	<style>
		:root {
			--dashboard-primary: #263859;
			--dashboard-secondary: #4b6cb7;
			--dashboard-bg: #f4f6f9;
			--dashboard-border: #e8edf3;
			--dashboard-muted: #8a97a6;
			--dashboard-text: #1f2d3d;
			--dashboard-soft: #eef3ff;
		}

		body.authentication-bg {
			min-height: 100vh;
			background:
				radial-gradient(circle at top left, rgba(75, 108, 183, 0.22), transparent 35%),
				radial-gradient(circle at bottom right, rgba(38, 56, 89, 0.20), transparent 38%),
				var(--dashboard-bg);
			display: flex;
			flex-direction: column;
			overflow-x: hidden;
		}

		.login-page {
			flex: 1;
			display: flex;
			align-items: center;
			padding: 40px 0;
			position: relative;
			z-index: 2;
		}

		.login-shell {
			background: #fff;
			border: 1px solid var(--dashboard-border);
			border-radius: 24px;
			overflow: hidden;
			box-shadow: 0 22px 55px rgba(31, 45, 61, 0.14);
		}

		.login-brand {
			height: 100%;
			min-height: 520px;
			background: linear-gradient(135deg, var(--dashboard-primary), var(--dashboard-secondary));
			color: #fff;
			padding: 38px;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			position: relative;
			overflow: hidden;
		}

		.login-brand:before {
			content: "";
			position: absolute;
			width: 360px;
			height: 360px;
			border-radius: 50%;
			background: rgba(255, 255, 255, 0.08);
			top: -130px;
			right: -120px;
		}

		.login-brand:after {
			content: "";
			position: absolute;
			width: 260px;
			height: 260px;
			border-radius: 50%;
			background: rgba(255, 255, 255, 0.06);
			bottom: -90px;
			left: -80px;
		}

		.login-brand-content {
			position: relative;
			z-index: 2;
		}

		.login-logo-box {
			background: rgba(255, 255, 255, 0.96);
			display: inline-flex;
			align-items: center;
			justify-content: center;
			padding: 14px 18px;
			border-radius: 18px;
			box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
		}

		.login-logo-box img {
			height: 42px;
		}

		.login-brand h2 {
			margin-top: 42px;
			margin-bottom: 14px;
			font-weight: 800;
			line-height: 1.5;
		}

		.login-brand p {
			max-width: 360px;
			line-height: 1.9;
			opacity: 0.82;
			margin: 0;
		}

		.login-brand-footer {
			position: relative;
			z-index: 2;
			font-size: 13px;
			opacity: 0.75;
		}

		.login-form-side {
			padding: 42px;
		}

		.login-title {
			color: var(--dashboard-text);
			font-weight: 800;
			margin-bottom: 8px;
		}

		.login-subtitle {
			color: var(--dashboard-muted);
			font-size: 13px;
			margin-bottom: 26px;
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

		.login-alert {
			border: 0;
			border-radius: 14px;
			background: #ffe8e8;
			color: #b42318;
			font-weight: 600;
		}

		.login-footer {
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
			.login-brand {
				min-height: auto;
				padding: 28px;
			}

			.login-brand h2 {
				margin-top: 28px;
			}

			.login-form-side {
				padding: 30px;
			}
		}

		@media (max-width: 575.98px) {
			.login-page {
				padding: 16px 0;
			}

			.login-shell {
				border-radius: 18px;
			}

			.login-brand {
				padding: 24px;
			}

			.login-form-side {
				padding: 24px;
			}
		}
	</style>
</head>

<body class="authentication-bg" dir="<?php echo ($defaultUserDir); ?>">

	<div class="floating-pattern">
		<span></span>
		<span></span>
		<span></span>
	</div>

	<main class="login-page">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xxl-9 col-xl-10 col-lg-11">
					<div class="login-shell">
						<div class="row g-0">

							<div class="col-lg-6 d-none d-lg-block">
								<div class="login-brand">
									<div class="login-brand-content">
										<a href="#" class="login-logo-box">
											<img src="./itheme/panel/images/intek.png" alt="logo">
										</a>

										<h2>
											<?php echo (_lang['login_user']); ?>
										</h2>

										<p>
											<?php echo _lang['project_name_owner']; ?>
										</p>
									</div>

									<div class="login-brand-footer">
										<script>document.write(new Date().getFullYear())</script>
										© <?php echo _lang['company_copy_right']; ?>
									</div>
								</div>
							</div>
<<<<<<< HEAD

							<div class="col-lg-6">
								<div class="login-form-side">

									<div class="text-center d-lg-none mb-4">
										<img src="./itheme/panel/images/intek.png" alt="logo" style="height:42px;">
									</div>

									<h3 class="login-title">
										<?php echo (_lang['login_user']); ?>
									</h3>

									<div class="login-subtitle">
										<?php echo _lang['project_name_owner']; ?>
									</div>

									<?php if (!empty($loginMessage) && $loginMessage != '') { ?>
										<div class="alert login-alert alert-dismissible fade show mb-4" role="alert">
											<?php echo $loginMessage; ?>
											<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
										</div>
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
												<option value="<?php echo $key; ?>">
													<?php echo $value; ?>
												</option>
											<?php } ?>
										</select>
									</div>

									<form role="form" method="POST" action="" autocomplete="on">
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
													type="email"
													name="email"
													id="emailaddress"
													required
													autocomplete="username"
													placeholder="<?php echo (_lang['email']); ?>">
											</div>
										</div>

										<div class="mb-3">
											<label for="password" class="form-label">
												<?php echo (_lang['password']); ?>
=======

							<div class="col-lg-6">
								<div class="login-form-side">

									<div class="text-center d-lg-none mb-4">
										<img src="./itheme/panel/images/intek.png" alt="logo" style="height:42px;">
									</div>

									<h3 class="login-title">
										<?php echo (_lang['login_user']); ?>
									</h3>

									<div class="login-subtitle">
										<?php echo _lang['project_name_owner']; ?>
									</div>

									<?php if (!empty($loginMessage) && $loginMessage != '') { ?>
											<div class="alert login-alert alert-dismissible fade show mb-4" role="alert">
												<?php echo $loginMessage; ?>
												<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
											</div>
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
													<option value="<?php echo $key; ?>">
														<?php echo $value; ?>
													</option>
											<?php } ?>
										</select>
									</div>

									<form role="form" method="POST" action="">
										<div class="mb-3">
											<label for="emailaddress" class="form-label">
												<?php echo (_lang['email']); ?>
>>>>>>> 5591029... some change
											</label>

											<div class="input-group">
												<span class="input-group-text">
<<<<<<< HEAD
													<i class="ri-lock-line"></i>
												</span>

												<input
													type="password"
													id="password"
													name="password"
													class="form-control"
													required
													autocomplete="current-password"
													placeholder="<?php echo (_lang['password']); ?>">

												<button class="btn toggle-password" type="button">
													<i class="ri-eye-line"></i>
												</button>
=======
													<i class="ri-mail-line"></i>
												</span>

												<input
													class="form-control"
													type="email"
													name="email"
													id="emailaddress"
													required
													placeholder="<?php echo (_lang['email']); ?>">
>>>>>>> 5591029... some change
											</div>
										</div>

										<div class="mb-4">
<<<<<<< HEAD
											<div class="form-check">
												<input
													class="form-check-input"
													type="checkbox"
													id="remember_login"
													name="remember_login"
													value="1"
													<?php echo !empty($_COOKIE['remember_user_token']) ? 'checked' : ''; ?>>

												<label class="form-check-label" for="remember_login">
													<?php echo _lang['remember_login']; ?>
												</label>
=======
											<label for="password" class="form-label">
												<?php echo (_lang['password']); ?>
											</label>

											<div class="input-group">
												<span class="input-group-text">
													<i class="ri-lock-line"></i>
												</span>

												<input
													type="password"
													id="password"
													name="password"
													class="form-control"
													placeholder="<?php echo (_lang['password']); ?>">

												<button class="btn toggle-password" type="button">
													<i class="ri-eye-line"></i>
												</button>
>>>>>>> 5591029... some change
											</div>
										</div>

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
<<<<<<< HEAD

					<div class="login-footer d-lg-none">
						<script>document.write(new Date().getFullYear())</script>
						© <?php echo _lang['company_copy_right']; ?>
					</div>

=======

					<div class="login-footer d-lg-none">
						<script>document.write(new Date().getFullYear())</script>
						© <?php echo _lang['company_copy_right']; ?>
					</div>

>>>>>>> 5591029... some change
				</div>
			</div>
		</div>
	</main>

	<script src="./itheme/panel/js/vendor.min.js"></script>
	<script src="./itheme/panel/js/app.min.js"></script>

	<script>
		document.addEventListener("DOMContentLoaded", () => {
			const language = document.getElementById("language");

			if (language) {
				language.addEventListener("change", function () {
					const selectedLanguage = this.value;
					document.cookie = `user_language=${encodeURIComponent(selectedLanguage)}; expires=${new Date(Date.now() + 150 * 24 * 60 * 60 * 1000).toUTCString()}; path=/`;
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