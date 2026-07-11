<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <?php if ($rbacClass->checkPermissionOperationByName('add_operation')) { ?>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <a href="./daily_report?add=r" class="btn btn-primary rounded-pill">
                                            <i class="mdi mdi-plus-circle me-1"></i>
                                            <?php echo _lang['add']; ?>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="table-responsive">
                                <table id="scroll-vertical-datatable"
                                    class="table table-hover table-sm dt-responsive nowrap w-100">

                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:50px;">#</th>

                                            <th style="width:140px;">
                                                <?php echo _lang['producer']; ?>
                                            </th>

                                            <th style="width:140px;">
                                                <?php echo _lang['requester']; ?>
                                            </th>

                                            <th style="width:90px;">
                                                <?php echo _lang['priority']; ?>
                                            </th>

                                            <th>
                                                <?php echo _lang['subject']; ?>
                                            </th>

                                            <th style="width:120px;">
                                                <?php echo _lang['company']; ?>
                                            </th>

                                            <th style="width:110px;">
                                                <?php echo _lang['progress_percentage']; ?>
                                            </th>

                                            <th style="width:90px;" class="text-center">
                                                <?php echo _lang['review_status']; ?>
                                            </th>

                                            <th style="width:70px;" class="text-center">
                                                <?php echo _lang['action']; ?>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php foreach ($allDailyReport as $daily_report) { ?>

                                            <?php

                                            $showPriority = '';

                                            switch ($daily_report['priority']) {

                                                case 'high':
                                                    $showPriority =
                                                        '<span class="badge bg-danger">'
                                                        . _lang['high'] .
                                                        '</span>';
                                                    break;

                                                case 'medium':
                                                    $showPriority =
                                                        '<span class="badge bg-warning text-dark">'
                                                        . _lang['medium'] .
                                                        '</span>';
                                                    break;

                                                default:
                                                    $showPriority =
                                                        '<span class="badge bg-primary">'
                                                        . _lang['low'] .
                                                        '</span>';
                                                    break;
                                            }

                                            $progress = (int) $daily_report['progress_percentage'];

                                            if ($progress < 30) {
                                                $progressClass = 'bg-danger';
                                            } elseif ($progress < 80) {
                                                $progressClass = 'bg-warning';
                                            } else {
                                                $progressClass = 'bg-success';
                                            }

                                            ?>

                                            <tr>

                                                <td>
                                                    <?php echo $daily_report['id']; ?>
                                                </td>

                                                <td>
                                                    <strong>
                                                        <?php echo $daily_report['name']; ?>
                                                    </strong>
                                                </td>

                                                <td>
                                                    <?php echo $daily_report['member_name']; ?>
                                                </td>

                                                <td>
                                                    <?php echo $showPriority; ?>
                                                </td>

                                                <td>

                                                    <div class="fw-bold">
                                                        <?php echo $textToolsClass->truncateText(
                                                            $daily_report['subject'],
                                                            100
                                                        ); ?>
                                                    </div>

                                                    <small class="text-muted">

                                                        <?php

                                                        $dateConverter = new DateConverter(
                                                            $daily_report['start_date'],
                                                            $config->getNowLanguage('a')
                                                        );

                                                        echo $dateConverter->convertToShamsi();

                                                        ?>

                                                    </small>

                                                </td>

                                                <td>
                                                    <small>
                                                        <?php echo $textToolsClass->truncateText(
                                                            $daily_report['company_name'],
                                                            60
                                                        ); ?>
                                                    </small>
                                                </td>

                                                <td>

                                                    <div class="d-flex align-items-center">

                                                        <div class="progress flex-grow-1" style="height:6px;">

                                                            <div class="progress-bar <?php echo $progressClass; ?>"
                                                                role="progressbar" style="width: <?php echo $progress; ?>%">
                                                            </div>

                                                        </div>

                                                        <small class="fw-bold ms-1">
                                                            <?php echo $progress; ?>%
                                                        </small>

                                                    </div>

                                                </td>

                                                <?php
                                                // Determine sortable value for review status: approved=2, manager_reviewed=1, only_creator=0
                                                $reviewOrder = 0;
                                                if (!empty($daily_report['is_approved']) && (int) $daily_report['is_approved'] === 1) {
                                                    $reviewOrder = 2;
                                                } elseif (!empty($daily_report['has_other_user_work']) && (int) $daily_report['has_other_user_work'] === 1) {
                                                    $reviewOrder = 1;
                                                } else {
                                                    $reviewOrder = 0;
                                                }
                                                ?>
                                                <td class="text-center" data-order="<?php echo $reviewOrder; ?>">

                                                    <?php if ($reviewOrder === 2) { ?>

                                                        <span class="badge bg-success" title="<?php echo _lang['approved']; ?>">
                                                            <i class="mdi mdi-check"></i>
                                                        </span>

                                                    <?php } elseif ($reviewOrder === 1) { ?>

                                                        <span class="badge bg-info"
                                                            title="<?php echo _lang['manager_reviewed']; ?>">
                                                            <i class="mdi mdi-comment-processing"></i>
                                                        </span>

                                                    <?php } else { ?>

                                                        <span class="badge bg-secondary"
                                                            title="<?php echo _lang['only_creator']; ?>">
                                                            <i class="mdi mdi-account"></i>
                                                        </span>

                                                    <?php } ?>

                                                </td>

                                                <?php
                                                $encrypted_report_id =
                                                    $encryptorClass->encrypt($daily_report['id']);
                                                ?>

                                                <td class="text-center">

                                                    <a href="./daily_report?id=<?php echo $encrypted_report_id; ?>"
                                                        class="btn btn-sm btn-outline-primary">

                                                        <i class="mdi mdi-eye"></i>

                                                    </a>

                                                </td>

                                            </tr>

                                        <?php } ?>

                                    </tbody>

                                </table>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-3">
                                        <?php echo _lang['daily_report_count_summary']; ?>
                                    </h5>
                                </div>

                                <?php if (!empty($dailyReportCounts)) { ?>
                                    <?php foreach ($dailyReportCounts as $countItem) { ?>

                                        <?php
                                        $isCurrentUser = ((int) $countItem['admin_id'] === (int) $_SESSION['admin_id']);

                                        $cardBorderClass = $isCurrentUser ? 'border-primary' : 'border-light';
                                        $cardTitle = $isCurrentUser ? _lang['my_reports'] : $countItem['admin_name'];
                                        $cardBadge = $isCurrentUser ? _lang['me'] : _lang['subordinate'];
                                        $cardBadgeClass = $isCurrentUser ? 'bg-primary' : 'bg-light text-dark';
                                        ?>

                                        <div class="col-xl-3 col-lg-4 col-md-6">
                                            <div class="card border <?php echo $cardBorderClass; ?> shadow-sm mb-3">
                                                <div class="card-body">

                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h5 class="mb-1">
                                                                <?php echo htmlspecialchars($cardTitle); ?>
                                                            </h5>

                                                            <small class="text-muted">
                                                                <?php echo htmlspecialchars($countItem['rbac_name'] ?? '-'); ?>
                                                            </small>
                                                        </div>

                                                        <span class="badge <?php echo $cardBadgeClass; ?> rounded-pill">
                                                            <?php echo $cardBadge; ?>
                                                        </span>
                                                    </div>

                                                    <div class="row text-center mt-3">
                                                        <div class="col-4">
                                                            <h4 class="mb-0">
                                                                <?php echo (int) $countItem['total_reports']; ?>
                                                            </h4>
                                                            <small class="text-muted">
                                                                <?php echo _lang['total']; ?>
                                                            </small>
                                                        </div>

                                                        <div class="col-4">
                                                            <h4 class="mb-0 text-warning">
                                                                <?php echo (int) $countItem['open_reports']; ?>
                                                            </h4>
                                                            <small class="text-muted">
                                                                <?php echo _lang['open']; ?>
                                                            </small>
                                                        </div>

                                                        <div class="col-4">
                                                            <h4 class="mb-0 text-success">
                                                                <?php echo (int) $countItem['done_reports']; ?>
                                                            </h4>
                                                            <small class="text-muted">
                                                                <?php echo _lang['completed']; ?>
                                                            </small>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    <?php } ?>
                                <?php } else { ?>

                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            <?php echo _lang['no_daily_report_for_display']; ?>
                                        </div>
                                    </div>

                                <?php } ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>