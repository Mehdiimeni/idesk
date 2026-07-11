<?php
///template/structure/user_operation.php
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
                            <?php echo (_lang['user_operation']); ?>
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

                                        <a href="./user_operation?add=r" class="btn btn-danger mb-2">
                                            <i class="mdi mdi-plus-circle me-2"></i>
                                            <?php echo (_lang['add']); ?>
                                        </a>
                                    </div>

                                </div>
                            <?php } ?>

                            <div class="table-responsive">
                                <table class="table table-centered w-100 dt-responsive nowrap"
                                    id="alternative-page-datatable">
                                    <thead>
                                        <tr>
                                            <th>
                                                <?php echo ("#"); ?>
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

                                        while ($userOperationDetails = $userOperationResult->fetch_assoc()) {

                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $userOperationDetails['id']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $userOperationDetails['rbac_name']; ?>
                                                </td>

                                                <td>
                                                    <?php $dateConverter = new DateConverter($userOperationDetails['last_updated_date'], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?>
                                                </td>
                                                <td>


                                                    <?php if ($rbacClass->checkPermissionOperationByName('delete_operation')) { ?>
                                                        <a href="javascript:void(0);" class="action-icon delete-item"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                            data-table="permissions_operation"
                                                            data-id="<?php echo $userOperationDetails['id']; ?>">
                                                            <i class="mdi mdi-delete"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <?php if ($userOperationDetails['status'] === 'Active') { ?>
                                                        <?php if ($rbacClass->checkPermissionOperationByName('inactive_operation')) { ?>
                                                            <a href="javascript:void(0);" class="action-icon inactive-item"
                                                                data-bs-toggle="modal" data-bs-target="#inactiveModal"
                                                                data-table="permissions_operation"
                                                                data-id="<?php echo $userOperationDetails['id']; ?>">
                                                                <i class="mdi mdi-smart-card"></i>
                                                            </a>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <?php if ($rbacClass->checkPermissionOperationByName('active_operation')) { ?>
                                                            <a href="javascript:void(0);" class="action-icon active-item"
                                                                data-bs-toggle="modal" data-bs-target="#activeModal"
                                                                data-table="permissions_operation"
                                                                data-id="<?php echo $userOperationDetails['id']; ?>">
                                                                <i class="mdi mdi-smart-card-off"></i>
                                                            </a>
                                                        <?php } ?>
                                                    <?php } ?>

                                                    <?php if ($rbacClass->checkPermissionOperationByName('edit_operation')) { ?>
                                                        <a href="./user_operation?id=<?php echo $userOperationDetails['id']; ?>"
                                                            class="action-icon">
                                                            <i class="mdi mdi-square-edit-outline"></i>
                                                        </a>
                                                    <?php } ?>

                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <div class="modal fade" id="deleteModal" tabindex="-1" userOperation="dialog"
                                    aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" userOperation="document">
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


                                <div class="modal fade" id="addModal" tabindex="-1" userOperation="dialog"
                                    aria-labelledby="addModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" userOperation="document">
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
                                                <!-- Form for adding userOperation -->
                                                <form id="addForm" name="addForm" class="form-control">

                                                    <input type="hidden" class="form-control addField" name="table_set"
                                                        id="table_set" value="permissions_operation">
                                                    <input class="form-control addField" type="hidden"
                                                        name="unique_fields" id="unique_fields"
                                                        value="<?php echo $unique_fields; ?>">
                                                    <div class="mb-3">
                                                        <label for="rbac_id" class="form-label">
                                                            <?php echo (_lang['role']); ?>
                                                        </label>
                                                        <select class="form-control addField" name="rbac_id"
                                                            id="rbac_id">
                                                            <?php
                                                            while ($rbacDetails = $rbacResult->fetch_assoc()) { ?>
                                                                <option value="<?php echo ($rbacDetails['id']); ?>">
                                                                    <?php echo ($rbacDetails['rbac_name']); ?> /
                                                                    <?php echo ($rbacDetails['company_name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="parts" class="form-label">
                                                            <?php echo (_lang['parts']); ?>
                                                        </label>

                                                        <select multiple="" class="form-control addField" id="optgroup"
                                                            name="parts" style="position: absolute; left: -9999px;">

                                                            <?php
                                                            while ($groupDetails = $groupResult->fetch_assoc()) { ?>
                                                                <optgroup
                                                                    label="<?php echo ($groupDetails['users_groups_name'] . " ( " . _lang['users'] . " )"); ?>">

                                                                    <?php
                                                                    $partResult = $structureModel->getPartByGroups($groupDetails['id']);
                                                                    while ($partDetails = $partResult->fetch_assoc()) { ?>

                                                                        <option value="u<?php echo ($partDetails['id']); ?>">
                                                                            <?php echo ($partDetails['users_parts_name']); ?>
                                                                        </option>
                                                                    <?php } ?>

                                                                </optgroup>
                                                            <?php } ?>

                                                            <?php
                                                            while ($adminGroupDetails = $adminGroupResult->fetch_assoc()) { ?>
                                                                <optgroup
                                                                    label="<?php echo ($adminGroupDetails['admins_groups_name'] . " ( " . _lang['admins'] . " )"); ?>">

                                                                    <?php
                                                                    $adminPartResult = $structureModel->getAdminPartByGroups($adminGroupDetails['id']);
                                                                    while ($adminPartDetails = $adminPartResult->fetch_assoc()) { ?>

                                                                        <option
                                                                            value="a<?php echo ($adminPartDetails['id']); ?>">
                                                                            <?php echo ($adminPartDetails['admins_parts_name']); ?>
                                                                        </option>
                                                                    <?php } ?>

                                                                </optgroup>
                                                            <?php } ?>

                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="operation" class="form-label">
                                                            <?php echo (_lang['operation']); ?>
                                                        </label>

                                                        <select id="operation" name="operation"
                                                            class="addField select2 form-control select2-multiple"
                                                            data-toggle="select2" multiple="multiple">
                                                            <?php
                                                            while ($operationDetails = $operationResult->fetch_assoc()) { ?>

                                                                <option value="<?php echo ($operationDetails['id']); ?>"
                                                                    id="customCheck<?php echo ($operationDetails['id']); ?>">
                                                                    <?php echo ($operationDetails['operation_name']); ?>
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

                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div>
            <!-- end row -->

        </div> <!-- container -->

    </div> <!-- content -->


</div>