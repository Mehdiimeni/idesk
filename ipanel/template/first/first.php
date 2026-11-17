<?php
///template/first/first.php
?>

<style>
    .dashboard-page {
        --db-border: #e5eaf2;
        --db-text: #172033;
        --db-muted: #7a8699;
        --db-soft: #f6f8fc;
        --db-primary: #356df3;
        --db-success: #20a66a;
        background: #f8fbff;
        margin-top: 0;
        min-height: calc(100vh - 130px);
    }

    .dashboard-page .container-fluid {
        width: 100%;
        max-width: none;
        margin: 0 auto;
        padding-left: 18px;
        padding-right: 18px;
    }

    .dashboard-hero {
        border: 1px solid #e2e8f5;
        border-radius: 20px;
        padding: 22px 24px;
        background:
            radial-gradient(circle at top left, rgba(53,109,243,.11), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #f6f9ff 100%);
        box-shadow: 0 12px 35px rgba(37, 54, 89, .06);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
    }

    .dashboard-hero-main {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .dashboard-hero-icon {
        width: 48px;
        height: 48px;
        border-radius: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #edf3ff;
        color: var(--db-primary);
        font-size: 23px;
        flex: 0 0 auto;
    }

    .dashboard-title {
        margin: 0;
        color: var(--db-text);
        font-weight: 800;
        font-size: 1.3rem;
    }

    .dashboard-subtitle {
        margin-top: 6px;
        color: var(--db-muted);
        font-size: 12px;
    }

    .dashboard-user,
    .dashboard-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .dashboard-user {
        padding: 8px 10px 8px 12px;
        border: 1px solid var(--db-border);
        border-radius: 15px;
        background: rgba(255,255,255,.76);
    }

    .dashboard-user-info {
        text-align: right;
        min-width: 150px;
    }

    .dashboard-user-info strong {
        display: block;
        color: var(--db-text);
        font-size: 13px;
        font-weight: 800;
    }

    .dashboard-user-info span {
        display: block;
        margin-top: 3px;
        color: var(--db-muted);
        font-size: 10px;
    }

    .dashboard-actions .btn {
        border-radius: 10px;
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        box-shadow: none;
    }

    .dashboard-actions .btn-light {
        color: #40506a;
        border: 1px solid #dce3ee;
        background: #fff;
    }

    .dashboard-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .dashboard-summary-card {
        min-height: 158px;
        background: #fff;
        color: var(--db-text);
        border: 1px solid var(--db-border);
        border-radius: 16px;
        padding: 16px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 7px 20px rgba(28, 41, 67, .04);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .dashboard-summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(53, 109, 243, .08);
        border-color: #cbd8fb;
    }

    .dashboard-summary-card::after {
        content: '';
        position: absolute;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        inset: auto -36px -44px auto;
        background: rgba(53, 109, 243, .035);
        pointer-events: none;
    }

    .dashboard-summary-card-primary,
    .dashboard-summary-card-success,
    .dashboard-period-week,
    .dashboard-period-month {
        border-top-width: 1px;
    }

    .summary-card-top,
    .activity-card-header,
    .activity-values {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-icon,
    .activity-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .stat-icon.primary {
        background: #eef3ff;
        color: var(--db-primary);
    }

    .stat-icon.success {
        background: #edf9f3;
        color: var(--db-success);
    }

    .activity-icon.weekly {
        color: #7c3aed;
        background: #f3efff;
    }

    .activity-icon.monthly {
        color: #d97706;
        background: #fff7e8;
    }

    .summary-open-icon {
        width: 30px;
        height: 30px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #8b98ad;
        background: var(--db-soft);
        border: 1px solid var(--db-border);
    }

    .summary-card-body {
        margin-top: 14px;
    }

    .stat-label,
    .activity-eyebrow {
        color: var(--db-muted);
        font-size: 11px;
        font-weight: 700;
    }

    .stat-number {
        margin-top: 5px;
        color: var(--db-text);
        font-size: 28px;
        line-height: 1;
        font-weight: 800;
    }

    .summary-caption {
        margin-top: 7px;
        color: var(--db-muted);
        font-size: 10px;
    }

    .summary-mini-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
    }

    .summary-mini-stats span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 7px;
        border-radius: 999px;
        background: var(--db-soft);
        border: 1px solid var(--db-border);
        color: var(--db-muted);
        font-size: 9px;
    }

    .summary-mini-stats strong {
        color: var(--db-text);
        font-size: 10px;
    }

    .dashboard-activity-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .activity-card-header strong {
        display: block;
        margin-top: 3px;
        font-size: 13px;
        color: var(--db-text);
    }

    .activity-values {
        margin-top: 18px;
        background: var(--db-soft);
        border: 1px solid var(--db-border);
        border-radius: 12px;
        padding: 10px 12px;
        gap: 10px;
    }

    .activity-value {
        flex: 1;
        min-width: 0;
    }

    .activity-value span {
        display: block;
        color: var(--db-muted);
        font-size: 9px;
        margin-bottom: 3px;
    }

    .activity-value strong {
        display: block;
        color: var(--db-text);
        font-size: 19px;
        line-height: 1.1;
    }

    .activity-divider {
        width: 1px;
        align-self: stretch;
        background: var(--db-border);
    }

    .dashboard-lower-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        align-items: start;
    }

    .dashboard-panel,
    .dashboard-stat-card {
        background: #fff;
        color: var(--db-text);
        border: 1px solid var(--db-border);
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(29, 42, 69, .04);
    }

    .dashboard-grid-panel {
        min-width: 0;
        margin-bottom: 0 !important;
        overflow: hidden;
    }

    .dashboard-panel {
        padding: 0;
    }

    .dashboard-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding: 16px 18px;
        border-bottom: 1px solid var(--db-border);
        margin-bottom: 0;
        background: linear-gradient(180deg, #fff 0%, #fbfdff 100%);
    }

    .dashboard-panel-header h4 {
        font-size: 14px;
        font-weight: 800;
        color: var(--db-text);
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }

    .dashboard-grid-panel .kanban-list,
    .dashboard-grid-panel .todo-list {
        max-height: 360px !important;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-gutter: stable;
        padding: 14px;
    }

    .kanban-item,
    .todo-item {
        background: #fbfcfe;
        color: var(--db-text);
        border: 1px solid #e8edf3;
        border-radius: 14px;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .kanban-item:hover,
    .todo-item:hover {
        transform: translateY(-1px);
        border-color: #cbd8fb;
        box-shadow: 0 8px 20px rgba(53, 109, 243, .06);
    }

    .kanban-item {
        padding: 14px;
        margin-bottom: 10px;
    }

    .todo-item {
        padding: 12px;
        margin-bottom: 9px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: 9px 12px;
    }

    .kanban-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        margin-bottom: 8px;
    }

    .kanban-date {
        margin-left: auto;
        color: var(--db-muted);
        font-size: 10px;
    }

    .kanban-title,
    .todo-title a {
        color: var(--db-text);
        font-weight: 800;
        text-decoration: none;
    }

    .kanban-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
        font-size: 12px;
    }

    .kanban-description {
        color: var(--db-muted);
        font-size: 11px;
        line-height: 1.8;
    }

    .todo-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
    }

    .todo-title i {
        color: var(--db-primary);
    }

    .todo-progress {
        grid-column: 1 / -1;
    }

    .todo-progress .progress {
        height: 5px;
        border-radius: 999px;
        background: #e7edf6;
    }

    .todo-progress .progress-bar {
        border-radius: 999px;
    }

    .dashboard-native-scroll {
        overflow-y: auto !important;
        overflow-x: hidden !important;
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
    }

    .dashboard-native-scroll::-webkit-scrollbar { width: 6px; }
    .dashboard-native-scroll::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, .24); border-radius: 999px; }
    .dashboard-native-scroll::-webkit-scrollbar-track { background: transparent; }

    .dashboard-requests-panel {
        overflow: hidden;
        padding: 0;
    }

    .dashboard-request-count {
        min-width: 30px;
        height: 30px;
        padding: 0 9px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef3ff;
        color: var(--db-primary);
        border: 1px solid #d8e5ff;
        font-size: 11px;
        font-weight: 800;
    }

    .dashboard-requests-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        max-height: 420px;
        padding: 14px;
    }

    .dashboard-request-card {
        min-width: 0;
        margin: 0;
        padding: 12px;
        border-radius: 14px;
        background: #fbfcfe;
        border: 1px solid #e8edf3;
        box-shadow: none;
        transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease;
    }

    .dashboard-request-card:hover {
        transform: translateY(-1px);
        border-color: #cbd8fb;
        box-shadow: 0 8px 20px rgba(53, 109, 243, .06);
    }

    .dashboard-request-card-confirm { border-inline-start: 3px solid #72d5a4; }

    .dashboard-request-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .dashboard-request-type {
        min-width: 0;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: var(--db-muted);
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .dashboard-request-type i { font-size: 14px; color: var(--db-primary); }

    .dashboard-request-card-body {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
        gap: 10px;
    }

    .dashboard-request-field { min-width: 0; }

    .dashboard-request-field-label {
        display: block;
        margin-bottom: 5px;
        color: var(--db-muted);
        font-size: 9px;
        font-weight: 700;
    }

    .dashboard-request-card .dashboard-input {
        width: 100% !important;
        max-width: 220px;
        min-width: 0;
        direction: ltr;
        text-align: center;
    }

    .dashboard-request-response {
        min-height: 34px;
        max-width: 100%;
        padding: 7px 10px;
        display: inline-flex;
        align-items: center;
        border-radius: 10px;
        background: #fff;
        border: 1px solid var(--db-border);
        color: var(--db-text);
        font-size: 11px;
        font-weight: 700;
        word-break: break-word;
    }

    .dashboard-request-action {
        min-width: 88px;
        height: 34px;
        border-radius: 10px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        box-shadow: none !important;
        font-size: 10px;
    }

    .dashboard-request-action:disabled { cursor: wait; opacity: .7; }

    .ticket-number,
    .kanban-tag,
    .type-badge {
        background: #eef3ff;
        color: var(--db-primary);
        border: 1px solid #d8e5ff;
    }

    .status-badge,
    .confirm-badge,
    .type-badge,
    .priority-badge,
    .kanban-tag {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 8px;
        font-size: 9px;
        font-weight: 700;
    }

    .ticket-number {
        font-weight: 800;
        padding: 4px 8px;
        border-radius: 999px;
        display: inline-block;
        text-decoration: none;
        font-size: 10px;
    }

    .confirm-badge {
        background: var(--db-soft);
        color: var(--db-text);
        border: 1px solid var(--db-border);
    }

    .priority-low { background: #eef3ff; color: #356df3; }
    .priority-medium { background: #fff7e8; color: #d97706; }
    .priority-high { background: #fff0f2; color: #d43f55; }

    .dashboard-input,
    .dashboard-page .form-control {
        background: #fff;
        color: var(--db-text);
        border: 1px solid #dbe2ed;
        border-radius: 10px;
        min-height: 34px;
        font-size: 11px;
    }

    .dashboard-page .form-control::placeholder { color: #9aa5b7; }

    @media (max-width: 1199.98px) {
        .dashboard-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .dashboard-requests-grid { grid-template-columns: 1fr; }
        .dashboard-lower-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 575.98px) {
        .dashboard-summary-grid { grid-template-columns: 1fr; }
        .dashboard-summary-card { min-height: auto; }
        .dashboard-page .container-fluid { padding-left: 12px; padding-right: 12px; }
    }

    @media (max-width: 768px) {
        .dashboard-hero,
        .dashboard-user {
            flex-direction: column;
            align-items: flex-start;
        }

        .dashboard-user { width: 100%; }
        .dashboard-user-info { text-align: right; }
        .kanban-date { margin-left: 0; }
        .todo-item { grid-template-columns: 1fr; }
        .dashboard-request-card-body { grid-template-columns: 1fr; }
        .dashboard-request-action,
        .dashboard-request-card .dashboard-input { width: 100% !important; max-width: none; }
    }
</style>
<div class="content-page dashboard-page">
    <div class="content">
        <div class="container-fluid">

            <div class="dashboard-hero mt-3 mb-3">
                <div class="dashboard-hero-main">
                    <span class="dashboard-hero-icon"><i class="ri-dashboard-line"></i></span>
                    <div>
                        <h3 class="dashboard-title"><?php echo _lang['my_dashboard']; ?></h3>
                        <div class="dashboard-subtitle"><?php echo _lang['project_name_owner']; ?></div>
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

            <div class="dashboard-summary-grid mb-3">
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

            <div class="dashboard-lower-grid mb-3">
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

                    <div class="kanban-list dashboard-native-scroll" style="max-height: 340px;">
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

                    <div class="todo-list dashboard-native-scroll" style="max-height: 300px;">
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

                    <div class="todo-list dashboard-native-scroll" style="max-height: 300px;">
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

            <?php
            $dashboardRequestCount = 0;
            if ($personHourDeliveryTimePermissionOperation) {
                $dashboardRequestCount += (int) $allRequests->num_rows;
            }
            if ($personHourDeliveryTimePermissionRequest) {
                $dashboardRequestCount += (int) $allResponse->num_rows;
            }
            ?>

            <?php if (($personHourDeliveryTimePermissionOperation || $personHourDeliveryTimePermissionRequest) && $dashboardRequestCount > 0): ?>
                <div class="dashboard-panel dashboard-requests-panel mb-3">
                    <div class="dashboard-panel-header dashboard-requests-header">
                        <div>
                            <h4>
                                <i class="mdi mdi-email-outline"></i>
                                <?php echo _lang['requests']; ?>
                            </h4>
                        </div>
                        <span class="dashboard-request-count"><?php echo number_format($dashboardRequestCount); ?></span>
                    </div>

                    <div class="dashboard-requests-grid dashboard-native-scroll">
                        <?php if ($personHourDeliveryTimePermissionOperation): ?>
                            <?php while ($requests = $allRequests->fetch_assoc()): ?>
                                <form class="dashboard-request-card updateForm" data-id="<?php echo (int) $requests['id']; ?>">
                                    <input type="hidden" name="id" value="<?php echo (int) $requests['id']; ?>">

                                    <div class="dashboard-request-card-head">
                                        <a href="./tickets?ticket_id=<?php echo $encryptorClassClass->encrypt($requests['section_element_id']); ?>"
                                           class="ticket-number">
                                            <?php echo htmlspecialchars((string) $requests['ticket_number'], ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                        <span class="dashboard-request-type">
                                            <i class="ri-question-answer-line"></i>
                                            <?php echo _lang[$requests['request']]; ?>
                                        </span>
                                    </div>

                                    <div class="dashboard-request-card-body">
                                        <div class="dashboard-request-field">
                                            <span class="dashboard-request-field-label"><?php echo _lang['response']; ?></span>

                                            <?php if ($requests['request'] == 'person_hour'): ?>
                                                <input type="text"
                                                       class="form-control form-control-sm dashboard-input text-center"
                                                       name="person_hour_response"
                                                       inputmode="numeric"
                                                       maxlength="2"
                                                       data-toggle="input-mask"
                                                       data-mask-format="00"
                                                       placeholder="HH">
                                            <?php elseif ($requests['request'] == 'delivery_time'): ?>
                                                <input type="text"
                                                       class="form-control form-control-sm dashboard-input text-center"
                                                       name="delivery_time_response"
                                                       inputmode="numeric"
                                                       data-toggle="input-mask"
                                                       data-mask-format="00/00/0000"
                                                       placeholder="DD/MM/YYYY">
                                            <?php endif; ?>
                                        </div>

                                        <button type="button" class="btn btn-primary btn-sm dashboard-request-action send-button">
                                            <i class="mdi mdi-send me-1"></i>
                                            <span><?php echo _lang['send']; ?></span>
                                        </button>
                                    </div>
                                </form>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <?php if ($personHourDeliveryTimePermissionRequest): ?>
                            <?php while ($response = $allResponse->fetch_assoc()): ?>
                                <form class="dashboard-request-card dashboard-request-card-confirm confirmationForm"
                                      data-id="<?php echo (int) $response['id']; ?>">
                                    <input type="hidden" name="id" value="<?php echo (int) $response['id']; ?>">

                                    <div class="dashboard-request-card-head">
                                        <a href="./tickets?ticket_id=<?php echo $encryptorClassClass->encrypt($response['section_element_id']); ?>"
                                           class="ticket-number">
                                            <?php echo htmlspecialchars((string) $response['ticket_number'], ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                        <span class="dashboard-request-type">
                                            <i class="ri-checkbox-circle-line"></i>
                                            <?php echo _lang[$response['request']]; ?>
                                        </span>
                                    </div>

                                    <div class="dashboard-request-card-body">
                                        <div class="dashboard-request-field">
                                            <span class="dashboard-request-field-label"><?php echo _lang['response']; ?></span>
                                            <span class="dashboard-request-response">
                                                <?php echo htmlspecialchars((string) $response['response'], ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </div>

                                        <button type="button" class="btn btn-success btn-sm dashboard-request-action confirmation-button">
                                            <i class="mdi mdi-check-circle-outline me-1"></i>
                                            <span><?php echo _lang['confirmation']; ?></span>
                                        </button>
                                    </div>
                                </form>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
