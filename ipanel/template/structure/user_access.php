<?php
///template/structure/user_access.php

$accessEsc = static function ($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$accessLang = static function ($key) {
    return _lang[$key] ?? $key;
};

$accessSystemBadge = static function ($scope) {
    switch ($scope) {
        case 'ipanel':
            return ['class' => 'primary', 'key' => 'access_system_ipanel', 'icon' => 'ri-shield-user-line'];
        case 'iweb':
            return ['class' => 'success', 'key' => 'access_system_iweb', 'icon' => 'ri-global-line'];
        case 'shared':
            return ['class' => 'info', 'key' => 'access_system_shared', 'icon' => 'ri-links-line'];
        default:
            return ['class' => 'secondary', 'key' => 'access_system_unassigned', 'icon' => 'ri-question-line'];
    }
};
?>

<style>
    .access-v2-page {
        --access-border: #e8edf4;
        --access-muted: #6c7a91;
        --access-surface: #ffffff;
        --access-soft: #f7f9fc;
        --access-primary: #3159d8;
    }

    .access-v2-page .access-hero {
        border: 1px solid var(--access-border);
        background: linear-gradient(135deg, #ffffff 0%, #f6f8ff 55%, #eef4ff 100%);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(31, 45, 79, .06);
        position: relative;
        overflow: hidden;
    }

    .access-v2-page .access-hero:after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(49, 89, 216, .06);
        left: -80px;
        top: -95px;
        pointer-events: none;
    }

    .access-v2-page .access-title-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(49, 89, 216, .1);
        color: var(--access-primary);
        font-size: 23px;
        flex: 0 0 auto;
    }

    .access-v2-page .access-subtitle {
        color: var(--access-muted);
        margin-top: 5px;
        margin-bottom: 0;
        line-height: 1.8;
    }

    .access-v2-page .access-system-switch {
        background: #eef2f8;
        padding: 5px;
        border-radius: 13px;
        display: inline-flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .access-v2-page .access-system-switch a {
        border-radius: 10px;
        padding: 9px 15px;
        color: #536176;
        text-decoration: none;
        font-weight: 600;
        transition: .18s ease;
    }

    .access-v2-page .access-system-switch a.active {
        background: #fff;
        color: var(--access-primary);
        box-shadow: 0 3px 12px rgba(31, 45, 79, .09);
    }

    .access-v2-page .access-nav-card {
        border: 1px solid var(--access-border);
        background: #fff;
        border-radius: 16px;
        padding: 8px;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .access-v2-page .access-nav-card a {
        text-decoration: none;
        color: #596579;
        border-radius: 11px;
        padding: 10px 14px;
        font-weight: 600;
        display: inline-flex;
        gap: 7px;
        align-items: center;
    }

    .access-v2-page .access-nav-card a.active {
        background: rgba(49, 89, 216, .09);
        color: var(--access-primary);
    }

    .access-v2-page .access-stat {
        height: 100%;
        border: 1px solid var(--access-border);
        background: #fff;
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 7px 22px rgba(31, 45, 79, .045);
    }

    .access-v2-page .access-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: #f1f5ff;
        color: var(--access-primary);
    }

    .access-v2-page .access-stat-value {
        font-size: 24px;
        font-weight: 800;
        margin-top: 12px;
        color: #27364b;
    }

    .access-v2-page .access-stat-label {
        margin-top: 3px;
        color: var(--access-muted);
        font-size: 13px;
    }

    .access-v2-page .access-main-card {
        border: 1px solid var(--access-border);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 9px 28px rgba(31, 45, 79, .05);
    }

    .access-v2-page .access-main-card .card-header {
        background: #fff;
        border-bottom: 1px solid var(--access-border);
        padding: 17px 20px;
    }

    .access-v2-page .access-main-card .card-body {
        padding: 18px 20px 22px;
    }

    .access-v2-page .access-role-name {
        font-weight: 700;
        color: #253247;
        margin-bottom: 3px;
    }

    .access-v2-page .access-role-meta {
        color: var(--access-muted);
        font-size: 12px;
    }

    .access-v2-page .access-scope-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 20px;
        padding: 6px 10px;
        font-weight: 600;
        font-size: 12px;
    }

    .access-v2-page .access-member-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 9px;
        padding: 5px 8px;
        background: #f5f7fb;
        border: 1px solid #edf0f5;
        color: #566277;
        margin: 2px;
        white-space: nowrap;
    }

    .access-v2-page .access-org-cell {
        min-width: 180px;
    }

    .access-v2-page .access-table thead th {
        background: #f8fafc;
        color: #566277;
        border-bottom: 1px solid var(--access-border);
        font-weight: 700;
        vertical-align: middle;
        white-space: nowrap;
    }

    .access-v2-page .access-table tbody td {
        vertical-align: middle;
        border-color: #f0f2f6;
    }

    .access-v2-page .access-table tbody tr:hover {
        background: #fbfcff;
    }

    .access-v2-page .access-action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        border: 1px solid #e8ecf2;
        background: #fff;
        text-decoration: none;
        margin: 2px;
    }

    .access-v2-page .access-action-btn:hover {
        background: #f5f7fb;
    }

    .access-v2-page .access-empty {
        text-align: center;
        padding: 45px 20px;
        color: var(--access-muted);
    }

    .access-v2-page .access-empty i {
        display: block;
        font-size: 38px;
        margin-bottom: 10px;
        color: #a6b0bf;
    }

    .access-v2-page .access-modal .modal-content {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 20px 70px rgba(21, 32, 52, .2);
        overflow: hidden;
    }

    .access-v2-page .access-modal .modal-header {
        background: #f8fafc;
        border-bottom: 1px solid var(--access-border);
        padding: 17px 20px;
    }

    .access-v2-page .access-modal .modal-body {
        padding: 20px;
    }

    .access-v2-page .access-modal form.form-control {
        border: 0;
        padding: 0;
    }

    .access-v2-page .access-modal .form-control,
    .access-v2-page .access-modal .form-select {
        border-radius: 10px;
        min-height: 42px;
        border-color: #dfe5ee;
    }

    .access-v2-page .access-note {
        border-radius: 14px;
        border: 1px solid #dbe7ff;
        background: #f4f8ff;
        color: #49617f;
        padding: 13px 15px;
        font-size: 13px;
        line-height: 1.8;
    }


    .access-v2-page .access-derived-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 10px;
    }

    .access-v2-page .access-derived-item {
        border: 1px solid #e4eaf3;
        background: #f8fafc;
        border-radius: 12px;
        padding: 10px 12px;
    }

    .access-v2-page .access-derived-label {
        display: block;
        color: #7c899e;
        font-size: 11px;
        margin-bottom: 3px;
    }

    .access-v2-page .access-derived-value {
        color: #27364b;
        font-weight: 700;
        min-height: 20px;
    }

    .access-v2-page .access-lock-note {
        border: 1px solid #f2dfb5;
        background: #fff9ea;
        color: #7b6530;
        padding: 11px 13px;
        border-radius: 12px;
        font-size: 12px;
        line-height: 1.8;
    }

    .access-v2-page .access-danger-note {
        border: 1px solid #f0c9ce;
        background: #fff5f6;
        color: #8a3b45;
        padding: 11px 13px;
        border-radius: 12px;
        font-size: 12px;
        line-height: 1.8;
    }

    .access-v2-page .access-action-btn.disabled-action {
        color: #b9c1cc !important;
        cursor: not-allowed;
        background: #f5f6f8;
    }

    @media (max-width: 575.98px) {
        .access-v2-page .access-derived-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 991.98px) {
        .access-v2-page .access-hero {
            padding: 18px;
        }

        .access-v2-page .access-system-switch {
            width: 100%;
        }

        .access-v2-page .access-system-switch a {
            flex: 1 1 auto;
            text-align: center;
        }
    }
</style>

<div class="content-page access-v2-page">
    <div class="content">
        <div class="container-fluid">

            <div class="access-hero mt-3 mb-3">
                <div class="row align-items-center g-3">
                    <div class="col-xl-7">
                        <div class="d-flex align-items-start gap-3">
                            <div class="access-title-icon">
                                <i class="ri-shield-keyhole-line"></i>
                            </div>
                            <div>
                                <h3 class="mb-1"><?php echo _lang['access_role_management']; ?></h3>
                                <p class="access-subtitle"><?php echo _lang['access_role_management_note']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5 text-xl-end">
                        <div class="access-system-switch" role="navigation" aria-label="<?php echo $accessEsc(_lang['access_system']); ?>">
                            <a href="./user_access?system=all" class="<?php echo $accessSystemFilter === 'all' ? 'active' : ''; ?>">
                                <i class="ri-layout-grid-line me-1"></i><?php echo _lang['access_system_all']; ?>
                            </a>
                            <a href="./user_access?system=ipanel" class="<?php echo $accessSystemFilter === 'ipanel' ? 'active' : ''; ?>">
                                <i class="ri-shield-user-line me-1"></i><?php echo _lang['access_system_ipanel']; ?>
                            </a>
                            <a href="./user_access?system=iweb" class="<?php echo $accessSystemFilter === 'iweb' ? 'active' : ''; ?>">
                                <i class="ri-global-line me-1"></i><?php echo _lang['access_system_iweb']; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="access-nav-card mb-3">
                <a href="./user_access" class="active"><i class="ri-user-settings-line"></i><?php echo _lang['role']; ?></a>
                <a href="./user_stracture"><i class="ri-node-tree"></i><?php echo _lang['user_stracture']; ?></a>
                <a href="./user_operation"><i class="ri-settings-3-line"></i><?php echo _lang['user_operation']; ?></a>
                <a href="./user_view"><i class="ri-eye-line"></i><?php echo _lang['user_view']; ?></a>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-6 col-xl-3">
                    <div class="access-stat">
                        <div class="access-stat-icon"><i class="ri-shield-user-line"></i></div>
                        <div class="access-stat-value"><?php echo number_format((int) $accessStats['total_roles']); ?></div>
                        <div class="access-stat-label"><?php echo _lang['access_total_roles']; ?></div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="access-stat">
                        <div class="access-stat-icon"><i class="ri-computer-line"></i></div>
                        <div class="access-stat-value"><?php echo number_format((int) $accessStats['ipanel_roles']); ?></div>
                        <div class="access-stat-label"><?php echo _lang['access_ipanel_roles']; ?></div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="access-stat">
                        <div class="access-stat-icon"><i class="ri-global-line"></i></div>
                        <div class="access-stat-value"><?php echo number_format((int) $accessStats['iweb_roles']); ?></div>
                        <div class="access-stat-label"><?php echo _lang['access_iweb_roles']; ?></div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="access-stat">
                        <div class="access-stat-icon"><i class="ri-user-unfollow-line"></i></div>
                        <div class="access-stat-value"><?php echo number_format((int) $accessStats['unassigned_roles']); ?></div>
                        <div class="access-stat-label"><?php echo _lang['access_unassigned_roles']; ?></div>
                    </div>
                </div>
            </div>

            <div class="card access-main-card mb-4">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h4 class="mb-1"><i class="ri-list-check-2 me-1 text-primary"></i><?php echo _lang['access_role_list']; ?></h4>
                            <div class="text-muted small"><?php echo _lang['access_system_usage_note']; ?></div>
                        </div>

                        <?php if ($canAddAccessRole) { ?>
                            <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="ri-add-line me-1"></i><?php echo _lang['add']; ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <div class="card-body">
                    <div id="accessFeedback" class="alert d-none mb-3" role="alert"></div>
                    <div class="access-note mb-3">
                        <i class="ri-information-line me-1"></i>
                        <?php echo _lang['access_compatibility_note']; ?>
                    </div>

                    <div class="table-responsive">
                        <table id="scroll-vertical-datatable" class="table access-table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo _lang['role']; ?></th>
                                    <th><?php echo _lang['access_system']; ?></th>
                                    <th><?php echo _lang['access_parent_role']; ?></th>
                                    <th><?php echo _lang['company']; ?> / <?php echo _lang['unit']; ?></th>
                                    <th><?php echo _lang['access_usage']; ?></th>
                                    <th><?php echo _lang['last_updated_date']; ?></th>
                                    <th><?php echo _lang['actions']; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($accessRoles as $rbacDetails) {
                                $systemBadge = $accessSystemBadge($rbacDetails['system_scope']);
                            ?>
                                <tr>
                                    <td><?php echo (int) $rbacDetails['id']; ?></td>
                                    <td>
                                        <div class="access-role-name"><?php echo $accessEsc($rbacDetails['rbac_name']); ?></div>
                                        <div class="access-role-meta">
                                            <?php echo $accessEsc($rbacDetails['activity_name'] ?: '-'); ?>
                                            <?php if (!empty($rbacDetails['tag_name'])) { ?>
                                                <span class="mx-1">•</span><?php echo $accessEsc($rbacDetails['tag_name']); ?>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="access-scope-badge bg-<?php echo $systemBadge['class']; ?>-light text-<?php echo $systemBadge['class']; ?>">
                                            <i class="<?php echo $systemBadge['icon']; ?>"></i>
                                            <?php echo _lang[$systemBadge['key']]; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?php echo $accessEsc($rbacDetails['manager_name']); ?></div>
                                        <?php if ((int) $rbacDetails['child_role_count'] > 0) { ?>
                                            <div class="small text-muted">
                                                <?php echo number_format((int) $rbacDetails['child_role_count']); ?>
                                                <?php echo _lang['access_child_roles']; ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                    <td class="access-org-cell">
                                        <div class="fw-semibold"><?php echo $accessEsc($rbacDetails['company_name'] ?: '-'); ?></div>
                                        <div class="small text-muted"><i class="ri-building-4-line me-1"></i><?php echo $accessEsc($rbacDetails['unit_name'] ?: '-'); ?></div>
                                    </td>
                                    <td>
                                        <span class="access-member-chip" title="<?php echo $accessEsc(_lang['admins']); ?>">
                                            <i class="ri-admin-line text-primary"></i>
                                            <?php echo number_format((int) $rbacDetails['ipanel_member_count']); ?>
                                        </span>
                                        <span class="access-member-chip" title="<?php echo $accessEsc(_lang['users']); ?>">
                                            <i class="ri-user-line text-success"></i>
                                            <?php echo number_format((int) $rbacDetails['iweb_member_count']); ?>
                                        </span>
                                        <?php if ((int) $rbacDetails['dependency_count'] > 0) { ?>
                                            <span class="access-member-chip" title="<?php echo $accessEsc($accessLang('access_dependencies')); ?>">
                                                <i class="ri-links-line text-warning"></i>
                                                <?php echo number_format((int) $rbacDetails['dependency_count']); ?>
                                            </span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php
                                        $dateConverter = new DateConverter($rbacDetails['last_updated_date'], $config->getNowLanguage('a'));
                                        echo $accessEsc($dateConverter->convertToShamsi());
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($canEditAccessRole) { ?>
                                            <button type="button" class="access-action-btn access-role-edit-btn text-primary border-0"
                                                    data-id="<?php echo (int) $rbacDetails['id']; ?>"
                                                    title="<?php echo $accessEsc(_lang['edit_item']); ?>">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                        <?php } ?>

                                        <?php if ($canDeleteAccessRole) { ?>
                                            <?php if (!(bool) $rbacDetails['is_protected'] && (int) $rbacDetails['dependency_count'] === 0) { ?>
                                                <button type="button" class="access-action-btn access-role-delete-btn text-danger border-0"
                                                        data-id="<?php echo (int) $rbacDetails['id']; ?>"
                                                        data-name="<?php echo $accessEsc($rbacDetails['rbac_name']); ?>"
                                                        title="<?php echo $accessEsc(_lang['delete_item']); ?>">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            <?php } else { ?>
                                                <span class="access-action-btn disabled-action"
                                                      title="<?php echo $accessEsc((bool) $rbacDetails['is_protected'] ? $accessLang('access_protected_role_note') : sprintf($accessLang('access_delete_dependency_lock'), (int) $rbacDetails['dependency_count'])); ?>">
                                                    <i class="ri-lock-line"></i>
                                                </span>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>

                        <?php if (empty($accessRoles)) { ?>
                            <div class="access-empty">
                                <i class="ri-inbox-2-line"></i>
                                <?php echo _lang['access_no_role_found']; ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <?php if ($canAddAccessRole) { ?>
            <div class="modal fade access-modal" id="addModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title"><i class="ri-add-circle-line text-primary me-1"></i><?php echo $accessLang('access_add_role'); ?></h5>
                                <div class="small text-muted mt-1"><?php echo $accessLang('access_add_role_note'); ?></div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $accessEsc(_lang['close']); ?>"></button>
                        </div>
                        <div class="modal-body">
                            <form id="accessAddForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="accessAddUnit" class="form-label"><?php echo _lang['unit']; ?></label>
                                        <select class="form-select" id="accessAddUnit" required>
                                            <option value=""><?php echo $accessLang('access_select_unit'); ?></option>
                                            <?php $lastCompany = null; foreach ($unitOptions as $unit) {
                                                if ($lastCompany !== $unit['company_name']) {
                                                    if ($lastCompany !== null) { echo '</optgroup>'; }
                                                    echo '<optgroup label="' . $accessEsc($unit['company_name']) . '">';
                                                    $lastCompany = $unit['company_name'];
                                                }
                                            ?>
                                                <option value="<?php echo (int) $unit['id']; ?>"
                                                        data-company="<?php echo (int) $unit['company_id']; ?>"
                                                        data-company-name="<?php echo $accessEsc($unit['company_name']); ?>"
                                                        data-activity="<?php echo (int) ($unit['activity_id'] ?? 0); ?>"
                                                        data-activity-name="<?php echo $accessEsc($unit['activity_name'] ?? ''); ?>">
                                                    <?php echo $accessEsc($unit['unit_name']); ?>
                                                </option>
                                            <?php } if ($lastCompany !== null) { echo '</optgroup>'; } ?>
                                        </select>
                                        <div class="access-derived-grid">
                                            <div class="access-derived-item">
                                                <span class="access-derived-label"><?php echo $accessLang('access_company_derived'); ?></span>
                                                <div class="access-derived-value" id="accessAddCompanyDerived">—</div>
                                            </div>
                                            <div class="access-derived-item">
                                                <span class="access-derived-label"><?php echo $accessLang('access_activity_derived'); ?></span>
                                                <div class="access-derived-value" id="accessAddActivityDerived">—</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="accessAddParent" class="form-label"><?php echo $accessLang('access_parent_role'); ?></label>
                                        <select class="form-select" id="accessAddParent">
                                            <option value="0"><?php echo $accessLang('access_root_role'); ?></option>
                                            <?php foreach ($allRoles as $roleOption) { ?>
                                                <option value="<?php echo (int) $roleOption['id']; ?>"
                                                        data-company="<?php echo (int) ($roleOption['company_id'] ?? 0); ?>"
                                                        data-global="<?php echo (int) ($roleOption['all_rbac'] ?? 0); ?>">
                                                    <?php echo $accessEsc($roleOption['rbac_name']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <div class="small text-muted mt-2"><?php echo $accessLang('access_parent_scope_note'); ?></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="accessAddName" class="form-label"><?php echo _lang['name']; ?></label>
                                        <input type="text" class="form-control" id="accessAddName" maxlength="255" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="accessAddTag" class="form-label"><?php echo _lang['tag']; ?> <span class="text-muted small"><?php echo $accessLang('access_optional'); ?></span></label>
                                        <select class="form-select" id="accessAddTag">
                                            <option value="0"><?php echo $accessLang('access_no_tag'); ?></option>
                                            <?php foreach ($tags as $tag) { ?>
                                                <option value="<?php echo (int) $tag['id']; ?>"><?php echo $accessEsc($tag['tag_name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label for="accessAddDescription" class="form-label"><?php echo _lang['description']; ?></label>
                                        <textarea class="form-control" id="accessAddDescription" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="access-note mt-3">
                                    <i class="ri-information-line me-1"></i><?php echo $accessLang('access_derived_structure_note'); ?>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button>
                                    <button type="submit" class="btn btn-primary"><i class="ri-save-line me-1"></i><?php echo _lang['add']; ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if ($canEditAccessRole) { ?>
            <div class="modal fade access-modal" id="editModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title"><i class="ri-edit-line text-primary me-1"></i><?php echo _lang['edit_item']; ?></h5>
                                <div class="small text-muted mt-1"><?php echo $accessLang('access_edit_role_note'); ?></div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $accessEsc(_lang['close']); ?>"></button>
                        </div>
                        <div class="modal-body">
                            <form id="accessEditForm">
                                <input type="hidden" id="accessEditId">

                                <div id="accessProtectedRoleNote" class="access-danger-note d-none mb-3">
                                    <i class="ri-shield-keyhole-line me-1"></i><?php echo $accessLang('access_protected_role_note'); ?>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="accessEditUnit" class="form-label"><?php echo _lang['unit']; ?></label>
                                        <select class="form-select" id="accessEditUnit" required>
                                            <option value=""><?php echo $accessLang('access_select_unit'); ?></option>
                                            <?php $lastCompany = null; foreach ($unitOptions as $unit) {
                                                if ($lastCompany !== $unit['company_name']) {
                                                    if ($lastCompany !== null) { echo '</optgroup>'; }
                                                    echo '<optgroup label="' . $accessEsc($unit['company_name']) . '">';
                                                    $lastCompany = $unit['company_name'];
                                                }
                                            ?>
                                                <option value="<?php echo (int) $unit['id']; ?>"
                                                        data-company="<?php echo (int) $unit['company_id']; ?>"
                                                        data-company-name="<?php echo $accessEsc($unit['company_name']); ?>"
                                                        data-activity="<?php echo (int) ($unit['activity_id'] ?? 0); ?>"
                                                        data-activity-name="<?php echo $accessEsc($unit['activity_name'] ?? ''); ?>">
                                                    <?php echo $accessEsc($unit['unit_name']); ?>
                                                </option>
                                            <?php } if ($lastCompany !== null) { echo '</optgroup>'; } ?>
                                        </select>
                                        <div id="accessUnitLockedNote" class="access-lock-note d-none mt-2">
                                            <i class="ri-lock-line me-1"></i><?php echo $accessLang('access_unit_change_locked_note'); ?>
                                        </div>
                                        <div class="access-derived-grid">
                                            <div class="access-derived-item">
                                                <span class="access-derived-label"><?php echo $accessLang('access_company_derived'); ?></span>
                                                <div class="access-derived-value" id="accessEditCompanyDerived">—</div>
                                            </div>
                                            <div class="access-derived-item">
                                                <span class="access-derived-label"><?php echo $accessLang('access_activity_derived'); ?></span>
                                                <div class="access-derived-value" id="accessEditActivityDerived">—</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="accessEditParent" class="form-label"><?php echo $accessLang('access_parent_role'); ?></label>
                                        <select class="form-select" id="accessEditParent">
                                            <option value="0"><?php echo $accessLang('access_root_role'); ?></option>
                                            <?php foreach ($allRoles as $roleOption) { ?>
                                                <option value="<?php echo (int) $roleOption['id']; ?>"
                                                        data-company="<?php echo (int) ($roleOption['company_id'] ?? 0); ?>"
                                                        data-global="<?php echo (int) ($roleOption['all_rbac'] ?? 0); ?>">
                                                    <?php echo $accessEsc($roleOption['rbac_name']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <div class="small text-muted mt-2"><?php echo $accessLang('access_parent_scope_note'); ?></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="accessEditName" class="form-label"><?php echo _lang['name']; ?></label>
                                        <input type="text" class="form-control" id="accessEditName" maxlength="255" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="accessEditTag" class="form-label"><?php echo _lang['tag']; ?> <span class="text-muted small"><?php echo $accessLang('access_optional'); ?></span></label>
                                        <select class="form-select" id="accessEditTag">
                                            <option value="0"><?php echo $accessLang('access_no_tag'); ?></option>
                                            <?php foreach ($tags as $tag) { ?>
                                                <option value="<?php echo (int) $tag['id']; ?>"><?php echo $accessEsc($tag['tag_name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label for="accessEditDescription" class="form-label"><?php echo _lang['description']; ?></label>
                                        <textarea class="form-control" id="accessEditDescription" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="access-note mt-3">
                                    <i class="ri-information-line me-1"></i><?php echo $accessLang('access_edit_derivation_note'); ?>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button>
                                    <button type="submit" class="btn btn-primary"><i class="ri-save-line me-1"></i><?php echo _lang['save_changes']; ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if ($canDeleteAccessRole) { ?>
            <div class="modal fade access-modal" id="deleteModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="ri-alert-line text-danger me-1"></i><?php echo _lang['delete_item']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $accessEsc(_lang['close']); ?>"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="accessDeleteId">
                            <p class="mb-2"><?php echo _lang['delete_confirm']; ?></p>
                            <div class="fw-semibold mb-3" id="accessDeleteName"></div>
                            <div class="access-danger-note">
                                <i class="ri-information-line me-1"></i><?php echo $accessLang('access_safe_delete_note'); ?>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button>
                                <button type="button" class="btn btn-danger" id="accessDeleteConfirm"><?php echo _lang['delete']; ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const endpoint = '../icore/json/user_access_manage.php';
    const csrf = <?php echo json_encode($userAccessCsrf, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const roleData = <?php echo json_encode($accessRoleEditData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    const feedback = document.getElementById('accessFeedback');

    function showFeedback(message, ok) {
        if (!feedback) return;
        feedback.className = 'alert mb-3 ' + (ok ? 'alert-success' : 'alert-danger');
        feedback.textContent = message || '';
        feedback.classList.remove('d-none');
        window.scrollTo({ top: Math.max(0, feedback.offsetTop - 120), behavior: 'smooth' });
    }

    async function accessRequest(payload) {
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
            data = { status: 'error', message: <?php echo json_encode($accessLang('access_unexpected_error')); ?> };
        }

        if (!response.ok || data.status !== 'success') {
            throw new Error(data.message || <?php echo json_encode($accessLang('access_unexpected_error')); ?>);
        }
        return data;
    }

    function selectedUnitMeta(select) {
        if (!select || !select.value) return null;
        const option = select.options[select.selectedIndex];
        if (!option || !option.value) return null;
        return {
            unitId: parseInt(option.value || '0', 10),
            companyId: parseInt(option.dataset.company || '0', 10),
            companyName: option.dataset.companyName || '',
            activityId: parseInt(option.dataset.activity || '0', 10),
            activityName: option.dataset.activityName || ''
        };
    }

    function setDerived(prefix, meta, fallback) {
        const companyEl = document.getElementById(prefix + 'CompanyDerived');
        const activityEl = document.getElementById(prefix + 'ActivityDerived');
        const companyName = meta?.companyName || fallback?.companyName || '—';
        const activityName = meta?.activityName || fallback?.activityName || '—';
        if (companyEl) companyEl.textContent = companyName || '—';
        if (activityEl) activityEl.textContent = activityName || '—';
    }

    function filterParents(parentSelect, companyId, currentRoleId, currentParentId) {
        if (!parentSelect) return;
        Array.from(parentSelect.options).forEach(option => {
            if (!option.value || option.value === '0') {
                option.hidden = false;
                option.disabled = false;
                return;
            }
            const roleId = parseInt(option.value || '0', 10);
            const optionCompany = parseInt(option.dataset.company || '0', 10);
            const globalRole = option.dataset.global === '1';
            const isCurrentParent = roleId === parseInt(currentParentId || '0', 10);
            const allowed = roleId !== parseInt(currentRoleId || '0', 10) && (globalRole || optionCompany === parseInt(companyId || '0', 10) || isCurrentParent);
            option.hidden = !allowed;
            option.disabled = !allowed;
        });

        const selected = parentSelect.options[parentSelect.selectedIndex];
        if (selected && selected.disabled) {
            parentSelect.value = '0';
        }
    }

    const addUnit = document.getElementById('accessAddUnit');
    const addParent = document.getElementById('accessAddParent');
    if (addUnit) {
        addUnit.addEventListener('change', function () {
            const meta = selectedUnitMeta(this);
            setDerived('accessAdd', meta, null);
            filterParents(addParent, meta?.companyId || 0, 0, addParent?.value || 0);
        });
    }

    const addForm = document.getElementById('accessAddForm');
    if (addForm) {
        addForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submit = this.querySelector('button[type="submit"]');
            submit.disabled = true;
            try {
                const data = await accessRequest({
                    action: 'add',
                    rbac_name: document.getElementById('accessAddName').value,
                    rbac_description: document.getElementById('accessAddDescription').value,
                    unit_id: document.getElementById('accessAddUnit').value,
                    manager_id: document.getElementById('accessAddParent').value,
                    tag_id: document.getElementById('accessAddTag').value
                });
                bootstrap.Modal.getInstance(document.getElementById('addModal'))?.hide();
                showFeedback(data.message, true);
                setTimeout(() => location.reload(), 500);
            } catch (err) {
                showFeedback(err.message, false);
            } finally {
                submit.disabled = false;
            }
        });
    }

    const editModalEl = document.getElementById('editModal');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    const editUnit = document.getElementById('accessEditUnit');
    const editParent = document.getElementById('accessEditParent');
    let activeEditRole = null;

    function refreshEditDerived() {
        const meta = selectedUnitMeta(editUnit);
        setDerived('accessEdit', meta, activeEditRole ? {
            companyName: activeEditRole.company_name || '',
            activityName: activeEditRole.activity_name || ''
        } : null);
        filterParents(editParent, meta?.companyId || activeEditRole?.company_id || 0, activeEditRole?.id || 0, activeEditRole?.manager_id || 0);
    }

    if (editUnit) {
        editUnit.addEventListener('change', refreshEditDerived);
    }

    document.querySelectorAll('.access-role-edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = parseInt(this.dataset.id || '0', 10);
            activeEditRole = roleData[id] || roleData[String(id)] || null;
            if (!activeEditRole) return;

            document.getElementById('accessEditId').value = activeEditRole.id || '';
            document.getElementById('accessEditName').value = activeEditRole.rbac_name || '';
            document.getElementById('accessEditDescription').value = activeEditRole.rbac_description || '';
            document.getElementById('accessEditTag').value = String(activeEditRole.tag_id || 0);
            document.getElementById('accessEditParent').value = String(activeEditRole.manager_id || 0);
            document.getElementById('accessEditUnit').value = activeEditRole.unit_id ? String(activeEditRole.unit_id) : '';

            const protectedRole = parseInt(activeEditRole.all_rbac || 0, 10) === 1;
            const unitLocked = protectedRole || !!activeEditRole.unit_change_locked;
            document.getElementById('accessProtectedRoleNote')?.classList.toggle('d-none', !protectedRole);
            document.getElementById('accessUnitLockedNote')?.classList.toggle('d-none', !unitLocked || protectedRole);

            document.getElementById('accessEditUnit').disabled = unitLocked;
            document.getElementById('accessEditParent').disabled = protectedRole;
            document.getElementById('accessEditTag').disabled = protectedRole;

            refreshEditDerived();
            editModal?.show();
        });
    });

    const editForm = document.getElementById('accessEditForm');
    if (editForm) {
        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!activeEditRole) return;
            const submit = this.querySelector('button[type="submit"]');
            submit.disabled = true;
            try {
                const protectedRole = parseInt(activeEditRole.all_rbac || 0, 10) === 1;
                const data = await accessRequest({
                    action: 'edit',
                    id: document.getElementById('accessEditId').value,
                    rbac_name: document.getElementById('accessEditName').value,
                    rbac_description: document.getElementById('accessEditDescription').value,
                    unit_id: protectedRole ? (activeEditRole.unit_id || 0) : document.getElementById('accessEditUnit').value,
                    manager_id: protectedRole ? (activeEditRole.manager_id || 0) : document.getElementById('accessEditParent').value,
                    tag_id: protectedRole ? (activeEditRole.tag_id || 0) : document.getElementById('accessEditTag').value
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

    const deleteModalEl = document.getElementById('deleteModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    document.querySelectorAll('.access-role-delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('accessDeleteId').value = this.dataset.id || '';
            document.getElementById('accessDeleteName').textContent = this.dataset.name || '';
            deleteModal?.show();
        });
    });

    const deleteConfirm = document.getElementById('accessDeleteConfirm');
    if (deleteConfirm) {
        deleteConfirm.addEventListener('click', async function () {
            this.disabled = true;
            try {
                const data = await accessRequest({
                    action: 'delete',
                    id: document.getElementById('accessDeleteId').value
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
