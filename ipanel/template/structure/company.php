<?php
///template/structure/company.php

$companyEsc = static function ($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$companyLang = static function ($key) {
    return _lang[$key] ?? $key;
};

$companyBoolClass = static function ($value) {
    return (int) $value === 1 ? 'enabled' : 'disabled';
};
?>

<style>
    .company-page {
        --company-primary: #3157a4;
        --company-primary-soft: #eef3ff;
        --company-border: #e6eaf1;
        --company-text: #24324a;
        --company-muted: #7a8699;
    }

    .company-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #e5eaf3;
        border-radius: 18px;
        background: linear-gradient(135deg, #f8faff 0%, #eef4ff 100%);
        padding: 22px 24px;
        box-shadow: 0 8px 28px rgba(31, 51, 89, .06);
    }

    .company-hero::after {
        content: "";
        position: absolute;
        width: 190px;
        height: 190px;
        border-radius: 50%;
        background: rgba(49, 87, 164, .06);
        inset-inline-start: -70px;
        top: -100px;
    }

    .company-hero-icon,
    .company-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--company-primary-soft);
        color: var(--company-primary);
        font-size: 22px;
        flex: 0 0 auto;
    }

    .company-hero h4 {
        margin: 0;
        color: var(--company-text);
        font-weight: 700;
    }

    .company-hero p {
        margin: 6px 0 0;
        color: var(--company-muted);
        max-width: 820px;
    }

    .company-stat-card {
        border: 1px solid var(--company-border);
        border-radius: 16px;
        background: #fff;
        padding: 17px;
        min-height: 104px;
        box-shadow: 0 6px 22px rgba(31, 51, 89, .045);
    }

    .company-stat-label {
        color: var(--company-muted);
        font-size: 12px;
        margin-bottom: 5px;
    }

    .company-stat-value {
        color: var(--company-text);
        font-weight: 800;
        font-size: 24px;
        line-height: 1;
    }

    .company-main-card {
        border: 1px solid var(--company-border);
        border-radius: 18px;
        box-shadow: 0 8px 28px rgba(31, 51, 89, .055);
        overflow: hidden;
    }

    .company-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px;
        border-bottom: 1px solid var(--company-border);
        background: #fff;
    }

    .company-search-wrap {
        position: relative;
        width: min(100%, 390px);
    }

    .company-search-wrap i {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        inset-inline-start: 13px;
        color: #95a0b3;
        font-size: 18px;
    }

    .company-search-wrap .form-control {
        min-height: 43px;
        border-radius: 12px;
        padding-inline-start: 40px;
        border-color: #dfe5ef;
    }

    .company-add-btn {
        min-height: 43px;
        border-radius: 12px;
        padding-inline: 16px;
    }

    .company-table thead th {
        background: #f8fafc;
        color: #667085;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        border-bottom: 1px solid #e9edf4;
    }

    .company-table tbody td {
        vertical-align: middle;
        border-color: #edf0f5;
        color: #344054;
    }

    .company-name {
        font-weight: 700;
        color: #253858;
    }

    .company-subtext {
        color: #98a2b3;
        font-size: 11px;
        margin-top: 3px;
    }

    .company-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 9px;
        background: #f4f6f9;
        color: #475467;
        font-size: 11px;
        font-weight: 700;
        padding: 5px 8px;
        white-space: nowrap;
    }

    .company-service-stack {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 135px;
    }

    .company-service-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        font-size: 11px;
        color: #667085;
    }

    .company-flag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 999px;
        padding: 4px 8px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .company-flag.enabled {
        background: #ecfdf3;
        color: #027a48;
    }

    .company-flag.disabled {
        background: #fef3f2;
        color: #b42318;
    }

    .company-action-btn {
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

    .company-action-btn:hover {
        background: #f7f9fc;
        color: var(--company-primary);
        border-color: #d7dfec;
    }

    .company-action-btn.delete:hover {
        color: #d92d20;
        background: #fff5f4;
        border-color: #f4cbc7;
    }

    .company-action-btn.locked {
        cursor: not-allowed;
        opacity: .4;
    }

    .company-empty {
        padding: 44px 20px !important;
        text-align: center;
        color: #98a2b3 !important;
    }

    .company-modal .modal-content {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 18px 55px rgba(16, 24, 40, .18);
    }

    .company-modal .modal-header {
        border-bottom: 1px solid #edf0f5;
        padding: 18px 20px;
    }

    .company-modal .modal-body {
        padding: 20px;
    }

    .company-modal .form-control,
    .company-modal .form-select {
        border-radius: 11px;
        border-color: #dfe5ef;
    }

    .company-setting-box {
        border: 1px solid #e8ecf2;
        border-radius: 14px;
        padding: 14px;
        background: #fafbfc;
    }

    .company-setting-box h6 {
        color: #344054;
        margin-bottom: 12px;
        font-weight: 700;
    }

    .company-switch-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 9px 0;
        border-top: 1px dashed #e7eaf0;
    }

    .company-switch-row:first-of-type {
        border-top: 0;
        padding-top: 0;
    }

    .company-delete-note {
        border-radius: 12px;
        background: #fff8eb;
        border: 1px solid #fde3b1;
        color: #8a6116;
        padding: 12px 14px;
        line-height: 1.8;
    }

    @media (max-width: 767.98px) {
        .company-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .company-search-wrap,
        .company-add-btn {
            width: 100%;
        }
    }
</style>

<div class="content-page company-page">
    <div class="content">
        <div class="container-fluid">

            <div class="company-hero mb-3">
                <div class="row align-items-center g-3 position-relative" style="z-index:1;">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-start gap-3">
                            <span class="company-hero-icon"><i class="ri-building-4-line"></i></span>
                            <div>
                                <h4><?php echo $companyLang('company_management'); ?></h4>
                                <p><?php echo $companyLang('company_management_note'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php if ($canAddCompany) { ?>
                        <div class="col-lg-4 text-lg-end">
                            <button type="button" class="btn btn-primary company-add-btn" data-bs-toggle="modal" data-bs-target="#companyAddModal">
                                <i class="ri-add-line me-1"></i><?php echo _lang['add']; ?>
                            </button>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div id="companyFeedback" class="alert d-none mb-3" role="alert"></div>

            <div class="row g-3 mb-3">
                <div class="col-xl-3 col-md-6">
                    <div class="company-stat-card d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <div class="company-stat-label"><?php echo $companyLang('company_total'); ?></div>
                            <div class="company-stat-value"><?php echo number_format($companySummary['total']); ?></div>
                        </div>
                        <span class="company-stat-icon"><i class="ri-building-line"></i></span>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="company-stat-card d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <div class="company-stat-label"><?php echo $companyLang('company_units_total'); ?></div>
                            <div class="company-stat-value"><?php echo number_format($companySummary['units']); ?></div>
                        </div>
                        <span class="company-stat-icon"><i class="ri-node-tree"></i></span>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="company-stat-card d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <div class="company-stat-label"><?php echo $companyLang('company_members_total'); ?></div>
                            <div class="company-stat-value"><?php echo number_format($companySummary['members']); ?></div>
                        </div>
                        <span class="company-stat-icon"><i class="ri-team-line"></i></span>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="company-stat-card d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <div class="company-stat-label"><?php echo $companyLang('company_roles_total'); ?></div>
                            <div class="company-stat-value"><?php echo number_format($companySummary['roles']); ?></div>
                        </div>
                        <span class="company-stat-icon"><i class="ri-shield-user-line"></i></span>
                    </div>
                </div>
            </div>

            <div class="company-main-card bg-white">
                <div class="company-toolbar">
                    <div>
                        <div class="fw-bold text-dark"><?php echo _lang['company']; ?></div>
                        <div class="small text-muted mt-1"><?php echo $companyLang('company_list_note'); ?></div>
                    </div>
                    <div class="company-search-wrap">
                        <i class="ri-search-line"></i>
                        <input type="search" class="form-control" id="companySearch" placeholder="<?php echo $companyEsc($companyLang('company_search_placeholder')); ?>" autocomplete="off">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table company-table mb-0" id="companyTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo _lang['name']; ?></th>
                                <th><?php echo _lang['activity']; ?></th>
                                <th><?php echo $companyLang('company_structure'); ?></th>
                                <th><?php echo $companyLang('company_service_development'); ?></th>
                                <th><?php echo $companyLang('company_service_support'); ?></th>
                                <th><?php echo _lang['last_updated_date']; ?></th>
                                <th><?php echo _lang['actions']; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($companyRows as $company) {
                            $searchText = implode(' ', [
                                $company['company_name'] ?? '',
                                $company['company_phone'] ?? '',
                                $company['activity_name'] ?? '',
                            ]);
                        ?>
                            <tr class="company-row" data-search="<?php echo $companyEsc($searchText); ?>">
                                <td><?php echo (int) $company['id']; ?></td>
                                <td>
                                    <div class="company-name"><?php echo $companyEsc($company['company_name']); ?></div>
                                    <div class="company-subtext">
                                        <i class="ri-phone-line me-1"></i>
                                        <?php echo $companyEsc($company['company_phone'] ?: '-'); ?>
                                    </div>
                                </td>
                                <td><?php echo $companyEsc($company['activity_name'] ?: '-'); ?></td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="company-count-badge"><i class="ri-node-tree"></i><?php echo $companyLang('company_units'); ?>: <?php echo (int) $company['unit_count']; ?></span>
                                        <span class="company-count-badge"><i class="ri-team-line"></i><?php echo $companyLang('company_members'); ?>: <?php echo (int) $company['member_count']; ?></span>
                                        <span class="company-count-badge"><i class="ri-shield-user-line"></i><?php echo $companyLang('company_roles'); ?>: <?php echo (int) $company['role_count']; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="company-service-stack">
                                        <div class="company-service-line">
                                            <span><?php echo $companyLang('company_execution'); ?></span>
                                            <span class="company-flag <?php echo $companyBoolClass($company['company_development']); ?>">
                                                <i class="<?php echo (int) $company['company_development'] === 1 ? 'ri-checkbox-circle-line' : 'ri-close-circle-line'; ?>"></i>
                                                <?php echo (int) $company['company_development'] === 1 ? _lang['yes'] : _lang['no']; ?>
                                            </span>
                                        </div>
                                        <div class="company-service-line">
                                            <span><?php echo $companyLang('company_delivery'); ?></span>
                                            <span class="company-flag <?php echo $companyBoolClass($company['delivery_development']); ?>">
                                                <i class="<?php echo (int) $company['delivery_development'] === 1 ? 'ri-checkbox-circle-line' : 'ri-close-circle-line'; ?>"></i>
                                                <?php echo (int) $company['delivery_development'] === 1 ? _lang['yes'] : _lang['no']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="company-service-stack">
                                        <div class="company-service-line">
                                            <span><?php echo $companyLang('company_execution'); ?></span>
                                            <span class="company-flag <?php echo $companyBoolClass($company['company_support']); ?>">
                                                <i class="<?php echo (int) $company['company_support'] === 1 ? 'ri-checkbox-circle-line' : 'ri-close-circle-line'; ?>"></i>
                                                <?php echo (int) $company['company_support'] === 1 ? _lang['yes'] : _lang['no']; ?>
                                            </span>
                                        </div>
                                        <div class="company-service-line">
                                            <span><?php echo $companyLang('company_delivery'); ?></span>
                                            <span class="company-flag <?php echo $companyBoolClass($company['delivery_support']); ?>">
                                                <i class="<?php echo (int) $company['delivery_support'] === 1 ? 'ri-checkbox-circle-line' : 'ri-close-circle-line'; ?>"></i>
                                                <?php echo (int) $company['delivery_support'] === 1 ? _lang['yes'] : _lang['no']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-nowrap"><?php echo $companyEsc($company['last_updated_display']); ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <?php if ($canEditCompany) { ?>
                                            <button type="button"
                                                    class="company-action-btn company-edit-btn"
                                                    data-id="<?php echo (int) $company['id']; ?>"
                                                    data-name="<?php echo $companyEsc($company['company_name']); ?>"
                                                    data-phone="<?php echo $companyEsc($company['company_phone']); ?>"
                                                    data-activity="<?php echo (int) $company['activity_id']; ?>"
                                                    data-company-development="<?php echo (int) $company['company_development']; ?>"
                                                    data-delivery-development="<?php echo (int) $company['delivery_development']; ?>"
                                                    data-company-support="<?php echo (int) $company['company_support']; ?>"
                                                    data-delivery-support="<?php echo (int) $company['delivery_support']; ?>"
                                                    title="<?php echo $companyEsc(_lang['edit_item']); ?>">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                        <?php } ?>

                                        <?php if ($canDeleteCompany) { ?>
                                            <?php if (!$company['is_in_use']) { ?>
                                                <button type="button"
                                                        class="company-action-btn delete company-delete-btn"
                                                        data-id="<?php echo (int) $company['id']; ?>"
                                                        data-name="<?php echo $companyEsc($company['company_name']); ?>"
                                                        title="<?php echo $companyEsc(_lang['delete']); ?>">
                                                    <i class="ri-delete-bin-6-line"></i>
                                                </button>
                                            <?php } else { ?>
                                                <button type="button"
                                                        class="company-action-btn locked"
                                                        disabled
                                                        title="<?php echo $companyEsc($companyLang('company_delete_blocked_title')); ?>">
                                                    <i class="ri-lock-line"></i>
                                                </button>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>

                            <tr id="companyNoResults" class="d-none">
                                <td colspan="8" class="company-empty">
                                    <i class="ri-search-eye-line fs-3 d-block mb-2"></i>
                                    <?php echo $companyLang('company_no_results'); ?>
                                </td>
                            </tr>

                            <?php if (empty($companyRows)) { ?>
                                <tr>
                                    <td colspan="8" class="company-empty">
                                        <i class="ri-inbox-line fs-3 d-block mb-2"></i>
                                        <?php echo $companyLang('company_no_results'); ?>
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

<?php
$renderCompanyServiceSwitches = static function ($prefix) use ($companyLang) {
?>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="company-setting-box h-100">
                <h6><i class="ri-code-box-line me-1"></i><?php echo $companyLang('company_service_development'); ?></h6>
                <div class="company-switch-row">
                    <span><?php echo $companyLang('company_execution'); ?></span>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" id="<?php echo $prefix; ?>CompanyDevelopment" checked>
                    </div>
                </div>
                <div class="company-switch-row">
                    <span><?php echo $companyLang('company_delivery'); ?></span>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" id="<?php echo $prefix; ?>DeliveryDevelopment" checked>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="company-setting-box h-100">
                <h6><i class="ri-customer-service-2-line me-1"></i><?php echo $companyLang('company_service_support'); ?></h6>
                <div class="company-switch-row">
                    <span><?php echo $companyLang('company_execution'); ?></span>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" id="<?php echo $prefix; ?>CompanySupport" checked>
                    </div>
                </div>
                <div class="company-switch-row">
                    <span><?php echo $companyLang('company_delivery'); ?></span>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" id="<?php echo $prefix; ?>DeliverySupport" checked>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
};
?>

<?php if ($canAddCompany) { ?>
<div class="modal fade company-modal" id="companyAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1"><?php echo $companyLang('company_add_title'); ?></h5>
                    <div class="text-muted small"><?php echo $companyLang('company_add_note'); ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $companyEsc(_lang['close']); ?>"></button>
            </div>
            <div class="modal-body">
                <form id="companyAddForm">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="companyAddName" class="form-label"><?php echo _lang['name']; ?></label>
                            <input type="text" class="form-control" id="companyAddName" maxlength="255" required>
                        </div>
                        <div class="col-md-6">
                            <label for="companyAddPhone" class="form-label"><?php echo _lang['phone']; ?></label>
                            <input type="tel" class="form-control" id="companyAddPhone" maxlength="20" placeholder="<?php echo $companyEsc($companyLang('company_phone_optional')); ?>">
                        </div>
                        <div class="col-12">
                            <label for="companyAddActivity" class="form-label"><?php echo _lang['activity']; ?></label>
                            <select class="form-select" id="companyAddActivity" required>
                                <option value=""><?php echo $companyLang('company_select_activity'); ?></option>
                                <?php foreach ($activityRows as $activity) { ?>
                                    <option value="<?php echo (int) $activity['id']; ?>"><?php echo $companyEsc($activity['activity_name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <?php $renderCompanyServiceSwitches('companyAdd'); ?>

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

<?php if ($canEditCompany) { ?>
<div class="modal fade company-modal" id="companyEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1"><?php echo $companyLang('company_edit_title'); ?></h5>
                    <div class="text-muted small"><?php echo $companyLang('company_edit_note'); ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $companyEsc(_lang['close']); ?>"></button>
            </div>
            <div class="modal-body">
                <form id="companyEditForm">
                    <input type="hidden" id="companyEditId">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="companyEditName" class="form-label"><?php echo _lang['name']; ?></label>
                            <input type="text" class="form-control" id="companyEditName" maxlength="255" required>
                        </div>
                        <div class="col-md-6">
                            <label for="companyEditPhone" class="form-label"><?php echo _lang['phone']; ?></label>
                            <input type="tel" class="form-control" id="companyEditPhone" maxlength="20">
                        </div>
                        <div class="col-12">
                            <label for="companyEditActivity" class="form-label"><?php echo _lang['activity']; ?></label>
                            <select class="form-select" id="companyEditActivity" required>
                                <?php foreach ($activityRows as $activity) { ?>
                                    <option value="<?php echo (int) $activity['id']; ?>"><?php echo $companyEsc($activity['activity_name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <?php $renderCompanyServiceSwitches('companyEdit'); ?>

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

<?php if ($canDeleteCompany) { ?>
<div class="modal fade company-modal" id="companyDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _lang['delete_item']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $companyEsc(_lang['close']); ?>"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="companyDeleteId">
                <p class="mb-2"><?php echo _lang['delete_confirm']; ?></p>
                <div class="fw-semibold mb-3" id="companyDeleteName"></div>
                <div class="company-delete-note">
                    <i class="ri-information-line me-1"></i><?php echo $companyLang('company_delete_safe_note'); ?>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button>
                    <button type="button" class="btn btn-danger" id="companyDeleteConfirm"><?php echo _lang['delete']; ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const endpoint = '../icore/json/company_manage.php';
    const csrf = <?php echo json_encode($companyCsrf, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const feedback = document.getElementById('companyFeedback');

    function showFeedback(message, ok) {
        if (!feedback) return;
        feedback.className = 'alert mb-3 ' + (ok ? 'alert-success' : 'alert-danger');
        feedback.textContent = message || '';
        feedback.classList.remove('d-none');
        window.scrollTo({ top: Math.max(0, feedback.offsetTop - 120), behavior: 'smooth' });
    }

    async function companyRequest(payload) {
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
            data = { status: 'error', message: <?php echo json_encode($companyLang('company_unexpected_error')); ?> };
        }

        if (!response.ok || data.status !== 'success') {
            throw new Error(data.message || <?php echo json_encode($companyLang('company_unexpected_error')); ?>);
        }
        return data;
    }

    const search = document.getElementById('companySearch');
    const noResults = document.getElementById('companyNoResults');
    if (search) {
        search.addEventListener('input', function () {
            const q = (this.value || '').trim().toLocaleLowerCase('fa');
            const rows = Array.from(document.querySelectorAll('#companyTable tbody .company-row'));
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

    function switchValue(id) {
        const el = document.getElementById(id);
        return el && el.checked ? '1' : '0';
    }

    const addForm = document.getElementById('companyAddForm');
    if (addForm) {
        addForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submit = this.querySelector('button[type="submit"]');
            submit.disabled = true;

            try {
                const data = await companyRequest({
                    action: 'add',
                    company_name: document.getElementById('companyAddName').value,
                    company_phone: document.getElementById('companyAddPhone').value,
                    activity_id: document.getElementById('companyAddActivity').value,
                    company_development: switchValue('companyAddCompanyDevelopment'),
                    delivery_development: switchValue('companyAddDeliveryDevelopment'),
                    company_support: switchValue('companyAddCompanySupport'),
                    delivery_support: switchValue('companyAddDeliverySupport')
                });
                bootstrap.Modal.getInstance(document.getElementById('companyAddModal'))?.hide();
                showFeedback(data.message, true);
                setTimeout(() => location.reload(), 500);
            } catch (err) {
                showFeedback(err.message, false);
            } finally {
                submit.disabled = false;
            }
        });
    }

    const editModalEl = document.getElementById('companyEditModal');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    document.querySelectorAll('.company-edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('companyEditId').value = this.dataset.id || '';
            document.getElementById('companyEditName').value = this.dataset.name || '';
            document.getElementById('companyEditPhone').value = this.dataset.phone || '';
            document.getElementById('companyEditActivity').value = this.dataset.activity || '';
            document.getElementById('companyEditCompanyDevelopment').checked = this.dataset.companyDevelopment === '1';
            document.getElementById('companyEditDeliveryDevelopment').checked = this.dataset.deliveryDevelopment === '1';
            document.getElementById('companyEditCompanySupport').checked = this.dataset.companySupport === '1';
            document.getElementById('companyEditDeliverySupport').checked = this.dataset.deliverySupport === '1';
            editModal?.show();
        });
    });

    const editForm = document.getElementById('companyEditForm');
    if (editForm) {
        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submit = this.querySelector('button[type="submit"]');
            submit.disabled = true;

            try {
                const data = await companyRequest({
                    action: 'edit',
                    id: document.getElementById('companyEditId').value,
                    company_name: document.getElementById('companyEditName').value,
                    company_phone: document.getElementById('companyEditPhone').value,
                    activity_id: document.getElementById('companyEditActivity').value,
                    company_development: switchValue('companyEditCompanyDevelopment'),
                    delivery_development: switchValue('companyEditDeliveryDevelopment'),
                    company_support: switchValue('companyEditCompanySupport'),
                    delivery_support: switchValue('companyEditDeliverySupport')
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

    const deleteModalEl = document.getElementById('companyDeleteModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    document.querySelectorAll('.company-delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('companyDeleteId').value = this.dataset.id || '';
            document.getElementById('companyDeleteName').textContent = this.dataset.name || '';
            deleteModal?.show();
        });
    });

    const deleteConfirm = document.getElementById('companyDeleteConfirm');
    if (deleteConfirm) {
        deleteConfirm.addEventListener('click', async function () {
            this.disabled = true;
            try {
                const data = await companyRequest({
                    action: 'delete',
                    id: document.getElementById('companyDeleteId').value
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
