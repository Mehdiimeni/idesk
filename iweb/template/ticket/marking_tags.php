<?php
///template/ticket/marking_tags.php
?>
<style>
/* Customer UI visual alignment with IPanel: scoped and nonfunctional */
.iweb-consistent-page {direction:rtl; min-width:0;}
.iweb-consistent-page .container-fluid {max-width:none; width:100%; padding-inline:clamp(12px,2vw,28px);}
.iweb-consistent-page .page-title-box {display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin:0 0 18px;padding:14px 18px;border-radius:12px;border:1px solid #e4ebf5;background:#fff;}
.iweb-consistent-page .page-title {font-size:1.1rem;font-weight:700;color:#26364b;margin:0;}
.iweb-consistent-page .card {border:1px solid #e6ecf5;border-radius:13px;box-shadow:0 3px 12px rgba(31,48,71,.04);min-width:0;}
.iweb-consistent-page .card-body {min-width:0;}
.iweb-consistent-page .btn {border-radius:8px;}
.iweb-consistent-page .form-control,.iweb-consistent-page .form-select {max-width:100%;min-width:0;}
.iweb-consistent-page .table-responsive {max-width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;}
.iweb-consistent-page table {vertical-align:middle;}
.iweb-consistent-page table th {background:#f6f8fc;color:#43536b;white-space:nowrap;}
.iweb-consistent-page .dataTables_wrapper {max-width:100%;min-width:0;}
.iweb-consistent-page .dataTables_wrapper .row {row-gap:10px;}
.iweb-consistent-page .dataTables_wrapper input {max-width:100%;}
.iweb-consistent-page .modal-dialog {max-width:min(96vw,560px);margin-inline:auto;}
.iweb-consistent-page .board {display:flex;align-items:flex-start;gap:14px;min-width:max-content;}
.iweb-consistent-page .tasks {flex:0 0 300px;min-width:300px;background:#fff;border:1px solid #e3eaf4;border-radius:14px;box-shadow:0 3px 13px rgba(31,48,71,.06);padding:12px;}
.iweb-consistent-page .task-header {display:flex;align-items:center;flex-wrap:wrap;gap:8px;padding:12px;background:#f5f8fd;border-radius:10px;color:#26364b;font-weight:700;}
.iweb-consistent-page .task-list-items .card {margin-bottom:12px!important;}
.iweb-consistent-page .iweb-board-scroll {overflow-x:auto;overscroll-behavior-inline:contain;-webkit-overflow-scrolling:touch;padding-bottom:14px;}
@media(max-width:767.98px){
 .iweb-consistent-page .container-fluid {padding-inline:12px;}
 .iweb-consistent-page .page-title-box {padding:12px;}
 .iweb-consistent-page .page-title {font-size:1rem;}
 .iweb-consistent-page .card-body {padding:14px!important;}
 .iweb-consistent-page .row>[class*="col-"] {min-width:0;}
 .iweb-consistent-page .btn {white-space:normal;}
 .iweb-consistent-page .tasks {min-width:min(280px,82vw);flex-basis:min(280px,82vw);}
 .iweb-consistent-page .modal-dialog {margin:10px auto;max-width:calc(100vw - 20px);}
 .iweb-consistent-page .dataTables_wrapper .dataTables_filter,.iweb-consistent-page .dataTables_wrapper .dataTables_length {text-align:start;}
}
</style>
<div class="content-page iweb-consistent-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">
                            <?php echo (_lang['marking_tags']); ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-5">

                                        <a href="#" class="btn btn-danger mb-2" data-bs-toggle="modal"
                                            data-bs-target="#addModal">
                                            <i class="mdi mdi-plus-circle me-2"></i>
                                            <?php echo (_lang['add']); ?>
                                        </a>
                                    </div>

                                </div>

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

                                        while ($markingTagsDetails = $markingTagsResult->fetch_assoc()) {

                                            ?>
                                            <tr>
                                                <td>
                                                    <?php echo $markingTagsDetails['id']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $markingTagsDetails['marking_tag']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $markingTagsDetails['description']; ?>
                                                </td>
                                                <td>
                                                    <?php $dateConverter = new DateConverter($markingTagsDetails['last_updated_date'], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?>
                                                </td>
                                                <td>


                                                    <a href="javascript:void(0);" class="action-icon delete-item"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-table="marking_tags"
                                                        data-id="<?php echo $markingTagsDetails['id']; ?>">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>

                                                    <a href="javascript:void(0);" class="action-icon edit-item"
                                                        data-bs-toggle="modal" data-bs-target="#editModal"
                                                        data-table="marking_tags"
                                                        data-id="<?php echo $markingTagsDetails['id']; ?>">
                                                        <i class="mdi mdi-square-edit-outline"></i>
                                                    </a>
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
                                                        id="table_set" value="marking_tags">
                                                    <input class="form-control addField" type="hidden"
                                                        name="unique_fields" id="unique_fields"
                                                        value="<?php echo $unique_fields; ?>">
                                                    <input class="form-control addField" type="hidden" name="user_id"
                                                        id="user_id" value="<?php echo $userId; ?>">
                                                    <input class="form-control addField" type="hidden" name="company_id"
                                                        id="company_id" value="<?php echo $companyId; ?>">
                                                    <div class="mb-3">
                                                        <label for="marking_tag" class="form-label">
                                                            <?php echo (_lang['name']); ?>
                                                        </label>
                                                        <input type="text" class="form-control addField"
                                                            id="marking_tag" name="marking_tag" required>

                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">
                                                            <?php echo (_lang['description']); ?>
                                                        </label>
                                                        <textarea class="form-control addField" id="description"
                                                            name="description" required></textarea>
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
                                                        <label for="marking_tag" class="form-label">
                                                            <?php echo (_lang['name']); ?>
                                                        </label>
                                                        <input type="text" class="form-control editField"
                                                            id="marking_tag" name="marking_tag" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">
                                                            <?php echo (_lang['description']); ?>
                                                        </label>
                                                        <textarea class="form-control editField" id="description"
                                                            name="description" required></textarea>
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