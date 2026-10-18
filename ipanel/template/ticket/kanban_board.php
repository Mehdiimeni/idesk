<?php
///template/ticket/kanban_board.php
?>
<style>
    .kanban-page .page-title-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .kanban-page .page-title {
        margin-bottom: 0;
        font-size: 1.35rem;
        font-weight: 700;
        color: #334155;
    }

    .kanban-page .page-subtitle {
        margin: .35rem 0 0;
        color: #64748b;
        font-size: .92rem;
    }

    .kanban-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .05);
    }

    .kanban-toolbar .toolbar-stat {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .kanban-toolbar .toolbar-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #2563eb;
        font-size: 1.2rem;
    }

    .kanban-toolbar .toolbar-title {
        font-size: .85rem;
        color: #64748b;
        margin-bottom: .15rem;
    }

    .kanban-toolbar .toolbar-value {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
    }

    .kanban-board-shell {
        padding: 1rem;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
        overflow-x: auto;
        overflow-y: hidden;
    }

    .kanban-board-shell .board {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        min-height: 65vh;
        padding-bottom: .25rem;
    }

    .kanban-board-shell .tasks {
        flex: 0 0 320px;
        width: 320px;
        min-width: 320px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .kanban-column-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: 1rem 1rem .85rem;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .kanban-column-title {
        display: flex;
        align-items: center;
        gap: .65rem;
        min-width: 0;
        flex: 1;
    }

    .kanban-column-bullet {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        background: linear-gradient(135deg, #60a5fa, #2563eb);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
        flex: 0 0 auto;
    }

    .kanban-column-name {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .kanban-column-count {
        padding: .15rem .6rem;
        border-radius: 999px;
        background: #e0ecff;
        color: #1d4ed8;
        font-size: .78rem;
        font-weight: 700;
        flex: 0 0 auto;
    }

    .kanban-column-actions {
        display: flex;
        align-items: center;
        gap: .35rem;
        flex: 0 0 auto;
    }

    .kanban-column-actions .btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 10px;
    }

    .task-list-items {
        padding: 1rem;
        min-height: 220px;
        background: #f8fafc;
    }

    .kanban-task-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .kanban-task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, .08);
        border-color: #cbd5e1;
    }

    .kanban-task-card .card-body {
        padding: .95rem;
    }

    .kanban-task-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: .75rem;
        margin-bottom: .75rem;
    }

    .kanban-task-badges {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
    }

    .kanban-status-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        padding: .38rem .6rem;
        font-size: .78rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .kanban-status-chip.alert-primary,
    .kanban-status-chip.alert-info,
    .kanban-status-chip.alert-secondary,
    .kanban-status-chip.alert-light {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .kanban-status-chip.alert-success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .kanban-status-chip.alert-warning {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .kanban-status-chip.alert-danger {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .kanban-task-menu .dropdown-toggle {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .kanban-task-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        flex-wrap: wrap;
        margin-bottom: .75rem;
        font-size: .78rem;
        color: #64748b;
    }

    .kanban-task-meta-left,
    .kanban-task-meta-right {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .4rem;
    }

    .kanban-task-date {
        display: inline-flex;
        align-items: center;
        gap: .28rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: .2rem .55rem;
    }

    .kanban-task-title {
        margin: 0 0 .6rem;
        font-size: .94rem;
        line-height: 1.7;
        font-weight: 700;
        color: #1e293b;
    }

    .kanban-task-title a {
        color: inherit;
        text-decoration: none;
    }

    .kanban-task-title a:hover {
        color: #2563eb;
    }

    .kanban-task-description {
        margin: 0;
        color: #475569;
        font-size: .84rem;
        line-height: 1.8;
        white-space: pre-line;
        word-break: break-word;
    }

    .kanban-empty-column,
    .kanban-empty-board {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: .55rem;
        text-align: center;
        color: #94a3b8;
        border: 2px dashed #dbe2ea;
        border-radius: 16px;
        background: rgba(255,255,255,.7);
    }

    .kanban-empty-column {
        min-height: 180px;
        padding: 1.5rem 1rem;
    }

    .kanban-empty-board {
        min-height: 320px;
        padding: 2rem;
        font-size: .95rem;
    }

    .kanban-empty-column i,
    .kanban-empty-board i {
        font-size: 1.6rem;
    }

    @media (max-width: 767.98px) {
        .kanban-board-shell {
            padding: .75rem;
        }

        .kanban-board-shell .tasks {
            flex-basis: 290px;
            width: 290px;
            min-width: 290px;
        }

        .kanban-toolbar {
            padding: .9rem 1rem;
        }
    }
</style>
<div class="content-page kanban-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div>
                            <h4 class="page-title"><?php echo (_lang['kanban_board']); ?></h4>
                            <p class="page-subtitle">نمای دیداری و مرتب برای مدیریت سریع‌تر تیکت‌ها و جابه‌جایی بین ستون‌ها</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $kanbanTags = [];
            if ($allKanbanTag) {
                while ($kanbanTagRow = $allKanbanTag->fetch_assoc()) {
                    $kanbanTags[] = $kanbanTagRow;
                }
            }
            $kanbanTagCount = count($kanbanTags);
            ?>

            <div class="kanban-toolbar">
                <div class="toolbar-stat">
                    <span class="toolbar-icon"><i class="mdi mdi-view-column-outline"></i></span>
                    <div>
                        <div class="toolbar-title">تعداد ستون‌های برد</div>
                        <div class="toolbar-value"><?php echo number_format($kanbanTagCount); ?></div>
                    </div>
                </div>

                <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addModalTag">
                    <i class="mdi mdi-plus-circle me-1"></i>
                    <?php echo (_lang['add']); ?>
                </a>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="kanban-board-shell">
                        <div class="board">
                            <?php
                            if (empty($kanbanTags)) {
                                ?>
                                <div class="kanban-empty-board w-100">
                                    <i class="mdi mdi-view-column-outline"></i>
                                    <div>هنوز ستونی برای Kanban ثبت نشده است.</div>
                                    <small>از دکمه «<?php echo (_lang['add']); ?>» برای ایجاد اولین ستون استفاده کنید.</small>
                                </div>
                                <?php
                            }

                            $countTag = 0;
                            foreach ($kanbanTags as $kanban_tag) {
                                $countTag++;

                                $strPlugin = '';
                                if ($countTag == 1) {
                                    $strPlugin = "data-plugin='dragula' data-containers='" . $allListTaskId . "'";
                                }

                                $kanbanItems = [];
                                $allKabanByTagId = $ticketModel->getAllKabanByTagId($kanban_tag['id']);
                                while ($kabanRow = $allKabanByTagId->fetch_assoc()) {
                                    $kanbanItems[] = $kabanRow;
                                }
                                ?>
                                <div class="tasks" <?php echo $strPlugin; ?>>
                                    <div class="kanban-column-header">
                                        <div class="kanban-column-title">
                                            <span class="kanban-column-bullet"></span>
                                            <span class="kanban-column-name"><?php echo htmlspecialchars($kanban_tag['board_tag'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="kanban-column-count"><?php echo number_format(count($kanbanItems)); ?></span>
                                        </div>

                                        <div class="kanban-column-actions">
                                            <a href="javascript:void(0);" class="action-icon edit-item" data-bs-toggle="modal"
                                                data-bs-target="#editModalTag" data-table="kanban_board_tags"
                                                data-id="<?php echo $kanban_tag['id']; ?>" title="<?php echo (_lang['edit_item']); ?>">
                                                <span class="btn btn-outline-info btn-sm">
                                                    <i class="mdi mdi-square-edit-outline"></i>
                                                </span>
                                            </a>

                                            <a href="javascript:void(0);" class="action-icon delete-item" data-bs-toggle="modal"
                                                data-bs-target="#deleteModalTag" data-table="kanban_board_tags"
                                                data-id="<?php echo $kanban_tag['id']; ?>" title="<?php echo (_lang['delete_item']); ?>">
                                                <span class="btn btn-outline-warning btn-sm">
                                                    <i class="mdi mdi-delete"></i>
                                                </span>
                                            </a>
                                        </div>
                                    </div>

                                    <div id="task-list-<?php echo ($countTag); ?>" class="task-list-items" data-board-tag-id="<?php echo $kanban_tag['id']; ?>">
                                        <?php
                                        if (empty($kanbanItems)) {
                                            ?>
                                            <div class="kanban-empty-column">
                                                <i class="mdi mdi-clipboard-text-outline"></i>
                                                <div>تسکی در این ستون وجود ندارد.</div>
                                            </div>
                                            <?php
                                        }

                                        foreach ($kanbanItems as $kaban_details) {
                                            $condition = $structureModel->getConditionsByName($kaban_details['status']);
                                            $conditionColor = $condition['condition_color'] ?? 'secondary';
                                            $conditionName = $condition['condition_name'] ?? '';

                                            $lastFinanceStatus = $ticketModel->getLastFinanceStatus($kaban_details['ticket_id'], 'tickets');
                                            $financeConditionName = '';
                                            $financeConditionColor = 'secondary';
                                            if (count($lastFinanceStatus) > 0) {
                                                $financeCondition = $structureModel->getConditionsByName($lastFinanceStatus[0]['status_name']);
                                                $financeConditionName = $financeCondition['condition_name'] ?? '';
                                                $financeConditionColor = $financeCondition['condition_color'] ?? 'secondary';
                                            }

                                            $dateConverter = new DateConverter($kaban_details['last_updated_date'], $config->getNowLanguage('a'));
                                            $showPriority = '';
                                            $priority = $kaban_details['priority'];
                                            if ($priority == 'low') {
                                                $showPriority = '<span class="badge bg-primary">' . _lang[$priority] . '</span>';
                                            } elseif ($priority == 'medium') {
                                                $showPriority = '<span class="badge bg-warning">' . _lang[$priority] . '</span>';
                                            } elseif ($priority == 'high') {
                                                $showPriority = '<span class="badge bg-danger">' . _lang[$priority] . '</span>';
                                            }
                                            ?>

                                            <div class="card mb-3 kanban-task-card" data-id="<?php echo $kaban_details['id']; ?>">
                                                <div class="card-body">
                                                    <div class="kanban-task-top">
                                                        <div class="kanban-task-badges">
                                                            <span class="kanban-status-chip alert-<?php echo $conditionColor; ?>">
                                                                <?php echo _lang[$conditionName]; ?>
                                                            </span>

                                                            <?php if (!empty($financeConditionName)) { ?>
                                                                <span class="kanban-status-chip alert-<?php echo $financeConditionColor; ?>">
                                                                    <?php echo _lang[$financeConditionName]; ?>
                                                                </span>
                                                            <?php } ?>
                                                        </div>

                                                        <div class="dropdown kanban-task-menu">
                                                            <a href="#" class="dropdown-toggle text-muted arrow-none"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="mdi mdi-dots-vertical font-18"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a href="javascript:void(0);" class="dropdown-item delete-item"
                                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                                    data-table="kanban_board"
                                                                    data-id="<?php echo $kaban_details['id']; ?>">
                                                                    <i class="mdi mdi-delete me-1"></i><?php echo (_lang['delete']); ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="kanban-task-meta">
                                                        <div class="kanban-task-meta-left">
                                                            <?php echo $showPriority; ?>
                                                            <span class="badge bg-soft-primary text-primary"><?php echo htmlspecialchars($kaban_details['type_group'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                        </div>
                                                        <div class="kanban-task-meta-right">
                                                            <span class="kanban-task-date">
                                                                <i class="mdi mdi-calendar-clock-outline"></i>
                                                                <?php echo $dateConverter->convertToShamsi(); ?>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <h5 class="kanban-task-title">
                                                        <a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($kaban_details['part_id']); ?>" class="text-body">
                                                            <?php echo '( ' . $kaban_details['ticket_number'] . ' ) ' . htmlspecialchars($textToolsClass->truncateText($kaban_details['ticket_title'], 220), ENT_QUOTES, 'UTF-8'); ?>
                                                        </a>
                                                    </h5>

                                                    <?php if (!empty($kaban_details['description'])) { ?>
                                                        <p class="kanban-task-description"><?php echo htmlspecialchars($kaban_details['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row-->

        </div> <!-- container -->


        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
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

        <div class="modal fade" id="addModalTag" tabindex="-1" role="dialog" aria-labelledby="addModalTagLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalTagLabel">
                            <?php echo (_lang['add']); ?>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="<?php echo (_lang['close']); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Form for adding activity -->
                        <form id="addForm" name="addForm" class="form-control">

                            <input type="hidden" class="form-control addField" name="table_set" id="tableSet"
                                value="kanban_board_tags">
                            <input class="form-control addField" type="hidden" name="unique_fields" id="unique_fields"
                                value="<?php echo $unique_fields; ?>">
                            <div class="mb-3">
                                <label for="board_tag" class="form-label">
                                    <?php echo (_lang['name']); ?>
                                </label>
                                <input type="text" class="form-control addField" id="board_tag" name="board_tag"
                                    required>

                            </div>
                            <input class="form-control addField" type="hidden" name="admin_id" id="admin_id"
                                value="<?php echo $_SESSION['admin_id']; ?>">
                            <button type="button" class="btn btn-primary" id="addDataBtn">
                                <?php echo (_lang['add']); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="deleteModalTag" tabindex="-1" role="dialog" aria-labelledby="deleteModalTagLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalTagLabel">
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


        <div class="modal fade" id="editModalTag" tabindex="-1" role="dialog" aria-labelledby="editModalTagLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalTagLabel">
                            <?php echo (_lang['edit_item']); ?>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="<?php echo (_lang['close']); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Form for editing activity -->
                        <form id="editForm" name="editForm" class="form-control">
                            <input type="hidden" class="form-control editField" name="table_set" id="table_set">
                            <input type="hidden" class="form-control editField" name="id" id="id">
                            <input class="form-control editField" type="hidden" name="unique_fields" id="unique_fields"
                                value="<?php echo $unique_fields; ?>">
                            <div class="mb-3">
                                <label for="board_tag" class="form-label">
                                    <?php echo (_lang['name']); ?>
                                </label>
                                <input type="text" class="form-control editField" id="board_tag" name="board_tag"
                                    required>
                            </div>

                            <button type="button" class="btn btn-primary" id="editDataBtn">
                                <?php echo (_lang['save_changes']); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- content -->

</div>
