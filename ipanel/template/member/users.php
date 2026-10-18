<?php
///ipanel/template/member/users.php

$usersEsc = static function ($value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$usersRoleLabel = static function ($role): string {
    $key = 'customer_users_role_' . strtolower((string) $role);
    return _lang[$key] ?? (string) $role;
};

$usersRoleClass = static function ($role): string {
    return match (strtoupper((string) $role)) {
        'ADMIN' => 'primary',
        'MANAGER' => 'warning',
        'EXPERT' => 'success',
        default => 'secondary',
    };
};
?>

<style>
    .customer-users-page {
        --cu-border: #e5eaf2;
        --cu-text: #172033;
        --cu-muted: #7a8699;
        --cu-soft: #f6f8fc;
        --cu-primary: #356df3;
    }

    .customer-users-hero {
        border: 1px solid #e2e8f5;
        border-radius: 20px;
        padding: 22px 24px;
        background:
            radial-gradient(circle at top left, rgba(53,109,243,.11), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #f6f9ff 100%);
        box-shadow: 0 12px 35px rgba(37, 54, 89, .06);
    }

    .customer-users-hero h3 {
        color: var(--cu-text);
        font-weight: 800;
        margin-bottom: 6px;
    }

    .customer-users-hero p { color: var(--cu-muted); margin: 0; }

    .customer-users-icon {
        width: 48px;
        height: 48px;
        border-radius: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #edf3ff;
        color: var(--cu-primary);
        font-size: 23px;
    }

    .company-user-card {
        display: block;
        height: 100%;
        border: 1px solid var(--cu-border);
        border-radius: 18px;
        background: #fff;
        padding: 18px;
        color: inherit;
        text-decoration: none;
        box-shadow: 0 8px 24px rgba(36, 50, 81, .045);
        transition: .18s ease;
    }

    .company-user-card:hover {
        transform: translateY(-3px);
        border-color: #cbd8fb;
        box-shadow: 0 14px 32px rgba(53, 109, 243, .09);
        color: inherit;
    }

    .company-user-card .company-title { font-size: 16px; font-weight: 800; color: var(--cu-text); }
    .company-user-card .company-meta { color: var(--cu-muted); font-size: 12px; }

    .company-user-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-top: 16px;
    }

    .company-user-stat {
        background: var(--cu-soft);
        border-radius: 12px;
        padding: 10px 7px;
        text-align: center;
    }

    .company-user-stat strong { display: block; font-size: 18px; color: var(--cu-text); }
    .company-user-stat span { color: var(--cu-muted); font-size: 10px; }

    .users-summary-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 14px;
    }

    .users-summary-card {
        border: 1px solid var(--cu-border);
        border-radius: 16px;
        padding: 16px;
        background: #fff;
        box-shadow: 0 7px 20px rgba(28, 41, 67, .04);
    }

    .users-summary-card .summary-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef3ff;
        color: var(--cu-primary);
        font-size: 18px;
        margin-bottom: 12px;
    }

    .users-summary-card strong { display: block; font-size: 22px; color: var(--cu-text); }
    .users-summary-card span { color: var(--cu-muted); font-size: 11px; }

    .users-panel {
        border: 1px solid var(--cu-border);
        border-radius: 18px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(29, 42, 69, .04);
    }

    .users-panel-header {
        padding: 16px 18px;
        border-bottom: 1px solid var(--cu-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .users-search-wrap {
        position: relative;
        width: min(340px, 100%);
    }

    .users-search-wrap i {
        position: absolute;
        right: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #95a0b4;
    }

    .users-search-wrap input {
        padding-right: 38px;
        border-radius: 12px;
        border-color: #dbe2ed;
    }

    .org-chart-wrap {
        padding: 22px;
        overflow-x: auto;
        background: linear-gradient(180deg, #fff 0%, #fafcff 100%);
    }

    .org-company-node {
        width: min(520px, 100%);
        margin: 0 auto;
        border-radius: 18px;
        padding: 18px 20px;
        background: linear-gradient(135deg, #1f3b7b 0%, #356df3 100%);
        color: #fff;
        text-align: center;
        position: relative;
    }

    .org-company-node .admins-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
        margin-top: 14px;
    }

    .org-user-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border-radius: 999px;
        padding: 7px 10px;
        font-size: 11px;
        background: #f7f9fd;
        border: 1px solid #e3e8f1;
        color: #354158;
        max-width: 100%;
    }

    .org-company-node .org-user-pill {
        background: rgba(255,255,255,.14);
        border-color: rgba(255,255,255,.2);
        color: #fff;
    }

    .org-user-pill.inactive { opacity: .55; }
    .org-user-pill .scope-badge { font-size: 9px; padding: 2px 6px; border-radius: 999px; background: rgba(53,109,243,.1); color: #356df3; }
    .org-company-node .org-user-pill .scope-badge { background: rgba(255,255,255,.16); color: #fff; }

    .org-line-v {
        width: 2px;
        height: 28px;
        background: #d9e2f5;
        margin: 0 auto;
    }

    .org-section-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
    }

    .org-section-card {
        border: 1px solid var(--cu-border);
        border-radius: 16px;
        background: #fff;
        padding: 16px;
        min-width: 0;
    }

    .org-section-card.inactive { background: #fbfbfc; opacity: .72; }
    .org-section-title { font-weight: 800; color: var(--cu-text); }
    .org-section-subtitle { font-size: 10px; color: var(--cu-muted); }

    .org-role-group {
        border-top: 1px dashed #e3e8f0;
        margin-top: 12px;
        padding-top: 12px;
    }

    .org-role-label {
        font-size: 10px;
        color: var(--cu-muted);
        margin-bottom: 8px;
    }

    .migration-pools-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 22px;
    }

    .migration-pool-card {
        border: 1px solid var(--cu-border);
        border-radius: 16px;
        background: #fff;
        padding: 15px;
    }

    .migration-pool-card.full-width { grid-column: 1 / -1; }
    .migration-pool-title { font-weight: 800; color: var(--cu-text); }
    .migration-pool-note { color: var(--cu-muted); font-size: 10px; margin-top: 3px; }

    .migration-user-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 0;
        border-top: 1px dashed #e7ebf2;
    }
    .migration-user-row:first-of-type { margin-top: 10px; }
    .migration-user-main { min-width: 0; }
    .migration-user-main strong { display: block; font-size: 12px; color: var(--cu-text); }
    .migration-user-main small { display: block; color: var(--cu-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .migration-assign { display: flex; gap: 6px; align-items: center; min-width: 250px; }
    .migration-assign .form-select { min-width: 165px; border-radius: 10px; }

    .user-role-badge { border-radius: 999px; font-weight: 600; }

    .users-table th { white-space: nowrap; font-size: 11px; color: #667188; background: #fbfcfe; }
    .users-table td { vertical-align: middle; font-size: 12px; }
    .users-table .user-name { color: var(--cu-text); font-weight: 700; }
    .users-table .user-sub { color: var(--cu-muted); font-size: 10px; }

    .portal-warning {
        border: 1px solid #f1d7a3;
        border-radius: 14px;
        background: #fff9eb;
        color: #805e17;
        padding: 13px 15px;
    }

    .user-form-modal .modal-content { border: 0; border-radius: 18px; overflow: hidden; }
    .user-form-modal .modal-header { border-bottom: 1px solid #edf0f5; background: #fafbfe; }
    .user-form-modal .form-label { font-size: 11px; color: #5f6b80; }
    .user-form-modal .form-control, .user-form-modal .form-select { border-radius: 11px; }

    @media (max-width: 1199px) {
        .users-summary-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 767px) {
        .users-summary-grid { grid-template-columns: repeat(2, 1fr); }
        .company-user-stats { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<div class="content-page customer-users-page">
    <div class="content">
        <div class="container-fluid">

            <div class="customer-users-hero mt-3 mb-3">
                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div class="d-flex align-items-center gap-3">
                        <span class="customer-users-icon"><i class="ri-team-line"></i></span>
                        <div>
                            <h3><?php echo _lang['customer_users_management']; ?></h3>
                            <p><?php echo _lang['customer_users_management_note']; ?></p>
                        </div>
                    </div>

                    <?php if ($selectedCompany): ?>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="./users" class="btn btn-light border rounded-3">
                                <i class="ri-building-2-line me-1"></i>
                                <?php echo _lang['customer_users_all_companies']; ?>
                            </a>
                            <?php if ($canAddUser && $portalManagementReady): ?>
                                <button class="btn btn-primary rounded-3" type="button" data-bs-toggle="modal" data-bs-target="#customerUserModal" id="customerUserAddBtn">
                                    <i class="ri-user-add-line me-1"></i><?php echo _lang['add']; ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!$portalManagementReady): ?>
                <div class="portal-warning mb-3">
                    <i class="ri-alert-line me-1"></i>
                    <?php echo _lang['customer_users_portal_tables_missing']; ?>
                </div>
            <?php endif; ?>

            <?php if (!$selectedCompany): ?>
                <div class="users-panel">
                    <div class="users-panel-header">
                        <div>
                            <strong><?php echo _lang['customer_users_company_pages']; ?></strong>
                            <div class="text-muted small mt-1"><?php echo _lang['customer_users_company_pages_note']; ?></div>
                        </div>
                        <div class="users-search-wrap">
                            <i class="ri-search-line"></i>
                            <input type="text" id="companyUserSearch" class="form-control" placeholder="<?php echo $usersEsc(_lang['customer_users_company_search']); ?>">
                        </div>
                    </div>
                    <div class="p-3">
                        <div class="row g-3" id="companyUserGrid">
                            <?php foreach ($companies as $company):
                                $cid = (int) $company['id'];
                                $roleStats = $companyRoleStats[$cid] ?? ['ADMIN' => 0, 'MANAGER' => 0, 'EXPERT' => 0];
                                ?>
                                <div class="col-xl-4 col-lg-6 company-user-item"
                                     data-search="<?php echo $usersEsc(mb_strtolower(($company['company_name'] ?? '') . ' ' . ($company['activity_name'] ?? '') . ' ' . ($company['company_phone'] ?? ''))); ?>">
                                    <a class="company-user-card" href="./users?company_id=<?php echo $cid; ?>">
                                        <div class="d-flex align-items-start justify-content-between gap-2">
                                            <div>
                                                <div class="company-title"><?php echo $usersEsc($company['company_name']); ?></div>
                                                <div class="company-meta mt-1">
                                                    <?php echo $usersEsc($company['activity_name'] ?: '-'); ?>
                                                    <?php if (!empty($company['company_phone'])): ?>
                                                        <span class="mx-1">•</span><?php echo $usersEsc($company['company_phone']); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <span class="customer-users-icon" style="width:38px;height:38px;font-size:17px"><i class="ri-arrow-left-up-line"></i></span>
                                        </div>
                                        <div class="company-user-stats">
                                            <div class="company-user-stat"><strong><?php echo (int) $company['total_users']; ?></strong><span><?php echo _lang['customer_users_total']; ?></span></div>
                                            <div class="company-user-stat"><strong><?php echo (int) $roleStats['ADMIN']; ?></strong><span><?php echo _lang['customer_users_role_admin']; ?></span></div>
                                            <div class="company-user-stat"><strong><?php echo (int) $roleStats['MANAGER']; ?></strong><span><?php echo _lang['customer_users_role_manager']; ?></span></div>
                                            <div class="company-user-stat"><strong><?php echo (int) $roleStats['EXPERT']; ?></strong><span><?php echo _lang['customer_users_role_expert']; ?></span></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-3 small text-muted">
                                            <span><i class="ri-node-tree me-1"></i><?php echo (int) $company['section_count']; ?> <?php echo _lang['customer_users_sections']; ?></span>
                                            <span><i class="ri-checkbox-circle-line text-success me-1"></i><?php echo (int) $company['active_users']; ?> <?php echo _lang['active']; ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div id="companyUserNoResult" class="text-center text-muted py-5 d-none"><?php echo _lang['customer_users_no_company']; ?></div>
                    </div>
                </div>
            <?php else: ?>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h4 class="mb-1 fw-bold"><?php echo $usersEsc($selectedCompany['company_name']); ?></h4>
                        <div class="text-muted small">
                            <?php echo $usersEsc($selectedCompany['activity_name'] ?: '-'); ?>
                            <?php if (!empty($selectedCompany['company_phone'])): ?>
                                <span class="mx-1">•</span><?php echo $usersEsc($selectedCompany['company_phone']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="users-summary-grid mb-3">
                    <div class="users-summary-card"><span class="summary-icon"><i class="ri-group-line"></i></span><strong><?php echo $companyStats['total']; ?></strong><span><?php echo _lang['customer_users_total']; ?></span></div>
                    <div class="users-summary-card"><span class="summary-icon"><i class="ri-shield-user-line"></i></span><strong><?php echo $companyStats['admins']; ?></strong><span><?php echo _lang['customer_users_role_admin']; ?></span></div>
                    <div class="users-summary-card"><span class="summary-icon"><i class="ri-user-star-line"></i></span><strong><?php echo $companyStats['managers']; ?></strong><span><?php echo _lang['customer_users_role_manager']; ?></span></div>
                    <div class="users-summary-card"><span class="summary-icon"><i class="ri-user-settings-line"></i></span><strong><?php echo $companyStats['experts']; ?></strong><span><?php echo _lang['customer_users_role_expert']; ?></span></div>
                    <div class="users-summary-card"><span class="summary-icon"><i class="ri-user-unfollow-line"></i></span><strong><?php echo $companyStats['unassigned']; ?></strong><span><?php echo _lang['customer_users_structure_pending']; ?></span></div>
                </div>

                <div class="users-panel mb-3">
                    <div class="users-panel-header">
                        <div>
                            <strong><?php echo _lang['customer_users_org_chart']; ?></strong>
                            <div class="text-muted small mt-1"><?php echo _lang['customer_users_org_chart_note']; ?></div>
                        </div>
                    </div>
                    <div class="org-chart-wrap">
                        <div class="org-company-node">
                            <div class="fw-bold fs-5"><?php echo $usersEsc($selectedCompany['company_name']); ?></div>
                            <div class="small opacity-75 mt-1"><?php echo _lang['customer_users_company_admins']; ?></div>
                            <div class="admins-row">
                                <?php if ($companyAdmins): ?>
                                    <?php foreach ($companyAdmins as $admin): ?>
                                        <span class="org-user-pill <?php echo $admin['status'] === 'Active' ? '' : 'inactive'; ?>">
                                            <i class="ri-shield-user-line"></i><?php echo $usersEsc($admin['name']); ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="small opacity-75"><?php echo _lang['customer_users_no_admin']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="org-line-v"></div>

                        <div class="org-section-grid">
                            <?php foreach ($companyChartSections as $sectionData):
                                $section = $sectionData['section'];
                                ?>
                                <div class="org-section-card <?php echo (int) $section['is_active'] === 1 ? '' : 'inactive'; ?>">
                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                        <div>
                                            <div class="org-section-title"><i class="ri-node-tree me-1"></i><?php echo $usersEsc($section['name']); ?></div>
                                            <div class="org-section-subtitle"><?php echo (int) $section['is_active'] === 1 ? _lang['active'] : _lang['inactive']; ?></div>
                                        </div>
                                        <span class="badge bg-light text-dark border"><?php echo count($sectionData['managers']) + count($sectionData['experts']); ?></span>
                                    </div>

                                    <div class="org-role-group">
                                        <div class="org-role-label"><?php echo _lang['customer_users_role_manager']; ?></div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php if ($sectionData['managers']): ?>
                                                <?php foreach ($sectionData['managers'] as $manager): ?>
                                                    <span class="org-user-pill <?php echo $manager['status'] === 'Active' ? '' : 'inactive'; ?>">
                                                        <i class="ri-user-star-line"></i><?php echo $usersEsc($manager['name']); ?>
                                                        <?php if (!empty($manager['send_all'])): ?><span class="scope-badge"><?php echo _lang['customer_users_send_all']; ?></span><?php endif; ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted small"><?php echo _lang['customer_users_no_manager']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="org-role-group">
                                        <div class="org-role-label"><?php echo _lang['customer_users_role_expert']; ?></div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php if ($sectionData['experts']): ?>
                                                <?php foreach ($sectionData['experts'] as $expert): ?>
                                                    <span class="org-user-pill <?php echo $expert['status'] === 'Active' ? '' : 'inactive'; ?>">
                                                        <i class="ri-user-settings-line"></i><?php echo $usersEsc($expert['name']); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted small"><?php echo _lang['customer_users_no_expert']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($companyUnassignedManagers || $companyUnassignedExperts || $companyLegacyUnclassified): ?>
                            <div class="migration-pools-grid">
                                <div class="migration-pool-card">
                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                        <div>
                                            <div class="migration-pool-title"><i class="ri-user-star-line text-warning me-1"></i><?php echo _lang['customer_users_waiting_managers']; ?></div>
                                            <div class="migration-pool-note"><?php echo _lang['customer_users_waiting_managers_note']; ?></div>
                                        </div>
                                        <span class="badge bg-light text-dark border"><?php echo count($companyUnassignedManagers); ?></span>
                                    </div>

                                    <?php if ($companyUnassignedManagers): ?>
                                        <?php foreach ($companyUnassignedManagers as $manager): ?>
                                            <div class="migration-user-row">
                                                <div class="migration-user-main">
                                                    <strong><?php echo $usersEsc($manager['name']); ?></strong>
                                                    <small><?php echo $usersEsc($manager['rbac_name'] ?: $manager['email']); ?></small>
                                                </div>
                                                <?php if ($canEditUser && $portalManagementReady && $manager['status'] === 'Active'): ?>
                                                    <div class="migration-assign">
                                                        <select class="form-select form-select-sm js-assign-section-select">
                                                            <option value="0"><?php echo _lang['customer_users_select_section']; ?></option>
                                                            <?php foreach ($companyManagerAssignableSections as $section): ?>
                                                                <option value="<?php echo (int) $section['id']; ?>"><?php echo $usersEsc($section['name']); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button type="button" class="btn btn-sm btn-outline-primary js-assign-section" data-id="<?php echo (int) $manager['id']; ?>" data-role="MANAGER" <?php echo empty($companyManagerAssignableSections) ? 'disabled' : ''; ?>>
                                                            <?php echo _lang['customer_users_assign']; ?>
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-muted small mt-3"><?php echo _lang['customer_users_waiting_managers_empty']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="migration-pool-card">
                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                        <div>
                                            <div class="migration-pool-title"><i class="ri-user-settings-line text-success me-1"></i><?php echo _lang['customer_users_waiting_experts']; ?></div>
                                            <div class="migration-pool-note"><?php echo _lang['customer_users_waiting_experts_note']; ?></div>
                                        </div>
                                        <span class="badge bg-light text-dark border"><?php echo count($companyUnassignedExperts); ?></span>
                                    </div>

                                    <?php if ($companyUnassignedExperts): ?>
                                        <?php foreach ($companyUnassignedExperts as $expert): ?>
                                            <div class="migration-user-row">
                                                <div class="migration-user-main">
                                                    <strong><?php echo $usersEsc($expert['name']); ?></strong>
                                                    <small><?php echo $usersEsc($expert['rbac_name'] ?: $expert['email']); ?></small>
                                                </div>
                                                <?php if ($canEditUser && $portalManagementReady && $expert['status'] === 'Active'): ?>
                                                    <div class="migration-assign">
                                                        <select class="form-select form-select-sm js-assign-section-select">
                                                            <option value="0"><?php echo _lang['customer_users_select_section']; ?></option>
                                                            <?php foreach ($companyExpertAssignableSections as $section): ?>
                                                                <option value="<?php echo (int) $section['id']; ?>"><?php echo $usersEsc($section['name']); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button type="button" class="btn btn-sm btn-outline-success js-assign-section" data-id="<?php echo (int) $expert['id']; ?>" data-role="EXPERT" <?php echo empty($companyExpertAssignableSections) ? 'disabled' : ''; ?>>
                                                            <?php echo _lang['customer_users_assign']; ?>
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-muted small mt-3"><?php echo _lang['customer_users_waiting_experts_empty']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($companyLegacyUnclassified): ?>
                                    <div class="migration-pool-card full-width">
                                        <div class="d-flex align-items-start justify-content-between gap-2">
                                            <div>
                                                <div class="migration-pool-title"><i class="ri-question-line text-secondary me-1"></i><?php echo _lang['customer_users_no_portal_role']; ?></div>
                                                <div class="migration-pool-note"><?php echo _lang['customer_users_no_portal_role_note']; ?></div>
                                            </div>
                                            <span class="badge bg-light text-dark border"><?php echo count($companyLegacyUnclassified); ?></span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 mt-3">
                                            <?php foreach ($companyLegacyUnclassified as $legacyUser): ?>
                                                <span class="org-user-pill <?php echo $legacyUser['status'] === 'Active' ? '' : 'inactive'; ?>">
                                                    <i class="ri-user-line"></i><?php echo $usersEsc($legacyUser['name']); ?>
                                                    <?php if (($legacyUser['migration_suggested_role'] ?? '') === 'MANAGER'): ?>
                                                        <span class="scope-badge"><?php echo _lang['customer_users_suggest_manager']; ?></span>
                                                    <?php elseif (($legacyUser['migration_suggested_role'] ?? '') === 'EXPERT'): ?>
                                                        <span class="scope-badge"><?php echo _lang['customer_users_suggest_expert']; ?></span>
                                                    <?php endif; ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="users-panel">
                    <div class="users-panel-header">
                        <div>
                            <strong><?php echo _lang['customer_users_list']; ?></strong>
                            <div class="text-muted small mt-1"><?php echo _lang['customer_users_list_note']; ?></div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                            <select id="userRoleFilter" class="form-select form-select-sm" style="width:auto;min-width:150px">
                                <option value="all"><?php echo _lang['view_all']; ?></option>
                                <option value="ADMIN"><?php echo _lang['customer_users_role_admin']; ?></option>
                                <option value="MANAGER"><?php echo _lang['customer_users_role_manager']; ?></option>
                                <option value="EXPERT"><?php echo _lang['customer_users_role_expert']; ?></option>
                                <option value="UNASSIGNED"><?php echo _lang['customer_users_no_portal_role']; ?></option>
                            </select>
                            <div class="users-search-wrap">
                                <i class="ri-search-line"></i>
                                <input type="text" id="userSearch" class="form-control form-control-sm" placeholder="<?php echo $usersEsc(_lang['customer_users_search']); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0 users-table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo _lang['name']; ?></th>
                                    <th><?php echo _lang['customer_users_portal_role']; ?></th>
                                    <th><?php echo _lang['customer_users_section']; ?></th>
                                    <th><?php echo _lang['unit']; ?></th>
                                    <th><?php echo _lang['access']; ?></th>
                                    <th><?php echo _lang['customer_users_send_scope']; ?></th>
                                    <th><?php echo _lang['status']; ?></th>
                                    <th class="text-end"><?php echo _lang['actions']; ?></th>
                                </tr>
                            </thead>
                            <tbody id="companyUsersTableBody">
                                <?php foreach ($companyUsers as $user):
                                    $role = strtoupper((string) ($user['portal_role'] ?? ''));
                                    $roleFilter = in_array($role, ['ADMIN','MANAGER','EXPERT'], true) && (int) ($user['profile_company_id'] ?? 0) === $selectedCompanyId ? $role : 'UNASSIGNED';
                                    $searchText = mb_strtolower(implode(' ', [
                                        $user['name'] ?? '', $user['email'] ?? '', $user['mobile'] ?? '', $user['unit_name'] ?? '',
                                        $user['rbac_name'] ?? '', $user['section_name'] ?? '', $role,
                                    ]));
                                    $editPayload = [
                                        'id' => (int) $user['id'],
                                        'name' => $user['name'],
                                        'email' => $user['email'],
                                        'mobile' => $user['mobile'],
                                        'unit_id' => (int) $user['unit_id'],
                                        'rbac_id' => (int) ($user['rbac_id'] ?? 0),
                                        'portal_role' => $roleFilter === 'UNASSIGNED' ? '' : $role,
                                        'section_id' => (int) ($user['section_id'] ?? 0),
                                    ];
                                    ?>
                                    <tr class="company-user-row" data-role="<?php echo $roleFilter; ?>" data-search="<?php echo $usersEsc($searchText); ?>">
                                        <td><?php echo (int) $user['id']; ?></td>
                                        <td>
                                            <div class="user-name"><?php echo $usersEsc($user['name']); ?></div>
                                            <div class="user-sub"><?php echo $usersEsc($user['email']); ?><?php if ($user['mobile']): ?> · <?php echo $usersEsc($user['mobile']); ?><?php endif; ?></div>
                                        </td>
                                        <td>
                                            <?php if ($roleFilter === 'UNASSIGNED'): ?>
                                                <span class="badge bg-light text-dark border user-role-badge"><?php echo _lang['customer_users_no_portal_role']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-<?php echo $usersRoleClass($role); ?> user-role-badge"><?php echo $usersEsc($usersRoleLabel($role)); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($role === 'ADMIN'): ?>
                                                <span class="text-muted">—</span>
                                            <?php elseif (in_array($role, ['MANAGER', 'EXPERT'], true) && empty($user['section_name'])): ?>
                                                <span class="badge bg-soft-warning text-warning border"><?php echo _lang['customer_users_pending_assignment']; ?></span>
                                            <?php else: ?>
                                                <?php echo $usersEsc($user['section_name'] ?: '-'); ?>
                                                <?php if ($user['section_name'] && (int) ($user['section_is_active'] ?? 1) !== 1): ?>
                                                    <span class="badge bg-light text-warning border ms-1"><?php echo _lang['inactive']; ?></span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $usersEsc($user['unit_name'] ?: '-'); ?></td>
                                        <td><?php echo $usersEsc($user['rbac_name'] ?: '-'); ?></td>
                                        <td>
                                            <?php if (!empty($user['send_all'])): ?>
                                                <span class="badge bg-soft-primary text-primary border"><?php echo _lang['customer_users_send_all']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border"><?php echo _lang['customer_users_send_subtree']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user['status'] === 'Active'): ?>
                                                <span class="badge bg-soft-success text-success border"><i class="ri-checkbox-circle-line me-1"></i><?php echo _lang['active']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted border"><i class="ri-forbid-line me-1"></i><?php echo _lang['inactive']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <?php if ($canEditUser && $portalManagementReady): ?>
                                                    <button type="button" class="btn btn-sm btn-light border js-edit-customer-user"
                                                            data-user="<?php echo $usersEsc(json_encode($editPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>"
                                                            title="<?php echo $usersEsc(_lang['edit']); ?>">
                                                        <i class="ri-edit-line"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($user['status'] === 'Active' && $canInactivateUser && $portalManagementReady): ?>
                                                    <button type="button" class="btn btn-sm btn-light border text-warning js-user-status" data-id="<?php echo (int) $user['id']; ?>" data-action="inactivate" title="<?php echo $usersEsc(_lang['inactive']); ?>">
                                                        <i class="ri-user-unfollow-line"></i>
                                                    </button>
                                                <?php elseif ($user['status'] !== 'Active' && $canActivateUser && $portalManagementReady): ?>
                                                    <button type="button" class="btn btn-sm btn-light border text-success js-user-status" data-id="<?php echo (int) $user['id']; ?>" data-action="activate" title="<?php echo $usersEsc(_lang['active']); ?>">
                                                        <i class="ri-user-follow-line"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div id="userNoResult" class="text-center text-muted py-5 d-none"><?php echo _lang['customer_users_no_result']; ?></div>
                    </div>
                    <div class="px-3 py-2 border-top bg-light small text-muted">
                        <i class="ri-information-line me-1"></i><?php echo _lang['customer_users_no_physical_delete']; ?>
                    </div>
                </div>

                <div class="modal fade user-form-modal" id="customerUserModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <h5 class="modal-title" id="customerUserModalTitle"><?php echo _lang['customer_users_add_title']; ?></h5>
                                    <div class="small text-muted mt-1"><?php echo _lang['customer_users_form_note']; ?></div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $usersEsc(_lang['close']); ?>"></button>
                            </div>
                            <div class="modal-body">
                                <div id="customerUserFeedback" class="alert d-none"></div>
                                <form id="customerUserForm" autocomplete="off">
                                    <input type="hidden" name="action" id="customerUserAction" value="add">
                                    <input type="hidden" name="user_id" id="customerUserId" value="0">
                                    <input type="hidden" name="company_id" value="<?php echo $selectedCompanyId; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $usersEsc($usersManagementCsrf); ?>">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label"><?php echo _lang['name']; ?></label>
                                            <input type="text" class="form-control" name="name" id="customerUserName" maxlength="200" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><?php echo _lang['mobile']; ?></label>
                                            <input type="text" class="form-control" name="mobile" id="customerUserMobile" maxlength="25" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><?php echo _lang['email']; ?></label>
                                            <input type="email" class="form-control" name="email" id="customerUserEmail" maxlength="200" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><?php echo _lang['password']; ?> <span class="text-muted" id="passwordOptionalLabel"></span></label>
                                            <input type="password" class="form-control" name="password" id="customerUserPassword">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label"><?php echo _lang['customer_users_portal_role']; ?></label>
                                            <select class="form-select" name="portal_role" id="customerUserRole" required>
                                                <option value="ADMIN"><?php echo _lang['customer_users_role_admin']; ?></option>
                                                <option value="MANAGER"><?php echo _lang['customer_users_role_manager']; ?></option>
                                                <option value="EXPERT"><?php echo _lang['customer_users_role_expert']; ?></option>
                                            </select>
                                        </div>
                                        <div class="col-md-4" id="customerUserSectionWrap">
                                            <label class="form-label"><?php echo _lang['customer_users_section']; ?></label>
                                            <select class="form-select" name="section_id" id="customerUserSection">
                                                <option value="0"><?php echo _lang['customer_users_select_section']; ?></option>
                                                <?php foreach ($companySections as $section): ?>
                                                    <option value="<?php echo (int) $section['id']; ?>" <?php echo (int) $section['is_active'] === 1 ? '' : 'disabled'; ?>>
                                                        <?php echo $usersEsc($section['name']); ?><?php echo (int) $section['is_active'] === 1 ? '' : ' - ' . $usersEsc(_lang['inactive']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label"><?php echo _lang['unit']; ?></label>
                                            <select class="form-select" name="unit_id" id="customerUserUnit" required>
                                                <?php foreach ($companyUnits as $unit): ?>
                                                    <option value="<?php echo (int) $unit['id']; ?>"><?php echo $usersEsc($unit['unit_name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label"><?php echo _lang['access']; ?></label>
                                            <select class="form-select" name="rbac_id" id="customerUserRbac" required>
                                                <?php foreach ($companyRbacs as $rbac): ?>
                                                    <option value="<?php echo (int) $rbac['id']; ?>"><?php echo $usersEsc($rbac['rbac_name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="form-text"><?php echo _lang['customer_users_rbac_note']; ?></div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button>
                                <button type="button" class="btn btn-primary" id="customerUserSaveBtn"><?php echo _lang['save_changes']; ?></button>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const companySearch = document.getElementById('companyUserSearch');
    if (companySearch) {
        companySearch.addEventListener('input', function () {
            const q = (this.value || '').trim().toLocaleLowerCase();
            let visible = 0;
            document.querySelectorAll('.company-user-item').forEach(item => {
                const show = !q || (item.dataset.search || '').includes(q);
                item.classList.toggle('d-none', !show);
                if (show) visible++;
            });
            document.getElementById('companyUserNoResult')?.classList.toggle('d-none', visible !== 0);
        });
    }

    const userSearch = document.getElementById('userSearch');
    const userRoleFilter = document.getElementById('userRoleFilter');
    const applyUserFilter = () => {
        if (!userSearch || !userRoleFilter) return;
        const q = (userSearch.value || '').trim().toLocaleLowerCase();
        const role = userRoleFilter.value;
        let visible = 0;
        document.querySelectorAll('.company-user-row').forEach(row => {
            const matchesSearch = !q || (row.dataset.search || '').includes(q);
            const matchesRole = role === 'all' || row.dataset.role === role;
            const show = matchesSearch && matchesRole;
            row.classList.toggle('d-none', !show);
            if (show) visible++;
        });
        document.getElementById('userNoResult')?.classList.toggle('d-none', visible !== 0);
    };
    userSearch?.addEventListener('input', applyUserFilter);
    userRoleFilter?.addEventListener('change', applyUserFilter);

    const modalEl = document.getElementById('customerUserModal');
    if (!modalEl) return;

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const form = document.getElementById('customerUserForm');
    const roleSelect = document.getElementById('customerUserRole');
    const sectionWrap = document.getElementById('customerUserSectionWrap');
    const sectionSelect = document.getElementById('customerUserSection');
    const feedback = document.getElementById('customerUserFeedback');
    const saveBtn = document.getElementById('customerUserSaveBtn');

    const setRoleState = () => {
        const isAdmin = roleSelect.value === 'ADMIN';
        const isAdd = document.getElementById('customerUserAction').value === 'add';
        sectionWrap.classList.toggle('d-none', isAdmin);
        sectionSelect.required = !isAdmin && isAdd;
        if (isAdmin) sectionSelect.value = '0';
    };
    roleSelect.addEventListener('change', setRoleState);

    const showFeedback = (message, success) => {
        feedback.textContent = message || '';
        feedback.className = 'alert ' + (success ? 'alert-success' : 'alert-danger');
        feedback.classList.remove('d-none');
    };

    const resetForm = () => {
        form.reset();
        document.getElementById('customerUserAction').value = 'add';
        document.getElementById('customerUserId').value = '0';
        document.getElementById('customerUserModalTitle').textContent = <?php echo json_encode(_lang['customer_users_add_title'], JSON_UNESCAPED_UNICODE); ?>;
        document.getElementById('passwordOptionalLabel').textContent = '';
        document.getElementById('customerUserPassword').required = true;
        feedback.classList.add('d-none');
        setRoleState();
    };

    document.getElementById('customerUserAddBtn')?.addEventListener('click', resetForm);

    document.querySelectorAll('.js-edit-customer-user').forEach(btn => {
        btn.addEventListener('click', function () {
            resetForm();
            let data = {};
            try { data = JSON.parse(this.dataset.user || '{}'); } catch (e) { return; }
            document.getElementById('customerUserAction').value = 'edit';
            document.getElementById('customerUserId').value = data.id || 0;
            document.getElementById('customerUserName').value = data.name || '';
            document.getElementById('customerUserEmail').value = data.email || '';
            document.getElementById('customerUserMobile').value = data.mobile || '';
            document.getElementById('customerUserUnit').value = String(data.unit_id || '');
            document.getElementById('customerUserRbac').value = String(data.rbac_id || '');
            roleSelect.value = data.portal_role || 'ADMIN';
            sectionSelect.value = String(data.section_id || 0);
            document.getElementById('customerUserModalTitle').textContent = <?php echo json_encode(_lang['customer_users_edit_title'], JSON_UNESCAPED_UNICODE); ?>;
            document.getElementById('passwordOptionalLabel').textContent = <?php echo json_encode(_lang['customer_users_password_optional'], JSON_UNESCAPED_UNICODE); ?>;
            document.getElementById('customerUserPassword').required = false;
            setRoleState();
            modal.show();
        });
    });

    const submitFormData = async (formData) => {
        const response = await fetch('./icore/json/user_manage.php', {
            method: 'POST',
            body: formData,
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        });
        let data = null;
        try { data = await response.json(); } catch (e) { throw new Error(<?php echo json_encode(_lang['customer_users_unexpected_error'], JSON_UNESCAPED_UNICODE); ?>); }
        if (!response.ok || data.status !== 'success') throw new Error(data.message || <?php echo json_encode(_lang['customer_users_unexpected_error'], JSON_UNESCAPED_UNICODE); ?>);
        return data;
    };

    saveBtn?.addEventListener('click', async function () {
        if (!form.reportValidity()) return;
        this.disabled = true;
        feedback.classList.add('d-none');
        try {
            const data = await submitFormData(new FormData(form));
            showFeedback(data.message, true);
            setTimeout(() => location.reload(), 650);
        } catch (e) {
            showFeedback(e.message, false);
        } finally {
            this.disabled = false;
        }
    });

    document.querySelectorAll('.js-assign-section').forEach(btn => {
        btn.addEventListener('click', async function () {
            const row = this.closest('.migration-user-row');
            const select = row?.querySelector('.js-assign-section-select');
            const sectionId = select?.value || '0';
            if (sectionId === '0') {
                alert(<?php echo json_encode(_lang['customer_users_select_section_first'], JSON_UNESCAPED_UNICODE); ?>);
                return;
            }

            if (!window.confirm(<?php echo json_encode(_lang['customer_users_assign_confirm'], JSON_UNESCAPED_UNICODE); ?>)) return;

            const fd = new FormData();
            fd.append('action', 'assign_section');
            fd.append('user_id', this.dataset.id || '0');
            fd.append('company_id', '<?php echo $selectedCompanyId; ?>');
            fd.append('section_id', sectionId);
            fd.append('csrf_token', '<?php echo $usersEsc($usersManagementCsrf); ?>');

            this.disabled = true;
            try {
                const data = await submitFormData(fd);
                alert(data.message);
                location.reload();
            } catch (e) {
                alert(e.message);
                this.disabled = false;
            }
        });
    });

    document.querySelectorAll('.js-user-status').forEach(btn => {
        btn.addEventListener('click', async function () {
            const action = this.dataset.action;
            const confirmText = action === 'activate'
                ? <?php echo json_encode(_lang['customer_users_activate_confirm'], JSON_UNESCAPED_UNICODE); ?>
                : <?php echo json_encode(_lang['customer_users_inactivate_confirm'], JSON_UNESCAPED_UNICODE); ?>;
            if (!window.confirm(confirmText)) return;

            const fd = new FormData();
            fd.append('action', action);
            fd.append('user_id', this.dataset.id || '0');
            fd.append('company_id', '<?php echo $selectedCompanyId; ?>');
            fd.append('csrf_token', '<?php echo $usersEsc($usersManagementCsrf); ?>');
            this.disabled = true;
            try {
                const data = await submitFormData(fd);
                alert(data.message);
                location.reload();
            } catch (e) {
                alert(e.message);
                this.disabled = false;
            }
        });
    });
});
</script>
