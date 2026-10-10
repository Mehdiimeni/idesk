<?php
///template/first/first.php
?>
<div class="content-page dashboard-page">
    <div class="content">
        <div class="container-fluid">

            <div class="dashboard-hero mb-4">
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
                        <strong><?php echo $profileName; ?></strong>
                        <span>
                            <?php echo $adminRbacName; ?>
                            /
                            <?php echo $adminCompanyName; ?>
                        </span>
                    </div>

                    <div class="dashboard-actions">
                        <a href="./myaccount" class="btn btn-light btn-sm">
                            <i class="mdi mdi-account-edit me-1"></i>
                            <?php echo _lang['my_account']; ?>
                        </a>

                        <a href="./chat_center" class="btn btn-warning btn-sm">
                            <i class="mdi mdi-chat-outline me-1"></i>
                            <?php echo _lang['chat_center']; ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="dashboard-summary-grid mb-4">
                <a href="./tickets?referred=0"
                   class="dashboard-summary-card dashboard-summary-card-primary text-decoration-none">
                    <div class="summary-card-top">
                        <div class="stat-icon primary">
                            <i class="ri-inbox-archive-fill"></i>
                        </div>
                        <span class="summary-open-icon"><i class="ri-arrow-left-up-line"></i></span>
                    </div>

                    <div class="summary-card-body">
                        <div class="stat-label"><?php echo _lang['dashboard_my_inbox']; ?></div>
                        <div class="stat-number text-primary"><?php echo number_format($intNoActionTicketCount); ?></div>
                        <div class="summary-caption"><?php echo _lang['dashboard_current_inbox_caption']; ?></div>

                        <div class="summary-mini-stats dashboard-inbox-breakdown">
                            <span>
                                <?php echo _lang['high']; ?>
                                <strong><?php echo number_format($dashboardInboxBreakdown['high']); ?></strong>
                            </span>
                            <span>
                                <?php echo _lang['medium']; ?>
                                <strong><?php echo number_format($dashboardInboxBreakdown['medium']); ?></strong>
                            </span>
                            <span>
                                <?php echo _lang['low']; ?>
                                <strong><?php echo number_format($dashboardInboxBreakdown['low']); ?></strong>
                            </span>
                            <span>
                                <?php echo _lang['dashboard_overdue']; ?>
                                <strong><?php echo number_format($dashboardInboxBreakdown['overdue']); ?></strong>
                            </span>
                        </div>
                    </div>
                </a>

                <a href="./tickets?referred=1"
                   class="dashboard-summary-card dashboard-summary-card-success text-decoration-none">
                    <div class="summary-card-top">
                        <div class="stat-icon success">
                            <i class="ri-inbox-unarchive-fill"></i>
                        </div>
                        <span class="summary-open-icon"><i class="ri-arrow-left-up-line"></i></span>
                    </div>

                    <div class="summary-card-body">
                        <div class="stat-label"><?php echo _lang['dashboard_forwarded_tickets']; ?></div>
                        <div class="stat-number text-success"><?php echo number_format($intForwardTicketCount); ?></div>
                        <div class="summary-caption"><?php echo _lang['dashboard_forwarded_by_me_caption']; ?></div>
                    </div>
                </a>

                <div class="dashboard-summary-card dashboard-activity-card dashboard-period-card dashboard-period-week">
                    <div class="activity-card-header">
                        <div>
                            <span class="activity-eyebrow"><?php echo _lang['dashboard_activity_report']; ?></span>
                            <strong><?php echo _lang['dashboard_last_7_days']; ?></strong>
                        </div>
                        <div class="activity-icon weekly"><i class="ri-calendar-check-line"></i></div>
                    </div>

                    <div class="activity-values">
                        <div class="activity-value">
                            <span><?php echo _lang['dashboard_received_tickets']; ?></span>
                            <strong><?php echo number_format($dashboardActivityStats['received_7d']); ?></strong>
                        </div>
                        <div class="activity-divider"></div>
                        <div class="activity-value">
                            <span><?php echo _lang['dashboard_sent_tickets']; ?></span>
                            <strong><?php echo number_format($dashboardActivityStats['sent_7d']); ?></strong>
                        </div>
                    </div>
                </div>

                <div class="dashboard-summary-card dashboard-activity-card dashboard-period-card dashboard-period-month">
                    <div class="activity-card-header">
                        <div>
                            <span class="activity-eyebrow"><?php echo _lang['dashboard_activity_report']; ?></span>
                            <strong><?php echo _lang['dashboard_last_30_days']; ?></strong>
                        </div>
                        <div class="activity-icon monthly"><i class="ri-bar-chart-box-line"></i></div>
                    </div>

                    <div class="activity-values">
                        <div class="activity-value">
                            <span><?php echo _lang['dashboard_received_tickets']; ?></span>
                            <strong><?php echo number_format($dashboardActivityStats['received_30d']); ?></strong>
                        </div>
                        <div class="activity-divider"></div>
                        <div class="activity-value">
                            <span><?php echo _lang['dashboard_sent_tickets']; ?></span>
                            <strong><?php echo number_format($dashboardActivityStats['sent_30d']); ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-lower-grid mb-4">
<?php if($allKanbanTag->num_rows > 0){ ?>

                <div class="dashboard-panel dashboard-grid-panel">
                    <div class="dashboard-panel-header">
                        <div>
                            <h4>
                                <i class="ri-kanban-view"></i>
                                <?php echo (_lang['kanban_board']); ?>
                            </h4>
                        </div>
                    </div>

                    <div class="kanban-list" data-simplebar style="max-height: 340px;">
                        <?php while ($kanban_tag = $allKanbanTag->fetch_assoc()) { ?>
                            <?php
                            $AllKabanByTagId = $ticketModel->getAllKabanByTagId($kanban_tag['id']);
                            while ($kaban_details = $AllKabanByTagId->fetch_assoc()) {

                                $condition = $structureModel->getConditionsByName($kaban_details['status']);

                                $dateConverter = new DateConverter($kaban_details['last_updated_date'], $config->getNowLanguage('a'));
                                $convertedDate = $dateConverter->convertToShamsi();

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
                                ?>

                                <div class="kanban-item">
                                    <div class="kanban-meta">
                                        <span class="status-badge alert-<?php echo $condition['condition_color']; ?>">
                                            <?php echo _lang[$condition['condition_name']]; ?>
                                        </span>

                                        <?php
                                        $lastFinanceStatus = $ticketModel->getLastFinanceStatus($kaban_details['ticket_id'], 'tickets');
                                        if (count($lastFinanceStatus) > 0) {
                                            $condition = $structureModel->getConditionsByName($lastFinanceStatus[0]["status_name"]);
                                            ?>
                                            <span class="status-badge alert-<?php echo $condition['condition_color']; ?>">
                                                <?php echo _lang[$condition['condition_name']]; ?>
                                            </span>
                                        <?php } ?>

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
            
<?php } ?>
<?php if ($allTodo->num_rows > 0) { ?>
                <div class="dashboard-panel dashboard-grid-panel">
                    <div class="dashboard-panel-header">
                        <div>
                            <h4>
                                <i class="ri-task-line"></i>
                                <?php echo (_lang['need_todo']); ?>
                            </h4>
                        </div>
                    </div>

                    <div class="todo-list" data-simplebar style="max-height: 300px;">
                        <?php while ($todo_details = $allTodo->fetch_assoc()) { 
                            
                           
                                                $encrypted_report_id =
                                                    $encryptorClass->encrypt($todo_details['id']);
                                                
                            ?>
                            <div class="todo-item">
                                <div class="todo-title">
                                    <i class="mdi mdi-arrow-bottom-left-bold-box"></i>
                                    <a href="./daily_report?id=<?php echo $encrypted_report_id; ?>">
                                        <?php echo (getTextLimit($todo_details['subject'])); ?>
                                    </a>
                                </div>

                                <div class="todo-progress">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: <?php echo $todo_details['progress_percentage']; ?>%;"
                                            aria-valuenow="<?php echo $todo_details['progress_percentage']; ?>"
                                            aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <?php echo getPriorityBadge($todo_details['priority']); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
          
<?php } ?>
            <?php  if ($permissionProjects && count($allProjects) > 0) { ?>
                <div class="dashboard-panel dashboard-grid-panel">
                    <div class="dashboard-panel-header">
                        <div>
                            <h4>
                                <i class="ri-folder-chart-line"></i>
                                <?php echo (_lang['projects']); ?>
                            </h4>
                        </div>
                    </div>

                    <div class="todo-list" data-simplebar style="max-height: 300px;">
                        <?php foreach ($allProjects as $projectDetails) {
                            if (!$projectsModel->checkMemberInProject($projectDetails['id']) && !$rbacClass->checkPermissionOperationByName('observer_project_operation'))
                                continue;
                            ?>
                            <div class="todo-item">
                                <div class="todo-title">
                                    <i class="mdi mdi-arrow-bottom-left-bold-box"></i>
                                    <a href="./projects?id=<?php echo $encryptorClass->encrypt($projectDetails['id']); ?>">
                                        <?php echo (getTextLimit($projectDetails['name'])); ?>
                                    </a>
                                </div>

                                <div class="todo-progress">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: <?php echo $projectDetails['progress_percentage']; ?>%;"
                                            aria-valuenow="<?php echo $projectDetails['progress_percentage']; ?>"
                                            aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <?php echo getPriorityBadge($projectDetails['priority']); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            </div>

                        <?php if (($personHourDeliveryTimePermissionOperation || $personHourDeliveryTimePermissionRequest) and $allRequests->num_rows > 0): ?>
                            <div class="dashboard-panel dashboard-requests-panel mb-4">
                                <div class="dashboard-panel-header">
                                    <div>
                                        <h4>
                                            <i class="mdi mdi-email-outline"></i>
                                            <?php echo _lang['requests']; ?>
                                        </h4>
                                    </div>
                                </div>
                        
                                <div class="table-responsive" data-simplebar style="max-height: 330px; overflow-x:hidden">
                                    <table id="dashboard-requests-table" class="table dashboard-table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th width="20%">
                                                    <?php echo _lang['ticket_number']; ?>
                                                </th>
                                                <th width="25%">
                                                    <?php echo _lang['request']; ?>
                                                </th>
                                                <th width="35%">
                                                    <?php echo _lang['response']; ?>
                                                </th>
                                                <th width="20%" class="text-center">
                                                    <?php echo _lang['action']; ?>
                                                </th>
                                            </tr>
                                        </thead>
                        
                                        <tbody>
                                            <?php if ($personHourDeliveryTimePermissionOperation): ?>
                                                <?php while ($requests = $allRequests->fetch_assoc()): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="./tickets?ticket_id=<?php echo $encryptorClassClass->encrypt($requests['section_element_id']); ?>"
                                                                class="ticket-number">
                                                                <?php echo $requests['ticket_number']; ?>
                                                            </a>
                                                        </td>
                        
                                                        <td>
                                                            <?php echo _lang[$requests['request']]; ?>
                                                        </td>
                        
                                                        <td>
                                                            <form class="updateForm" data-id="<?php echo $requests['id']; ?>">
                                                                <input type="hidden" name="id" value="<?php echo $requests['id']; ?>">
                        
                                                                <?php if ($requests['request'] == 'person_hour'): ?>
                                                                    <input type="text" class="form-control form-control-sm dashboard-input text-center"
                                                                        name="person_hour_response" data-toggle="input-mask" data-mask-format="00"
                                                                        placeholder="HH" style="width: 90px;">
                                                                <?php elseif ($requests['request'] == 'delivery_time'): ?>
                                                                    <input type="text" class="form-control form-control-sm dashboard-input text-center"
                                                                        name="delivery_time_response" data-toggle="input-mask" data-mask-format="00/00/0000"
                                                                        placeholder="DD/MM/YYYY" style="width: 150px;">
                                                                <?php endif; ?>
                                                            </form>
                                                        </td>
                        
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 send-button">
                                                                <i class="mdi mdi-send me-1"></i>
                                                                <?php echo _lang['send']; ?>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                        
                                            <?php if ($personHourDeliveryTimePermissionRequest): ?>
                                                <?php while ($response = $allResponse->fetch_assoc()): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="./tickets?ticket_id=<?php echo $encryptorClassClass->encrypt($response['section_element_id']); ?>"
                                                                class="ticket-number">
                                                                <?php echo $response['ticket_number']; ?>
                                                            </a>
                                                        </td>
                        
                                                        <td>
                                                            <?php echo _lang[$response['request']]; ?>
                                                        </td>
                        
                                                        <td>
                                                            <span class="confirm-badge">
                                                                <?php echo $response['response']; ?>
                                                            </span>
                                                        </td>
                        
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-success btn-sm rounded-pill px-3 confirmation-button">
                                                                <i class="mdi mdi-check-circle-outline me-1"></i>
                                                                <?php echo _lang['confirmation']; ?>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

        </div>
    </div>
</div>

<style>
    .dashboard-page {
        background: transparent;
        margin-top: 10px;
    }

    .dashboard-page .container-fluid {
        max-width: 1320px;
        margin-left: auto;
        margin-right: auto;
    }

    .dashboard-hero {
        background: linear-gradient(135deg, #263859, #4b6cb7);
        border-radius: 18px;
        padding: 24px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 18px 35px rgba(0, 0, 0, 0.25);
    }

    .dashboard-title {
        margin: 0;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dashboard-subtitle {
        margin-top: 8px;
        opacity: 0.85;
        font-size: 13px;
    }

    .dashboard-user,
    .dashboard-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .dashboard-user-info {
        text-align: right;
    }

    .dashboard-user-info strong {
        display: block;
        font-size: 16px;
    }

    .dashboard-user-info span {
        display: block;
        margin-top: 4px;
        font-size: 13px;
        opacity: 0.85;
    }

    .dashboard-stat-card,
    .dashboard-panel {
        background: var(--bs-secondary-bg);
        color: var(--bs-body-color);
        border: 1px solid var(--bs-border-color);
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.14);
    }

    .dashboard-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .dashboard-summary-card {
        min-height: 190px;
        background: var(--bs-secondary-bg);
        color: var(--bs-body-color);
        border: 1px solid var(--bs-border-color);
        border-radius: 20px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .dashboard-summary-card::after {
        content: '';
        position: absolute;
        inset: auto -35px -55px auto;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: rgba(75, 108, 183, .05);
        pointer-events: none;
    }

    .dashboard-summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.13);
        border-color: rgba(75, 108, 183, .28);
    }

    .dashboard-summary-card-primary {
        border-top: 3px solid #5f8cff;
    }

    .dashboard-summary-card-success {
        border-top: 3px solid #2fd18a;
    }


    .dashboard-period-week {
        border-top: 3px solid #8b5cf6;
    }

    .dashboard-period-month {
        border-top: 3px solid #f59e0b;
    }

    .dashboard-inbox-breakdown {
        margin-top: 14px;
    }

    .dashboard-inbox-breakdown span:nth-child(1) strong {
        font-weight: 800;
    }

    .dashboard-period-card .activity-values {
        margin-top: 20px;
    }

    .summary-mini-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .summary-mini-stats span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
        border-radius: 999px;
        background: var(--bs-tertiary-bg);
        border: 1px solid var(--bs-border-color);
        color: var(--bs-secondary-color);
        font-size: 11px;
    }

    .summary-mini-stats strong {
        color: var(--bs-body-color);
        font-size: 12px;
    }

    .summary-card-top,
    .activity-card-header,
    .activity-values {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .summary-open-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--bs-secondary-color);
        background: var(--bs-tertiary-bg);
        border: 1px solid var(--bs-border-color);
    }

    .summary-card-body {
        margin-top: 18px;
    }

    .summary-caption {
        margin-top: 9px;
        color: var(--bs-secondary-color);
        font-size: 12px;
    }

    .dashboard-activity-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .dashboard-referral-card .summary-card-body {
        position: relative;
        z-index: 1;
    }

    .dashboard-referral-card .stat-number {
        margin-top: 8px;
        letter-spacing: -.5px;
    }

    .activity-card-header strong {
        display: block;
        margin-top: 3px;
        font-size: 15px;
        color: var(--bs-body-color);
    }

    .activity-eyebrow {
        color: var(--bs-secondary-color);
        font-size: 11px;
        font-weight: 700;
    }

    .activity-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .activity-icon.weekly {
        color: #8b5cf6;
        background: rgba(139, 92, 246, .12);
    }

    .activity-icon.monthly {
        color: #f59e0b;
        background: rgba(245, 158, 11, .12);
    }

    .activity-values {
        margin-top: 25px;
        background: var(--bs-tertiary-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 14px;
        padding: 12px 14px;
        gap: 10px;
    }

    .activity-value {
        flex: 1;
        min-width: 0;
    }

    .activity-value span {
        display: block;
        color: var(--bs-secondary-color);
        font-size: 11px;
        margin-bottom: 4px;
    }

    .activity-value strong {
        display: block;
        color: var(--bs-body-color);
        font-size: 22px;
        line-height: 1.1;
    }

    .activity-divider {
        width: 1px;
        align-self: stretch;
        background: var(--bs-border-color);
    }


    .dashboard-lower-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(520px, 100%), 1fr));
        gap: 16px;
        align-items: start;
    }

    .dashboard-grid-panel {
        min-width: 0;
        margin-bottom: 0 !important;
    }

    .dashboard-grid-panel .kanban-list,
    .dashboard-grid-panel .todo-list {
        max-height: 360px !important;
    }

    .dashboard-stat-card {
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 18px;
        transition: all 0.2s ease;
    }

    .dashboard-panel {
        padding: 18px;
    }

    .dashboard-stat-card:hover,
    .kanban-item:hover,
    .todo-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.22);
    }

    .dashboard-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--bs-border-color);
        margin-bottom: 12px;
    }

    .dashboard-panel-header h4 {
        font-size: 16px;
        font-weight: 800;
        color: var(--bs-body-color);
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .stat-icon {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
    }

    .stat-icon.primary {
        background: rgba(75, 108, 183, 0.18);
        color: #5f8cff;
    }

    .stat-icon.success {
        background: rgba(25, 135, 84, 0.18);
        color: #2fd18a;
    }

    .stat-number {
        font-size: 34px;
        line-height: 1;
        font-weight: 800;
    }

    .stat-label {
        color: var(--bs-secondary-color);
        font-size: 14px;
        margin-bottom: 6px;
    }

    .kanban-list,
    .todo-list {
        padding-right: 4px;
    }

    .kanban-item,
    .todo-item {
        background: var(--bs-tertiary-bg);
        color: var(--bs-body-color);
        border: 1px solid var(--bs-border-color);
        border-radius: 16px;
        transition: all 0.2s ease;
    }

    .kanban-item {
        padding: 16px;
        margin-bottom: 12px;
    }

    .todo-item {
        padding: 14px;
        margin-bottom: 10px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: 10px 12px;
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
        color: var(--bs-secondary-color);
        font-size: 12px;
    }

    .kanban-title,
    .todo-title a {
        color: var(--bs-body-color);
        font-weight: 800;
        text-decoration: none;
    }

    .kanban-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .kanban-description {
        color: var(--bs-secondary-color);
        font-size: 13px;
        line-height: 1.8;
    }

    .todo-title {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .todo-title i {
        color: #5f8cff;
    }

    .todo-progress {
        grid-column: 1 / -1;
    }

    .todo-progress .progress {
        height: 6px;
        border-radius: 999px;
        background: var(--bs-border-color);
    }

    .todo-progress .progress-bar {
        border-radius: 999px;
    }

    .dashboard-requests-panel {
        overflow: hidden;
    }

    .dashboard-requests-panel .table-responsive {
        max-height: 360px !important;
        overflow-x: auto !important;
    }

    .dashboard-table {
        border-collapse: separate;
        border-spacing: 0 8px;
        color: var(--bs-body-color);
    }

    .dashboard-table thead th {
        background: transparent;
        border: 0;
        color: var(--bs-secondary-color);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        padding: 8px 12px;
    }

    .dashboard-table tbody tr {
        background: var(--bs-tertiary-bg);
        box-shadow: inset 0 0 0 1px var(--bs-border-color);
    }

    .dashboard-table tbody tr:hover {
        background: rgba(75, 108, 183, 0.12);
    }

    .dashboard-table tbody td {
        border: 0;
        padding: 14px 12px;
        color: var(--bs-body-color);
    }

    .dashboard-table tbody td:first-child {
        border-radius: 12px 0 0 12px;
    }

    .dashboard-table tbody td:last-child {
        border-radius: 0 12px 12px 0;
    }

    .ticket-number,
    .kanban-tag,
    .type-badge {
        background: rgba(75, 108, 183, 0.16);
        color: #5f8cff;
        border: 1px solid rgba(95, 140, 255, 0.25);
    }

    .status-badge,
    .confirm-badge,
    .type-badge,
    .priority-badge,
    .kanban-tag {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    .ticket-number {
        font-weight: 800;
        padding: 5px 10px;
        border-radius: 999px;
        display: inline-block;
        text-decoration: none;
    }

    .confirm-badge {
        background: var(--bs-tertiary-bg);
        color: var(--bs-body-color);
        border: 1px solid var(--bs-border-color);
    }

    .priority-low {
        background: rgba(13, 110, 253, 0.18);
        color: #6ea8fe;
    }

    .priority-medium {
        background: rgba(255, 193, 7, 0.18);
        color: #ffc107;
    }

    .priority-high {
        background: rgba(220, 53, 69, 0.18);
        color: #ff6b7a;
    }

    .dashboard-input,
    .dashboard-page .form-control {
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
        border: 1px solid var(--bs-border-color);
        border-radius: 999px;
        min-height: 36px;
    }

    .dashboard-page .form-control::placeholder {
        color: var(--bs-secondary-color);
    }

    @media (max-width: 1199.98px) {
        .dashboard-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .dashboard-summary-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-summary-card {
            min-height: auto;
        }
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

        .kanban-date {
            margin-left: 0;
        }

        .todo-item {
            grid-template-columns: 1fr;
        }
    }
</style>