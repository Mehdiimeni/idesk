<?php
///template/ticket/ticket_details.php
?>

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">
                                    <?php echo _lang['ticket_details']; ?>
                                </li>
                                <li class="breadcrumb-item"><a href="./tickets">
                                        <?php echo _lang['tickets']; ?>
                                    </a></li>
                            </ol>
                        </div>
                        <h4 class="page-title">
                            <?php echo _lang['ticket_details']; ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <?php if (
                    ($showAccounting) and (
                        $rbacClass->checkPermissionOperationByName('condition_clearing', 'u') or
                        $rbacClass->checkPermissionOperationByName('condition_acepted_invoice', 'u') or
                        $rbacClass->checkPermissionOperationByName('condition_reject_invoice', 'u') or
                        $rbacClass->checkPermissionOperationByName('condition_official_bill', 'u') or
                        $rbacClass->checkPermissionOperationByName('condition_invoice', 'u'))
                ) { ?>
                    <div class="col-lg-12">

                        <!-- Checkout Steps -->
                        <ul class="nav nav-pills bg-nav-pills nav-justified mb-3 order-secondary border">

                            <li class="nav-item">
                                <a href="#payment-information" data-bs-toggle="collapse" aria-controls="payment-information"
                                    aria-expanded="false" class="nav-link rounded-0">
                                    <i class="mdi mdi-cash-multiple font-18"></i>
                                    <span class="d-none d-lg-block"><?php echo _lang['accounting']; ?></span>
                                </a>
                            </li>
                        </ul>

                        <!-- Steps Information -->
                        <div class="tab-content">
                            <!-- Payment Content-->
                            <div class="collapse" id="payment-information">
                                <div class="card d-block">

                                    <div class="card-body border">


                                        <h4 class="mt-2"><?php echo _lang['accounting']; ?></h4>

                                        <p class="text-muted mb-4"><?php echo _lang['accounting_tips']; ?></p>

                                        <!-- Pay with Paypal box-->
                                        <div class="border p-3 mb-3 rounded">
                                            <div class="row">
                                                <?php if ($rbacClass->checkPermissionOperationByName('accounting_view_operation', 'u')) { ?>
                                                    <div class="card-body">
                                                        <?php if ($rbacClass->checkPermissionOperationByName('accounting_add_operation', 'u')) { ?>

                                                            <form validate
                                                                action="./tickets?ticket_id=<?php echo ($_GET['ticket_id']); ?>"
                                                                method="post" enctype="multipart/form-data">
                                                                <div class="mb-3 mt-3 mt-xl-0">
                                                                    <label for="attach_file" class="mb-0">
                                                                        <?php echo _lang['attach_file']; ?>
                                                                    </label>

                                                                    <div class="input-group mb-1">
                                                                        <input type="text" class="form-control"
                                                                            name="accounting_file_title" required
                                                                            id="accounting_file_title"
                                                                            aria-describedby="basic-addon1"
                                                                            placeholder="<?php echo _lang['file_title']; ?>">
                                                                    </div>

                                                                    <div class="input-group mb-1">
                                                                        <input type="file" name="accounting_attach_file" required
                                                                            id="accounting_attach_file" class="form-control">

                                                                        <button class="btn btn-outline-secondary" type="submit">
                                                                            <?php echo _lang['attach']; ?>
                                                                        </button>
                                                                    </div>

                                                                    <div class="input-group mb-1">
                                                                        <?php if ($rbacClass->checkPermissionOperationByName('accounting_local_operation', 'u')) { ?>

                                                                            <input type="checkbox" id="switch3" name="local"
                                                                                data-switch="success" />

                                                                            <label for="switch3"
                                                                                data-on-label="<?php echo _lang['no']; ?>"
                                                                                data-off-label="<?php echo _lang['yes']; ?>"></label>


                                                                            <span class="mb-1"
                                                                                style="margin-right: 5px; margin-left:5px;">
                                                                                <?php echo _lang['global_send']; ?>
                                                                            </span>

                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        <?php } ?>



                                                        <h5 class="card-title mb-3">
                                                            <?php echo (_lang['sended_files']); ?>
                                                        </h5>
                                                        <?php foreach ($allAccountingFileInfo as $accountingfileInfo) {

                                                            if (!empty($accountingfileInfo['CompanyId']) && $accountingfileInfo['CompanyId'] != $structureModel->getCompanyByUnitId($_SESSION['unit_id'])) {
                                                                continue;
                                                            }

                                                            $fileDownloadUrl = "./tickets?ticket_id=" . $_GET['ticket_id'] . "&accounting_file=" . $accountingfileInfo['downloadLink'];
                                                            ?>
                                                            <div class="card mb-1 shadow-none border">
                                                                <div class="p-2">
                                                                    <div class="row align-items-center">
                                                                        <div class="col-auto">
                                                                            <div class="avatar-sm">
                                                                                <span class="avatar-title rounded">
                                                                                    .
                                                                                    <?php echo $accountingfileInfo['fileType']; ?>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col ps-0">
                                                                            <a href="<?php echo ($fileDownloadUrl); ?>"
                                                                                class="text-muted fw-bold">
                                                                                <?php echo empty($accountingfileInfo['fileTitle']) ? $accountingfileInfo['fileName'] : $accountingfileInfo['fileTitle']; ?>
                                                                            </a>
                                                                            <p class="mb-0">
                                                                                <?php echo $accountingfileInfo['fileSize']; ?>
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-auto">
                                                                            <!-- Button -->
                                                                            <a href="<?php echo ($fileDownloadUrl); ?>"
                                                                                class="btn btn-link btn-lg text-muted">
                                                                                <i class="ri-download-2-line"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        <?php } ?>

                                                    </div>

                                                <?php } ?>
                                            </div>
                                        </div>
                                        <!-- end Pay with Paypal box-->

                                    </div>

                                </div> <!-- end row-->
                            </div>
                            <!-- End Payment Information Content-->

                        </div> <!-- end tab content-->

                    </div> <!-- end card-body-->
                <?php } ?>
                <div class="col-xxl-8 col-lg-6">
                    <!-- project card -->
                    <div class="card d-block ribbon-box">

                        <div class="card-body border">
                            <?php echo ($showPriority); ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">

                                <h4 class="mt-0 m-1">
                                    <?php echo $ticketDetail['ticket_title']; ?> (
                                    <?php echo $ticketDetail['ticket_number']; ?> )
                                </h4>

                                <?php if ($rbacClass->checkPermissionOperationByName('condition_operation', 'u')) { ?>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                            aria-expanded="false" title="<?php echo _lang['chnage_status']; ?>">
                                            <i class="ri-honour-fill"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <!-- item-->
                                            <?php if ($changeCondition) { ?>
                                                <?php while ($conditions = $allConditions->fetch_assoc()) { ?>
                                                    <?php if ($rbacClass->checkPermissionOperationByName($conditions['condition_name'], 'u')) { ?>
                                                        <a href="javascript:void(0);"
                                                            id="<?php echo $textToolsClass->capitalizeFirstLetter($conditions['condition_name']); ?>"
                                                            class="dropdown-item operation-link"
                                                            data-operation="<?php echo $textToolsClass->capitalizeFirstLetter($conditions['condition_name']); ?>"
                                                            data-tableset="<?php echo $conditions['condition_part']; ?>"
                                                            data-id="<?php echo $ticketDetail['id']; ?>"
                                                            data-needs-description="<?php echo $conditions['need_description']; ?>">
                                                            <?php if ($conditions['need_description']) {
                                                                echo " * ";
                                                            }
                                                            echo _lang[$conditions['condition_name']]; ?>
                                                        </a>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <a href="javascript:void(0);" class="dropdown-item">
                                                    <?php echo _lang['user_ticket_before_condition_note1']; ?>
                                                </a>
                                            <?php } ?>

                                        </div>
                                    </div>
                                <?php } ?>
                                <!-- project title-->
                            </div>

                            <?php $condition = $structureModel->getConditionsByName($ticketDetail['last_status_name']); ?>
                            <div class="alert alert-<?php echo $condition['condition_color']; ?> mb-3">
                                <?php echo _lang[$condition['condition_name']]; ?>
                            </div>


                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-start mb-3">

                                        <div>
                                            <span class="badge bg-light text-dark border fs-6 px-3 py-2">
                                                <i class="mdi mdi-tag-outline me-1"></i>
                                                <?php echo $ticketDetail['type_group']; ?>
                                                /
                                                <?php echo $ticketDetail['type_name']; ?>
                                            </span>
                                        </div>

                                        <?php if (($ticketDetail['indicator_number'] != null)): ?>
                                            <span class="badge bg-primary fs-6 px-3 py-2">
                                                <?php echo $ticketDetail['indicator_number']; ?>
                                            </span>
                                        <?php endif; ?>

                                    </div>

                                    <div class="ticket-description">
                                        <?php
                                        echo nl2br(
                                            htmlspecialchars(
                                                html_entity_decode($ticketDetail['ticket_description'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                                                ENT_QUOTES | ENT_SUBSTITUTE,
                                                'UTF-8'
                                            )
                                        );
                                        ?>
                                    </div>

                                </div>
                            </div>

                            <div class="row border rounded bg-light py-2 px-3 align-items-center">

                                <div class="col-md-4">
                                    <i class="mdi mdi-account text-primary"></i>
                                    <strong><?php echo $userProfile['name']; ?>
                                    </strong>
                                    <small class="text-muted ms-1" data-bs-toggle="tooltip"
                                        title="<?php echo $userProfile['mobile']; ?>">
                                        (
                                        <?php echo $userProfile['company_name']; ?>)
                                    </small>
                                </div>

                                <div class="col-md-4 text-center">
                                    <i class="mdi mdi-calendar-plus text-success"></i>
                                    <small class="text-muted">
                                        <?php echo _lang['added_date']; ?>:
                                    </small>
                                    <strong>
                                        <?php $dateConverter = new DateConverter($ticketDetail['creation_date'], $config->getNowLanguage('a'));
                                        echo $dateConverter->convertToShamsi(); ?>
                                    </strong>
                                </div>

                                <div class="col-md-4 text-end">
                                    <i class="mdi mdi-update text-warning"></i>
                                    <small class="text-muted">
                                        <?php echo _lang['last_uapdate_date']; ?>:
                                    </small>
                                    <strong>
                                        <?php $dateConverter = new DateConverter($ticketDetail['last_updated_date'], $config->getNowLanguage('a'));
                                        echo $dateConverter->convertToShamsi(); ?>
                                    </strong>
                                </div>

                            </div>




                        </div> <!-- end card-body-->

                    </div> <!-- end card-->


                    <?php if ($rbacClass->checkPermissionOperationByName('view_comment_operation', 'u')) { ?>
                    
                        <style>
                            .comment-card {
                                border: 1px solid #edf0f2 !important;
                                border-radius: 10px;
                                transition: all .2s ease;
                            }
                    
                            .comment-card:hover {
                                box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
                            }
                    
                            .comment-border-success {
                                border-inline-start: 4px solid #b7f0d6 !important;
                            }
                    
                            .comment-border-danger {
                                border-inline-start: 4px solid #ffd6d6 !important;
                            }
                    
                            .comment-border-primary {
                                border-inline-start: 4px solid #cfe2ff !important;
                            }
                    
                            .reply-card {
                                background: #fafafa;
                            }
                    
                            .comment-text {
                                line-height: 1.8;
                                color: #6c757d;
                            }
                        </style>
                    
                        <div class="card border shadow-sm">
                            <div class="card-body">
                    
                                <h4 class="mt-0 mb-3">
                                    <?php echo _lang['comments']; ?>
                                </h4>
                    
                                <form validate action="./tickets?ticket_id=<?php echo $_GET['ticket_id']; ?>" method="post">
                                    <input type="hidden" name="parent_id" value="">
                                    <input type="hidden" name="creator_id" value="">
                    
                                    <textarea class="form-control form-control-light mb-2" placeholder="<?php echo _lang['write_message']; ?>"
                                        required rows="3" name="comment_text"></textarea>
                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <?php if ($rbacClass->checkPermissionOperationByName('add_comment_operation', 'u')) { ?>
                                            <button type="submit" name="submit" class="btn btn-primary btn-sm px-3">
                                                <?php echo _lang['submit']; ?>
                                            </button>
                                        <?php } ?>
                                    </div>
                                </form>
                    
                                <?php while ($commentDetail = $allComments->fetch_assoc()) { ?>
                    
                                    <?php
                                    if ($commentDetail['local'] != 0 && $commentDetail['company_id'] != $_SESSION['company_id']) {
                                        continue;
                                    }

                                    $creator_id = $commentDetail['user_id'] != ''
                                        ? $commentDetail['user_id']
                                        : $commentDetail['admin_id'];

                                    $commentBorderClass = 'comment-border-primary';

                                    if ($commentDetail['local']) {
                                        $commentBorderClass = 'comment-border-danger';
                                    }

                                    if ($commentDetail['user_id']) {
                                        $commentBorderClass = 'comment-border-success';
                                    }

                                    $dateConverter = new DateConverter(
                                        $commentDetail['creation_date'],
                                        $config->getNowLanguage('a')
                                    );
                                    $commentDate = $dateConverter->convertToShamsi();

                                    $commentText = htmlspecialchars(
                                        html_entity_decode($commentDetail['comment_text'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                                        ENT_QUOTES | ENT_SUBSTITUTE,
                                        'UTF-8'
                                    );
                                    ?>
                    
                                    <div class="card comment-card mb-3 <?php echo $commentBorderClass; ?>">
                                        <div class="card-body">
                    
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <strong><?php echo $commentDetail['name']; ?></strong>
                                                </div>
                    
                                                <small class="text-muted">
                                                    <?php echo $commentDate; ?>
                                                </small>
                                            </div>
                    
                                            <div class="comment-text">
                                                <?= nl2br($commentText) ?>
                                            </div>
                    
                                            <?php if ($rbacClass->checkPermissionOperationByName('reply_comment_operation', 'u')) { ?>
                                                <div class="mt-3 text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 reply-comment-btn"
                                                        data-bs-toggle="modal" data-bs-target="#replyCommentModal"
                                                        data-parent-id="<?php echo $commentDetail['id']; ?>"
                                                        data-creator-id="<?php echo $creator_id; ?>"
                                                        data-reply-to="<?php echo htmlspecialchars($commentDetail['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <i class="mdi mdi-reply"></i>
                                                        <?php echo _lang['reply']; ?>
                                                    </button>
                                                </div>
                                            <?php } ?>
                    
                                            <?php
                                            $replyComments = $commentModel->getCommentPartByParentId($commentDetail['id']);

                                            while ($replyDetail = $replyComments->fetch_assoc()) {

                                                if ($replyDetail['local'] != 0 && $replyDetail['company_id'] != $_SESSION['company_id']) {
                                                    continue;
                                                }

                                                $replyBorderClass = 'comment-border-primary';

                                                if ($replyDetail['local']) {
                                                    $replyBorderClass = 'comment-border-danger';
                                                }

                                                if ($replyDetail['user_id']) {
                                                    $replyBorderClass = 'comment-border-success';
                                                }

                                                $dateConverter = new DateConverter(
                                                    $replyDetail['creation_date'],
                                                    $config->getNowLanguage('a')
                                                );
                                                $replyDate = $dateConverter->convertToShamsi();

                                                $replyText = htmlspecialchars(
                                                    html_entity_decode($replyDetail['comment_text'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                                                    ENT_QUOTES | ENT_SUBSTITUTE,
                                                    'UTF-8'
                                                );
                                                ?>
                    
                                                <div class="ms-md-5 ms-3 mt-3">
                                                    <div class="card comment-card reply-card <?php echo $replyBorderClass; ?>">
                                                        <div class="card-body py-3">
                    
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <strong><?php echo $replyDetail['name']; ?></strong>
                                                                </div>
                    
                                                                <small class="text-muted">
                                                                    <?php echo $replyDate; ?>
                                                                </small>
                                                            </div>
                    
                                                            <div class="comment-text">
                                                                <?= nl2br($replyText) ?>
                                                            </div>
                    
                                                        </div>
                                                    </div>
                                                </div>
                    
                                            <?php } ?>
                    
                                        </div>
                                    </div>
                    
                                <?php } ?>
                    
                            </div>
                        </div>
                    
                        <div class="modal fade" id="replyCommentModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form validate class="modal-content" action="./tickets?ticket_id=<?php echo $_GET['ticket_id']; ?>"
                                    method="post">
                    
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <?php echo _lang['reply']; ?>
                                            <small class="text-muted" id="replyToName"></small>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                    
                                    <div class="modal-body">
                                        <input type="hidden" name="parent_id" id="modal_parent_id">
                                        <input type="hidden" name="creator_id" id="modal_creator_id">
                    
                                        <textarea class="form-control" name="comment_text" id="modal_comment_text" rows="4" required
                                            placeholder="<?php echo _lang['your_answer']; ?>"></textarea>
                                    </div>
                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                            <?php echo _lang['cancel'] ?? 'انصراف'; ?>
                                        </button>
                    
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            <?php echo _lang['answer']; ?>
                                        </button>
                                    </div>
                    
                                </form>
                            </div>
                        </div>
                    
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const replyButtons = document.querySelectorAll('.reply-comment-btn');

                                replyButtons.forEach(function (button) {
                                    button.addEventListener('click', function () {
                                        document.getElementById('modal_parent_id').value = this.dataset.parentId;
                                        document.getElementById('modal_creator_id').value = this.dataset.creatorId;
                                        document.getElementById('replyToName').textContent = ' - ' + this.dataset.replyTo;
                                        document.getElementById('modal_comment_text').value = '';
                                    });
                                });
                            });
                        </script>
                    
                    <?php } ?>

                    <!-- end card-->
                </div> <!-- end col -->

                <div class="col-lg-6 col-xxl-4">
                    <div class="card shadow-sm border mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="ri-settings-3-line me-2 text-primary"></i>
                                <?php echo _lang['operations']; ?>
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">



                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#KanbanBoard">
                                    <button type="button" tabindex="0" class="btn btn-warning btn-sm"
                                        data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-content="<?php echo _lang['kanban_board_note']; ?>"
                                        title="<?php echo _lang['kanban_board']; ?>">
                                        <i class="ri-layout-masonry-line me-1"></i>
                                        <?php echo _lang['kanban_board']; ?>
                                    </button>
                                </a>

                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#Mark">
                                    <button type="button" tabindex="0" class="btn btn-secondary btn-sm"
                                        data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-content="<?php echo _lang['mark_note']; ?>"
                                        title="<?php echo _lang['mark']; ?>">
                                        <i class="ri-price-tag-3-line me-1"></i>
                                        <?php
                                        echo _lang['mark'];
                                        if (isset($ticketsModel->getMarkingTagByTicketId($ticketDetail["id"])['marking_tag'])) {
                                            echo " ( " . $ticketsModel->getMarkingTagByTicketId($ticketDetail["id"])['marking_tag'] . " ) ";
                                        }
                                        ?>
                                    </button>
                                </a>
                            </div>
                        </div> <!-- end card-body -->

                    </div>
                    <?php if ($rbacClass->checkPermissionOperationByName('attach_view_operation', 'u')) { ?>

                        <div class="card border">
                            <div class="card-body">
                                <?php if ($rbacClass->checkPermissionOperationByName('attach_add_operation', 'u')) { ?>

                                    <form validate action="./tickets?ticket_id=<?php echo ($_GET['ticket_id']); ?>"
                                        method="post" enctype="multipart/form-data">
                                        <div class="mb-3 mt-3 mt-xl-0">
                                            <label for="attach_file" class="mb-0">
                                                <?php echo _lang['attach_file']; ?>
                                            </label>

                                            <div class="input-group mb-1">

                                                <input type="text" class="form-control" name="file_title" required
                                                    id="file_title" aria-describedby="basic-addon1"
                                                    placeholder="<?php echo _lang['file_title']; ?>">
                                            </div>

                                            <div class="input-group mb-1">
                                                <input type="file" name="attach_file" required id="attach_file"
                                                    class="form-control">

                                                <button class="btn btn-outline-secondary" type="submit">
                                                    <?php echo _lang['attach']; ?>
                                                </button>
                                            </div>

                                            <div class="input-group mb-1">
                                                <?php if ($rbacClass->checkPermissionOperationByName('attach_local_operation', 'u')) { ?>

                                                    <input type="checkbox" id="switch3" name="local" checked
                                                        data-switch="success" />

                                                    <label for="switch3" data-on-label="<?php echo _lang['no']; ?>"
                                                        data-off-label="<?php echo _lang['yes']; ?>"></label>


                                                    <span class="mb-1" style="margin-right: 5px; margin-left:5px;">
                                                        <?php echo _lang['global_send']; ?>
                                                    </span>

                                                <?php } ?>
                                            </div>
                                        </div>
                                    </form>
                                <?php } ?>



                                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                                    <i class="ri-attachment-2"></i>
                                    <?php echo (_lang['sended_files']); ?>
                                </h5>

                                <div class="vstack gap-2">
                                    <?php foreach ($allFileInfo as $fileInfo) {

                                        if (!empty($fileInfo['CompanyId']) && $fileInfo['CompanyId'] != $structureModel->getCompanyByUnitId($_SESSION['unit_id'])) {
                                            continue;
                                        }

                                        $fileDownloadUrl = "./tickets?ticket_id=" . ($_GET['ticket_id']) . "&file=" . $fileInfo['downloadLink'];
                                        ?>

                                        <div class="card mb-0 shadow-sm border">
                                            <div class="card-body p-2">
                                                <div class="d-flex align-items-center gap-3">

                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-sm">
                                                            <span
                                                                class="avatar-title rounded bg-light text-dark border fw-bold text-uppercase">
                                                                .<?php echo $fileInfo['fileType']; ?>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <a href="<?php echo ($fileDownloadUrl); ?>"
                                                            class="d-block text-dark fw-semibold text-decoration-none text-truncate">
                                                            <?php echo empty($fileInfo['fileTitle']) ? $fileInfo['fileName'] : $fileInfo['fileTitle']; ?>
                                                        </a>

                                                        <div class="small text-muted mt-1">
                                                            <i class="ri-file-line me-1"></i>
                                                            <?php echo $fileInfo['fileSize']; ?>
                                                        </div>
                                                    </div>

                                                    <div class="flex-shrink-0">
                                                        <a href="<?php echo ($fileDownloadUrl); ?>"
                                                            class="btn btn-sm btn-light border rounded-circle">
                                                            <i class="ri-download-2-line"></i>
                                                        </a>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    <?php } ?>
                                </div>

                            </div>
                        </div>

                    <?php } ?>


                    <!-- Modal for status description -->
                    <div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="descriptionModalLabel">
                                        <?php echo (_lang['need_description']); ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <textarea id="status_description" class="form-control"
                                        placeholder="<?php echo (_lang['description']); ?>"></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal"><?php echo (_lang['close']); ?></button>
                                    <button type="button" id="submitDescription"
                                        class="btn btn-primary"><?php echo (_lang['submit']); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kaban board modal -->
                    <div class="modal fade" id="KanbanBoard" tabindex="-1" aria-labelledby="KanbanBoardLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="KanbanBoardLabel">
                                        <?php echo _lang['kanban_board']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form id="kabanForm" action="./tickets?ticket_id=<?php echo ($_GET['ticket_id']); ?>"
                                    method="post" enctype="multipart/form-data">
                                    <div class="modal-body p-3">


                                        <input type="hidden" value="tickets" id="part_name" name="part_name">
                                        <input type="hidden" value="<?php echo ($_GET['ticket_id']); ?>" id="part_id"
                                            name="part_id">


                                        <div class="mb-3">
                                            <label class="form-label"><?php echo (_lang['tag']); ?></label>
                                            <select name="board_tag_id" class="form-select form-control-light" required>
                                                <option value="" selected><?php echo (_lang['select']); ?></option>
                                                <?php while ($kanban_tag = $allKanbanTag->fetch_assoc()) { ?>
                                                    <option value="<?php echo $kanban_tag["id"]; ?>">
                                                        <?php echo $kanban_tag["board_tag"]; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                        </div>



                                        <div class="mb-3">
                                            <label for="description"
                                                class="form-label"><?php echo (_lang['description']); ?></label>
                                            <textarea name="description" required
                                                class="form-control form-control-light" id="description"
                                                rows="3"></textarea>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                            <?php echo _lang['close']; ?>
                                        </button>
                                        <button type="submit" name="kanban_board" id="submitKaban"
                                            class="btn btn-primary">
                                            <?php echo _lang['add']; ?>
                                        </button>


                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- mark modal -->
                    <div class="modal fade" id="Mark" tabindex="-1" aria-labelledby="MarkLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="MarkLabel">
                                        <?php echo _lang['mark']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form id="markForm" action="./tickets?ticket_id=<?php echo ($_GET['ticket_id']); ?>"
                                    method="post" enctype="multipart/form-data">
                                    <div class="modal-body p-3">


                                        <input type="hidden" value="tickets" id="part_name" name="part_name">
                                        <input type="hidden"
                                            value="<?php echo (@$ticketsModel->getMarkingTagByTicketId($ticketDetail["id"])['id']); ?>"
                                            id="marking_before_id" name="marking_before_id">
                                        <input type="hidden" value="<?php echo ($_GET['ticket_id']); ?>" id="part_id"
                                            name="part_id">


                                        <div class="mb-3">
                                            <label class="form-label">
                                                <?php echo (_lang['mark']); ?>
                                            </label>
                                            <select name="marking_tag_id" class="form-control form-select"
                                                id="marking_tag_id" required>
                                                <option selected value=""></option>
                                                <option value="0">
                                                    <?php echo (_lang['non_mark']); ?>
                                                </option>
                                                <?php while ($markListsDetails = $markListsResult->fetch_assoc()) { ?>
                                                    <option value="<?php echo ($markListsDetails['id']); ?>">
                                                        <?php echo ($markListsDetails['marking_tag']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                            <?php echo _lang['close']; ?>
                                        </button>
                                        <button type="submit" name="submitMark" id="submitMark" class="btn btn-primary">
                                            <?php echo _lang['add']; ?>
                                        </button>


                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <script>
                        document.getElementById('submitKaban').addEventListener('click', function (event) {
                            var description = document.getElementById('description').value;
                            var receiver = document.getElementById('board_tag').value;

                            if (description.trim() === '' || receiver === null || receiver.length === 0) {
                                event.preventDefault();
                                alert('لطفا تمامی فیلدها را پر کنید.');
                            }

                            if (description.trim() === '' || description.trim().length < 5) {
                                event.preventDefault();
                                alert('توضیحات نباید کوتاه باشد.');
                                return;
                            }
                        });
                    </script>


                </div>
            </div>
            <!-- end row -->

        </div> <!-- container -->

    </div> <!-- content -->