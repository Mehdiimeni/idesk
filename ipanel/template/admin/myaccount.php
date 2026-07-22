<?php
///template/admin/myaccount.php
?>

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">

                        <h4 class="page-title">
                            <?php echo _lang['profile']; ?>
                        </h4>
                    </div>
                </div>
            </div>
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
                                        </div>
                                    </div>
                                </div> <!-- end col-->


                            </div> <!-- end row -->

                        </div> <!-- end card-body/ profile-user-box-->
                    </div><!--end profile/ card -->
                </div> <!-- end col-->
            </div>
            <!-- end row -->


            <div class="row">

                <div class="col-xl-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">
                                <?php echo (_lang['edit_profile']); ?>
                            </h4>
                            <form id="editForm" name="editForm" method="post" action="./myaccount" class="form-control" enctype="multipart/form-data">
                                <input type="hidden" class="form-control editField" name="table_set" id="table_set">
                                <input type="hidden" class="form-control editField" name="id" id="id">


                                <div class="mb-3">
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
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <?php echo (_lang['email']); ?>
                                    </label>
                                    <input class="form-control editField" id="email" name="email" type="email" value="<?php echo ($_SESSION["email"]); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <?php echo (_lang['password']); ?>
                                    </label>
                                    <input class="form-control editField" id="password" name="password" value="" type="password">
                                </div>

                        
                                <button type="submit" class="btn btn-primary" id="editDataBtn">
                                    <?php echo (_lang['save_changes']); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>


            </div>
            <!-- end row -->

        </div> <!-- container -->

    </div> <!-- content -->


</div>