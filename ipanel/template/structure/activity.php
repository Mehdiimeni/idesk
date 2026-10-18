<?php
///template/structure/activity.php

$activityEsc = static function ($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$activityLang = static function ($key) {
    return _lang[$key] ?? $key;
};

$activityLower = static function ($value) {
    $value = (string) $value;
    return function_exists('mb_strtolower')
        ? mb_strtolower($value, 'UTF-8')
        : strtolower($value);
};
?>

<style>
    .activity-page {
        --activity-primary: #3157a4;
        --activity-primary-soft: #eef3ff;
        --activity-border: #e6eaf1;
        --activity-text: #24324a;
        --activity-muted: #7a8699;
    }

    .activity-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #e5eaf3;
        border-radius: 18px;
        background: linear-gradient(135deg, #f8faff 0%, #eef4ff 100%);
        padding: 22px 24px;
        box-shadow: 0 8px 28px rgba(31, 51, 89, .06);
    }

    .activity-hero::after {
        content: "";
        position: absolute;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(49, 87, 164, .06);
        inset-inline-start: -70px;
        top: -90px;
    }

    .activity-hero-icon,
    .activity-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--activity-primary-soft);
        color: var(--activity-primary);
        font-size: 22px;
        flex: 0 0 auto;
    }

    .activity-hero h4 {
        margin: 0;
        color: var(--activity-text);
        font-weight: 700;
    }

    .activity-hero p {
        margin: 6px 0 0;
        color: var(--activity-muted);
        max-width: 760px;
    }

    .activity-stat-card {
        border: 1px solid var(--activity-border);
        border-radius: 16px;
        background: #fff;
        padding: 17px;
        min-height: 104px;
        box-shadow: 0 6px 22px rgba(31, 51, 89, .045);
    }

    .activity-stat-label {
        color: var(--activity-muted);
        font-size: 12px;
        margin-bottom: 5px;
    }

    .activity-stat-value {
        color: var(--activity-text);
        font-weight: 800;
        font-size: 24px;
        line-height: 1;
    }

    .activity-main-card {
        border: 1px solid var(--activity-border);
        border-radius: 18px;
        box-shadow: 0 8px 28px rgba(31, 51, 89, .055);
        overflow: hidden;
    }

    .activity-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px;
        border-bottom: 1px solid var(--activity-border);
        background: #fff;
    }

    .activity-search-wrap {
        position: relative;
        width: min(100%, 390px);
    }

    .activity-search-wrap i {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        inset-inline-start: 13px;
        color: #95a0b3;
        font-size: 18px;
    }

    .activity-search-wrap .form-control {
        min-height: 43px;
        border-radius: 12px;
        padding-inline-start: 40px;
        border-color: #dfe5ef;
    }

    .activity-add-btn {
        min-height: 43px;
        border-radius: 12px;
        padding-inline: 16px;
    }

    .activity-table thead th {
        background: #f8fafc;
        color: #667085;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        border-bottom: 1px solid #e9edf4;
    }

    .activity-table tbody td {
        vertical-align: middle;
        border-color: #edf0f5;
        color: #344054;
    }

    .activity-name {
        font-weight: 700;
        color: #253858;
    }

    .activity-description {
        max-width: 340px;
        color: #667085;
        white-space: normal;
        line-height: 1.8;
    }

    .activity-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 28px;
        border-radius: 9px;
        background: #f4f6f9;
        color: #475467;
        font-weight: 700;
    }

    .activity-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    .activity-status.used {
        background: #ecfdf3;
        color: #027a48;
    }

    .activity-status.unused {
        background: #f2f4f7;
        color: #667085;
    }

    .activity-action-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e4e8ef;
        background: #fff;
        color: #667085;
        transition: .18s ease;
    }

    .activity-action-btn:hover {
        background: #f7f9fc;
        color: var(--activity-primary);
        border-color: #d7dfec;
    }

    .activity-action-btn.delete:hover {
        color: #d92d20;
        background: #fff5f4;
        border-color: #f4cbc7;
    }

    .activity-action-btn.locked {
        cursor: not-allowed;
        opacity: .4;
    }

    .activity-empty {
        padding: 44px 20px !important;
        text-align: center;
        color: #98a2b3 !important;
    }

    .activity-modal .modal-content {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 18px 55px rgba(16, 24, 40, .18);
    }

    .activity-modal .modal-header {
        border-bottom: 1px solid #edf0f5;
        padding: 18px 20px;
    }

    .activity-modal .modal-body {
        padding: 20px;
    }

    .activity-modal .form-control {
        border-radius: 11px;
        border-color: #dfe5ef;
    }

    .activity-modal textarea.form-control {
        min-height: 110px;
        resize: vertical;
    }

    .activity-delete-note {
        border-radius: 12px;
        background: #fff8eb;
        border: 1px solid #fde3b1;
        color: #8a6116;
        padding: 12px 14px;
        line-height: 1.8;
    }

    @media (max-width: 767.98px) {
        .activity-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .activity-search-wrap {
            width: 100%;
        }

        .activity-add-btn {
            width: 100%;
        }
    }
</style>

<div class="content-page activity-page">
    <div class="content">
        <div class="container-fluid">

            <div class="activity-hero mb-3">
                <div class="row align-items-center g-3 position-relative" style="z-index:1;">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-start gap-3">
                            <span class="activity-hero-icon"><i class="ri-building-2-line"></i></span>
                            <div>
                                <h4><?php echo $activityLang('activity_management'); ?></h4>
                                <p><?php echo $activityLang('activity_management_note'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php if ($canAddActivity) { ?>
                        <div class="col-lg-4 text-lg-end">
                            <button type="button" class="btn btn-primary activity-add-btn" data-bs-toggle="modal" data-bs-target="#activityAddModal">
                                <i class="ri-add-line me-1"></i>
                                <?php echo _lang['add']; ?>
                            </button>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div id="activityFeedback" class="alert d-none mb-3" role="alert"></div>

            <div class="row g-3 mb-3">
                <div class="col-6 col-xl-3">
                    <div class="activity-stat-card d-flex align-items-center gap-3 h-100">
                        <span class="activity-stat-icon"><i class="ri-stack-line"></i></span>
                        <div>
                            <div class="activity-stat-label"><?php echo $activityLang('activity_total'); ?></div>
                            <div class="activity-stat-value"><?php echo number_format((int) $activitySummary['total']); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="activity-stat-card d-flex align-items-center gap-3 h-100">
                        <span class="activity-stat-icon"><i class="ri-links-line"></i></span>
                        <div>
                            <div class="activity-stat-label"><?php echo $activityLang('activity_in_use'); ?></div>
                            <div class="activity-stat-value"><?php echo number_format((int) $activitySummary['in_use']); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="activity-stat-card d-flex align-items-center gap-3 h-100">
                        <span class="activity-stat-icon"><i class="ri-community-line"></i></span>
                        <div>
                            <div class="activity-stat-label"><?php echo $activityLang('activity_linked_companies'); ?></div>
                            <div class="activity-stat-value"><?php echo number_format((int) $activitySummary['companies']); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="activity-stat-card d-flex align-items-center gap-3 h-100">
                        <span class="activity-stat-icon"><i class="ri-shield-user-line"></i></span>
                        <div>
                            <div class="activity-stat-label"><?php echo $activityLang('activity_linked_roles'); ?></div>
                            <div class="activity-stat-value"><?php echo number_format((int) $activitySummary['roles']); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card activity-main-card mb-4">
                <div class="activity-toolbar">
                    <div>
                        <h5 class="mb-1"><?php echo _lang['activity']; ?></h5>
                        <div class="text-muted small"><?php echo $activityLang('activity_list_note'); ?></div>
                    </div>
                    <div class="activity-search-wrap">
                        <i class="ri-search-line"></i>
                        <input type="search" id="activitySearch" class="form-control"
                               placeholder="<?php echo $activityEsc($activityLang('activity_search_placeholder')); ?>"
                               autocomplete="off">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table activity-table mb-0" id="activityTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo _lang['name']; ?></th>
                                <th><?php echo _lang['description']; ?></th>
                                <th><?php echo $activityLang('activity_linked_companies'); ?></th>
                                <th><?php echo $activityLang('activity_linked_roles'); ?></th>
                                <th><?php echo $activityLang('activity_usage'); ?></th>
                                <th><?php echo _lang['last_updated_date']; ?></th>
                                <th><?php echo _lang['actions']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($activityRows as $activity) {
                            $searchText = trim(
                                ($activity['activity_name'] ?? '') . ' ' .
                                ($activity['activity_description'] ?? '') . ' ' .
                                ($activity['id'] ?? '')
                            );
                        ?>
                            <tr class="activity-row" data-search="<?php echo $activityEsc($activityLower($searchText)); ?>">
                                <td><?php echo (int) $activity['id']; ?></td>
                                <td><span class="activity-name"><?php echo $activityEsc($activity['activity_name']); ?></span></td>
                                <td>
                                    <div class="activity-description">
                                        <?php
                                        $description = trim((string) ($activity['activity_description'] ?? ''));
                                        echo $description !== ''
                                            ? nl2br($activityEsc($description))
                                            : '<span class="text-muted">-</span>';
                                        ?>
                                    </div>
                                </td>
                                <td><span class="activity-count-badge"><?php echo number_format((int) $activity['company_count']); ?></span></td>
                                <td><span class="activity-count-badge"><?php echo number_format((int) $activity['role_count']); ?></span></td>
                                <td>
                                    <?php if ($activity['is_in_use']) { ?>
                                        <span class="activity-status used"><i class="ri-checkbox-circle-line"></i><?php echo $activityLang('activity_used'); ?></span>
                                    <?php } else { ?>
                                        <span class="activity-status unused"><i class="ri-subtract-line"></i><?php echo $activityLang('activity_unused'); ?></span>
                                    <?php } ?>
                                </td>
                                <td class="text-nowrap"><?php echo $activityEsc($activity['last_updated_display']); ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <?php if ($canEditActivity) { ?>
                                            <button type="button"
                                                    class="activity-action-btn activity-edit-btn"
                                                    data-id="<?php echo (int) $activity['id']; ?>"
                                                    data-name="<?php echo $activityEsc($activity['activity_name']); ?>"
                                                    data-description="<?php echo $activityEsc($activity['activity_description']); ?>"
                                                    title="<?php echo $activityEsc(_lang['edit_item']); ?>">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                        <?php } ?>

                                        <?php if ($canDeleteActivity) { ?>
                                            <?php if (!$activity['is_in_use']) { ?>
                                                <button type="button"
                                                        class="activity-action-btn delete activity-delete-btn"
                                                        data-id="<?php echo (int) $activity['id']; ?>"
                                                        data-name="<?php echo $activityEsc($activity['activity_name']); ?>"
                                                        title="<?php echo $activityEsc(_lang['delete']); ?>">
                                                    <i class="ri-delete-bin-6-line"></i>
                                                </button>
                                            <?php } else { ?>
                                                <button type="button"
                                                        class="activity-action-btn locked"
                                                        disabled
                                                        title="<?php echo $activityEsc($activityLang('activity_delete_blocked_title')); ?>">
                                                    <i class="ri-lock-line"></i>
                                                </button>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>

                            <tr id="activityNoResults" class="d-none">
                                <td colspan="8" class="activity-empty">
                                    <i class="ri-search-eye-line fs-3 d-block mb-2"></i>
                                    <?php echo $activityLang('activity_no_results'); ?>
                                </td>
                            </tr>

                            <?php if (empty($activityRows)) { ?>
                                <tr>
                                    <td colspan="8" class="activity-empty">
                                        <i class="ri-inbox-line fs-3 d-block mb-2"></i>
                                        <?php echo $activityLang('activity_no_results'); ?>
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

<?php if ($canAddActivity) { ?>
<div class="modal fade activity-modal" id="activityAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1"><?php echo $activityLang('activity_add_title'); ?></h5>
                    <div class="text-muted small"><?php echo $activityLang('activity_add_note'); ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $activityEsc(_lang['close']); ?>"></button>
            </div>
            <div class="modal-body">
                <form id="activityAddForm">
                    <div class="mb-3">
                        <label for="activityAddName" class="form-label"><?php echo _lang['name']; ?></label>
                        <input type="text" class="form-control" id="activityAddName" maxlength="255" required>
                    </div>
                    <div class="mb-3">
                        <label for="activityAddDescription" class="form-label"><?php echo _lang['description']; ?></label>
                        <textarea class="form-control" id="activityAddDescription"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo _lang['add']; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($canEditActivity) { ?>
<div class="modal fade activity-modal" id="activityEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1"><?php echo $activityLang('activity_edit_title'); ?></h5>
                    <div class="text-muted small"><?php echo $activityLang('activity_edit_note'); ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $activityEsc(_lang['close']); ?>"></button>
            </div>
            <div class="modal-body">
                <form id="activityEditForm">
                    <input type="hidden" id="activityEditId">
                    <div class="mb-3">
                        <label for="activityEditName" class="form-label"><?php echo _lang['name']; ?></label>
                        <input type="text" class="form-control" id="activityEditName" maxlength="255" required>
                    </div>
                    <div class="mb-3">
                        <label for="activityEditDescription" class="form-label"><?php echo _lang['description']; ?></label>
                        <textarea class="form-control" id="activityEditDescription"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo _lang['save_changes']; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($canDeleteActivity) { ?>
<div class="modal fade activity-modal" id="activityDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _lang['delete_item']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $activityEsc(_lang['close']); ?>"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="activityDeleteId">
                <p class="mb-2"><?php echo _lang['delete_confirm']; ?></p>
                <div class="fw-semibold mb-3" id="activityDeleteName"></div>
                <div class="activity-delete-note">
                    <i class="ri-information-line me-1"></i>
                    <?php echo $activityLang('activity_delete_safe_note'); ?>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button>
                    <button type="button" class="btn btn-danger" id="activityDeleteConfirm"><?php echo _lang['delete']; ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const endpoint = '../icore/json/activity_manage.php';
    const csrf = <?php echo json_encode($activityCsrf, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const feedback = document.getElementById('activityFeedback');

    function showFeedback(message, ok) {
        if (!feedback) return;
        feedback.className = 'alert mb-3 ' + (ok ? 'alert-success' : 'alert-danger');
        feedback.textContent = message || '';
        feedback.classList.remove('d-none');
        window.scrollTo({ top: Math.max(0, feedback.offsetTop - 120), behavior: 'smooth' });
    }

    async function activityRequest(payload) {
        const body = new URLSearchParams();
        Object.keys(payload).forEach(key => body.append(key, payload[key] ?? ''));
        body.append('csrf', csrf);

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body.toString(),
            credentials: 'same-origin'
        });

        let data;
        try {
            data = await response.json();
        } catch (e) {
            data = { status: 'error', message: <?php echo json_encode($activityLang('activity_unexpected_error')); ?> };
        }

        if (!response.ok || data.status !== 'success') {
            throw new Error(data.message || <?php echo json_encode($activityLang('activity_unexpected_error')); ?>);
        }

        return data;
    }

    const search = document.getElementById('activitySearch');
    const noResults = document.getElementById('activityNoResults');
    if (search) {
        search.addEventListener('input', function () {
            const q = (this.value || '').trim().toLocaleLowerCase('fa');
            const rows = Array.from(document.querySelectorAll('#activityTable tbody .activity-row'));
            let visible = 0;

            rows.forEach(row => {
                const haystack = (row.dataset.search || '').toLocaleLowerCase('fa');
                const show = q === '' || haystack.includes(q);
                row.classList.toggle('d-none', !show);
                if (show) visible++;
            });

            if (noResults) {
                noResults.classList.toggle('d-none', visible !== 0 || rows.length === 0);
            }
        });
    }

    const addForm = document.getElementById('activityAddForm');
    if (addForm) {
        addForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submit = this.querySelector('button[type="submit"]');
            submit.disabled = true;

            try {
                const data = await activityRequest({
                    action: 'add',
                    activity_name: document.getElementById('activityAddName').value,
                    activity_description: document.getElementById('activityAddDescription').value
                });
                bootstrap.Modal.getInstance(document.getElementById('activityAddModal'))?.hide();
                showFeedback(data.message, true);
                setTimeout(() => location.reload(), 500);
            } catch (err) {
                showFeedback(err.message, false);
            } finally {
                submit.disabled = false;
            }
        });
    }

    const editModalEl = document.getElementById('activityEditModal');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    document.querySelectorAll('.activity-edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('activityEditId').value = this.dataset.id || '';
            document.getElementById('activityEditName').value = this.dataset.name || '';
            document.getElementById('activityEditDescription').value = this.dataset.description || '';
            editModal?.show();
        });
    });

    const editForm = document.getElementById('activityEditForm');
    if (editForm) {
        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submit = this.querySelector('button[type="submit"]');
            submit.disabled = true;

            try {
                const data = await activityRequest({
                    action: 'edit',
                    id: document.getElementById('activityEditId').value,
                    activity_name: document.getElementById('activityEditName').value,
                    activity_description: document.getElementById('activityEditDescription').value
                });
                editModal?.hide();
                showFeedback(data.message, true);
                setTimeout(() => location.reload(), 500);
            } catch (err) {
                showFeedback(err.message, false);
            } finally {
                submit.disabled = false;
            }
        });
    }

    const deleteModalEl = document.getElementById('activityDeleteModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    document.querySelectorAll('.activity-delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('activityDeleteId').value = this.dataset.id || '';
            document.getElementById('activityDeleteName').textContent = this.dataset.name || '';
            deleteModal?.show();
        });
    });

    const deleteConfirm = document.getElementById('activityDeleteConfirm');
    if (deleteConfirm) {
        deleteConfirm.addEventListener('click', async function () {
            this.disabled = true;
            try {
                const data = await activityRequest({
                    action: 'delete',
                    id: document.getElementById('activityDeleteId').value
                });
                deleteModal?.hide();
                showFeedback(data.message, true);
                setTimeout(() => location.reload(), 500);
            } catch (err) {
                deleteModal?.hide();
                showFeedback(err.message, false);
            } finally {
                this.disabled = false;
            }
        });
    }
});
</script>
