<?php
///template/structure/user_stracture.php
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
                            <?php echo (_lang['user_stracture']); ?>
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

                                        while ($userStractureDetails = $userStractureResult->fetch_assoc()) {

                                        ?>
                                            <tr>
                                            <td>
                                                    <?php echo $userStractureDetails['id']; ?>
                                                </td>

                                                <td>
                                                    <?php echo $userStractureDetails['rbac_name']; ?>
                                                </td>

                                                <td>
                                                    <?php $dateConverter = new DateConverter($userStractureDetails['last_updated_date'], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?>
                                                </td>
                                                <td>

                                                    <?php if ($rbacClass->checkPermissionOperationByName('delete_operation')) { ?>
                                                        <a href="javascript:void(0);" class="action-icon delete-item" data-bs-toggle="modal" data-bs-target="#deleteModal" data-table="permissions_stracture" data-id="<?php echo $userStractureDetails['id']; ?>">
                                                            <i class="mdi mdi-delete"></i>
                                                        </a>

                                                    <?php } ?>


                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <div class="modal fade" id="deleteModal" tabindex="-1" userStracture="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" userStracture="document">
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


                                <div class="modal fade" id="addModal" tabindex="-1" userStracture="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" userStracture="document">
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
                                                <!-- Form for adding userStracture -->
                                                <form id="addForm" name="addForm" class="form-control">

                                                    <input type="hidden" class="form-control addField" name="table_set" id="table_set" value="permissions_stracture">
                                                    <input class="form-control addField" type="hidden" name="unique_fields" id="unique_fields" value="<?php echo $unique_fields; ?>">
                                                    <div class="mb-3">
                                                        <label for="rbac_id" class="form-label">
                                                            <?php echo (_lang['role']); ?>
                                                        </label>
                                                        <select class="form-control  select2 addField" data-toggle="select2" name="rbac_id" id="rbac_id">
                                                            <?php
                                                            while ($rbacDetails = $rbacResult->fetch_assoc()) { ?>
                                                                <option value="<?php echo ($rbacDetails['id']); ?>">
                                                                    <?php echo ($rbacDetails['rbac_name']); ?> / <?php echo ($rbacDetails['company_name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="stracture" class="form-label">
                                                            <?php echo (_lang['stracture']); ?>
                                                        </label>

                                                        <select multiple="" class="form-control addField" id="optgroup" name="stracture" style="position: absolute; left: -9999px;">

                                                            <?php
                                                            while ($activityDetails = $activityResult->fetch_assoc()) { ?>
                                                                <optgroup label="<?php echo ($activityDetails['activity_name']); ?>">

                                                                    <?php
                                                                    $companyResult = $structureModel->getCompaniesByActivity($activityDetails['id']);
                                                                    while ($companyDetails = $companyResult->fetch_assoc()) { ?>
                                                                <optgroup label="<?php echo ($companyDetails['company_name']); ?>">

                                                                    <?php
                                                                        $unitResult = $structureModel->getUnitsByCompany($companyDetails['id']);
                                                                        while ($unitDetails = $unitResult->fetch_assoc()) { ?>
                                                                        <option value="<?php echo ($unitDetails['id']); ?>">
                                                                            <?php echo ($unitDetails['unit_name']); ?>
                                                                        </option>
                                                                    <?php } ?>

                                                                </optgroup>
                                                            <?php } ?>

                                                            </optgroup>
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


                        </div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div>
        <!-- end row -->

    </div> <!-- container -->

</div> <!-- content -->


</div>