<?php
///template/structure/user_view_details.php
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
                            <?php echo _lang['user_view']; ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <form>

                <div class="row">
                    <?php
                    $operationsResult = $structureModel->getOperations();
                    
                    $arrayOperationSet = @unserialize($permission_data['operation']);

                    while ($operationsDetails = $operationsResult->fetch_assoc()) {
                        $selected = '';
                        if (is_array($arrayOperationSet) && count($arrayOperationSet) > 0 && in_array($operationsDetails['id'], $arrayOperationSet)) {
                            $selected = 'checked';
                        }
                    ?>
                        <div class="col-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check form-switch">
                                        <input <?php echo $selected; ?> type="checkbox" name="operation[]" class="form-check-input operation-checkbox" id="<?php echo $operationsDetails['id']; ?>" value="<?php echo $operationsDetails['id']; ?>">
                                        <label class="form-check-label" for="<?php echo $operationsDetails['id']; ?>"><?php echo _lang[$operationsDetails['operation_name']]; ?></label>
                                        <span> ( <?php echo $operationsDetails['operation_description']; ?> ) </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>


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
                                <button type="button" id="submit-operation-btn" class="btn btn-primary mb-2">
                                    <?php echo _lang['submit']; ?>
                                </button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

        </div>
        <!-- end row-->
    </div> <!-- container -->
</div> <!-- content -->