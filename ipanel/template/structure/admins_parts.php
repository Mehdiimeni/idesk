<?php
///template/structure/admins_parts.php
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
                            <?php echo (_lang['admins_parts']); ?>
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

                                        <a href="#" class="btn btn-danger mb-2" data-bs-toggle="modal" data-bs-target="#addModal">
                                            <i class="mdi mdi-plus-circle me-2"></i>
                                            <?php echo (_lang['add']); ?>
                                        </a>
                                    </div>

                                </div>
                            <?php } ?>

                            <div class="table-responsive">
                                <table id="scroll-vertical-datatable" class="table table-striped dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                        <th>
                                                <?php echo ("#"); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['name']); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['admins_groups']); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['caption']); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['description']); ?>
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

                                        while ($adminPartsDetails = $adminPartsResult->fetch_assoc()) {

                                        ?>
                                            <tr>
                                            <td>
                                                    <?php echo $adminPartsDetails['id']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $adminPartsDetails['admins_parts_name']; ?>
                                                </td>
                                                <td>
                                                    <?php echo _lang[$structureModel->getadminGroupById($adminPartsDetails['admins_groups_id'])['admins_groups_name']]; ?>
                                                </td>
                                                <td>
                                                    <?php echo _lang[$adminPartsDetails['admins_parts_caption']]; ?>
                                                </td>
                                                <td>
                                                    <?php echo $adminPartsDetails['admins_parts_description']; ?>
                                                </td>
                                                <td>
                                                    <?php $dateConverter = new DateConverter($adminPartsDetails['last_updated_date'], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?>
                                                </td>
                                                <td>


                                                    <?php if ($rbacClass->checkPermissionOperationByName('delete_operation')) { ?>
                                                        <a href="javascript:void(0);" class="action-icon delete-item" data-bs-toggle="modal" data-bs-target="#deleteModal" data-table="admins_parts" data-id="<?php echo $adminPartsDetails['id']; ?>">
                                                            <i class="mdi mdi-delete"></i>
                                                        </a>
                                                    <?php } ?>

                                                    <?php if ($rbacClass->checkPermissionOperationByName('edit_operation')) { ?>
                                                        <a href="javascript:void(0);" class="action-icon edit-item" data-bs-toggle="modal" data-bs-target="#editModal" data-table="admins_parts" data-id="<?php echo $adminPartsDetails['id']; ?>">
                                                            <i class="mdi mdi-square-edit-outline"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel">
                                                    <?php echo (_lang['delete_item']); ?>
                                                </h5>
                                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo (_lang['close']); ?>">
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


                                <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addModalLabel">
                                                    <?php echo (_lang['add']); ?>
                                                </h5>
                                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo (_lang['close']); ?>">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Form for adding admins_parts -->
                                                <form id="addForm" name="addForm" class="form-control">

                                                    <input type="hidden" class="form-control addField" name="table_set" id="tableSet" value="admins_parts">
                                                    <input class="form-control addField" type="hidden" name="unique_fields" id="unique_fields" value="<?php echo $unique_fields; ?>">
                                                    <div class="mb-3">
                                                        <label for="admins_groups_id" class="form-label">
                                                            <?php echo (_lang['admins_parts']); ?>
                                                        </label>

                                                        <select class="form-control addField" name="admins_groups_id" id="admins_groups_id">
                                                            <?php
                                                            while ($adminGroupsDetails = $adminGroupsResult->fetch_assoc()) { ?>
                                                                <option value="<?php echo ($adminGroupsDetails['id']); ?>">
                                                                    <?php echo ($adminGroupsDetails['admins_groups_name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>

                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="admins_parts_name" class="form-label">
                                                            <?php echo (_lang['name']); ?>
                                                        </label>
                                                        <input type="text" class="form-control addField" id="admins_parts_name" name="admins_parts_name" required>

                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="admins_parts_caption" class="form-label">
                                                            <?php echo (_lang['caption']); ?>
                                                        </label>
                                                        <input type="text" class="form-control addField" id="admins_parts_caption" name="admins_parts_caption" required>

                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="admins_parts_description" class="form-label">
                                                            <?php echo (_lang['description']); ?>
                                                        </label>
                                                        <textarea class="form-control addField" id="admins_parts_description" name="admins_parts_description" required></textarea>
                                                    </div>
                                                    <button type="button" class="btn btn-primary" id="addDataBtn">
                                                        <?php echo (_lang['add']); ?>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">
                                                    <?php echo (_lang['edit_item']); ?>
                                                </h5>
                                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo (_lang['close']); ?>">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Form for editing admins_parts -->
                                                <form id="editForm" name="editForm" class="form-control">

                                                    <input type="hidden" class="form-control editField" name="table_set" id="tableSet" value="admins_parts">
                                                    <input type="hidden" class="form-control editField" name="id" id="id">

                                                    <input class="form-control editField" type="hidden" name="unique_fields" id="unique_fields" value="<?php echo $unique_fields; ?>">
                                                    <div class="mb-3">
                                                        <label for="admins_groups_id" class="form-label">
                                                            <?php echo (_lang['admins_parts']); ?>
                                                        </label>

                                                        <select class="form-control editField" name="admins_groups_id" id="admins_groups_id">
                                                            <?php
                                                            while ($adminGroupsDetails = $adminGroupsResult->fetch_assoc()) { ?>
                                                                <option value="<?php echo ($adminGroupsDetails['id']); ?>">
                                                                    <?php echo ($adminGroupsDetails['admins_groups_name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>

                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="admins_parts_name" class="form-label">
                                                            <?php echo (_lang['name']); ?>
                                                        </label>
                                                        <input type="text" class="form-control editField" id="admins_parts_name" name="admins_parts_name" required>

                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="admins_parts_caption" class="form-label">
                                                            <?php echo (_lang['caption']); ?>
                                                        </label>
                                                        <input type="text" class="form-control editField" id="admins_parts_caption" name="admins_parts_caption" required>

                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="admins_parts_description" class="form-label">
                                                            <?php echo (_lang['description']); ?>
                                                        </label>
                                                        <textarea class="form-control editField" id="admins_parts_description" name="admins_parts_description" required></textarea>
                                                    </div>
                                                    <button type="button" class="btn btn-primary" id="editDataBtn">
                                                        <?php echo (_lang['edit_item']); ?>
                                                    </button>
                                                </form>
                                            </div>
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