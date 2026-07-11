<?php
///template/member/users.php
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
                            <?php echo (_lang['users']); ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($rbacClass->checkPermissionOperationByName('add_operation')) { ?>
                                <div class="row mb-2">
                                    <div class="col-sm-5">

                                        <a href="#" class="btn btn-danger mb-2" data-bs-toggle="modal"
                                            data-bs-target="#addModal">
                                            <i class="mdi mdi-plus-circle me-2"></i>
                                            <?php echo (_lang['add']); ?>
                                        </a>
                                    </div>

                                </div>
                            <?php } ?>

                            <div class="table-responsive">
                                <table id="scroll-vertical-datatable"
                                    class="table table-striped dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                        <th>
                                                <?php echo ("#"); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['name']); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['mobile']); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['email']); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['unit']); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['company']); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['role']); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['last_updated_date']); ?>
                                            </th>
                                            <th style="width: 125px;">
                                                <?php echo (_lang['actions']); ?>
                                            </th>
                                        </tr>
                                    </thead>



                                    <tbody>
                                        <?php

                                        while ($usersDetails = $usersResult->fetch_assoc()) {

                                            ?>
                                            <tr>
                                            <td>
                                                    <?php echo $usersDetails['id']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $usersDetails['name']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $usersDetails['mobile']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $usersDetails['email']; ?>
                                                </td>
                                                <td>
                                                    <?php echo ($userModel->getUnitNameById($usersDetails['unit_id'])['unit_name']); ?>
                                                </td>
                                                <td>
                                                    <?php echo ($structureModel->getCompanyById($userModel->getUnitNameById($usersDetails['unit_id'])['company_id'])['company_name']); ?>
                                                </td>
                                                <td>
                                                    <?php echo $usersDetails['role']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $usersDetails['last_updated_date']; ?>
                                                </td>
                                                <td>
                                                    <?php if ($rbacClass->checkPermissionOperationByName('delete_operation')) { ?>
                                                        <a href="javascript:void(0);" class="action-icon delete-item"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                            data-table="users" data-id="<?php echo $usersDetails['id']; ?>">
                                                            <i class="mdi mdi-delete"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <?php if ($usersDetails['status'] === 'Active') { ?>
                                                        <?php if ($rbacClass->checkPermissionOperationByName('inactive_operation')) { ?>
                                                            <a href="javascript:void(0);" class="action-icon inactive-item"
                                                                data-bs-toggle="modal" data-bs-target="#inactiveModal"
                                                                data-table="users" data-id="<?php echo $usersDetails['id']; ?>">
                                                                <i class="mdi mdi-smart-card"></i>
                                                            </a>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <?php if ($rbacClass->checkPermissionOperationByName('active_operation')) { ?>
                                                            <a href="javascript:void(0);" class="action-icon active-item"
                                                                data-bs-toggle="modal" data-bs-target="#activeModal"
                                                                data-table="users" data-id="<?php echo $usersDetails['id']; ?>">
                                                                <i class="mdi mdi-smart-card-off"></i>
                                                            </a>
                                                        <?php } ?>
                                                    <?php } ?>

                                                    <?php if ($rbacClass->checkPermissionOperationByName('edit_operation')) { ?>
                                                        <a href="javascript:void(0);" class="action-icon edit-item"
                                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                                            data-table="users" data-id="<?php echo $usersDetails['id']; ?>">
                                                            <i class="mdi mdi-square-edit-outline"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog"
                                    aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel">
                                                    <?php echo (_lang['delete_item']); ?>
                                                </h5>
                                                <button type="button" class="close" data-bs-dismiss="modal"
                                                    aria-label="<?php echo (_lang['close']); ?>">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>
                                                    <?php echo (_lang['delete_confirm']); ?>
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <?php echo (_lang['close']); ?>
                                                </button>
                                                <button type="button" class="btn btn-danger confirm-delete">
                                                    <?php echo (_lang['delete']); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="modal fade" id="activeModal" tabindex="-1" role="dialog"
                                    aria-labelledby="activeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="activeModalLabel">
                                                    <?php echo (_lang['active_item']); ?>
                                                </h5>
                                                <button type="button" class="close" data-bs-dismiss="modal"
                                                    aria-label="<?php echo (_lang['close']); ?>">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>
                                                    <?php echo (_lang['active_confirm']); ?>
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <?php echo (_lang['close']); ?>
                                                </button>
                                                <button type="button" class="btn btn-danger confirm-active">
                                                    <?php echo (_lang['active']); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="inactiveModal" tabindex="-1" role="dialog"
                                    aria-labelledby="inactiveModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="inactiveModalLabel">
                                                    <?php echo (_lang['inactive_item']); ?>
                                                </h5>
                                                <button type="button" class="close" data-bs-dismiss="modal"
                                                    aria-label="<?php echo (_lang['close']); ?>">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>
                                                    <?php echo (_lang['inactive_confirm']); ?>
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <?php echo (_lang['close']); ?>
                                                </button>
                                                <button type="button" class="btn btn-danger confirm-inactive">
                                                    <?php echo (_lang['inactive']); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="addModal" tabindex="-1" role="dialog"
                                    aria-labelledby="addModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addModalLabel">
                                                    <?php echo (_lang['add']); ?>
                                                </h5>
                                                <button type="button" class="close" data-bs-dismiss="modal"
                                                    aria-label="<?php echo (_lang['close']); ?>">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Form for adding users -->
                                                <form id="addForm" name="addForm" class="form-control">
                                                    <input type="hidden" class="form-control addField" name="table_set"
                                                        id="tableSet" value="users">
                                                    <input class="form-control addField" type="hidden"
                                                        name="unique_fields" id="unique_fields"
                                                        value="<?php echo $unique_fields; ?>">

                                                    <div class="mb-3">
                                                        <label for="rbac_id" class="form-label">
                                                            <?php echo (_lang['access']); ?>
                                                        </label>

                                                        <select class="form-control addField" name="rbac_id"
                                                            id="rbac_id">
                                                            <?php
                                                            $rbacResult = $structureModel->getRBAC();
                                                            while ($rbacDetails = $rbacResult->fetch_assoc()) { ?>
                                                                <option value="<?php echo ($rbacDetails['id']); ?>">
                                                                    <?php echo ($rbacDetails['rbac_name']); ?> / <?php echo ($rbacDetails['company_name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>

                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">
                                                            <?php echo (_lang['name']); ?>
                                                        </label>
                                                        <input type="text" class="form-control addField" id="name"
                                                            name="name" required>

                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="mobile" class="form-label">
                                                            <?php echo (_lang['mobile']); ?>
                                                        </label>
                                                        <input class="form-control addField" id="mobile" name="mobile"
                                                            type="tel" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">
                                                            <?php echo (_lang['email']); ?>
                                                        </label>
                                                        <input class="form-control addField" id="email" name="email"
                                                            type="email" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">
                                                            <?php echo (_lang['password']); ?>
                                                        </label>
                                                        <input class="form-control addField" id="password"
                                                            name="password" type="password" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="unit_id" class="form-label">
                                                            <?php echo (_lang['unit']); ?>
                                                        </label>

                                                        <select class="form-control addField" name="unit_id"
                                                            id="unit_id">
                                                            <?php
                                                            $unitsResult = $structureModel->getUnits();
                                                            while ($unitsDetails = $unitsResult->fetch_assoc()) { ?>
                                                                <option value="<?php echo ($unitsDetails['id']); ?>">
                                                                    <?php echo ($unitsDetails['unit_name']); ?> /
                                                                    <?php echo ($structureModel->getCompanyById($userModel->getUnitNameById($unitsDetails['id'])['company_id'])['company_name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>

                                                    </div>
                                                    <button type="button" class="btn btn-primary" id="addDataBtn">
                                                        <?php echo (_lang['add']); ?>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="modal fade" id="editModal" tabindex="-1" role="dialog"
                                    aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">
                                                    <?php echo (_lang['edit_item']); ?>
                                                </h5>
                                                <button type="button" class="close" data-bs-dismiss="modal"
                                                    aria-label="<?php echo (_lang['close']); ?>">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Form for editing users -->
                                                <form id="editForm" name="editForm" class="form-control">
                                                    <input type="hidden" class="form-control editField" name="table_set"
                                                        id="table_set">
                                                    <input type="hidden" class="form-control editField" name="id"
                                                        id="id">

                                                    <input class="form-control editField" type="hidden"
                                                        name="unique_fields" id="unique_fields"
                                                        value="<?php echo $unique_fields; ?>">

                                                    <div class="mb-3">
                                                        <label for="rbac_id" class="form-label">
                                                            <?php echo (_lang['access']); ?>
                                                        </label>

                                                        <select class="form-control editField" name="rbac_id"
                                                            id="rbac_id">
                                                            <?php
                                                            $rbacResult = $structureModel->getRBAC();
                                                            while ($rbacDetails = $rbacResult->fetch_assoc()) { ?>
                                                                <option value="<?php echo ($rbacDetails['id']); ?>">
                                                                    <?php echo ($rbacDetails['rbac_name']); ?> / <?php echo ($rbacDetails['company_name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>

                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">
                                                            <?php echo (_lang['name']); ?>
                                                        </label>
                                                        <input type="text" class="form-control editField" id="name"
                                                            name="name" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="mobile" class="form-label">
                                                            <?php echo (_lang['mobile']); ?>
                                                        </label>
                                                        <input class="form-control editField" id="mobile" name="mobile"
                                                            type="tel" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">
                                                            <?php echo (_lang['email']); ?>
                                                        </label>
                                                        <input class="form-control editField" id="email" name="email"
                                                            type="email" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">
                                                            <?php echo (_lang['password']); ?>
                                                        </label>
                                                        <input class="form-control editField" id="password"
                                                            name="password" type="password" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="unit_id" class="form-label">
                                                            <?php echo (_lang['unit']); ?>
                                                        </label>

                                                        <select class="form-control editField" name="unit_id"
                                                            id="unit_id">
                                                            <?php
                                                            $unitsResult = $structureModel->getUnits();
                                                            while ($unitsDetails = $unitsResult->fetch_assoc()) { ?>
                                                                <option value="<?php echo ($unitsDetails['id']); ?>">
                                                                    <?php echo ($unitsDetails['unit_name']); ?> /
                                                                    <?php echo ($structureModel->getCompanyById($userModel->getUnitNameById($unitsDetails['id'])['company_id'])['company_name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>

                                                    </div>
                                                    <button type="button" class="btn btn-primary" id="editDataBtn">
                                                        <?php echo (_lang['save_changes']); ?>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div>
            <!-- end row -->

        </div> <!-- container -->

    </div> <!-- content -->


</div>