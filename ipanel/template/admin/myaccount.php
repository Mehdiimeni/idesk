<?php
///template/admin/myaccount.php
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row mt-3">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="page-title mb-0">
                            <i class="ri-user-settings-line me-1"></i>
                            <?php echo _lang['profile']; ?>
                        </h4>
                    </div>
                </div>
            </div>
<<<<<<< HEAD

            <div class="row">
                <div class="col-12">

                    <div class="card border-0 shadow-sm overflow-hidden mb-4">
                        <div class="card-body p-0">

                            <div class="profile-header-soft">
                                <div class="row align-items-center">

                                    <div class="col-auto">
                                        <div class="profile-avatar-soft">
                                            <i class="ri-admin-line"></i>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <h4 class="mb-1 text-white">
                                            <?php echo htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8'); ?>
                                        </h4>

                                        <div class="text-white-50">
                                            <i class="ri-phone-line me-1"></i>
                                            <?php echo htmlspecialchars($adminMobile, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>

                                        <div class="text-white-50 mt-1">
                                            <i class="ri-mail-line me-1"></i>
                                            <?php echo htmlspecialchars($adminEmail, ENT_QUOTES, 'UTF-8'); ?>
=======
            <!-- end page title -->
            <div class="row">
                <div class="col-sm-12">
                    <!-- Profile -->
                    <div class="card bg-primary">
                        <div class="card-body profile-user-box">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="row align-items-center">
                                        
                                        <div class="col">
                                            <div>
                                                <h4 class="mt-1 mb-1 text-white">
                                                    <?php echo ($_SESSION["name"]); ?>
                                                </h4>
                                                <p class="font-13 text-white-50">
                                                    <?php echo ($_SESSION["mobile"]); ?>
                                                </p>
                                            </div>
>>>>>>> 5591029... some change
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <div class="row justify-content-start">
                <div class="col-xl-6 col-lg-8">

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom">
                            <h4 class="header-title mb-0">
                                <i class="ri-edit-2-line me-1"></i>
                                <?php echo _lang['edit_profile']; ?>
                            </h4>
                        </div>

                        <div class="card-body">

                            <form id="editForm" name="editForm" method="post" action="./myaccount"
                                enctype="multipart/form-data">

                                <input type="hidden" class="editField" name="table_set" id="table_set">
                                <input type="hidden" class="editField" name="id" id="id">

                                <div class="mb-3">
<<<<<<< HEAD
                                    <label for="name" class="form-label fw-bold">
                                        <?php echo _lang['name']; ?>
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-user-line"></i>
                                        </span>

                                        <input type="text" class="form-control editField" id="name" name="name"
                                            value="<?php echo htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8'); ?>"
                                            required>
                                    </div>
=======
                                    <label for="name" class="form-label">
                                        <?php echo (_lang['name']); ?>
                                    </label>
                                    <input type="text" class="form-control editField" id="name" name="name" value="<?php echo ($_SESSION["name"]); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="mobile" class="form-label">
                                        <?php echo (_lang['mobile']); ?>
                                    </label>
                                    <input class="form-control editField" id="mobile" name="mobile" type="tel" value="<?php echo ($_SESSION["mobile"]); ?>" required>
>>>>>>> 5591029... some change
                                </div>

                                <div class="mb-3">
                                    <label for="mobile" class="form-label fw-bold">
                                        <?php echo _lang['mobile']; ?>
                                    </label>
<<<<<<< HEAD

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-phone-line"></i>
                                        </span>

                                        <input class="form-control editField" id="mobile" name="mobile" type="tel"
                                            value="<?php echo htmlspecialchars($adminMobile, ENT_QUOTES, 'UTF-8'); ?>"
                                            required>
                                    </div>
=======
                                    <input class="form-control editField" id="email" name="email" type="email" value="<?php echo ($_SESSION["email"]); ?>" required>
>>>>>>> 5591029... some change
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold">
                                        <?php echo _lang['email']; ?>
                                    </label>
<<<<<<< HEAD

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-mail-line"></i>
                                        </span>

                                        <input class="form-control editField" id="email" name="email" type="email"
                                            value="<?php echo htmlspecialchars($adminEmail, ENT_QUOTES, 'UTF-8'); ?>"
                                            required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label fw-bold">
                                        <?php echo _lang['password']; ?>
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-lock-line"></i>
                                        </span>

                                        <input class="form-control editField" id="password" name="password" value=""
                                            type="password" autocomplete="new-password">

                                        <button class="btn btn-light border toggle-password" type="button">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-4" id="editDataBtn">
                                        <i class="ri-save-3-line me-1"></i>
                                        <?php echo _lang['save_changes']; ?>
                                    </button>
                                </div>

=======
                                    <input class="form-control editField" id="password" name="password" value="" type="password">
                                </div>

                        
                                <button type="submit" class="btn btn-primary" id="editDataBtn">
                                    <?php echo (_lang['save_changes']); ?>
                                </button>
>>>>>>> 5591029... some change
                            </form>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .profile-header-soft {
        background: linear-gradient(135deg, #263859, #4b6cb7);
        padding: 32px;
        border-radius: 0;
    }

    .profile-avatar-soft {
        width: 76px;
        height: 76px;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.16);
    }

    .input-group-text {
        min-width: 46px;
        justify-content: center;
        background: var(--bs-tertiary-bg);
        border-color: var(--bs-border-color);
    }

    .form-control {
        border-color: var(--bs-border-color);
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(75, 108, 183, 0.15);
        border-color: #4b6cb7;
    }

    .card {
        border-radius: 18px;
    }

    .card-header {
        padding: 18px 22px;
    }

    .card-body {
        padding: 24px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".toggle-password").forEach(function (button) {
            button.addEventListener("click", function () {
                const input = this.closest(".input-group").querySelector("input");
                const icon = this.querySelector("i");

                const type = input.getAttribute("type") === "password" ? "text" : "password";

                input.setAttribute("type", type);
                icon.classList.toggle("ri-eye-line");
                icon.classList.toggle("ri-eye-off-line");
            });
        });
    });
</script>