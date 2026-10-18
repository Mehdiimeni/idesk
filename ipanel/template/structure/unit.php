<?php
///template/structure/unit.php

$unitEsc = static function ($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$unitLang = static function ($key) {
    return _lang[$key] ?? $key;
};
?>

<style>
    .unit-page {
        --unit-primary: #3157a4;
        --unit-primary-soft: #eef3ff;
        --unit-border: #e6eaf1;
        --unit-text: #24324a;
        --unit-muted: #7a8699;
    }

    .unit-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #e5eaf3;
        border-radius: 18px;
        background: linear-gradient(135deg, #f8faff 0%, #eef4ff 100%);
        padding: 22px 24px;
        box-shadow: 0 8px 28px rgba(31, 51, 89, .06);
    }

    .unit-hero::after {
        content: "";
        position: absolute;
        width: 190px;
        height: 190px;
        border-radius: 50%;
        background: rgba(49, 87, 164, .06);
        inset-inline-start: -70px;
        top: -100px;
    }

    .unit-hero-icon,
    .unit-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--unit-primary-soft);
        color: var(--unit-primary);
        font-size: 22px;
        flex: 0 0 auto;
    }

    .unit-hero h4 {
        margin: 0;
        color: var(--unit-text);
        font-weight: 700;
    }

    .unit-hero p {
        margin: 6px 0 0;
        color: var(--unit-muted);
        max-width: 860px;
    }

    .unit-stat-card {
        border: 1px solid var(--unit-border);
        border-radius: 16px;
        background: #fff;
        padding: 17px;
        min-height: 104px;
        box-shadow: 0 6px 22px rgba(31, 51, 89, .045);
    }

    .unit-stat-label {
        color: var(--unit-muted);
        font-size: 12px;
        margin-bottom: 5px;
    }

    .unit-stat-value {
        color: var(--unit-text);
        font-weight: 800;
        font-size: 24px;
        line-height: 1;
    }

    .unit-main-card {
        border: 1px solid var(--unit-border);
        border-radius: 18px;
        box-shadow: 0 8px 28px rgba(31, 51, 89, .055);
        overflow: hidden;
    }

    .unit-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px;
        border-bottom: 1px solid var(--unit-border);
        background: #fff;
    }

    .unit-search-wrap {
        position: relative;
        width: min(100%, 390px);
    }

    .unit-search-wrap i {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        inset-inline-start: 13px;
        color: #95a0b3;
        font-size: 18px;
    }

    .unit-search-wrap .form-control {
        min-height: 43px;
        border-radius: 12px;
        padding-inline-start: 40px;
        border-color: #dfe5ef;
    }

    .unit-add-btn {
        min-height: 43px;
        border-radius: 12px;
        padding-inline: 16px;
    }

    .unit-table thead th {
        background: #f8fafc;
        color: #667085;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        border-bottom: 1px solid #e9edf4;
    }

    .unit-table tbody td {
        vertical-align: middle;
        border-color: #edf0f5;
        color: #344054;
    }

    .unit-name {
        font-weight: 700;
        color: #253858;
    }

    .unit-subtext {
        color: #98a2b3;
        font-size: 11px;
        margin-top: 3px;
    }

    .unit-type-badge,
    .unit-count-badge,
    .unit-state-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 9px;
        font-size: 11px;
        font-weight: 700;
        padding: 5px 8px;
        white-space: nowrap;
    }

    .unit-type-badge,
    .unit-count-badge {
        background: #f4f6f9;
        color: #475467;
    }

    .unit-state-badge.used {
        background: #ecfdf3;
        color: #027a48;
    }

    .unit-state-badge.free {
        background: #f2f4f7;
        color: #667085;
    }

    .unit-action-btn {
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

    .unit-action-btn:hover {
        background: #f7f9fc;
        color: var(--unit-primary);
        border-color: #d7dfec;
    }

    .unit-action-btn.delete:hover {
        color: #d92d20;
        background: #fff5f4;
        border-color: #f4cbc7;
    }

    .unit-action-btn.locked {
        cursor: not-allowed;
        opacity: .4;
    }

    .unit-empty {
        padding: 44px 20px !important;
        text-align: center;
        color: #98a2b3 !important;
    }

    .unit-modal .modal-content {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 18px 55px rgba(16, 24, 40, .18);
    }

    .unit-modal .modal-header {
        border-bottom: 1px solid #edf0f5;
        padding: 18px 20px;
    }

    .unit-modal .modal-body {
        padding: 20px;
    }

    .unit-modal .form-control,
    .unit-modal .form-select {
        border-radius: 11px;
        border-color: #dfe5ef;
    }

    .unit-safety-note {
        border-radius: 12px;
        background: #fff8eb;
        border: 1px solid #fde3b1;
        color: #8a6116;
        padding: 12px 14px;
        line-height: 1.8;
    }

    .unit-info-note {
        border-radius: 12px;
        background: #f5f8ff;
        border: 1px solid #dce6fb;
        color: #36558d;
        padding: 11px 13px;
        line-height: 1.75;
        font-size: 12px;
    }

    @media (max-width: 767.98px) {
        .unit-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .unit-search-wrap,
        .unit-add-btn {
            width: 100%;
        }
    }
</style>

<div class="content-page unit-page">
    <div class="content">
        <div class="container-fluid">

            <div class="unit-hero mb-3">
                <div class="row align-items-center g-3 position-relative" style="z-index:1;">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-start gap-3">
                            <span class="unit-hero-icon"><i class="ri-node-tree"></i></span>
                            <div>
                                <h4><?php echo $unitLang('unit_management'); ?></h4>
                                <p><?php echo $unitLang('unit_management_note'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php if ($canAddUnit) { ?>
                        <div class="col-lg-4 text-lg-end">
                            <button type="button" class="btn btn-primary unit-add-btn" data-bs-toggle="modal" data-bs-target="#unitAddModal">
                                <i class="ri-add-line me-1"></i><?php echo _lang['add']; ?>
                            </button>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div id="unitFeedback" class="alert d-none mb-3" role="alert"></div>

            <div class="row g-3 mb-3">
                <div class="col-xl-3 col-md-6">
                    <div class="unit-stat-card d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <div class="unit-stat-label"><?php echo $unitLang('unit_total'); ?></div>
                            <div class="unit-stat-value"><?php echo number_format($unitSummary['total']); ?></div>
                        </div>
                        <span class="unit-stat-icon"><i class="ri-node-tree"></i></span>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="unit-stat-card d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <div class="unit-stat-label"><?php echo $unitLang('unit_companies_total'); ?></div>
                            <div class="unit-stat-value"><?php echo number_format($unitSummary['companies']); ?></div>
                        </div>
                        <span class="unit-stat-icon"><i class="ri-building-4-line"></i></span>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="unit-stat-card d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <div class="unit-stat-label"><?php echo $unitLang('unit_members_total'); ?></div>
                            <div class="unit-stat-value"><?php echo number_format($unitSummary['members']); ?></div>
                        </div>
                        <span class="unit-stat-icon"><i class="ri-team-line"></i></span>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="unit-stat-card d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <div class="unit-stat-label"><?php echo $unitLang('unit_roles_total'); ?></div>
                            <div class="unit-stat-value"><?php echo number_format($unitSummary['roles']); ?></div>
                        </div>
                        <span class="unit-stat-icon"><i class="ri-shield-user-line"></i></span>
                    </div>
                </div>
            </div>

            <div class="unit-main-card bg-white">
                <div class="unit-toolbar">
                    <div>
                        <div class="fw-bold text-dark"><?php echo _lang['unit']; ?></div>
                        <div class="small text-muted mt-1"><?php echo $unitLang('unit_list_note'); ?></div>
                    </div>
                    <div class="unit-search-wrap">
                        <i class="ri-search-line"></i>
                        <input type="search" class="form-control" id="unitSearch" placeholder="<?php echo $unitEsc($unitLang('unit_search_placeholder')); ?>" autocomplete="off">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table unit-table mb-0" id="unitTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo _lang['name']; ?></th>
                                <th><?php echo _lang['company']; ?></th>
                                <th><?php echo _lang['type']; ?></th>
                                <th><?php echo $unitLang('unit_structure'); ?></th>
                                <th><?php echo $unitLang('unit_usage'); ?></th>
                                <th><?php echo _lang['last_updated_date']; ?></th>
                                <th><?php echo _lang['actions']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($unitRows as $unit) {
                            $searchText = implode(' ', [
                                $unit['unit_name'] ?? '',
                                $unit['unit_type'] ?? '',
                                $unit['company_name'] ?? '',
                            ]);
                        ?>
                            <tr class="unit-row" data-search="<?php echo $unitEsc($searchText); ?>">
                                <td><?php echo (int) $unit['id']; ?></td>
                                <td>
                                    <div class="unit-name"><?php echo $unitEsc($unit['unit_name']); ?></div>
                                    <div class="unit-subtext"><?php echo $unitLang('unit_id_label'); ?>: <?php echo (int) $unit['id']; ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?php echo $unitEsc($unit['company_name'] ?: '-'); ?></div>
                                </td>
                                <td>
                                    <span class="unit-type-badge">
                                        <i class="ri-price-tag-3-line"></i><?php echo $unitEsc($unit['unit_type'] ?: '-'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="unit-count-badge"><i class="ri-user-line"></i><?php echo $unitLang('unit_users'); ?>: <?php echo (int) $unit['user_count']; ?></span>
                                        <span class="unit-count-badge"><i class="ri-user-star-line"></i><?php echo $unitLang('unit_admins'); ?>: <?php echo (int) $unit['admin_count']; ?></span>
                                        <span class="unit-count-badge"><i class="ri-shield-user-line"></i><?php echo $unitLang('unit_roles'); ?>: <?php echo (int) $unit['role_count']; ?></span>
                                        <?php if ((int) $unit['daily_report_count'] > 0) { ?>
                                            <span class="unit-count-badge"><i class="ri-file-list-3-line"></i><?php echo $unitLang('unit_reports'); ?>: <?php echo (int) $unit['daily_report_count']; ?></span>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="unit-state-badge <?php echo $unit['is_in_use'] ? 'used' : 'free'; ?>">
                                        <i class="<?php echo $unit['is_in_use'] ? 'ri-checkbox-circle-line' : 'ri-checkbox-blank-circle-line'; ?>"></i>
                                        <?php echo $unitLang($unit['is_in_use'] ? 'unit_used' : 'unit_unused'); ?>
                                    </span>
                                </td>
                                <td class="text-nowrap"><?php echo $unitEsc($unit['last_updated_display']); ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <?php if ($canEditUnit) { ?>
                                            <button type="button"
                                                    class="unit-action-btn unit-edit-btn"
                                                    data-id="<?php echo (int) $unit['id']; ?>"
                                                    data-name="<?php echo $unitEsc($unit['unit_name']); ?>"
                                                    data-type="<?php echo $unitEsc($unit['unit_type']); ?>"
                                                    data-company="<?php echo (int) $unit['company_id']; ?>"
                                                    data-in-use="<?php echo $unit['is_in_use'] ? '1' : '0'; ?>"
                                                    title="<?php echo $unitEsc(_lang['edit_item']); ?>">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                        <?php } ?>

                                        <?php if ($canDeleteUnit) { ?>
                                            <?php if (!$unit['is_in_use']) { ?>
                                                <button type="button"
                                                        class="unit-action-btn delete unit-delete-btn"
                                                        data-id="<?php echo (int) $unit['id']; ?>"
                                                        data-name="<?php echo $unitEsc($unit['unit_name']); ?>"
                                                        title="<?php echo $unitEsc(_lang['delete']); ?>">
                                                    <i class="ri-delete-bin-6-line"></i>
                                                </button>
                                            <?php } else { ?>
                                                <button type="button" class="unit-action-btn locked" disabled title="<?php echo $unitEsc($unitLang('unit_delete_blocked_title')); ?>">
                                                    <i class="ri-lock-line"></i>
                                                </button>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>

                            <tr id="unitNoResults" class="d-none">
                                <td colspan="8" class="unit-empty">
                                    <i class="ri-search-eye-line fs-3 d-block mb-2"></i>
                                    <?php echo $unitLang('unit_no_results'); ?>
                                </td>
                            </tr>

                            <?php if (empty($unitRows)) { ?>
                                <tr>
                                    <td colspan="8" class="unit-empty">
                                        <i class="ri-inbox-line fs-3 d-block mb-2"></i>
                                        <?php echo $unitLang('unit_no_results'); ?>
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

<?php if ($canAddUnit) { ?>
<div class="modal fade unit-modal" id="unitAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1"><?php echo $unitLang('unit_add_title'); ?></h5>
                    <div class="small text-muted"><?php echo $unitLang('unit_add_note'); ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $unitEsc(_lang['close']); ?>"></button>
            </div>
            <div class="modal-body">
                <form id="unitAddForm">
                    <div class="mb-3">
                        <label class="form-label" for="unitAddName"><?php echo _lang['name']; ?></label>
                        <input type="text" class="form-control" id="unitAddName" maxlength="255" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="unitAddType"><?php echo $unitLang('unit_type_label'); ?></label>
                        <input type="text" class="form-control" id="unitAddType" maxlength="255" placeholder="<?php echo $unitEsc($unitLang('unit_type_placeholder')); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="unitAddCompany"><?php echo _lang['company']; ?></label>
                        <select class="form-select" id="unitAddCompany" required>
                            <option value=""><?php echo $unitLang('unit_select_company'); ?></option>
                            <?php foreach ($companyRows as $company) { ?>
                                <option value="<?php echo (int) $company['id']; ?>"><?php echo $unitEsc($company['company_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="unit-info-note">
                        <i class="ri-information-line me-1"></i><?php echo $unitLang('unit_unique_note'); ?>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo _lang['add']; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($canEditUnit) { ?>
<div class="modal fade unit-modal" id="unitEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1"><?php echo $unitLang('unit_edit_title'); ?></h5>
                    <div class="small text-muted"><?php echo $unitLang('unit_edit_note'); ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $unitEsc(_lang['close']); ?>"></button>
            </div>
            <div class="modal-body">
                <form id="unitEditForm">
                    <input type="hidden" id="unitEditId">
                    <div class="mb-3">
                        <label class="form-label" for="unitEditName"><?php echo _lang['name']; ?></label>
                        <input type="text" class="form-control" id="unitEditName" maxlength="255" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="unitEditType"><?php echo $unitLang('unit_type_label'); ?></label>
                        <input type="text" class="form-control" id="unitEditType" maxlength="255" placeholder="<?php echo $unitEsc($unitLang('unit_type_placeholder')); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="unitEditCompany"><?php echo _lang['company']; ?></label>
                        <select class="form-select" id="unitEditCompany" required>
                            <?php foreach ($companyRows as $company) { ?>
                                <option value="<?php echo (int) $company['id']; ?>"><?php echo $unitEsc($company['company_name']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="small text-muted mt-2 d-none" id="unitEditCompanyLockNote">
                            <i class="ri-lock-line me-1"></i><?php echo $unitLang('unit_company_locked_note'); ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo _lang['save_changes']; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($canDeleteUnit) { ?>
<div class="modal fade unit-modal" id="unitDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _lang['delete_item']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $unitEsc(_lang['close']); ?>"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="unitDeleteId">
                <p class="mb-2"><?php echo _lang['delete_confirm']; ?></p>
                <div class="fw-semibold mb-3" id="unitDeleteName"></div>
                <div class="unit-safety-note">
                    <i class="ri-information-line me-1"></i><?php echo $unitLang('unit_delete_safe_note'); ?>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button>
                    <button type="button" class="btn btn-danger" id="unitDeleteConfirm"><?php echo _lang['delete']; ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const endpoint = '../icore/json/unit_manage.php';
    const csrf = <?php echo json_encode($unitCsrf, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const feedback = document.getElementById('unitFeedback');

    function showFeedback(message, ok) {
        if (!feedback) return;
        feedback.className = 'alert mb-3 ' + (ok ? 'alert-success' : 'alert-danger');
        feedback.textContent = message || '';
        feedback.classList.remove('d-none');
        window.scrollTo({ top: Math.max(0, feedback.offsetTop - 120), behavior: 'smooth' });
    }

    async function unitRequest(payload) {
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
            data = { status: 'error', message: <?php echo json_encode($unitLang('unit_unexpected_error')); ?> };
        }

        if (!response.ok || data.status !== 'success') {
            throw new Error(data.message || <?php echo json_encode($unitLang('unit_unexpected_error')); ?>);
        }
        return data;
    }

    const search = document.getElementById('unitSearch');
    const noResults = document.getElementById('unitNoResults');
    if (search) {
        search.addEventListener('input', function () {
            const q = (this.value || '').trim().toLocaleLowerCase('fa');
            const rows = Array.from(document.querySelectorAll('#unitTable tbody .unit-row'));
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

    const addForm = document.getElementById('unitAddForm');
    if (addForm) {
        addForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submit = this.querySelector('button[type="submit"]');
            submit.disabled = true;
            try {
                const data = await unitRequest({
                    action: 'add',
                    unit_name: document.getElementById('unitAddName').value,
                    unit_type: document.getElementById('unitAddType').value,
                    company_id: document.getElementById('unitAddCompany').value
                });
                bootstrap.Modal.getInstance(document.getElementById('unitAddModal'))?.hide();
                showFeedback(data.message, true);
                setTimeout(() => location.reload(), 500);
            } catch (err) {
                showFeedback(err.message, false);
            } finally {
                submit.disabled = false;
            }
        });
    }

    const editModalEl = document.getElementById('unitEditModal');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    document.querySelectorAll('.unit-edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const inUse = this.dataset.inUse === '1';
            document.getElementById('unitEditId').value = this.dataset.id || '';
            document.getElementById('unitEditName').value = this.dataset.name || '';
            document.getElementById('unitEditType').value = this.dataset.type || '';
            const companySelect = document.getElementById('unitEditCompany');
            companySelect.value = this.dataset.company || '';
            companySelect.disabled = inUse;
            document.getElementById('unitEditCompanyLockNote')?.classList.toggle('d-none', !inUse);
            editModal?.show();
        });
    });

    const editForm = document.getElementById('unitEditForm');
    if (editForm) {
        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submit = this.querySelector('button[type="submit"]');
            submit.disabled = true;
            try {
                const data = await unitRequest({
                    action: 'edit',
                    id: document.getElementById('unitEditId').value,
                    unit_name: document.getElementById('unitEditName').value,
                    unit_type: document.getElementById('unitEditType').value,
                    company_id: document.getElementById('unitEditCompany').value
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

    const deleteModalEl = document.getElementById('unitDeleteModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    document.querySelectorAll('.unit-delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('unitDeleteId').value = this.dataset.id || '';
            document.getElementById('unitDeleteName').textContent = this.dataset.name || '';
            deleteModal?.show();
        });
    });

    const deleteConfirm = document.getElementById('unitDeleteConfirm');
    if (deleteConfirm) {
        deleteConfirm.addEventListener('click', async function () {
            this.disabled = true;
            try {
                const data = await unitRequest({
                    action: 'delete',
                    id: document.getElementById('unitDeleteId').value
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
