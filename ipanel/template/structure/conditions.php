<?php
///template/structure/conditions.php
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
                            <?php echo (_lang['condition']); ?>
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
                                                <?php echo (_lang['part']); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['need_description']); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['finance']); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['color']); ?>
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
                                        $conditionsResult = $structureModel->getConditions();
                                        while ($conditionsDetails = $conditionsResult->fetch_assoc()) {

                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $conditionsDetails['id']; ?>
                                                </td>
                                                <td>
                                                    <?php echo _lang[$conditionsDetails['condition_name']]; ?>
                                                </td>
                                                <td>
                                                    <?php echo $conditionsDetails['condition_part']; ?>
                                                </td>
                                                <td>
                                                    <?php if ($conditionsDetails['need_description']) {
                                                        echo _lang['yes'];
                                                    } else {
                                                        echo _lang['no'];
                                                    } ?>
                                                </td>
                                                <td>
                                                    <?php if ($conditionsDetails['finance']) {
                                                        echo _lang['yes'];
                                                    } else {
                                                        echo _lang['no'];
                                                    } ?>
                                                </td>
                                                <td>

                                                    <div
                                                        class="alert alert-<?php echo $conditionsDetails['condition_color']; ?>">
                                                        <?php echo $conditionsDetails['condition_color']; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php $dateConverter = new DateConverter($conditionsDetails['last_updated_date'], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?>
                                                </td>
                                                <td>


                                                    <?php if ($rbacClass->checkPermissionOperationByName('delete_operation')) { ?>
                                                        <a href="javascript:void(0);" class="action-icon delete-item"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                            data-table="conditions"
                                                            data-id="<?php echo $conditionsDetails['id']; ?>">
                                                            <i class="mdi mdi-delete"></i>
                                                        </a>
                                                    <?php } ?>

                                                    <?php if ($rbacClass->checkPermissionOperationByName('edit_operation')) { ?>
                                                        <a href="javascript:void(0);" class="action-icon edit-item"
                                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                                            data-table="conditions"
                                                            data-id="<?php echo $conditionsDetails['id']; ?>">
                                                            <i class="mdi mdi-square-edit-outline"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <div class="modal fade" id="deleteModal" tabindex="-1" operation="dialog"
                                    aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" operation="document">
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


                                <div class="modal fade" id="addModal" tabindex="-1" operation="dialog"
                                    aria-labelledby="addModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" operation="document">
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
                                                <!-- Form for adding operation -->
                                                <form id="addForm" name="addForm" class="form-control">

                                                    <input type="hidden" class="form-control addField" name="table_set"
                                                        id="table_set" value="conditions">
                                                    <input class="form-control addField" type="hidden"
                                                        name="unique_fields" id="unique_fields"
                                                        value="<?php echo $unique_fields; ?>">
                                                    <div class="mb-3">
                                                        <label for="condition_name" class="form-label">
                                                            <?php echo (_lang['name']); ?>
                                                        </label>
                                                        <input type="text" class="form-control addField"
                                                            id="condition_name" name="condition_name" required>

                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="condition_part"
                                                            class="form-label"><?php echo (_lang['part']); ?>
                                                        </label>
                                                        <input type="text" class="form-control addField"
                                                            id="condition_part" name="condition_part" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="need_description" class="form-label">
                                                            <?php echo (_lang['need_description']); ?>
                                                        </label>
                                                        <select class="form-control addField" name="need_description"
                                                            id="need_description" required>

                                                            <option selected value="0">
                                                                <?php echo _lang['no']; ?>
                                                            </option>
                                                            <option value="1">
                                                                <?php echo _lang['yes']; ?>
                                                            </option>

                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="finance" class="form-label">
                                                            <?php echo (_lang['finance']); ?>
                                                        </label>
                                                        <select class="form-control addField" name="finance"
                                                            id="finance" required>

                                                            <option selected value="0">
                                                                <?php echo _lang['no']; ?>
                                                            </option>
                                                            <option value="1">
                                                                <?php echo _lang['yes']; ?>
                                                            </option>

                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="condition_color" class="form-label">
                                                            <?php echo (_lang['color']); ?>
                                                        </label>
                                                        <select class="form-control addField" name="condition_color"
                                                            id="condition_color" required>
                                                            <?php
                                                            $colorArray = array(
                                                                'primary',
                                                                'primary alert-dismissible text-bg-primary',
                                                                'primary alert-dismissible',
                                                                'primary bg-transparent text-primary',
                                                                'primary text-primary',
                                                                'secondary',
                                                                'secondary alert-dismissible text-bg-secondary',
                                                                'secondary alert-dismissible',
                                                                'secondary bg-transparent text-secondary',
                                                                'secondary text-secondary',
                                                                'success',
                                                                'success alert-dismissible text-bg-success',
                                                                'success alert-dismissible',
                                                                'success bg-transparent text-success',
                                                                'success text-success',
                                                                'info',
                                                                'info alert-dismissible text-bg-info',
                                                                'info alert-dismissible',
                                                                'info bg-transparent text-info',
                                                                'info text-info',
                                                                'warning',
                                                                'warning alert-dismissible text-bg-warning',
                                                                'warning alert-dismissible',
                                                                'warning bg-transparent text-warning',
                                                                'warning text-warning',
                                                                'danger',
                                                                'danger alert-dismissible text-bg-danger',
                                                                'danger alert-dismissible',
                                                                'danger bg-transparent text-danger',
                                                                'danger text-danger',
                                                                'light',
                                                                'light alert-dismissible text-bg-light',
                                                                'light alert-dismissible',
                                                                'light bg-transparent text-light',
                                                                'light text-light',
                                                                'dark',
                                                                'dark alert-dismissible text-bg-dark',
                                                                'dark alert-dismissible',
                                                                'dark bg-transparent text-dark',
                                                                'dark text-dark',

                                                            );
                                                            foreach ($colorArray as $color) { ?>
                                                                <option class="alert alert-<?php echo ($color); ?>"
                                                                    value="<?php echo ($color); ?>">
                                                                    <?php echo ($color); ?>
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


                                <div class="modal fade" id="editModal" tabindex="-1" operation="dialog"
                                    aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" operation="document">
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
                                                <!-- Form for editing operation -->
                                                <form id="editForm" name="editForm" class="form-control">
                                                    <input type="hidden" class="form-control editField" name="table_set"
                                                        id="table_set">
                                                    <input type="hidden" class="form-control editField" name="id"
                                                        id="id">
                                                    <input class="form-control editField" type="hidden"
                                                        name="unique_fields" id="unique_fields"
                                                        value="<?php echo $unique_fields; ?>">
                                                    <div class="mb-3">
                                                        <label for="condition_name"
                                                            class="form-label"><?php echo (_lang['name']); ?>
                                                        </label>
                                                        <input type="text" class="form-control editField"
                                                            id="condition_name" name="condition_name" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="condition_part"
                                                            class="form-label"><?php echo (_lang['part']); ?>
                                                        </label>
                                                        <input type="text" class="form-control editField"
                                                            id="condition_part" name="condition_part" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="need_description" class="form-label">
                                                            <?php echo (_lang['need_description']); ?>
                                                        </label>
                                                        <select class="form-control editField" name="need_description"
                                                            id="need_description" required>

                                                            <option value="0">
                                                                <?php echo _lang['no']; ?>
                                                            </option>
                                                            <option value="1">
                                                                <?php echo _lang['yes']; ?>
                                                            </option>

                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="finance" class="form-label">
                                                            <?php echo (_lang['finance']); ?>
                                                        </label>
                                                        <select class="form-control editField" name="finance"
                                                            id="finance" required>

                                                            <option value="0">
                                                                <?php echo _lang['no']; ?>
                                                            </option>
                                                            <option value="1">
                                                                <?php echo _lang['yes']; ?>
                                                            </option>

                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="condition_color" class="form-label">
                                                            <?php echo (_lang['color']); ?>
                                                        </label>
                                                        <select class="form-control editField" name="condition_color"
                                                            id="condition_color" required>
                                                            <?php
                                                            $colorArray = array(
                                                                'primary',
                                                                'primary alert-dismissible text-bg-primary',
                                                                'primary alert-dismissible',
                                                                'primary bg-transparent text-primary',
                                                                'primary text-primary',
                                                                'secondary',
                                                                'secondary alert-dismissible text-bg-secondary',
                                                                'secondary alert-dismissible',
                                                                'secondary bg-transparent text-secondary',
                                                                'secondary text-secondary',
                                                                'success',
                                                                'success alert-dismissible text-bg-success',
                                                                'success alert-dismissible',
                                                                'success bg-transparent text-success',
                                                                'success text-success',
                                                                'info',
                                                                'info alert-dismissible text-bg-info',
                                                                'info alert-dismissible',
                                                                'info bg-transparent text-info',
                                                                'info text-info',
                                                                'warning',
                                                                'warning alert-dismissible text-bg-warning',
                                                                'warning alert-dismissible',
                                                                'warning bg-transparent text-warning',
                                                                'warning text-warning',
                                                                'danger',
                                                                'danger alert-dismissible text-bg-danger',
                                                                'danger alert-dismissible',
                                                                'danger bg-transparent text-danger',
                                                                'danger text-danger',
                                                                'light',
                                                                'light alert-dismissible text-bg-light',
                                                                'light alert-dismissible',
                                                                'light bg-transparent text-light',
                                                                'light text-light',
                                                                'dark',
                                                                'dark alert-dismissible text-bg-dark',
                                                                'dark alert-dismissible',
                                                                'dark bg-transparent text-dark',
                                                                'dark text-dark',

                                                            );
                                                            foreach ($colorArray as $color) { ?>
                                                                <option class="alert alert-<?php echo ($color); ?>"
                                                                    value="<?php echo ($color); ?>">
                                                                    <?php echo ($color); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <button type="button" class="btn btn-primary"
                                                        id="editDataBtn"><?php echo (_lang['save_changes']); ?></button>
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