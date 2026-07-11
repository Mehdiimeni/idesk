<?php ///template/first/first.php ?>

<div class="content-page dashboard-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row mb-4">
                <div class="col-12">
                    <div class="dashboard-hero">
                        <div>
                            <h3 class="dashboard-title">
                                <i class="ri-dashboard-line"></i>
                                <?php echo _lang['my_dashboard']; ?>
                            </h3>
                            <div class="dashboard-subtitle">
                                <?php echo _lang['project_name_owner']; ?>
                            </div>
                        </div>

                        <div class="dashboard-user">
                            <div class="dashboard-user-info">
                                <strong><?php echo $_SESSION["name"]; ?></strong>
                                <span>
                                    <?php echo $_SESSION["user_unit"]; ?>
                                    /
                                    <?php echo $_SESSION["user_company"]; ?>
                                </span>
                            </div>

                            <div class="dashboard-actions">
                                <a href="./myaccount" class="dashboard-soft-btn soft-primary">
                                    <i class="ri-user-settings-line me-1"></i>
                                    <?php echo _lang['my_account']; ?>
                                </a>

                                <a href="./chat_center" class="dashboard-soft-btn soft-warning">
                                    <i class="ri-chat-3-line me-1"></i>
                                    <?php echo _lang['chat_center']; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                <div class="col-12">
                    <div class="dashboard-top-summary">

                        <a href="./tickets" class="dashboard-ticket-total text-decoration-none">
                            <div class="stat-icon">
                                <i class="ri-file-text-line"></i>
                            </div>

                            <div>
                                <div class="stat-number">
                                    <?php
                                    echo $rbacClass->checkPermissionOperationByName('view_all_ticket_operation', 'u')
                                        ? $ticketModel->getTotalTicket('', $_SESSION['company_id'])
                                        : $ticketModel->getTotalTicket();
                                    ?>
                                </div>
                                <div class="stat-label">
                                    <?php echo _lang['tickets']; ?>
                                </div>
                            </div>
                        </a>

                        <div class="dashboard-main-actions">



                            <a href="./tickets?add=r" class="dashboard-main-btn soft-danger-btn">
                                <i class="ri-add-circle-line"></i>
                                <?php echo _lang['new_ticket']; ?>
                            </a>

                            <?php if ($rbacClass->checkPermissionOperationByName('view_all_ticket_operation', 'u')): ?>
                                <a href="./priority_list" class="dashboard-main-btn soft-warning-btn">
                                    <i class="ri-file-list-2-line"></i>
                                    <?php echo _lang['priority_list']; ?>
                                </a>
                            <?php endif; ?>

                            <a href="./kanban_board" class="dashboard-main-btn soft-primary-btn">
                                <i class="ri-dashboard-line"></i>
                                <?php echo _lang['kanban_board']; ?>
                            </a>

                            <a href="./marking_tags" class="dashboard-main-btn soft-success-btn">
                                <i class="ri-dashboard-line"></i>
                                <?php echo _lang['marking_tags']; ?>
                            </a>

                        </div>

                    </div>
                </div>

                <div class="col-12">
                    <div class="row g-4">

                        <?php if ($rbacClass->checkPermissionOperationByName('condition_acepted_test', 'u')): ?>
                            <div class="col-xl-6 col-lg-6 col-md-12">
                                <div class="dashboard-panel h-100">
                                    <div class="dashboard-panel-header">
                                        <div>
                                            <h4>
                                                <i class="ri-alarm-warning-line"></i>
                                                <?php echo _lang['description']; ?>
                                            </h4>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table dashboard-table align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th><?php echo _lang['ticket_number']; ?></th>
                                                    <th><?php echo _lang['title']; ?></th>
                                                    <th><?php echo _lang['status']; ?></th>
                                                    <th class="text-center"><?php echo _lang['action']; ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php while ($ticket_data = $commentTicketsNeed->fetch_assoc()):
                                                    $conditionsState = $structureModel->getLastConditionDescription($ticket_data['ticket_id'], 'tickets');
                                                    if ($conditionsState['view_to'])
                                                        continue;
                                                    $condition = $structureModel->getConditionsByName($ticket_data['ticket_status']);
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <span class="ticket-number">
                                                                <?php echo $ticket_data['ticket_number']; ?>
                                                            </span>
                                                        </td>

                                                        <td data-bs-toggle="tooltip"
                                                            title="<?php echo htmlspecialchars($ticket_data['ticket_title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?php echo $textToolsClass->truncateText($ticket_data['ticket_title'], 70); ?>
                                                        </td>

                                                        <td>
                                                            <span
                                                                class="soft-status-badge soft-<?php echo $condition['condition_color']; ?>">
                                                                <?php echo _lang[$condition['condition_name']]; ?>
                                                            </span>
                                                        </td>




                                                        <td>
                                                            <div class="table-actions">
                                                                <?php if ($rbacClass->checkPermissionOperationByName('view_operation', 'u')): ?>
                                                                    <a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($ticket_data['ticket_id']); ?>&rgid=<?php echo $conditionsState['id']; ?>"
                                                                        class="action-icon primary">
                                                                        <i class="ri-eye-line"></i>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if ($ticketModel->getCountFile($ticket_data['ticket_id'], 'tickets') > 0): ?>
                                                                    <span class="action-icon secondary">
                                                                        <i class="ri-attachment-2"></i>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>

                                                <?php while ($ticket_data = $commentTicketsReject->fetch_assoc()):
                                                    $conditionsState = $structureModel->getLastConditionDescription($ticket_data['ticket_id'], 'tickets');
                                                    if ($conditionsState['view_to'])
                                                        continue;
                                                    $condition = $structureModel->getConditionsByName($ticket_data['ticket_status']);
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <span class="ticket-number">
                                                                <?php echo $ticket_data['ticket_number']; ?>
                                                            </span>
                                                        </td>

                                                        <td data-bs-toggle="tooltip"
                                                            title="<?php echo htmlspecialchars($ticket_data['ticket_title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?php echo $textToolsClass->truncateText($ticket_data['ticket_title'], 70); ?>
                                                        </td>

                                                        <td>
                                                            <span
                                                                class="soft-status-badge soft-<?php echo $condition['condition_color']; ?>">
                                                                <?php echo _lang[$condition['condition_name']]; ?>
                                                            </span>
                                                        </td>



                                                        <td>
                                                            <div class="table-actions">
                                                                <?php if ($rbacClass->checkPermissionOperationByName('view_operation', 'u')): ?>
                                                                    <a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($ticket_data['ticket_id']); ?>&rgid=<?php echo $conditionsState['id']; ?>"
                                                                        class="action-icon primary">
                                                                        <i class="ri-eye-line"></i>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if ($ticketModel->getCountFile($ticket_data['ticket_id'], 'tickets') > 0): ?>
                                                                    <span class="action-icon secondary">
                                                                        <i class="ri-attachment-2"></i>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($rbacClass->checkPermissionOperationByName('condition_acepted_test', 'u') || $rbacClass->checkPermissionOperationByName('condition_acepted_invoice', 'u')): ?>
                            <div class="col-xl-6 col-lg-6 col-md-12">
                                <div class="dashboard-panel h-100">
                                    <div class="dashboard-panel-header">
                                        <div>
                                            <h4>
                                                <i class="ri-alert-line"></i>
                                                <?php echo _lang['need_confirmation']; ?>
                                            </h4>
                                            <span><?php echo _lang['need_confirmation_note1']; ?></span>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table dashboard-table align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th><?php echo _lang['ticket_number']; ?></th>
                                                    <th><?php echo _lang['title']; ?></th>
                                                    <th><?php echo _lang['type']; ?></th>
                                                    <th><?php echo _lang['need_confirmation']; ?></th>
                                                    <th class="text-center"><?php echo _lang['action']; ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php if ($rbacClass->checkPermissionOperationByName('condition_acepted_invoice', 'u')): ?>
                                                    <?php foreach ($invoiceTickets as $ticket_data): ?>
                                                        <tr>
                                                            <td>
                                                                <span class="ticket-number">
                                                                    <?php echo $ticket_data['ticket_number']; ?>
                                                                </span>
                                                            </td>

                                                            <td data-bs-toggle="tooltip"
                                                                title="<?php echo htmlspecialchars($ticket_data['ticket_title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                <?php echo $textToolsClass->truncateText($ticket_data['ticket_title'], 70); ?>
                                                            </td>

                                                            <td><?php echo $ticket_data['type_name']; ?></td>

                                                            <td>
                                                                <span class="confirm-badge">
                                                                    <?php echo _lang['confirmation_invoce']; ?>
                                                                </span>
                                                            </td>

                                                            <td>
                                                                <div class="table-actions">
                                                                    <?php if ($rbacClass->checkPermissionOperationByName('view_operation', 'u')): ?>
                                                                        <a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($ticket_data['ticket_id']); ?>"
                                                                            class="action-icon primary">
                                                                            <i class="ri-eye-line"></i>
                                                                        </a>
                                                                    <?php endif; ?>

                                                                    <?php if ($ticketModel->getCountFile($ticket_data['ticket_id'], 'tickets') > 0): ?>
                                                                        <span class="action-icon secondary">
                                                                            <i class="ri-attachment-2"></i>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>

                                                <?php if ($rbacClass->checkPermissionOperationByName('condition_acepted_test', 'u')): ?>
                                                    <?php foreach ($testTickets as $ticket_data): ?>
                                                        <tr>
                                                            <td>
                                                                <span class="ticket-number">
                                                                    <?php echo $ticket_data['ticket_number']; ?>
                                                                </span>
                                                            </td>

                                                            <td data-bs-toggle="tooltip"
                                                                title="<?php echo htmlspecialchars($ticket_data['ticket_title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                <?php echo $textToolsClass->truncateText($ticket_data['ticket_title'], 70); ?>
                                                            </td>

                                                            <td><?php echo $ticket_data['type_name']; ?></td>

                                                            <td>
                                                                <span class="confirm-badge">
                                                                    <?php echo _lang['confirmation_test']; ?>
                                                                </span>
                                                            </td>

                                                            <td>
                                                                <div class="table-actions">
                                                                    <?php if ($rbacClass->checkPermissionOperationByName('view_operation', 'u')): ?>
                                                                        <a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($ticket_data['ticket_id']); ?>"
                                                                            class="action-icon primary">
                                                                            <i class="ri-eye-line"></i>
                                                                        </a>
                                                                    <?php endif; ?>

                                                                    <?php if ($ticketModel->getCountFile($ticket_data['ticket_id'], 'tickets') > 0): ?>
                                                                        <span class="action-icon secondary">
                                                                            <i class="ri-attachment-2"></i>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <?php if ($permissionKabanBoard && $allKanbanTag->num_rows > 0) { ?>
                    <div class="col-12">
                        <div class="dashboard-panel">
                            <div class="dashboard-panel-header">
                                <div>
                                    <h4>
                                        <i class="ri-kanban-view"></i>
                                        <?php echo (_lang['kanban_board']); ?>
                                    </h4>
                                </div>
                            </div>

                            <div class="kanban-list" data-simplebar style="max-height: 320px;">
                                <?php while ($kanban_tag = $allKanbanTag->fetch_assoc()) { ?>
                                    <?php
                                    $AllKabanByTagId = $ticketModel->getAllKabanByTagId($kanban_tag['id']);
                                    while ($kaban_details = $AllKabanByTagId->fetch_assoc()) {
                                        $condition = $structureModel->getConditionsByName($kaban_details['status']);

                                        $showPriority = '';
                                        $priority = $kaban_details['priority'];

                                        if ($priority == 'low') {
                                            $showPriority = '<span class="priority-badge priority-low">' . _lang[$priority] . '</span>';
                                        } elseif ($priority == 'medium') {
                                            $showPriority = '<span class="priority-badge priority-medium">' . _lang[$priority] . '</span>';
                                        } elseif ($priority == 'high') {
                                            $showPriority = '<span class="priority-badge priority-high">' . _lang[$priority] . '</span>';
                                        }

                                        if ($kaban_details['part_name'] == 'tickets') {
                                            $urlSet = "./tickets?ticket_id=";
                                        } elseif ($kaban_details['part_name'] == 'projects') {
                                            $urlSet = "./projects?id=";
                                        } else {
                                            $urlSet = "";
                                        }

                                        $dateConverter = new DateConverter($kaban_details['last_updated_date'], $config->getNowLanguage('a'));
                                        $convertedDate = $dateConverter->convertToShamsi();
                                        ?>

                                        <div class="kanban-item">
                                            <div class="kanban-meta">
                                                <span class="soft-status-badge soft-<?php echo $condition['condition_color']; ?>">
                                                    <?php echo _lang[$condition['condition_name']]; ?>
                                                </span>

                                                <span class="type-badge">
                                                    <?php echo $kaban_details['type_group']; ?>
                                                </span>

                                                <?php echo $showPriority; ?>

                                                <span class="kanban-date">
                                                    <?php echo $convertedDate; ?>
                                                </span>
                                            </div>

                                            <a href="<?php echo $urlSet . $encryptorClass->encrypt($kaban_details['part_id']); ?>"
                                                class="kanban-title">
                                                <span class="kanban-tag">
                                                    <?php echo ($kanban_tag["board_tag"]); ?>
                                                </span>

                                                <span>
                                                    <?php echo '( ' . $kaban_details['ticket_number'] . ' ) ' . $textToolsClass->truncateText($kaban_details['ticket_title'], 250); ?>
                                                </span>
                                            </a>

                                            <div class="kanban-description">
                                                <?php echo $textToolsClass->truncateText($kaban_details['description'], length: 350); ?>
                                            </div>
                                        </div>

                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-page {
        background: var(--dashboard-bg, #f6f8fb);
        margin-top: 10px;
        min-height: calc(100vh - 80px);
    }

    .dashboard-hero,
    .dashboard-stat-card,
    .dashboard-panel {
        background: var(--dashboard-card-bg, #ffffff);
        border: 1px solid var(--dashboard-border, #e9eef5);
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .04);
    }

    .dashboard-hero {
        padding: 20px 22px;
        color: var(--dashboard-text, #263447);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
    }

    .dashboard-title {
        margin: 0;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 9px;
        color: var(--dashboard-title, #263447);
    }

    .dashboard-subtitle {
        margin-top: 6px;
        color: var(--dashboard-muted, #8a97a6);
        font-size: 13px;
    }

    .dashboard-user {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .dashboard-user-info {
        text-align: right;
    }

    .dashboard-user-info strong {
        display: block;
        font-size: 15px;
        color: var(--dashboard-title, #263447);
    }

    .dashboard-user-info span {
        display: block;
        margin-top: 4px;
        color: var(--dashboard-muted, #8a97a6);
        font-size: 13px;
    }

    .dashboard-actions,
    .dashboard-action-bar {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .dashboard-action-bar {
        justify-content: flex-end;
        margin-bottom: 14px;
        background: var(--dashboard-card-bg, #ffffff);
        border: 1px solid var(--dashboard-border, #e9eef5);
        border-radius: 14px;
        padding: 10px;
    }

    .dashboard-soft-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid transparent;
        transition: all .15s ease;
    }

    .dashboard-soft-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .soft-primary {
        background: #eef3ff;
        color: #4b6cb7;
        border-color: #dbe6ff;
    }

    .soft-warning {
        background: #fff7e6;
        color: #b7791f;
        border-color: #ffe6b3;
    }

    .soft-danger {
        background: #fff0f0;
        color: #d64545;
        border-color: #ffd7d7;
    }

    .dashboard-stat-card {
        padding: 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
        transition: all .18s ease;
    }

    .dashboard-stat-card:hover,
    .condition-row:hover,
    .kanban-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, .07);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: #f0f4ff;
        color: #4b6cb7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .stat-number {
        font-size: 32px;
        line-height: 1;
        font-weight: 900;
        color: var(--dashboard-title, #263447);
    }

    .stat-label {
        margin-top: 6px;
        color: var(--dashboard-muted, #8a97a6);
        font-size: 14px;
    }

    .dashboard-panel {
        padding: 16px;
    }

    .dashboard-panel-title,
    .dashboard-panel-header h4 {
        font-size: 15px;
        font-weight: 800;
        color: var(--dashboard-title, #263447);
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .dashboard-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--dashboard-border, #e9eef5);
        margin-bottom: 12px;
    }

    .dashboard-panel-header span {
        display: block;
        margin-top: 4px;
        color: var(--dashboard-muted, #8a97a6);
        font-size: 12px;
    }

    .condition-row {
        text-decoration: none;
        color: var(--dashboard-text, #334155);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 4px;
        border-bottom: 1px solid var(--dashboard-border, #e9eef5);
        transition: all .16s ease;
    }

    .condition-row:last-child {
        border-bottom: 0;
    }

    .condition-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dashboard-table {
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .dashboard-table thead th {
        background: transparent;
        border: 0;
        color: var(--dashboard-muted, #8a97a6);
        font-size: 12px;
        font-weight: 800;
        padding: 8px 12px;
        white-space: nowrap;
    }

    .dashboard-table tbody tr {
        background: var(--dashboard-row-bg, #fbfcfe);
        box-shadow: inset 0 0 0 1px var(--dashboard-border, #e9eef5);
        transition: all .15s ease;
    }

    .dashboard-table tbody tr:hover {
        box-shadow: inset 0 0 0 1px var(--dashboard-border-hover, #dfe7f1), 0 8px 18px rgba(15, 23, 42, .05);
    }

    .dashboard-table tbody td {
        border: 0;
        padding: 13px 12px;
        color: var(--dashboard-text, #334155);
        vertical-align: middle;
    }

    .dashboard-table tbody td:first-child {
        border-radius: 12px 0 0 12px;
    }

    .dashboard-table tbody td:last-child {
        border-radius: 0 12px 12px 0;
    }

    .ticket-number,
    .confirm-badge,
    .type-badge,
    .priority-badge,
    .kanban-tag,
    .soft-status-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.4;
        border: 1px solid transparent;
    }

    .ticket-number {
        color: #4b6cb7;
        background: #eef3ff;
        border-color: #dbe6ff;
    }

    .confirm-badge {
        background: #f4f6f8;
        color: #64748b;
        border-color: #e6ebf0;
    }

    .type-badge {
        background: #eef3ff;
        color: #4b6cb7;
        border-color: #dbe6ff;
    }

    .priority-low {
        background: #eef6ff;
        color: #357abd;
        border-color: #dceeff;
    }

    .priority-medium {
        background: #fff7e6;
        color: #b7791f;
        border-color: #ffe6b3;
    }

    .priority-high {
        background: #fff0f0;
        color: #d64545;
        border-color: #ffd7d7;
    }

    .soft-success {
        background: #eefaf3;
        color: #2f8f5b;
        border-color: #d7f0e2;
    }

    .soft-danger {
        background: #fff0f0;
        color: #d64545;
        border-color: #ffd7d7;
    }

    .soft-warning {
        background: #fff7e6;
        color: #b7791f;
        border-color: #ffe6b3;
    }

    .soft-info {
        background: #eef8ff;
        color: #3182bd;
        border-color: #d9efff;
    }

    .soft-primary {
        background: #eef3ff;
        color: #4b6cb7;
        border-color: #dbe6ff;
    }

    .soft-secondary,
    .soft-dark,
    .soft-light {
        background: #f4f6f8;
        color: #64748b;
        border-color: #e6ebf0;
    }

    .table-actions {
        display: flex;
        justify-content: center;
        gap: 7px;
    }

    .action-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all .15s ease;
        border: 1px solid transparent;
    }

    .action-icon:hover {
        transform: translateY(-1px);
    }

    .action-icon.primary {
        background: #eef3ff;
        color: #4b6cb7;
        border-color: #dbe6ff;
    }

    .action-icon.secondary {
        background: #f4f6f8;
        color: #64748b;
        border-color: #e6ebf0;
    }

    .kanban-list {
        padding-right: 4px;
    }

    .kanban-item {
        border: 1px solid var(--dashboard-border, #e9eef5);
        border-radius: 16px;
        padding: 15px;
        margin-bottom: 12px;
        background: var(--dashboard-row-bg, #fbfcfe);
        transition: all .18s ease;
    }

    .kanban-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        margin-bottom: 10px;
    }

    .kanban-date {
        margin-left: auto;
        color: var(--dashboard-muted, #8a97a6);
        font-size: 12px;
    }

    .kanban-title {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--dashboard-title, #263447);
        font-weight: 800;
        text-decoration: none;
        margin-bottom: 8px;
    }

    .kanban-tag {
        background: var(--dashboard-card-bg, #ffffff);
        color: #4b6cb7;
        border-color: #dbe6ff;
        flex: 0 0 auto;
    }

    .kanban-description {
        color: var(--dashboard-muted, #64748b);
        font-size: 13px;
        line-height: 1.8;
    }

    html[data-layout-color="dark"] .dashboard-page,
    body[data-layout-color="dark"] .dashboard-page,
    body[data-bs-theme="dark"] .dashboard-page,
    [data-bs-theme="dark"] .dashboard-page {
        --dashboard-bg: #161b22;
        --dashboard-card-bg: #1f2630;
        --dashboard-row-bg: #242c37;
        --dashboard-border: #323b48;
        --dashboard-border-hover: #465365;
        --dashboard-title: #eef2f7;
        --dashboard-text: #d7dee8;
        --dashboard-muted: #98a6b8;
    }

    html[data-layout-color="dark"] .soft-primary,
    body[data-layout-color="dark"] .soft-primary,
    body[data-bs-theme="dark"] .soft-primary,
    [data-bs-theme="dark"] .soft-primary {
        background: rgba(75, 108, 183, .16);
        color: #a9c0ff;
        border-color: rgba(169, 192, 255, .18);
    }

    html[data-layout-color="dark"] .soft-warning,
    body[data-layout-color="dark"] .soft-warning,
    body[data-bs-theme="dark"] .soft-warning,
    [data-bs-theme="dark"] .soft-warning {
        background: rgba(183, 121, 31, .16);
        color: #ffd58a;
        border-color: rgba(255, 213, 138, .18);
    }

    html[data-layout-color="dark"] .soft-danger,
    body[data-layout-color="dark"] .soft-danger,
    body[data-bs-theme="dark"] .soft-danger,
    [data-bs-theme="dark"] .soft-danger {
        background: rgba(214, 69, 69, .14);
        color: #ffaaaa;
        border-color: rgba(255, 170, 170, .18);
    }

    html[data-layout-color="dark"] .soft-status-badge,
    body[data-layout-color="dark"] .soft-status-badge,
    body[data-bs-theme="dark"] .soft-status-badge,
    [data-bs-theme="dark"] .soft-status-badge,
    html[data-layout-color="dark"] .ticket-number,
    body[data-layout-color="dark"] .ticket-number,
    body[data-bs-theme="dark"] .ticket-number,
    [data-bs-theme="dark"] .ticket-number,
    html[data-layout-color="dark"] .confirm-badge,
    body[data-layout-color="dark"] .confirm-badge,
    body[data-bs-theme="dark"] .confirm-badge,
    [data-bs-theme="dark"] .confirm-badge,
    html[data-layout-color="dark"] .type-badge,
    body[data-layout-color="dark"] .type-badge,
    body[data-bs-theme="dark"] .type-badge,
    [data-bs-theme="dark"] .type-badge {
        background: rgba(255, 255, 255, .06);
        border-color: rgba(255, 255, 255, .10);
        color: #d7dee8;
    }

    html[data-layout-color="dark"] .stat-icon,
    body[data-layout-color="dark"] .stat-icon,
    body[data-bs-theme="dark"] .stat-icon,
    [data-bs-theme="dark"] .stat-icon,
    html[data-layout-color="dark"] .action-icon.primary,
    body[data-layout-color="dark"] .action-icon.primary,
    body[data-bs-theme="dark"] .action-icon.primary,
    [data-bs-theme="dark"] .action-icon.primary {
        background: rgba(75, 108, 183, .16);
        color: #a9c0ff;
        border-color: rgba(169, 192, 255, .18);
    }

    html[data-layout-color="dark"] .action-icon.secondary,
    body[data-layout-color="dark"] .action-icon.secondary,
    body[data-bs-theme="dark"] .action-icon.secondary,
    [data-bs-theme="dark"] .action-icon.secondary {
        background: rgba(255, 255, 255, .06);
        color: #b7c1ce;
        border-color: rgba(255, 255, 255, .10);
    }

    @media (max-width: 768px) {

        .dashboard-hero,
        .dashboard-user {
            flex-direction: column;
            align-items: flex-start;
        }

        .dashboard-user-info {
            text-align: left;
        }

        .dashboard-action-bar {
            justify-content: flex-start;
        }

        .kanban-date {
            margin-left: 0;
        }
    }

    .dashboard-top-summary {
        background: var(--dashboard-card-bg, #ffffff);
        border: 1px solid var(--dashboard-border, #e9eef5);
        border-radius: 18px;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .04);
    }

    .dashboard-ticket-total {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 220px;
        color: inherit;
    }

    .dashboard-ticket-total:hover {
        color: inherit;
    }

    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        background: #eef3ff;
        color: #4b6cb7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
    }

    .stat-number {
        font-size: 30px;
        line-height: 1;
        font-weight: 900;
        color: var(--dashboard-title, #263447);
    }

    .stat-label {
        margin-top: 6px;
        color: var(--dashboard-muted, #8a97a6);
        font-size: 14px;
    }

    .dashboard-main-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .dashboard-main-btn {
        min-width: 126px;
        min-height: 42px;
        padding: 9px 16px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        border: 1px solid transparent;
        transition: all .15s ease;
    }

    .dashboard-main-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
    }

    .soft-primary-btn {
        background: #eef3ff;
        color: #4b6cb7;
        border-color: #dbe6ff;
    }

    .soft-success-btn {
    background: #eefaf3;
    color: #2f8f5b;
    border-color: #d7f0e2;
}

    .soft-danger-btn {
        background: #fff0f0;
        color: #d64545;
        border-color: #ffd7d7;
    }

    .soft-warning-btn {
        background: #fff7e6;
        color: #b7791f;
        border-color: #ffe6b3;
    }

    @media (max-width: 768px) {
        .dashboard-top-summary {
            flex-direction: column;
            align-items: stretch;
        }

        .dashboard-ticket-total {
            justify-content: center;
        }

        .dashboard-main-actions {
            justify-content: center;
        }

        .dashboard-main-btn {
            flex: 1 1 100%;
        }
    }
</style>