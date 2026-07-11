<?php
///template/structure/user_operation_details.php
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
                            <?php echo _lang['user_operation']; ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->


            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4"><?php echo _lang['admins_parts']; ?></h5>
                            <div id="admin-tree"></div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4"><?php echo _lang['users_parts']; ?></h5>
                            <div id="user-tree"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="rbac_id" class="form-label">
                                    <?php echo _lang['role']; ?>
                                </label>
                                <select class="form-control" name="rbac_id" id="rbac_id">

                                    <option selected value="<?php echo ($rbac_data['id']); ?>">
                                        <?php echo ($rbac_data['rbac_name']); ?> /
                                        <?php echo ($rbac_data['company_name']); ?>
                                    </option>

                                </select>
                            </div>
                            <?php if ($rbacClass->checkPermissionOperationByName('edit_operation')) { ?>

                            <button type="button" id="edit-tree-btn" class="btn btn-primary mb-2">
                                <?php echo _lang['submit']; ?>
                            </button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- end row-->

    </div> <!-- container -->

</div> <!-- content -->