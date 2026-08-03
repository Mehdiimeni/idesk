<?php ///template/ticket/ticket_details.php ?>
<style>
    input[data-switch]+label {

        background-color: #fcddddff;

    }

    input[data-switch="info"]:checked+label {
        background-color: #87c8daff;
    }
</style>
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
                        $rbacClass->checkPermissionOperationByName('condition_clearing') or
                        $rbacClass->checkPermissionOperationByName('condition_acepted_invoice') or
                        $rbacClass->checkPermissionOperationByName('condition_reject_invoice') or
                        $rbacClass->checkPermissionOperationByName('condition_official_bill') or
                        $rbacClass->checkPermissionOperationByName('condition_invoice'))
                ) { ?>
                    <div class="col-lg-12">

                        <!-- Checkout Steps -->
                        <ul class="nav nav-pills bg-nav-pills nav-justified mb-3 order-secondary border">

                            <li class="nav-item">
                                <a href="#payment-information" data-bs-toggle="collapse" aria-controls="payment-information"
                                    aria-expanded="false" class="nav-link rounded-0">
                                    <i class="mdi mdi-cash-multiple font-18"></i>
                                    <span class="d-none d-lg-block">
                                        <?php echo _lang['accounting']; ?>
                                    </span>
                                </a>
                            </li>
                        </ul>

                        <!-- Steps Information -->
                        <div class="tab-content">
                            <!-- Payment Content-->
                            <div class="collapse" id="payment-information">
                                <div class="card d-block">

                                    <div class="card-body border">


                                        <h4 class="mt-2">
                                            <?php echo _lang['accounting']; ?>
                                        </h4>

                                        <p class="text-muted mb-4">
                                            <?php echo _lang['accounting_tips']; ?>
                                        </p>

                                        <!--  box-->
                                        <div class="border p-3 mb-3 rounded">
                                            <div class="row">
                                                <?php if ($rbacClass->checkPermissionOperationByName('accounting_view_operation')) { ?>
                                                    <div class="card-body">
                                                        <?php if ($rbacClass->checkPermissionOperationByName('accounting_add_operation')) { ?>

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
                                                                        <?php if ($rbacClass->checkPermissionOperationByName('accounting_local_operation')) { ?>



                                                                            <span class="mb-1"
                                                                                style="margin-right: 5px; margin-left:5px;">
                                                                                <?php echo _lang['show_for_customers']; ?>
                                                                            </span>
                                                                            <input type="checkbox" id="switch_accounting_file"
                                                                                name="global" data-switch="info" />

                                                                            <label for="switch_accounting_file"
                                                                                data-on-label="<?php echo _lang['yes']; ?>"
                                                                                data-off-label="<?php echo _lang['no']; ?>"></label>




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

                                                            $fileDownloadUrl = "./tickets?ticket_id=" . $_GET['ticket_id'] . "&accounting_file=." . $accountingfileInfo['downloadLink'];

                                                            $divTypeClass = 'alert alert-info  mb-md-1 mb-3 border-info border';
                                                            $localCaption = " ( " . _lang['view_all'] . " ) ";

                                                            if ($accountingfileInfo['local']) {
                                                                $divTypeClass = 'alert alert-danger  mb-md-1 mb-3 alert-danger border';
                                                                $localCaption = " ( " . _lang['local_show_only'] . " ) ";
                                                            }

                                                            if ($accountingfileInfo['user']) {
                                                                $divTypeClass = 'alert   mb-md-1 mb-3 border-success border';
                                                                $localCaption = " ( " . _lang['customer_send'] . " ) ";
                                                            }

                                                            ?>
                                                            <div class="<?php echo $divTypeClass; ?>">
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
                                                                                <?php
                                                                                echo $localCaption;
                                                                                echo empty($accountingfileInfo['fileTitle']) ? $accountingfileInfo['fileName'] : $accountingfileInfo['fileTitle'];

                                                                                ?>
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
                                        <!-- end  box-->

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
                                    <?php

                                    echo $ticketDetail['ticket_title']; ?> (
                                    <?php if ($rbacClass->checkPermissionPartByName('workflow', 'workflow')) { ?><a
                                            href="./workflow?ticket_number=<?php echo $ticketDetail['ticket_number']; ?>"
                                            target="_blank">
                                        <?php } ?>
                                        <?php echo $ticketDetail['ticket_number']; ?>
                                        <?php if ($rbacClass->checkPermissionPartByName('workflow', 'workflow')) { ?>
                                        </a>
                                    <?php } ?> )


                                </h4>

                                <?php if ($rbacClass->checkPermissionOperationByName('condition_operation')) { ?>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                            aria-expanded="false" title="<?php echo _lang['chnage_status']; ?>">
                                            <i class="ri-honour-fill"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <?php if ($setManhourTimeInsert) { ?>
                                                <!-- item-->
                                                <?php while ($conditions = $allConditions->fetch_assoc()) { ?>
                                                    <?php if ($rbacClass->checkPermissionOperationByName($conditions['condition_name'])) { ?>
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
                                                <a href="javascript:void(0);"
                                                    class="dropdown-item"><?php echo _lang['please_insert_manhour_note1']; ?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <!-- project title-->
                            </div>

                            <?php
                            $lastNonFinanceStatus = $ticketsModel->getLastNonFinanceStatus($ticketDetail['id'], 'tickets');
                            if (count($lastNonFinanceStatus) > 0) {

                                $conditionNonFinance = $structureModel->getConditionsByName($lastNonFinanceStatus[0]["status_name"]); ?>
                                <div class="alert alert-<?php echo $conditionNonFinance['condition_color']; ?>   mb-3">
                                    <?php echo _lang[$conditionNonFinance['condition_name']]; ?>

                                </div>
                            <?php } ?>
                            <?php
                            $lastFinanceStatus = $ticketsModel->getLastFinanceStatus($ticketDetail['id'], 'tickets');
                            if (count($lastFinanceStatus) > 0) {

                                $conditionFinance = $structureModel->getConditionsByName($lastFinanceStatus[0]["status_name"]);
                                ?>

                                <div class="alert alert-<?php echo $conditionFinance['condition_color']; ?>  mb-3">
                                    <?php echo _lang['finance'] . " : " . _lang[$conditionFinance['condition_name']]; ?>

                                </div>
                            <?php } ?>
                            <?php if ($rbacClass->checkPermissionOperationByName('status_history_view_operation')): ?>
                                <div class="accordion mb-4" id="statusHistoryAccordion">
                                    <div class="accordion-item border shadow-sm">
                                        <h2 class="accordion-header" id="statusHistoryHeading">
                                            <button class="accordion-button collapsed fw-semibold" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#statusHistoryCollapse"
                                                aria-expanded="false" aria-controls="statusHistoryCollapse">

                                                <i class="fas fa-history me-2 text-primary"></i>
                                                <?php echo _lang['status_history_view']; ?>

                                                <span class="badge bg-secondary ms-2">
                                                    <?php echo count($ticketAllStatusHistory); ?>
                                                </span>
                                            </button>
                                        </h2>

                                        <div id="statusHistoryCollapse" class="accordion-collapse collapse"
                                            aria-labelledby="statusHistoryHeading" data-bs-parent="#statusHistoryAccordion">

                                            <div class="accordion-body p-3 bg-light">

                                                <?php foreach ($ticketAllStatusHistory as $ticketStatusHistory):

                                                    $conditionHistory = $structureModel->getConditionsByName($ticketStatusHistory['status_name']);
                                                    $dateConverter = new DateConverter(
                                                        $ticketStatusHistory['creation_date'],
                                                        $config->getNowLanguage('a')
                                                    );
                                                    ?>

                                                    <div
                                                        class="card border-<?php echo $conditionHistory['condition_color']; ?> shadow-sm mb-2">
                                                        <div class="card-body py-2 px-3">

                                                            <div class="d-flex justify-content-between align-items-start">

                                                                <div>
                                                                    <span
                                                                        class="badge alert-<?php echo $conditionHistory['condition_color']; ?>">
                                                                        <?php echo _lang[$conditionHistory['condition_name']]; ?>
                                                                    </span>

                                                                    <?php if ($ticketStatusHistory['status_description'] != NULL): ?>
                                                                        <div class="mt-2 text-muted small">
                                                                            <i class="fas fa-comment-alt me-1"></i>
                                                                            <?php echo $ticketStatusHistory['status_description']; ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>

                                                                <div class="text-end">
                                                                    <small class="text-muted">
                                                                        <i class="far fa-calendar-alt me-1"></i>
                                                                        <?php echo $dateConverter->convertToShamsi(); ?>
                                                                    </small>
                                                                </div>

                                                            </div>

                                                            <hr class="my-2">

                                                            <div class="small text-muted d-flex flex-wrap gap-3">

                                                                <span>
                                                                    <i class="fas fa-user me-1"></i>
                                                                    <?php echo $ticketStatusHistory['person_name']; ?>
                                                                </span>

                                                                <?php if (!empty($ticketStatusHistory['unit_name'])): ?>
                                                                    <span>
                                                                        <i class="fas fa-building me-1"></i>
                                                                        <?php echo $ticketStatusHistory['unit_name']; ?>
                                                                    </span>
                                                                <?php endif; ?>

                                                            </div>

                                                        </div>
                                                    </div>

                                                <?php endforeach; ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>


                            <?php if (isset($lastForwardDescription['forwards_description']) && $lastForwardDescription['forwards_description'] != '') { ?>


                                <div class="alert alert-info" role="alert">
                                    <div class="" id="CardheadingOne">
                                        <a class="custom-accordion-title " data-bs-toggle="collapse" href="#collapseOne"
                                            aria-expanded="true" aria-controls="collapseOne">
                                            <i class="ri-information-line me-1 align-middle font-16"></i><strong>
                                                <?php echo $lastForwardDescription['person_name']; ?> :
                                            </strong>
                                            <?php echo $lastForwardDescription['forwards_description']; ?>
                                            <span class="float-end"><?php $dateConverter = new DateConverter($lastForwardDescription['creation_date'], $config->getNowLanguage('a'));
                                            echo $dateConverter->convertToShamsi(); ?></span>
                                        </a>

                                    </div>

                                    <div id="collapseOne" class="collapse" aria-labelledby="CardheadingOne"
                                        data-bs-parent="#CardaccordionExample">
                                        <?php foreach ($allForwardDescription as $forwardsDescription) { ?>
                                            <div class="pt-1 mt-2">
                                                <a class="custom-accordion-title" data-bs-toggle="collapse" href="#collapseOne"
                                                    aria-expanded="true" aria-controls="collapseOne">
                                                    <i class="ri-information-line me-1 align-middle font-16"></i>
                                                    <strong><?php echo htmlspecialchars($forwardsDescription['person_name']); ?>
                                                        :</strong>
                                                    <?php echo htmlspecialchars($forwardsDescription['forwards_description'] ?? ''); ?>
                                                    <span class="float-end"><?php
                                                    $dateConverter = new DateConverter($forwardsDescription['creation_date'], $config->getNowLanguage('a'));
                                                    echo htmlspecialchars($dateConverter->convertToShamsi());
                                                    ?></span>
                                                </a>
                                            </div>
                                        <?php } ?>
                                    </div>

                                </div>


                            <?php } ?>
                            <?php if (isset($response_person_hour['response']) && $response_person_hour['response'] != '' && $rbacClass->checkPermissionOperationByName('person_hour_view_operation')) { ?>
                                <div class="alert alert-light" role="alert">
                                    <?php echo ($response_person_hour['name']); ?> <a href="#"
                                        class="alert-link"><?php echo _lang['person_hour']; ?></a> :
                                    <?php echo ($response_person_hour['response']); ?>
                                    <span class="float-end"><?php $dateConverter = new DateConverter($response_person_hour['last_updated_date'], $config->getNowLanguage('a'));
                                    echo $dateConverter->convertToShamsi(); ?></span>
                                </div>
                            <?php } ?>
                            <?php if (isset($response_delivery_time['response']) && $response_delivery_time['response'] != '' && $rbacClass->checkPermissionOperationByName('delivery_time_view_operation')) { ?>
                                <div class="alert alert-secondary" role="alert">
                                    <?php echo ($response_delivery_time['name']); ?> <a href="#"
                                        class="alert-link"><?php echo _lang['delivery_time']; ?></a> :
                                    <?php echo ($response_delivery_time['response']); ?>
                                    <span class="float-end"><?php $dateConverter = new DateConverter($response_delivery_time['last_updated_date'], $config->getNowLanguage('a'));
                                    echo $dateConverter->convertToShamsi(); ?></span>
                                </div>
                            <?php } ?>


                            <?php if (isset($lastStatusDescription['status_description']) && $lastStatusDescription['status_description'] != '') { ?>
                                <div class="alert alert-warning" role="alert">
                                    <i class="ri-alert-line me-1 align-middle font-16"></i><strong>
                                        <?php echo _lang['stop_description']; ?> :
                                    </strong>
                                    <?php echo $lastStatusDescription['status_description']; ?>
                                </div>
                            <?php } ?>

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

                                        <?php if (($ticketDetail['indicator_number'] != null) && ($permissionViewIndicatorNumbert)): ?>
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



                            <?php if ($rbacClass->checkPermissionOperationByName('view_by_operation')) { ?>

                                <div id="tooltip-container" class="mt-3">

                                    <small class="text-muted d-block mb-2">
                                        <i class="mdi mdi-eye-outline"></i>
                                        <?php echo _lang['view_by']; ?>
                                    </small>

                                    <div class="d-flex flex-wrap gap-2">

                                        <?php foreach ($allViewBy as $viewBy) {

                                            $dateConverter = new DateConverter(
                                                $viewBy['creation_date'],
                                                $config->getNowLanguage('a')
                                            );

                                            $convertedDate = $dateConverter->convertToShamsi();
                                            ?>

                                            <span class="badge rounded-pill bg-light text-dark border px-3 py-2"
                                                data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="تاریخ مشاهده: <?php echo $convertedDate; ?>"
                                                style="cursor:pointer;">

                                                <i class="mdi mdi-account-outline me-1"></i>
                                                <?php echo $viewBy['person_name']; ?>

                                            </span>

                                        <?php } ?>

                                    </div>

                                </div>

                            <?php } ?>


                        </div> <!-- end card-body-->

                    </div> <!-- end card-->

                    <?php if ($rbacClass->checkPermissionOperationByName('view_comment_operation')) { ?>

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

                            .comment-badge {
                                font-weight: 400;
                                opacity: .75;
                            }

                            .comment-inactive {
                                background-color: #f3f4f6 !important;
                                border-color: #d7dce1 !important;
                                opacity: .58;
                                filter: grayscale(1);
                            }

                            .comment-inactive .comment-text,
                            .comment-inactive strong,
                            .comment-inactive small {
                                color: #8a9198 !important;
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

                                    <textarea class="form-control form-control-light mb-2"
                                        placeholder="<?php echo _lang['write_message']; ?>" required rows="3"
                                        name="comment_text"></textarea>

                                    <div class="d-flex justify-content-between align-items-center mb-3">

                                        <div>
                                            <?php if ($rbacClass->checkPermissionOperationByName('local_comment_operation')) { ?>
                                                <span class="me-2">
                                                    <?php echo _lang['show_for_customers']; ?>
                                                </span>

                                                <input type="checkbox" id="switch_comment" name="global" data-switch="info" />

                                                <label for="switch_comment" data-on-label="<?php echo _lang['yes']; ?>"
                                                    data-off-label="<?php echo _lang['no']; ?>">
                                                </label>
                                            <?php } ?>
                                        </div>

                                        <?php if ($rbacClass->checkPermissionOperationByName('add_comment_operation')) { ?>
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

                                    $commentIsActive = !isset($commentDetail['is_active']) || (int) $commentDetail['is_active'] === 1;
                                    $isCommentOwner = !empty($commentDetail['admin_id']) &&
                                        (int) $commentDetail['admin_id'] === (int) $_SESSION['admin_id'];

                                    if (!$commentIsActive && !$isCommentOwner && !$canDeactivateCommentByOperation) {
                                        continue;
                                    }

                                    $canDeactivateThisComment = $commentIsActive &&
                                        ($isCommentOwner || $canDeactivateCommentByOperation);

                                    $creator_id = $commentDetail['user_id'] != ''
                                        ? $commentDetail['user_id']
                                        : $commentDetail['admin_id'];

                                    $commentBorderClass = 'comment-border-primary';
                                    $commentBadgeClass = 'bg-primary';
                                    $commentBadgeText = _lang['view_all'];

                                    if ($commentDetail['local']) {
                                        $commentBorderClass = 'comment-border-danger';
                                        $commentBadgeClass = 'bg-danger';
                                        $commentBadgeText = _lang['local_show_only'];
                                    }

                                    if ($commentDetail['user_id']) {
                                        $commentBorderClass = 'comment-border-success';
                                        $commentBadgeClass = 'bg-success';
                                        $commentBadgeText = _lang['customer_send'];
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

                                    <div
                                        class="card comment-card mb-3 <?php echo $commentBorderClass; ?> <?php echo !$commentIsActive ? 'comment-inactive' : ''; ?>">
                                        <div class="card-body">

                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($commentDetail['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></strong>

                                                    <span class="badge comment-badge <?php echo $commentBadgeClass; ?> ms-2">
                                                        <?php echo $commentBadgeText; ?>
                                                    </span>

                                                    <?php if (!$commentIsActive) { ?>
                                                        <span class="badge bg-secondary ms-1">
                                                            <?php echo isset(_lang['deactivated']) ? _lang['deactivated'] : 'غیرفعال شده'; ?>
                                                        </span>
                                                    <?php } ?>
                                                </div>

                                                <small class="text-muted">
                                                    <?php echo $commentDate; ?>
                                                </small>
                                            </div>

                                            <div class="comment-text">
                                                <?= nl2br($commentText) ?>
                                            </div>

                                            <?php if ($commentIsActive && ($rbacClass->checkPermissionOperationByName('reply_comment_operation') || $canDeactivateThisComment)) { ?>
                                                <div class="mt-3 text-end d-flex justify-content-end gap-2">
                                                    <?php if ($rbacClass->checkPermissionOperationByName('reply_comment_operation')) { ?>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 reply-comment-btn"
                                                            data-bs-toggle="modal" data-bs-target="#replyCommentModal"
                                                            data-parent-id="<?php echo $commentDetail['id']; ?>"
                                                            data-creator-id="<?php echo $creator_id; ?>"
                                                            data-reply-to="<?php echo htmlspecialchars($commentDetail['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                                                            <i class="mdi mdi-reply"></i>
                                                            <?php echo _lang['reply']; ?>
                                                        </button>
                                                    <?php } ?>

                                                    <?php if ($canDeactivateThisComment) { ?>
                                                        <form method="post"
                                                            action="./tickets?ticket_id=<?php echo $_GET['ticket_id']; ?>"
                                                            onsubmit="return confirm('<?php echo isset(_lang['deactivate_comment_confirm']) ? _lang['deactivate_comment_confirm'] : 'آیا از غیرفعال‌سازی این کامنت مطمئن هستید؟'; ?>');">
                                                            <input type="hidden" name="comment_id"
                                                                value="<?php echo (int) $commentDetail['id']; ?>">
                                                            <button type="submit" name="deactivate_comment" value="1"
                                                                class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                                <i class="mdi mdi-eye-off-outline"></i>
                                                                <?php echo isset(_lang['deactivate']) ? _lang['deactivate'] : 'غیرفعال کردن'; ?>
                                                            </button>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>

                                            <?php
                                            $replyComments = $commentModel->getCommentPartByParentId($commentDetail['id']);

                                            while ($replyDetail = $replyComments->fetch_assoc()) {

                                                if ($replyDetail['local'] != 0 && $replyDetail['company_id'] != $_SESSION['company_id']) {
                                                    continue;
                                                }

                                                $replyIsActive = !isset($replyDetail['is_active']) || (int) $replyDetail['is_active'] === 1;
                                                $isReplyOwner = !empty($replyDetail['admin_id']) &&
                                                    (int) $replyDetail['admin_id'] === (int) $_SESSION['admin_id'];

                                                if (!$replyIsActive && !$isReplyOwner && !$canDeactivateCommentByOperation) {
                                                    continue;
                                                }

                                                $canDeactivateThisReply = $replyIsActive &&
                                                    ($isReplyOwner || $canDeactivateCommentByOperation);

                                                $replyBorderClass = 'comment-border-primary';
                                                $replyBadgeClass = 'bg-primary';
                                                $replyBadgeText = _lang['view_all'];

                                                if ($replyDetail['local']) {
                                                    $replyBorderClass = 'comment-border-danger';
                                                    $replyBadgeClass = 'bg-danger';
                                                    $replyBadgeText = _lang['local_show_only'];
                                                }

                                                if ($replyDetail['user_id']) {
                                                    $replyBorderClass = 'comment-border-success';
                                                    $replyBadgeClass = 'bg-success';
                                                    $replyBadgeText = _lang['customer_send'];
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
                                                    <div
                                                        class="card comment-card reply-card <?php echo $replyBorderClass; ?> <?php echo !$replyIsActive ? 'comment-inactive' : ''; ?>">
                                                        <div class="card-body py-3">

                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <strong><?php echo htmlspecialchars($replyDetail['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></strong>

                                                                    <span
                                                                        class="badge comment-badge <?php echo $replyBadgeClass; ?> ms-2">
                                                                        <?php echo $replyBadgeText; ?>
                                                                    </span>

                                                                    <?php if (!$replyIsActive) { ?>
                                                                        <span class="badge bg-secondary ms-1">
                                                                            <?php echo isset(_lang['deactivated']) ? _lang['deactivated'] : 'غیرفعال شده'; ?>
                                                                        </span>
                                                                    <?php } ?>
                                                                </div>

                                                                <small class="text-muted">
                                                                    <?php echo $replyDate; ?>
                                                                </small>
                                                            </div>

                                                            <div class="comment-text">
                                                                <?= nl2br($replyText) ?>
                                                            </div>

                                                            <?php if ($canDeactivateThisReply) { ?>
                                                                <div class="mt-3 text-end">
                                                                    <form method="post"
                                                                        action="./tickets?ticket_id=<?php echo $_GET['ticket_id']; ?>"
                                                                        onsubmit="return confirm('<?php echo isset(_lang['deactivate_comment_confirm']) ? _lang['deactivate_comment_confirm'] : 'آیا از غیرفعال‌سازی این کامنت مطمئن هستید؟'; ?>');">
                                                                        <input type="hidden" name="comment_id"
                                                                            value="<?php echo (int) $replyDetail['id']; ?>">
                                                                        <button type="submit" name="deactivate_comment" value="1"
                                                                            class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                                            <i class="mdi mdi-eye-off-outline"></i>
                                                                            <?php echo isset(_lang['deactivate']) ? _lang['deactivate'] : 'غیرفعال کردن'; ?>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            <?php } ?>

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
                                <form validate class="modal-content"
                                    action="./tickets?ticket_id=<?php echo $_GET['ticket_id']; ?>" method="post">

                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <?php echo _lang['reply']; ?>
                                            <small class="text-muted" id="replyToName"></small>
                                        </h5>

                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <input type="hidden" name="parent_id" id="modal_parent_id">
                                        <input type="hidden" name="creator_id" id="modal_creator_id">

                                        <textarea class="form-control mb-3" name="comment_text" id="modal_comment_text"
                                            rows="4" required placeholder="<?php echo _lang['your_answer']; ?>"></textarea>

                                        <?php if ($rbacClass->checkPermissionOperationByName('local_comment_operation')) { ?>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">
                                                    <?php echo _lang['show_for_customers']; ?>
                                                </span>

                                                <input type="checkbox" id="switch_reply_comment" name="global"
                                                    data-switch="info" />

                                                <label for="switch_reply_comment" data-on-label="<?php echo _lang['yes']; ?>"
                                                    data-off-label="<?php echo _lang['no']; ?>">
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                            <?php echo defined('_lang') && isset(_lang['cancel']) ? _lang['cancel'] : 'انصراف'; ?>
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
                                document.querySelectorAll('.reply-comment-btn').forEach(function (button) {
                                    button.addEventListener('click', function () {
                                        document.getElementById('modal_parent_id').value = this.dataset.parentId;
                                        document.getElementById('modal_creator_id').value = this.dataset.creatorId;
                                        document.getElementById('replyToName').textContent = ' - ' + this.dataset.replyTo;
                                        document.getElementById('modal_comment_text').value = '';

                                        const replySwitch = document.getElementById('switch_reply_comment');
                                        if (replySwitch) {
                                            replySwitch.checked = false;
                                        }
                                    });
                                });
                            });
                        </script>

                    <?php } ?>

                    <?php if ($rbacClass->checkPermissionOperationByName('view_schedule_operation') && $allSchedule->num_rows > 0) { ?>

                        <div class="accordion mb-4" id="scheduleAccordion">
                            <div class="accordion-item border shadow-sm">

                                <h2 class="accordion-header" id="scheduleHeading">
                                    <button class="accordion-button collapsed fw-semibold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#scheduleCollapse" aria-expanded="false"
                                        aria-controls="scheduleCollapse">

                                        <i class="ri-calendar-event-line me-2 text-primary"></i>
                                        <?php echo _lang['schedule_list']; ?>

                                        <span class="badge bg-secondary ms-2">
                                            <?php echo $allSchedule->num_rows; ?>
                                        </span>
                                    </button>
                                </h2>

                                <div id="scheduleCollapse" class="accordion-collapse collapse"
                                    aria-labelledby="scheduleHeading" data-bs-parent="#scheduleAccordion">

                                    <div class="accordion-body p-0">

                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-3">
                                                            <?php echo _lang['date_time']; ?>
                                                        </th>
                                                        <th>
                                                            <?php echo _lang['description']; ?>
                                                        </th>
                                                        <th class="text-end pe-3">
                                                            <?php echo _lang['status']; ?>
                                                        </th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php while ($schedule = $allSchedule->fetch_assoc()) { ?>
                                                        <tr>
                                                            <td class="ps-3">
                                                                <span class="badge rounded-pill <?php
                                                                if (strtotime($schedule['date_time']) <= strtotime('today')) {
                                                                    echo 'bg-danger';
                                                                } elseif (strtotime($schedule['date_time']) <= strtotime('+3 days')) {
                                                                    echo 'bg-warning text-dark';
                                                                } else {
                                                                    echo 'bg-primary';
                                                                }
                                                                ?>">
                                                                    <i class="ri-time-line me-1"></i>
                                                                    <?php
                                                                    $dateConverter = new DateConverter(
                                                                        $schedule['date_time'],
                                                                        $config->getNowLanguage('a')
                                                                    );
                                                                    echo $dateConverter->convertToShamsi();
                                                                    ?>
                                                                </span>
                                                            </td>

                                                            <td>
                                                                <span class="text-muted">
                                                                    <?php echo $textToolsClass->truncateText($schedule['description']); ?>
                                                                </span>
                                                            </td>

                                                            <td class="text-end pe-3">

                                                                <div class="d-inline-flex align-items-center gap-2">

                                                                    <?php
                                                                    $scheduleCondition = $structureModel->getConditionsByName($schedule['status']);
                                                                    ?>

                                                                    <span
                                                                        class="badge bg-<?php echo $scheduleCondition['condition_color']; ?>">
                                                                        <?php echo _lang[$scheduleCondition['condition_name']]; ?>
                                                                    </span>

                                                                    <?php if ($rbacClass->checkPermissionOperationByName('condition_operation')) { ?>
                                                                        <div class="dropdown d-inline-block">
                                                                            <a href="#"
                                                                                class="btn btn-sm btn-light border rounded-circle"
                                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                                <i class="ri-more-fill"></i>
                                                                            </a>

                                                                            <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                                                                <?php while ($scheduleConditions = $allScheduleConditions->fetch_assoc()) { ?>
                                                                                    <?php if ($rbacClass->checkPermissionOperationByName($scheduleConditions['condition_name'])) { ?>
                                                                                        <a href="javascript:void(0);"
                                                                                            id="<?php echo $textToolsClass->capitalizeFirstLetter($scheduleConditions['condition_name']); ?>"
                                                                                            class="dropdown-item operation-link"
                                                                                            data-operation="<?php echo $textToolsClass->capitalizeFirstLetter($scheduleConditions['condition_name']); ?>"
                                                                                            data-tableset="<?php echo $scheduleConditions['condition_part']; ?>"
                                                                                            data-id="<?php echo $schedule['id']; ?>">
                                                                                            <?php echo _lang[$scheduleConditions['condition_name']]; ?>
                                                                                        </a>
                                                                                    <?php } ?>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>

                                                                </div>

                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                    <?php } ?>

                    <!-- end card-->
                </div> <!-- end col -->

                <div class="col-lg-6 col-xxl-4">
                    <?php if ($rbacClass->checkPermissionOperationByName('assign_other_operation') or $rbacClass->checkPermissionOperationByName('schedule_operation')) { ?>

                        <div class="card shadow-sm border mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="ri-settings-3-line me-2 text-primary"></i>
                                    <?php echo _lang['operations']; ?>
                                </h5>
                            </div>

                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2">

                                    <?php if ($rbacClass->checkPermissionOperationByName('assign_other_operation')) { ?>
                                        <?php if ($setManhourTimeInsert) { ?>
                                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#AssignTo">
                                                <button type="button" tabindex="0" class="btn btn-info btn-sm"
                                                    data-bs-toggle="popover" data-bs-trigger="hover"
                                                    data-bs-content="<?php echo _lang['assign_note']; ?>"
                                                    title="<?php echo _lang['assign']; ?>">
                                                    <i class="ri-user-add-line me-1"></i>
                                                    <?php echo _lang['assign']; ?>
                                                </button>
                                            </a>
                                        <?php } else { ?>
                                            <a href="javascript:void(0);">
                                                <button type="button" tabindex="0" class="btn btn-info btn-sm"
                                                    data-bs-toggle="popover" data-bs-trigger="hover"
                                                    data-bs-content="<?php echo _lang['please_insert_manhour_note1']; ?>"
                                                    title="<?php echo _lang['assign']; ?>">
                                                    <i class="ri-user-add-line me-1"></i>
                                                    <?php echo _lang['assign']; ?>
                                                </button>
                                            </a>
                                        <?php } ?>
                                    <?php } ?>

                                    <?php if ($rbacClass->checkPermissionOperationByName('schedule_operation')) { ?>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#Schedule">
                                            <button type="button" tabindex="0" class="btn btn-success btn-sm"
                                                data-bs-toggle="popover" data-bs-trigger="hover"
                                                data-bs-content="<?php echo _lang['schedule_note']; ?>"
                                                title="<?php echo _lang['schedule']; ?>">
                                                <i class="ri-calendar-event-line me-1"></i>
                                                <?php echo _lang['schedule']; ?>
                                            </button>
                                        </a>
                                    <?php } ?>

                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#KanbanBoard">
                                        <button type="button" tabindex="0" class="btn btn-warning btn-sm"
                                            data-bs-toggle="popover" data-bs-trigger="hover"
                                            data-bs-content="<?php echo _lang['kanban_board_note']; ?>"
                                            title="<?php echo _lang['kanban_board']; ?>">
                                            <i class="ri-layout-masonry-line me-1"></i>
                                            <?php echo _lang['kanban_board']; ?>
                                        </button>
                                    </a>

                                    <?php if ($rbacClass->checkPermissionOperationByName('priority_operation')) { ?>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#Priority">
                                            <button type="button" tabindex="0" class="btn btn-primary btn-sm"
                                                data-bs-toggle="popover" data-bs-trigger="hover"
                                                data-bs-content="<?php echo _lang['priority_note']; ?>"
                                                title="<?php echo _lang['priority']; ?>">
                                                <i class="ri-flag-line me-1"></i>
                                                <?php echo _lang['priority']; ?>
                                            </button>
                                        </a>
                                    <?php } ?>

                                    <?php if ($rbacClass->checkPermissionOperationByName('change_type_operation')) { ?>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#ChangeType">
                                            <button type="button" tabindex="0" class="btn btn-danger btn-sm"
                                                data-bs-toggle="popover" data-bs-trigger="hover"
                                                data-bs-content="<?php echo _lang['change_type_note']; ?>"
                                                title="<?php echo _lang['type']; ?>">
                                                <i class="ri-exchange-line me-1"></i>
                                                <?php echo _lang['type']; ?>
                                            </button>
                                        </a>
                                    <?php } ?>
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
                            </div>
                        </div>



                    <?php } ?>

                    <?php if ($rbacClass->checkPermissionOperationByName('man_hour_view_operation')) { ?>

                        <div class="card shadow-sm border mb-3">

                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="ri-time-line me-2 text-primary"></i>
                                    <?php echo (_lang['man_hour']); ?>
                                </h5>

                                <span class="badge bg-primary">
                                    <?php echo (_lang['registered']); ?> :
                                    <?php echo ($manhourModel->getTotalManHourPart()); ?>
                                    <?php echo (_lang['hour']); ?>
                                </span>
                            </div>

                            <div class="card-body py-2" data-simplebar style="max-height: 250px;">

                                <?php while ($manHourDetail = $allManHour->fetch_assoc()) {
                                    if ($manHourDetail['company_id'] != '') {
                                        if ($manHourDetail['company_id'] != $structureModel->getCompanyByUnitId($_SESSION['unit_id']))
                                            continue;
                                    }
                                    ?>

                                    <div class="border rounded p-2 mb-2 bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="text-primary">
                                                <i class="ri-user-line me-1"></i>
                                                <?php echo ($manHourDetail['name']); ?>
                                            </strong>

                                            <span class="badge bg-warning text-dark">
                                                <?php echo (_lang[$manHourDetail['todo']]); ?>
                                            </span>
                                        </div>

                                        <div class="small text-muted mt-1">
                                            <?php echo ($manHourDetail['subject']); ?>
                                        </div>

                                        <div class="d-flex justify-content-between mt-2">
                                            <small class="text-muted">
                                                <i class="ri-time-line me-1"></i>
                                                <?php echo ($manHourDetail['man_hour_number']); ?>
                                                <?php echo (_lang['hour']); ?>
                                            </small>

                                            <small class="text-muted">
                                                <?php echo ($manHourDetail['creation_date']); ?>
                                            </small>
                                        </div>
                                    </div>

                                <?php } ?>

                            </div>

                            <?php if ($rbacClass->checkPermissionOperationByName('man_hour_add_operation')) { ?>
                                <div class="card-body border-top">

                                    <form class="needs-validation" validate name="man-hour" id="man-hour"
                                        action="./tickets?ticket_id=<?php echo ($_GET['ticket_id']); ?>" method="post"
                                        onsubmit="return validateForm();">

                                        <div class="row g-2 align-items-center">

                                            <div class="col-md-7">
                                                <div class="input-group">
                                                    <button class="btn btn-primary dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                        id="dropdownTodoMenuButton">
                                                        <?php echo (_lang['subject']); ?>
                                                    </button>

                                                    <div class="dropdown-menu" aria-labelledby="dropdownTodoMenuButton">
                                                        <?php while ($todoListsDetails = $todoListsResult->fetch_assoc()) { ?>
                                                            <a class="dropdown-item dropdown-todo" href="#"
                                                                data-value="<?php echo $todoListsDetails['todo_list_name']; ?>">
                                                                <?php echo _lang[$todoListsDetails['todo_list_name']]; ?>
                                                            </a>
                                                        <?php } ?>
                                                    </div>

                                                    <input required type="hidden" name="selected_todo_list"
                                                        id="selectedTodoList">

                                                    <input required type="text" class="form-control" name="subject"
                                                        placeholder="<?php echo (_lang['enter_description']); ?>"
                                                        aria-label="<?php echo (_lang['enter_description']); ?>"
                                                        aria-describedby="basic-addon1">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <input type="number" min="0" name="man_hour" class="form-control"
                                                    placeholder="<?php echo (_lang['enter_man_hour']); ?>" required>
                                            </div>

                                            <div class="col-md-2 d-grid">
                                                <button type="submit" name="hourSubmit" class="btn btn-primary">
                                                    <?php echo (_lang['add']); ?>
                                                </button>
                                            </div>

                                        </div>
                                    </form>

                                    <script>
                                        function validateForm() {
                                            const selectedTodoList = document.getElementById('selectedTodoList').value;
                                            if (!selectedTodoList) {
                                                alert("<?php echo _lang['please_select_todo_list']; ?>");
                                                return false;
                                            }
                                            return true;
                                        }
                                    </script>

                                </div>
                            <?php } ?>

                        </div>

                    <?php } ?>

                    <?php if ($rbacClass->checkPermissionOperationByName('attach_view_operation')) { ?>

                        <div class="card border">
                            <div class="card-body">
                                <?php if ($rbacClass->checkPermissionOperationByName('attach_add_operation')) { ?>

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
                                                <?php if ($rbacClass->checkPermissionOperationByName('attach_local_operation')) { ?>

                                                    <span class="mb-1" style="margin-right: 5px; margin-left:5px;">
                                                        <?php echo _lang['show_for_customers']; ?>
                                                    </span>
                                                    <input type="checkbox" id="switch_ticket_file" name="global"
                                                        data-switch="info" />

                                                    <label for="switch_ticket_file" data-on-label="<?php echo _lang['yes']; ?>"
                                                        data-off-label="<?php echo _lang['no']; ?>"></label>


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

                                        $fileDownloadUrl = "./tickets?ticket_id=" . $_GET['ticket_id'] . "&file=." . $fileInfo['downloadLink'];

                                        $divTypeClass = 'alert alert-info mb-0 border-info border shadow-sm';
                                        $localCaption = " ( " . _lang['view_all'] . " ) ";

                                        if ($fileInfo['local']) {
                                            $divTypeClass = 'alert alert-danger mb-0 border-danger border shadow-sm';
                                            $localCaption = " ( " . _lang['local_show_only'] . " ) ";
                                        }

                                        if ($fileInfo['user']) {
                                            $divTypeClass = 'alert alert-success mb-0 border-success border shadow-sm';
                                            $localCaption = " ( " . _lang['customer_send'] . " ) ";
                                        }

                                        ?>
                                        <div class="<?php echo $divTypeClass; ?>">
                                            <div class="d-flex align-items-center gap-3">

                                                <div class="flex-shrink-0">
                                                    <div class="avatar-sm">
                                                        <span
                                                            class="avatar-title rounded bg-white text-dark border fw-bold text-uppercase">
                                                            .<?php echo $fileInfo['fileType']; ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="flex-grow-1 overflow-hidden">
                                                    <a href="<?php echo ($fileDownloadUrl); ?>"
                                                        class="d-block text-dark fw-semibold text-decoration-none text-truncate">
                                                        <?php
                                                        echo $localCaption;
                                                        echo empty($fileInfo['fileTitle']) ? $fileInfo['fileName'] : $fileInfo['fileTitle'];
                                                        ?>
                                                    </a>

                                                    <div
                                                        class="d-flex flex-wrap align-items-center gap-2 mt-1 small text-muted">
                                                        <span>
                                                            <i class="ri-file-line me-1"></i>
                                                            <?php echo $fileInfo['fileSize']; ?>
                                                        </span>

                                                        <span class="text-muted">|</span>

                                                        <span>
                                                            <i class="ri-calendar-line me-1"></i>
                                                            <?php
                                                            $dateConverter = new DateConverter(
                                                                $fileInfo['creation_date'],
                                                                $config->getNowLanguage('a')
                                                            );
                                                            echo $dateConverter->convertToShamsi();
                                                            ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="flex-shrink-0">
                                                    <a href="<?php echo ($fileDownloadUrl); ?>"
                                                        class="btn btn-sm btn-light border rounded-circle"
                                                        title="<?php echo (_lang['download'] ?? 'Download'); ?>">
                                                        <i class="ri-download-2-line"></i>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>

                                    <?php } ?>
                                </div>

                            </div>
                        </div>

                    <?php } ?>

                    <!-- AssignTo Modal -->
                    <div class="modal fade" id="AssignTo" tabindex="-1" aria-labelledby="AssignToLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="AssignToLabel">
                                        <?php echo _lang['assign']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <form id="assignForm" action="./tickets?ticket_id=<?php echo ($_GET['ticket_id']); ?>"
                                    method="post" enctype="multipart/form-data">

                                    <div class="modal-body p-3">

                                        <input type="hidden" value="tickets" id="section_part_name"
                                            name="section_part_name">

                                        <input type="hidden" value="<?php echo ($_GET['ticket_id']); ?>"
                                            id="section_element_id" name="section_element_id">

                                        <?php if (isset($_GET['referred']) && $_GET['referred'] != '') { ?>
                                            <input type="hidden" value="<?php echo ($_GET['referred']); ?>" id="referred"
                                                name="referred">
                                        <?php } ?>

                                        <?php if (isset($_GET['condition_name']) && $_GET['condition_name'] != '') { ?>
                                            <input type="hidden" value="<?php echo ($_GET['condition_name']); ?>"
                                                id="condition_name" name="condition_name">
                                        <?php } ?>

                                        <div class="mb-3">
                                            <label for="receiver_person_id_a" class="form-label">
                                                <?php echo _lang['assign']; ?>
                                                <?php echo _lang['admins']; ?>
                                            </label>

                                            <select class="form-control select2-multiple" id="receiver_person_id_a"
                                                name="receiver_person_id_a[]"
                                                data-placeholder="<?php echo _lang['choose']; ?>">

                                                <option value="">
                                                    <?php echo (_lang['select']); ?>
                                                </option>

                                                <?php
                                                $rbacAdminsInfo = $rbacClass->getAdminsByOperationName('assign_other_operation');
                                                if ($rbacAdminsInfo) {
                                                    while ($rbacAdminsInfoDetail = $rbacAdminsInfo->fetch_assoc()) {
                                                        ?>
                                                        <option value="<?php echo $rbacAdminsInfoDetail['id']; ?>">
                                                            <?php echo $rbacAdminsInfoDetail['name']; ?>
                                                        </option>
                                                    <?php }
                                                } ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">
                                                <?php echo _lang['description']; ?>
                                            </label>
                                            <textarea class="form-control" id="description" name="forwards_description"
                                                rows="4"></textarea>
                                        </div>

                                        <?php if ($rbacClass->checkPermissionOperationByName('signature_operation')) { ?>
                                            <div class="mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="checkbox-signup"
                                                        name="sender_signature">

                                                    <label class="form-check-label" for="checkbox-signup">
                                                        <?php echo _lang['signature']; ?>
                                                        <a href="#" class="text-muted">
                                                            <?php echo _lang['signature_note']; ?>
                                                        </a>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if ($rbacClass->checkPermissionOperationByName('person_hour_request')) { ?>
                                            <div class="mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="person_hour"
                                                        name="person_hour">

                                                    <label class="form-check-label" for="person_hour">
                                                        <?php echo _lang['person_hour_request']; ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if ($rbacClass->checkPermissionOperationByName('delivery_time_request')) { ?>
                                            <div class="mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="delivery_time"
                                                        name="delivery_time">

                                                    <label class="form-check-label" for="delivery_time">
                                                        <?php echo _lang['delivery_time_request']; ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php } ?>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                            <?php echo _lang['close']; ?>
                                        </button>

                                        <button type="submit" name="forward" id="submitAssign" class="btn btn-primary">
                                            <?php echo _lang['assign']; ?>
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                    <script>
                        $('#AssignTo').on('shown.bs.modal', function () {
                            if ($('#receiver_person_id_a').hasClass('select2-hidden-accessible')) {
                                $('#receiver_person_id_a').select2('destroy');
                            }

                            $('#receiver_person_id_a').select2({
                                dropdownParent: $('#AssignTo'),
                                width: '100%',
                                placeholder: $('#receiver_person_id_a').data('placeholder')
                            });
                        });
                    </script>

                    <script>
                        document.getElementById('submitAssign').addEventListener('click', function (event) {
                            var description = document.getElementById('description').value;
                            var receiver = document.getElementById('receiver_person_id_a').value;


                            if (receiver === null || receiver.length === 0) {
                                event.preventDefault();
                                alert('لطفا تمامی فیلدها را پر کنید.');
                                return;
                            }

                            if (description.trim() === '' || description.trim().length < 5) {
                                event.preventDefault();
                                alert('توضیحات نباید کوتاه باشد.');
                                return;
                            }
                        });
                    </script>


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


                    <!-- Schedule Modal -->

                    <div class="modal fade" id="Schedule" tabindex="-1" aria-labelledby="ScheduleLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ScheduleLabel">
                                        <?php echo _lang['schedule']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form validate action="./tickets?ticket_id=<?php echo ($_GET['ticket_id']); ?>"
                                    method="post" enctype="multipart/form-data">

                                    <div class="modal-body p-3">

                                        <input type="hidden" value="tickets" id="section_part_name"
                                            name="section_part_name">
                                        <input type="hidden" value="<?php echo ($_GET['ticket_id']); ?>"
                                            id="section_element_id" name="section_element_id">

                                        <div class="mb-3">
                                            <label for="date_time" class="form-label">
                                                <?php echo _lang['date_time']; ?>
                                            </label>
                                            <input type="text" id="datetime-datepicker"
                                                class="form-control persianDatepicker" name="date_time"
                                                placeholder="<?php echo _lang['date_time']; ?>" required>

                                        </div>


                                        <div class="mb-3">
                                            <label for="description" class="form-label">
                                                <?php echo _lang['description']; ?>
                                            </label>
                                            <textarea class="form-control" id="description" name="description"
                                                rows="4"></textarea>
                                        </div>


                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                            <?php echo _lang['close']; ?>
                                        </button>
                                        <button type="submit" name="schedule" value="tickets" class="btn btn-primary">
                                            <?php echo _lang['assign']; ?>
                                        </button>
                                    </div>
                                </form>
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


                    <!-- priority modal -->
                    <div class="modal fade" id="Priority" tabindex="-1" aria-labelledby="PriorityLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="PriorityLabel">
                                        <?php echo _lang['priority']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form id="priorityForm" action="./tickets?ticket_id=<?php echo ($_GET['ticket_id']); ?>"
                                    method="post" enctype="multipart/form-data">
                                    <div class="modal-body p-3">


                                        <input type="hidden" value="tickets" id="part_name" name="part_name">
                                        <input type="hidden" value="<?php echo ($_GET['ticket_id']); ?>" id="part_id"
                                            name="part_id">


                                        <div class="mb-3">
                                            <label class="form-label"><?php echo (_lang['priority']); ?></label>
                                            <select name="priority" class="form-control form-select" id="priority"
                                                required>
                                                <option selected value="low"><?php echo (_lang['low']); ?></option>
                                                <option value="medium"><?php echo (_lang['medium']); ?></option>
                                                <option value="high"><?php echo (_lang['high']); ?></option>
                                            </select>
                                        </div>


                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                            <?php echo _lang['close']; ?>
                                        </button>
                                        <button type="submit" name="submitPriority" id="submitPriority"
                                            class="btn btn-primary">
                                            <?php echo _lang['add']; ?>
                                        </button>


                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <!-- change type modal -->
                    <div class="modal fade" id="ChangeType" tabindex="-1" aria-labelledby="ChangeTypeLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ChangeTypeLabel">
                                        <?php echo _lang['type']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form id="markForm" action="./tickets?ticket_id=<?php echo ($_GET['ticket_id']); ?>"
                                    method="post" enctype="multipart/form-data">
                                    <div class="modal-body p-3">


                                        <input type="hidden" value="<?php echo ($_GET['ticket_id']); ?>" id="ticket_id"
                                            name="ticket_id">


                                        <div class="mb-3">
                                            <label class="form-label">
                                                <?php echo (_lang['type']); ?>
                                            </label>
                                            <select class="form-control select2" data-toggle="select2" name="type_id"
                                                id="type_id">
                                                <option selected><?php echo (_lang['select']); ?></option>
                                                <?php

                                                foreach ($types as $group => $typeList) {
                                                    echo '<optgroup label="' . htmlspecialchars($group) . '">';
                                                    foreach ($typeList as $type) {
                                                        echo '<option value="' . htmlspecialchars($type['id']) . '">' . htmlspecialchars($group) . ' - ' . htmlspecialchars($type['type_name']) . '</option>';
                                                    }
                                                    echo '</optgroup>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                            <?php echo _lang['close']; ?>
                                        </button>
                                        <button type="submit" name="submitTypeChange" id="submitTypeChange"
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
                                            <label class="form-label"><?php echo (_lang['mark']); ?></label>
                                            <select name="marking_tag_id" class="form-control form-select"
                                                id="marking_tag_id" required>
                                                <option selected value=""></option>
                                                <option value="0"><?php echo (_lang['non_mark']); ?></option>
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