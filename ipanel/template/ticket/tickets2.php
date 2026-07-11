<?php
///template/ticket/tickets.php
?>
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <h4 class="page-title mb-0 font-weight-bold text-primary">
                                <i class="ri-customer-service-2-line me-2"></i>
                                <?php echo _lang['tickets']; ?>
                            </h4>
                        </div>
                        <?php if ($permissionTableFilter) { ?>
                            <a title="<?php echo (_lang['table_filter']); ?>" data-bs-toggle="offcanvas"
                                href="#theme-settings-offcanvas" class="btn btn-sm btn-outline-primary rounded">
                                <i class="ri-filter-3-line me-1"></i>
                                <?php echo _lang['filters']; ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-2">

                            <!-- Action Buttons -->
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php if ($permissionAddTicket || $permissionViewMark) { ?>
                                            <?php if ($permissionAddTicket) { ?>
                                                <a href="./tickets?add=r" class="btn btn-sm btn-danger rounded">
                                                    <i class="ri-add-line me-1"></i>
                                                    <?php echo _lang['new_ticket']; ?>
                                                </a>
                                            <?php } ?>
                                            <?php if ($permissionViewMark) { ?>
                                                <div class="dropdown">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary rounded dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-bookmark-line me-1"></i>
                                                        <?php echo _lang['ticket_mark']; ?>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item"
                                                            href="./tickets"><?php echo _lang['all']; ?></a>
                                                        <?php while ($markListsDetails = $markListsResult->fetch_assoc()) { ?>
                                                            <a class="dropdown-item"
                                                                href="./tickets?mark=<?php echo $markListsDetails['id']; ?>">
                                                                <?php echo _lang[$markListsDetails['mark_list_name']]; ?>
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>

                                        <div class="dropdown">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-success rounded dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-eye-line me-1"></i> <?php echo _lang['view']; ?>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item"
                                                    href="./tickets"><?php echo _lang['normal']; ?></a>
                                                <a class="dropdown-item"
                                                    href="./tickets?details=long"><?php echo _lang['with_details']; ?></a>
                                            </div>
                                        </div>

                                        <div class="dropdown">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-info rounded dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-list-settings-line me-1"></i>
                                                <span id="limit-display"
                                                    data-all="<?php echo htmlspecialchars(_lang['show_all']); ?>"
                                                    data-last-items="<?php echo htmlspecialchars(_lang['last_items']); ?>">
                                                    <?php
                                                    if (isset($_SESSION['ticket_show_limit'])) {
                                                        echo ($_SESSION['ticket_show_limit'] == 0) ? _lang['show_all'] : $_SESSION['ticket_show_limit'] . ' ' . _lang['last_items'];
                                                    } else {
                                                        echo _lang['show_all'];
                                                    }
                                                    ?>
                                                </span>
                                            </button>
                                            <div class="dropdown">
                                                <div class="dropdown-menu ticket-load-dropdown">
                                                    <?php echo $viewValueSelect; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tickets Table -->
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped mb-0 w-100"
                                    id="alternative-page-datatable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="70">#</th>

                                            <th width="100"><?php echo _lang['group']; ?></th>
                                            <th width="80"><?php echo _lang['priority']; ?></th>
                                            <th width="70"><?php echo _lang['comments']; ?></th>
                                            <th><?php echo _lang['title']; ?></th>
                                            <th width="100"><?php echo _lang['status']; ?></th>
                                            <th width="100"><?php echo _lang['finance']; ?>
                                            </th>
                                            <th width="150"><?php echo _lang['company']; ?></th>
                                            <th width="120"><?php echo _lang['user']; ?></th>
                                            <?php if ($permissionViewLocation) { ?>
                                                <th width="100"><?php echo _lang['inbox']; ?></th>
                                            <?php } ?>
                                            <th width="100"><?php echo _lang['added_date']; ?></th>
                                            <th width="100"><?php echo _lang['type']; ?></th>
                                            <?php if ($permissionAddTicket or $permissionViewIndicatorNumbert) { ?>
                                                <th width="100"><?php echo _lang['indicator']; ?></th>
                                            <?php } ?>
                                            <th width="120"><?php echo _lang['action']; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allTickets as $ticket_data) {
                                            if ($permissionViewBeforeStatus) {
                                                $beforeStatus = (isset($ticket_data['before_status_name']) && $ticket_data['before_status_name'] != '')
                                                    ? _lang['condition_backward'] . " : " . _lang[strtolower($ticket_data['before_status_name'])] . ' - ' . $ticket_data['before_person_name'] . " ( " . $ticket_data['status_description'] . " ) "
                                                    : '';
                                            } else {
                                                $beforeStatus = '';
                                            }

                                            $lastFinanceStatus = $ticketModel->getLastFinanceStatus($ticket_data['ticket_id'], 'tickets');
                                            $lastNonFinanceStatus = $ticketModel->getLastNonFinanceStatus($ticket_data['ticket_id'], 'tickets');


                                            ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo $ticket_data['ticket_id']; ?></td>

                                                <td><span
                                                        class="badge bg-soft-primary text-primary"><?php echo $ticket_data['type_group']; ?></span>

                                                    <?php if ($permissionViewMark) {
                                                        if ($ticket_data['mark_id'] > 0) { ?>
                                                            <i class="ri-bookmark-fill text-warning" data-bs-toggle="tooltip"
                                                                title="<?php echo _lang[$ticket_data['mark_name']]; ?>"></i>
                                                        <?php }
                                                    } ?>
                                                </td>
                                                <td><?php echo getPriorityBadge($ticket_data['ticket_priority']); ?></td>
                                                <td>
                                                    <?php echo $ticket_data['comment_count']; ?>
                                                    <?php if ($ticket_data['comment_local'] > 0): ?>
                                                        <span
                                                            class="badge bg-warning rounded-circle p-1"><?php echo $ticket_data['comment_local']; ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-bs-toggle="tooltip"
                                                    title="<?php echo htmlspecialchars($ticket_data['ticket_title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php echo (isset($_GET['details']) && $_GET['details'] == 'long')
                                                        ? $ticket_data['ticket_title']
                                                        : $textToolsClass->truncateText($ticket_data['ticket_title'], 30); ?>
                                                </td>
                                                <td data-bs-toggle="tooltip" title="<?php echo $beforeStatus; ?>">
                                                    <?php if (count($lastNonFinanceStatus) > 0) {
                                                        $conditionNonFinance = $structureModel->getConditionsByName($lastNonFinanceStatus[0]["status_name"]); ?>
                                                        <span
                                                            class="badge alert-<?php echo $conditionNonFinance['condition_color']; ?>">
                                                            <?php echo _lang[$conditionNonFinance['condition_name']]; ?>
                                                        </span>
                                                    <?php } else {
                                                        echo "---";
                                                    } ?>
                                                </td>
                                                <td>
                                                    <?php if (count($lastFinanceStatus) > 0) {
                                                        $conditionFinance = $structureModel->getConditionsByName($lastFinanceStatus[0]["status_name"]); ?>
                                                        <span
                                                            class="badge alert-<?php echo $conditionFinance['condition_color']; ?>">
                                                            <?php echo _lang[$conditionFinance['condition_name']]; ?>
                                                        </span>
                                                    <?php } else {
                                                        echo "---";
                                                    } ?>
                                                </td>
                                                </td>
                                                <td data-bs-toggle="tooltip"
                                                    title="<?php echo htmlspecialchars($ticket_data['company_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php echo (isset($_GET['details']) && $_GET['details'] == 'long')
                                                        ? $ticket_data['company_name']
                                                        : $textToolsClass->truncateText($ticket_data['company_name'] ?? '', 20); ?>
                                                </td>
                                                <td><?php echo $ticket_data['user_name']; ?></td>
                                                <?php if ($permissionViewLocation) { ?>
                                                    <td><?php echo $ticket_data['last_receiver_name']; ?></td>
                                                <?php } ?>
                                                <td>
                                                    <?php $dateConverter = new DateConverter($ticket_data['ticket_creation_date'], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?>
                                                </td>
                                                <td><?php echo $ticket_data['type_name']; ?></td>
                                                <?php if ($permissionAddTicket or $permissionViewIndicatorNumbert) { ?>
                                                    <td><?php echo $ticket_data['indicator_number'] ?? ''; ?></td>
                                                <?php } ?>
                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <?php if ($permissionView) { ?>
                                                            <?php
                                                            // ابتدا مقدار ticket_id را رمزنگاری می‌کنیم
                                                            $encrypted_ticket_id = $encryptorClass->encrypt($ticket_data['ticket_id']);

                                                            // بررسی پارامتر referred
                                                            if (isset($_GET['referred']) && !empty($_GET['referred'])) {
                                                                $ticketUrl = "./tickets?referred=" . urlencode($_GET['referred']) . "&ticket_id=" . $encrypted_ticket_id;
                                                            }
                                                            // بررسی پارامتر condition_name
                                                            elseif (isset($_GET['condition_name']) && !empty($_GET['condition_name'])) {
                                                                $ticketUrl = "./tickets?condition_name=" . urlencode($_GET['condition_name']) . "&ticket_id=" . $encrypted_ticket_id;
                                                            }
                                                            // حالت پیش‌فرض
                                                            else {
                                                                $ticketUrl = "./tickets?ticket_id=" . $encrypted_ticket_id;
                                                            }
                                                            ?>
                                                            <a href="<?php echo $ticketUrl; ?>"
                                                                class="btn btn-xs btn-outline-primary rounded">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <?php if ($permissionViewWorkflowPart and $ticket_data['count_forward'] > 0) { ?>
                                                            <a href="./workflow?ticket_number=<?php echo $ticket_data['ticket_number']; ?>"
                                                                class="btn btn-xs btn-outline-info rounded"
                                                                title="<?php echo $ticket_data['count_forward']; ?>">
                                                                <i class="ri-flow-chart"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <?php if ($permissionViewManHourPart and $ticket_data['man_hour_count'] > 0) { ?>
                                                            <a href="./man_hour?ticket_number=<?php echo $ticket_data['ticket_number']; ?>"
                                                                class="btn btn-xs btn-outline-warning rounded"
                                                                title="<?php echo $ticket_data['man_hour_count']; ?>">
                                                                <i class="ri-time-line"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <?php if ($ticket_data['file_count'] > 0) { ?>
                                                            <?php if ($permissionViewFileManagerPart) { ?>
                                                                <a href="./file_manager?ticket_number=<?php echo $ticket_data['ticket_number']; ?>"
                                                                    class="btn btn-xs btn-outline-success rounded"
                                                                    title="<?php echo $ticket_data['file_count']; ?>">
                                                                    <i class="ri-attachment-2"></i>
                                                                </a>
                                                            <?php } else { ?>
                                                                <span class="btn btn-xs btn-outline-success rounded">
                                                                    <i class="ri-attachment-2"></i>
                                                                </span>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div>
            <!-- end row -->

        </div> <!-- container -->
    </div> <!-- content -->

    <?php if ($permissionTableFilter) { ?>
        <div class="offcanvas offcanvas-end" tabindex="-1" id="theme-settings-offcanvas" style="width: 300px;">
            <div class="offcanvas-header border-bottom p-3">
                <h5 class="m-0">
                    <i class="ri-filter-3-line me-2"></i>
                    <?php echo (_lang['table_filter']); ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body p-0">
                <div data-simplebar class="h-100">
                    <div class="p-3">
                        <div class="accordion custom-accordion" id="custom-accordion">
                            <!-- Company Section -->
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                                    <button class="accordion-button bg-soft-primary" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true"
                                        aria-controls="panelsStayOpen-collapseOne">
                                        <i class="ri-building-2-line me-2"></i>
                                        <?php echo _lang['company']; ?>
                                    </button>
                                </h2>
                                <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="panelsStayOpen-headingOne">
                                    <div class="accordion-body">
                                        <div class="form-check form-switch mb-2">
                                            <input type="checkbox" class="form-check-input" id="selectAllCompanies">
                                            <label class="form-check-label fw-semibold"
                                                for="selectAllCompanies"><?php echo _lang['select_all']; ?></label>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex flex-column gap-2">
                                            <?php
                                            $company_profilesResult->data_seek(0);
                                            while ($company_profilesDetails = $company_profilesResult->fetch_assoc()) {
                                                $company_id = $company_profilesDetails['id'];
                                                $checked = isset($_COOKIE['company_' . $company_id]) && $_COOKIE['company_' . $company_id] === 'true' ? 'checked' : '';
                                                ?>
                                                <div class="form-check form-switch mb-0">
                                                    <input type="checkbox" <?php echo $checked; ?>
                                                        class="form-check-input company-checkbox"
                                                        id="company_<?php echo $company_id; ?>">
                                                    <label class="form-check-label"
                                                        for="company_<?php echo $company_id; ?>"><?php echo $company_profilesDetails['company_name']; ?></label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Units Section -->
                            <div class="accordion-item border-0 mt-2">
                                <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                                    <button class="accordion-button collapsed bg-soft-primary" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo"
                                        aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                        <i class="ri-community-line me-2"></i>
                                        <?php echo _lang['unit']; ?>
                                    </button>
                                </h2>
                                <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse"
                                    aria-labelledby="panelsStayOpen-headingTwo">
                                    <div class="accordion-body">
                                        <div class="form-check form-switch mb-2">
                                            <input type="checkbox" class="form-check-input" id="selectAllUnits">
                                            <label class="form-check-label fw-semibold"
                                                for="selectAllUnits"><?php echo _lang['select_all']; ?></label>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex flex-column gap-2">
                                            <?php
                                            $unitsResult->data_seek(0);
                                            while ($unitsDetails = $unitsResult->fetch_assoc()) {
                                                $unit_id = $unitsDetails['id'];
                                                $checked = isset($_COOKIE['unit_' . $unit_id]) && $_COOKIE['unit_' . $unit_id] === 'true' ? 'checked' : '';
                                                ?>
                                                <div class="form-check form-switch mb-0">
                                                    <input type="checkbox" <?php echo $checked; ?>
                                                        class="form-check-input unit-checkbox"
                                                        id="unit_<?php echo $unit_id; ?>">
                                                    <label class="form-check-label" for="unit_<?php echo $unit_id; ?>"><?php
                                                       $companyResult = $structureModel->getCompanyById($unitsDetails['company_id']);
                                                       echo $companyResult['company_name'];
                                                       ?> - <?php echo $unitsDetails['unit_name']; ?>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Types Section -->
                            <div class="accordion-item border-0 mt-2">
                                <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                    <button class="accordion-button collapsed bg-soft-primary" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree"
                                        aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                        <i class="ri-list-check-2 me-2"></i>
                                        <?php echo _lang['type']; ?>
                                    </button>
                                </h2>
                                <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="panelsStayOpen-headingThree">
                                    <div class="accordion-body">
                                        <div class="form-check form-switch mb-2">
                                            <input type="checkbox" class="form-check-input" id="selectAllTypes">
                                            <label class="form-check-label fw-semibold"
                                                for="selectAllTypes"><?php echo _lang['select_all']; ?></label>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex flex-column gap-2">
                                            <?php
                                            $typesResult->data_seek(0);
                                            while ($typesDetails = $typesResult->fetch_assoc()) {
                                                $type_id = $typesDetails['id'];
                                                $checked = isset($_COOKIE['type_' . $type_id]) && $_COOKIE['type_' . $type_id] === 'true' ? 'checked' : '';
                                                ?>
                                                <div class="form-check form-switch mb-0">
                                                    <input type="checkbox" <?php echo $checked; ?>
                                                        class="form-check-input type-checkbox"
                                                        id="type_<?php echo $type_id; ?>">
                                                    <label class="form-check-label"
                                                        for="type_<?php echo $type_id; ?>"><?php echo $typesDetails['type_group']; ?>
                                                        - <?php echo $typesDetails['type_name']; ?></label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Section -->
                            <div class="accordion-item border-0 mt-2">
                                <h2 class="accordion-header" id="panelsStayOpen-headingFour">
                                    <button class="accordion-button collapsed bg-soft-primary" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour"
                                        aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
                                        <i class="ri-list-check me-2"></i>
                                        <?php echo _lang['status']; ?>
                                    </button>
                                </h2>
                                <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse"
                                    aria-labelledby="panelsStayOpen-headingFour">
                                    <div class="accordion-body">
                                        <div class="form-check form-switch mb-2">
                                            <input type="checkbox" class="form-check-input" id="selectAllStatuses">
                                            <label class="form-check-label fw-semibold"
                                                for="selectAllStatuses"><?php echo _lang['select_all']; ?></label>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex flex-column gap-2">
                                            <?php
                                            $allConditions->data_seek(0);
                                            while ($conditions = $allConditions->fetch_assoc()) {
                                                $condition_id = $conditions['id'];
                                                $checked = isset($_COOKIE['condition_' . $condition_id]) && $_COOKIE['condition_' . $condition_id] === 'true' ? 'checked' : '';
                                                ?>
                                                <div class="form-check form-switch mb-0">
                                                    <input type="checkbox" <?php echo $checked; ?>
                                                        class="form-check-input status-checkbox"
                                                        id="condition_<?php echo $condition_id; ?>">
                                                    <label class="form-check-label"
                                                        for="condition_<?php echo $condition_id; ?>"><?php echo _lang[$conditions['condition_name']]; ?></label>
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

            <script>
                // Select All functionality for each section
                document.getElementById('selectAllCompanies').addEventListener('change', function () {
                    const checkboxes = document.querySelectorAll('.company-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });

                document.getElementById('selectAllUnits').addEventListener('change', function () {
                    const checkboxes = document.querySelectorAll('.unit-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });

                document.getElementById('selectAllTypes').addEventListener('change', function () {
                    const checkboxes = document.querySelectorAll('.type-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });

                document.getElementById('selectAllStatuses').addEventListener('change', function () {
                    const checkboxes = document.querySelectorAll('.status-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            </script>

        </div>
    <?php } ?>
</div>

<style>
    .card {
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table {
        font-size: 0.85rem;
    }

    .table th {
        white-space: nowrap;
        padding: 8px 12px;
    }

    .table td {
        padding: 8px 12px;
        vertical-align: middle;
    }

    .table-responsive {
        -webkit-overflow-scrolling: touch;
    }

    #alternative-page-datatable {
        width: 100% !important;
        min-width: 1200px;
    }

    .badge {
        font-size: 0.75rem;
        padding: 3px 6px;
        font-weight: 500;
    }

    .btn-xs {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }

    .accordion-button {
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
    }

    .accordion-body {
        padding: 0.75rem 1rem;
    }

    .form-check-label {
        font-size: 0.85rem;
    }
</style>