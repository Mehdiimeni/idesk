<?php
///template/ticket/workflow.php

$workflowEsc = static function ($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
};

$workflowPriorityClass = static function ($priority) {
    switch (strtolower((string) $priority)) {
        case 'high':
            return 'danger';
        case 'medium':
            return 'warning';
        case 'low':
            return 'primary';
        default:
            return 'secondary';
    }
};

$workflowStatusDescriptionLang = _lang;
$workflowStatusDescriptionFormatter = static function ($description) use ($workflowStatusDescriptionLang) {
    $description = trim((string) ($description ?? ''));

    if ($description === '') {
        return '';
    }

    if (preg_match(
        '/^Auto status change:\s*([a-z0-9_]+)\s*->\s*([a-z0-9_]+)(?:\s+after\s+(\d+)\s+days?)?$/i',
        $description,
        $matches
    )) {
        $fromKey = strtolower($matches[1]);
        $toKey = strtolower($matches[2]);

        $fromLabel = $workflowStatusDescriptionLang[$fromKey];
        $toLabel = $workflowStatusDescriptionLang[$toKey];

        $formatted = $workflowStatusDescriptionLang['auto_status_change']
            . ': ' . $fromLabel . ' → ' . $toLabel;

        if (!empty($matches[3])) {
            $daysTemplate = $workflowStatusDescriptionLang['auto_status_after_days'];
            $formatted .= ' - ' . sprintf($daysTemplate, (int) $matches[3]);
        }

        return $formatted;
    }

    return $description;
};
?>

<style>
    .workflow-page-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 8px 28px rgba(15, 23, 42, .07);
        overflow: hidden;
    }

    .workflow-search-panel {
        background: linear-gradient(135deg, #172554 0%, #1e3a8a 48%, #2563eb 100%);
        border-radius: 16px;
        padding: 20px;
        color: #fff;
        margin-bottom: 20px;
    }

    .workflow-search-panel .form-control {
        min-height: 46px;
        border: 0;
        border-radius: 12px 0 0 12px;
        box-shadow: none;
    }

    [dir="rtl"] .workflow-search-panel .form-control {
        border-radius: 0 12px 12px 0;
    }

    .workflow-search-panel .btn {
        min-width: 110px;
        border: 0;
        border-radius: 0 12px 12px 0;
    }

    [dir="rtl"] .workflow-search-panel .btn {
        border-radius: 12px 0 0 12px;
    }

    .workflow-ticket-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #e8edf5;
        border-radius: 18px;
        background: #fff;
        padding: 22px;
        margin-bottom: 24px;
    }

    .workflow-ticket-hero::before {
        content: "";
        position: absolute;
        inset-inline-start: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(180deg, #2563eb, #7c3aed);
    }

    .workflow-ticket-number {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 11px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 800;
        font-size: .82rem;
        margin-bottom: 10px;
    }

    .workflow-ticket-title {
        color: #172033;
        font-weight: 800;
        line-height: 1.7;
        margin: 0;
        font-size: 1.15rem;
    }

    .workflow-meta-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-top: 18px;
    }

    .workflow-meta-item {
        background: #f8fafc;
        border: 1px solid #eef2f7;
        border-radius: 13px;
        padding: 11px 13px;
        min-width: 0;
    }

    .workflow-meta-label {
        display: block;
        color: #8a94a6;
        font-size: .72rem;
        margin-bottom: 5px;
    }

    .workflow-meta-value {
        color: #25324b;
        font-size: .86rem;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .workflow-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 8px 0 20px;
    }

    .workflow-section-title h5 {
        margin: 0;
        font-weight: 800;
        color: #25324b;
    }

    .workflow-total-time {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 11px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: .78rem;
        font-weight: 700;
    }

    .workflow-modern-timeline .timeline-box {
        border: 1px solid #edf1f6;
        border-radius: 15px;
        box-shadow: 0 5px 18px rgba(15, 23, 42, .06);
        padding: 16px 17px;
        background: #fff;
    }

    .workflow-modern-timeline .timeline-box:hover {
        border-color: #dbe6f7;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .09);
    }

    .workflow-event-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 10px;
    }

    .workflow-event-person {
        color: #23304a;
        font-weight: 800;
        font-size: .91rem;
        line-height: 1.6;
    }

    .workflow-event-date {
        color: #8a94a6;
        font-size: .72rem;
        white-space: nowrap;
    }

    .workflow-event-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 9px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: .69rem;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .workflow-forward-comment,
    .workflow-status-comment {
        border-radius: 12px;
        padding: 10px 12px;
        margin-top: 10px;
        font-size: .82rem;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    .workflow-forward-comment {
        background: #eff6ff;
        border: 1px solid #dbeafe;
        color: #1e3a5f;
    }

    .workflow-status-comment {
        background: #f8fafc;
        border: 1px solid #e9eef5;
        color: #475569;
    }

    .workflow-event-footer {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        align-items: center;
        margin-top: 12px;
    }

    .workflow-event-footer .btn,
    .workflow-event-footer .alert {
        margin: 0;
    }

    .workflow-step-time {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 9px;
        background: #f8fafc;
        border: 1px solid #edf1f6;
        color: #64748b;
        border-radius: 999px;
        font-size: .68rem;
    }

    .workflow-empty {
        border: 1px dashed #d7deea;
        border-radius: 16px;
        background: #fbfcfe;
        padding: 35px 20px;
        text-align: center;
        color: #7b879a;
    }

    @media (max-width: 991.98px) {
        .workflow-meta-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .workflow-search-panel,
        .workflow-ticket-hero {
            padding: 15px;
        }

        .workflow-meta-grid {
            grid-template-columns: 1fr;
        }

        .workflow-event-head {
            flex-direction: column;
        }

        .workflow-event-date {
            white-space: normal;
        }
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card workflow-page-card">
            <div class="card-body p-3 p-lg-4">

                <?php if ($rbacClass->checkPermissionOperationByName('search_operation')) { ?>
                    <div class="workflow-search-panel">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="mdi mdi-timeline-clock-outline font-22"></i>
                            <h4 class="m-0 text-white fw-bold"><?php echo _lang['workflow']; ?></h4>
                        </div>

                        <form action="./workflow" method="get" class="m-0">
                            <div class="input-group">
                                <input type="text"
                                    id="ticket_number"
                                    name="ticket_number"
                                    class="form-control"
                                    value="<?php echo $workflowEsc($workflowTicketNumber); ?>"
                                    placeholder="<?php echo _lang['ticket_number']; ?>"
                                    aria-label="<?php echo _lang['ticket_number']; ?>"
                                    autocomplete="off">
                                <button class="btn btn-dark" type="submit">
                                    <i class="mdi mdi-magnify me-1"></i><?php echo _lang['search']; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php } ?>

                <?php if ($workflowTicketNumber !== '') { ?>

                    <?php if (!empty($ticket_id) && !empty($ticketHeader)) { ?>
                        <?php
                        $headerPriority = strtolower((string) ($ticketHeader['priority'] ?? ''));
                        $headerPriorityClass = $workflowPriorityClass($headerPriority);
                        $headerCondition = !empty($ticketHeader['ticket_status'])
                            ? $structureModel->getConditionsByName($ticketHeader['ticket_status'])
                            : null;
                        ?>

                        <div class="workflow-ticket-hero" dir="rtl">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                <div class="flex-grow-1">
                                    <div class="workflow-ticket-number">
                                        <i class="mdi mdi-ticket-confirmation-outline"></i>
                                        <?php echo _lang['ticket_number']; ?>:
                                        <?php echo $workflowEsc($ticketHeader['ticket_number']); ?>
                                    </div>

                                    <h3 class="workflow-ticket-title">
                                        <?php echo $workflowEsc($ticketHeader['ticket_title']); ?>
                                    </h3>
                                </div>

                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <?php if ($headerPriority !== '') { ?>
                                        <span class="badge bg-<?php echo $workflowEsc($headerPriorityClass); ?> px-3 py-2">
                                            <?php echo _lang['priority']; ?>:
                                            <?php echo isset(_lang[$headerPriority]) ? _lang[$headerPriority] : $workflowEsc($headerPriority); ?>
                                        </span>
                                    <?php } ?>

                                    <?php if (!empty($headerCondition) && isset($headerCondition['condition_name'])) { ?>
                                        <span class="badge alert-<?php echo $workflowEsc($headerCondition['condition_color']); ?> px-3 py-2">
                                            <?php echo isset(_lang[$headerCondition['condition_name']])
                                                ? _lang[$headerCondition['condition_name']]
                                                : $workflowEsc($headerCondition['condition_name']); ?>
                                        </span>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="workflow-meta-grid">
                                <div class="workflow-meta-item">
                                    <span class="workflow-meta-label"><?php echo _lang['company']; ?></span>
                                    <span class="workflow-meta-value">
                                        <i class="mdi mdi-office-building-outline me-1"></i>
                                        <?php echo $workflowEsc($ticketHeader['company_name']); ?>
                                    </span>
                                </div>

                                <div class="workflow-meta-item">
                                    <span class="workflow-meta-label"><?php echo _lang['user']; ?></span>
                                    <span class="workflow-meta-value">
                                        <i class="mdi mdi-account-outline me-1"></i>
                                        <?php echo $workflowEsc($ticketHeader['sender_name']); ?>
                                    </span>
                                </div>

                                <div class="workflow-meta-item">
                                    <span class="workflow-meta-label"><?php echo _lang['type']; ?></span>
                                    <span class="workflow-meta-value">
                                        <i class="mdi mdi-shape-outline me-1"></i>
                                        <?php echo $workflowEsc($ticketHeader['type_name']); ?>
                                        <?php if (!empty($ticketHeader['type_group'])) { ?>
                                            <span class="text-muted">/ <?php echo $workflowEsc($ticketHeader['type_group']); ?></span>
                                        <?php } ?>
                                    </span>
                                </div>

                                <div class="workflow-meta-item">
                                    <span class="workflow-meta-label"><?php echo _lang['added_date']; ?></span>
                                    <span class="workflow-meta-value">
                                        <i class="mdi mdi-timeline-clock-outline me-1"></i>
                                        <?php
                                        if (!empty($ticketHeader['creation_date'])) {
                                            $headerDateConverter = new DateConverter(
                                                $ticketHeader['creation_date'],
                                                $config->getNowLanguage('a')
                                            );
                                            echo $workflowEsc($headerDateConverter->convertToShamsi());
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="workflow-section-title" dir="rtl">
                            <h5>
                                <i class="mdi mdi-timeline-text-outline text-primary me-1"></i>
                                <?php echo _lang['workflow']; ?>
                            </h5>

                            <span class="workflow-total-time">
                                <i class="mdi mdi-clock-outline"></i>
                                <?php echo _lang['total']; ?>:
                                <?php echo round((float) ($timeDifference['hours'] ?? 0), 1); ?>
                                <?php echo _lang['hour']; ?>
                            </span>
                        </div>

                        <div class="timeline workflow-modern-timeline" dir="ltr">
                            <?php
                            $forwardCount = 0;
                            $setDiff = 0;
                            $arrayTime = [];

                            foreach ($resultTicket as $ticket_data) {
                                $arrayTime[$forwardCount] = $ticket_data['creation_date'];

                                if ($forwardCount > 0) {
                                    $setDiff = 1;
                                    $stepDiffTime = $ticketModel->getTimeDifferenceArray(
                                        $arrayTime[$forwardCount - 1],
                                        $arrayTime[$forwardCount]
                                    );
                                }

                                $forwardCount++;

                                $eventDateConverter = new DateConverter(
                                    $ticket_data['creation_date'],
                                    $config->getNowLanguage('a')
                                );

                                $sourceType = strtolower((string) ($ticket_data['source_type'] ?? ''));
                                ?>

                                <div class="timeline-lg-item timeline-item-<?php echo $workflowEsc($ticket_data['position']); ?>">
                                    <div class="timeline-desk">
                                        <div class="timeline-box" dir="rtl">
                                            <span class="arrow-alt"></span>
                                            <span class="timeline-icon"><i class="mdi mdi-adjust"></i></span>

                                            <div class="workflow-event-head">
                                                <div>
                                                    <div class="workflow-event-badge">
                                                        <?php if ($sourceType === 'forward') { ?>
                                                            <i class="mdi mdi-forward-outline"></i><?php echo _lang['forward']; ?>
                                                        <?php } elseif ($sourceType === 'priority') { ?>
                                                            <i class="mdi mdi-sort-variant"></i><?php echo _lang['priority']; ?>
                                                        <?php } else { ?>
                                                            <i class="mdi mdi-list-status"></i><?php echo _lang['status']; ?>
                                                        <?php } ?>
                                                    </div>

                                                    <div class="workflow-event-person">
                                                        <?php if ($sourceType === 'forward') { ?>
                                                            <?php echo $workflowEsc($ticket_data['person_name']); ?>
                                                            <i class="mdi mdi-arrow-left mx-1 text-primary"></i>
                                                            <?php echo $workflowEsc($ticket_data['receiver_name']); ?>
                                                        <?php } else { ?>
                                                            <?php echo $workflowEsc($ticket_data['person_name']); ?>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <div class="workflow-event-date">
                                                    <i class="mdi mdi-calendar-clock-outline me-1"></i>
                                                    <?php echo $workflowEsc($eventDateConverter->convertToShamsi()); ?>
                                                </div>
                                            </div>

                                            <?php if ($sourceType === 'forward' && !empty($ticket_data['forwards_description'])) { ?>
                                                <div class="workflow-forward-comment">
                                                    <div class="fw-bold mb-1">
                                                        <i class="mdi mdi-comment-text-outline me-1"></i>
                                                        <?php echo _lang['forward']; ?> / <?php echo _lang['description']; ?>
                                                    </div>
                                                    <?php echo nl2br($workflowEsc($ticket_data['forwards_description'])); ?>
                                                </div>
                                            <?php } ?>

                                            <?php if (!empty($ticket_data['status_description'])) { ?>
                                                <div class="workflow-status-comment">
                                                    <div class="fw-bold mb-1">
                                                        <i class="mdi mdi-text-box-outline me-1"></i>
                                                        <?php echo _lang['description']; ?>
                                                    </div>
                                                    <?php echo nl2br($workflowEsc($workflowStatusDescriptionFormatter($ticket_data['status_description']))); ?>
                                                </div>
                                            <?php } ?>

                                            <div class="workflow-event-footer">
                                                <?php if (!empty($ticket_data['status_name'])) {
                                                    $condition = $structureModel->getConditionsByName($ticket_data['status_name']);

                                                    if (!empty($condition)) { ?>
                                                        <span class="alert alert-<?php echo $workflowEsc($condition['condition_color']); ?> py-1 px-2 small">
                                                            <?php echo isset(_lang[$condition['condition_name']])
                                                                ? _lang[$condition['condition_name']]
                                                                : $workflowEsc($condition['condition_name']); ?>
                                                        </span>
                                                    <?php }
                                                } ?>

                                                <?php if ($sourceType === 'priority') {
                                                    $priority = strtolower((string) ($ticket_data['priority'] ?? ''));
                                                    $priorityClass = $workflowPriorityClass($priority);
                                                    ?>
                                                    <span class="badge bg-<?php echo $workflowEsc($priorityClass); ?> px-2 py-1">
                                                        <?php echo isset(_lang[$priority]) ? _lang[$priority] : $workflowEsc($priority); ?>
                                                    </span>
                                                <?php } ?>

                                                <?php if (!empty($ticket_data['company_name'])) { ?>
                                                    <span class="workflow-step-time">
                                                        <i class="mdi mdi-office-building-outline"></i>
                                                        <?php echo $workflowEsc($ticket_data['company_name']); ?>
                                                    </span>
                                                <?php } ?>

                                                <?php if (!empty($ticket_data['unit_name'])) { ?>
                                                    <span class="workflow-step-time">
                                                        <i class="mdi mdi-domain"></i>
                                                        <?php echo $workflowEsc($ticket_data['unit_name']); ?>
                                                    </span>
                                                <?php } ?>

                                                <?php if (!empty($ticket_data['view_to'])) { ?>
                                                    <span class="workflow-step-time">
                                                        <i class="mdi mdi-eye-outline"></i>
                                                    </span>
                                                <?php } ?>

                                                <?php if ($setDiff) { ?>
                                                    <span class="workflow-step-time">
                                                        <i class="mdi mdi-timer-sand"></i>
                                                        <?php echo _lang['day']; ?>: <?php echo (int) $stepDiffTime['days']; ?> /
                                                        <?php echo _lang['hour']; ?>: <?php echo (int) $stepDiffTime['hours']; ?> /
                                                        <?php echo _lang['minute']; ?>: <?php echo (int) $stepDiffTime['minutes']; ?>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                    <?php } else { ?>
                        <div class="workflow-empty">
                            <i class="mdi mdi-ticket-outline font-28 d-block mb-2"></i>
                            <?php echo isset(_lang['ticket_not_in_list']) ? _lang['ticket_not_in_list'] : _lang['ticket']; ?>
                        </div>
                    <?php } ?>

                <?php } ?>

            </div>
        </div>
    </div>
</div>
