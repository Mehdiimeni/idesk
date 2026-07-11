<?php ///template/ticket/tickets.php ?>
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="page-title mb-0 text-primary fw-bold">
                            <i class="ri-ticket-2-line me-2"></i>
                            <?php echo _lang['tickets']; ?>
                        </h4>
                        <?php if ($rbacClass->checkPermissionOperationByName('table_filter_operation', 'u')) { ?>
                            <a title="<?php echo (_lang['table_filter']); ?>" data-bs-toggle="offcanvas" href="#theme-settings-offcanvas"
                                class="btn btn-sm btn-outline-primary rounded">
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
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">

                            <!-- Action Buttons -->
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="d-flex flex-wrap gap-1">

                                        <a href="<?= htmlspecialchars($addTicketUrl, ENT_QUOTES, 'UTF-8') ?>"
                                            class="btn btn-sm btn-danger rounded">
                                            <i class="ri-add-line me-1"></i>
                                            <?= _lang['new_ticket']; ?>
                                        </a>

                                        <div class="dropdown">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary rounded dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-bookmark-line me-1"></i>
                                                    <?= _lang['ticket_mark']; ?>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item"
                                                        href="<?= htmlspecialchars($viewUrlNormal, ENT_QUOTES, 'UTF-8') ?>"><?= _lang['all']; ?></a>

                                                    <?php foreach ($ticketMarks as $mark): ?>
                                                        <a class="dropdown-item"
                                                            href="<?= htmlspecialchars('./tickets?mark=' . $mark['id'], ENT_QUOTES, 'UTF-8') ?>">
                                                            <?= $mark['marking_tag'] ?>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                        <!-- Dropdown نمایش -->
                                        <div class="dropdown">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-success rounded dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-eye-line me-1"></i> <?= _lang['view']; ?>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item"
                                                    href="<?= htmlspecialchars($viewUrlNormal, ENT_QUOTES, 'UTF-8') ?>">
                                                    <?= _lang['normal']; ?>
                                                </a>
                                                <a class="dropdown-item"
                                                    href="<?= htmlspecialchars($viewUrlWithDetails, ENT_QUOTES, 'UTF-8') ?>">
                                                    <?= _lang['with_details']; ?>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped mb-0 w-100"
                                    id="alternative-page-datatable">
                                    <thead class="table-light">

                                    </thead>
                                    <tbody>

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

    <!-- Filter Offcanvas -->
    <div class="offcanvas offcanvas-end border-0" tabindex="-1" id="theme-settings-offcanvas" style="width: 320px;">
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
                        <!-- Types Section -->
                        <div class="accordion-item border-0 mt-2">
                            <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                <button class="accordion-button bg-soft-primary" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false"
                                    aria-controls="panelsStayOpen-collapseThree">
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
                                <button class="accordion-button bg-soft-primary" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false"
                                    aria-controls="panelsStayOpen-collapseFour">
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
            document.addEventListener("DOMContentLoaded", function () {
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
            });

            // send table column to json
            window.datatableColumns = <?php echo $datatableColumnsJson; ?>;
        </script>
    </div>
</div>

<style>
.ticket-table-toolbar {
    background: #ffffff;
    border: 1px solid #edf0f2;
    border-radius: 14px;
    padding: 10px 12px;
}

#alternative-page-datatable {
    border-collapse: separate !important;
    border-spacing: 0 8px !important;
}

#alternative-page-datatable thead th {
    background: #f8fafc;
    color: #6c757d;
    font-size: .78rem;
    font-weight: 700;
    border: none !important;
    white-space: nowrap;
}

#alternative-page-datatable tbody tr {
    background: #ffffff;
    box-shadow: 0 1px 4px rgba(15, 23, 42, .06);
    transition: all .15s ease;
}

#alternative-page-datatable tbody tr:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, .09);
}

#alternative-page-datatable tbody td {
    border-top: 1px solid #eef1f4;
    border-bottom: 1px solid #eef1f4;
    vertical-align: middle;
    padding: 10px 12px;
}

#alternative-page-datatable tbody td:first-child {
    border-inline-start: 1px solid #eef1f4;
    border-radius: 12px 0 0 12px;
    font-weight: 700;
}

#alternative-page-datatable tbody td:last-child {
    border-inline-end: 1px solid #eef1f4;
    border-radius: 0 12px 12px 0;
}

.ticket-search-input {
    min-width: 260px;
}

.ticket-loading {
    display: inline-flex;
    align-items: center;
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 999px;
    padding: 8px 14px;
    box-shadow: 0 4px 16px rgba(0,0,0,.08);
}

.dt-buttons .btn {
    margin-inline-start: 1px;
}
</style>