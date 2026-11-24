<?php
if (!function_exists('cp_admin_e')) {
    function cp_admin_e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
/** Translate audit identifiers at render time; stored codes remain unchanged. */
if (!function_exists('cp_admin_audit_action_label')) {
    function cp_admin_audit_action_label($code): string {
        $key = 'cp_audit_action_' . strtolower((string)$code);
        return isset(_lang[$key]) ? (string)_lang[$key] : (string)$code;
    }
}
if (!function_exists('cp_admin_audit_entity_label')) {
    function cp_admin_audit_entity_label($code): string {
        $key = 'cp_audit_entity_' . strtolower((string)$code);
        return isset(_lang[$key]) ? (string)_lang[$key] : (string)$code;
    }
}
if (!function_exists('cp_admin_audit_role_label')) {
    function cp_admin_audit_role_label($code): string {
        $key = 'cp_role_' . strtolower((string)$code);
        return isset(_lang[$key]) ? (string)_lang[$key] : (string)$code;
    }
}
if (!function_exists('cp_admin_msg')) {
    function cp_admin_msg($code): string
    {
        $map = [
            'PORTAL_SETTINGS_SAVED' => _lang['cp_admin_settings_saved'],
            'PORTAL_PROFILE_SAVED' => _lang['cp_admin_profile_saved'],
            'PORTAL_PERMISSION_SAVED' => _lang['cp_admin_permission_saved'],
            'EDIT_PERMISSION_REQUIRED' => _lang['cp_admin_edit_permission_required'],
            'CSRF_INVALID' => _lang['cp_error_security'],
            'PORTAL_MAPPING_REQUIRED_BEFORE_ENABLE' => _lang['cp_error_mapping'],
            'PORTAL_ADMIN_REQUIRED_BEFORE_ENABLE' => _lang['cp_error_admin_required'],
            'LAST_PORTAL_ADMIN_REQUIRED' => _lang['cp_error_last_portal_admin'],
            'INVALID_PORTAL_UNIT' => _lang['cp_admin_invalid_mapping'],
            'INVALID_PORTAL_RBAC' => _lang['cp_admin_invalid_mapping'],
            'USER_COMPANY_MISMATCH' => _lang['cp_admin_invalid_user_company'],
            'SECTION_ALREADY_HAS_MANAGER' => _lang['cp_error_section_manager_exists'],
            'SECTION_MANAGER_REQUIRED' => _lang['cp_error_section_manager_required'],
            'PERMISSION_ROLE_MISMATCH' => _lang['cp_admin_permission_role_mismatch'],
            'USER_HAS_OPEN_DRAFTS' => _lang['cp_error_user_open_drafts'],
            'LAST_MANAGER_WITH_ACTIVE_EXPERTS' => _lang['cp_error_last_manager'],
            'SECTION_NOT_ACTIVE' => _lang['cp_error_section_required'],
            'INVALID_PORTAL_ROLE' => _lang['cp_error_invalid_data'],
            'INVALID_PORTAL_PERMISSION' => _lang['cp_error_security'],
            'INVALID_COMPANY' => _lang['cp_error_security'],
            'PORTAL_SETTINGS_SAVE_FAILED' => _lang['cp_error_system'],
            'AUDIT_WRITE_FAILED' => _lang['cp_error_system'],
            'INVALID_ACTION' => _lang['cp_error_security'],
        ];
        return $map[(string)$code] ?? (string)$code;
    }
}

$selectedCompany = null;
foreach ($cpCompanies as $companyRow) {
    if ((int)$companyRow['id'] === (int)$selectedCompanyId) {
        $selectedCompany = $companyRow;
        break;
    }
}
$portalProfilesByUser = [];
foreach ($cpPortalUsers as $p) {
    $portalProfilesByUser[(int)$p['user_id']] = $p;
}
$permissionLabels = [
    'customer_admin_create_manager' => _lang['cp_perm_customer_admin_create_manager'],
    'customer_admin_create_expert' => _lang['cp_perm_customer_admin_create_expert'],
    'manager_create_expert' => _lang['cp_perm_manager_create_expert'],
    'manager_create_ticket_with_experts' => _lang['cp_perm_manager_create_ticket_with_experts'],
    'manager_return_ticket_to_expert' => _lang['cp_perm_manager_return_ticket_to_expert'],
    'customer_admin_assign_manager_to_section' => _lang['cp_perm_customer_admin_assign_manager_to_section'],
    'manager_assign_unassigned_expert' => _lang['cp_perm_manager_assign_unassigned_expert'],
    'customer_admin_reassign_expert' => _lang['cp_perm_customer_admin_reassign_expert'],
];
?>
<style>
    .cp-admin-page {
        --cu-border: #e5eaf2;
        --cu-text: #172033;
        --cu-muted: #7a8699;
        --cu-soft: #f6f8fc;
        --cu-primary: #356df3;
        background: #f8fbff;
        min-height: calc(100vh - 130px);
    }

    .cp-admin-shell {
        width: 100%;
        margin: 0 auto;
        padding-bottom: 2rem;
    }

    .cp-admin-hero {
        border: 1px solid #e2e8f5;
        border-radius: 20px;
        padding: 22px 24px;
        background:
            radial-gradient(circle at top left, rgba(53,109,243,.11), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #f6f9ff 100%);
        box-shadow: 0 12px 35px rgba(37, 54, 89, .06);
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .cp-admin-hero-main {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .cp-admin-hero h3 {
        color: var(--cu-text);
        font-weight: 800;
        margin: 0 0 6px;
    }

    .cp-admin-hero p {
        color: var(--cu-muted);
        margin: 0;
    }

    .cp-admin-hero-icon {
        width: 48px;
        height: 48px;
        border-radius: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #edf3ff;
        color: var(--cu-primary);
        font-size: 23px;
        flex: 0 0 auto;
    }

    .cp-admin-badge {
        background: #eef3ff;
        color: var(--cu-primary);
        border: 1px solid #d8e5ff;
        border-radius: 999px;
        padding: .55rem .9rem;
        font-weight: 700;
        font-size: .78rem;
    }

    .cp-admin-card {
        border: 1px solid var(--cu-border);
        border-radius: 18px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(29, 42, 69, .04);
    }

    .cp-admin-card-head {
        padding: 16px 18px;
        border-bottom: 1px solid var(--cu-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        background: linear-gradient(180deg, #fff 0%, #fbfdff 100%);
    }

    .cp-admin-card-head h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        color: var(--cu-text);
    }

    .cp-admin-card-body {
        padding: 18px;
    }

    .cp-admin-note {
        font-size: .82rem;
        color: var(--cu-muted);
        background: var(--cu-soft);
        border: 1px solid #e7ebf1;
        border-radius: 12px;
        padding: 10px 12px;
        line-height: 1.85;
    }

    .cp-admin-page .form-label {
        color: #435067;
        font-weight: 700;
        font-size: .83rem;
        margin-bottom: .45rem;
    }

    .cp-admin-page .form-select,
    .cp-admin-page .form-control {
        border-color: #dbe2ed;
        border-radius: 12px;
        min-height: 42px;
    }

    .cp-admin-page .form-select:focus,
    .cp-admin-page .form-control:focus {
        border-color: #9db8ff;
        box-shadow: 0 0 0 .2rem rgba(53, 109, 243, .12);
    }

    .cp-admin-status {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .38rem .75rem;
        border-radius: 999px;
        font-weight: 700;
        font-size: .76rem;
    }

    .cp-enabled {
        color: #15803d;
        background: #edfdf3;
        border: 1px solid #c7f2d5;
    }

    .cp-disabled {
        color: #b91c1c;
        background: #fff1f2;
        border: 1px solid #ffd6db;
    }

    .cp-admin-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .cp-admin-kpi {
        border: 1px solid var(--cu-border);
        border-radius: 16px;
        background: #fff;
        padding: 16px;
        box-shadow: 0 7px 20px rgba(28, 41, 67, .04);
    }

    .cp-admin-kpi .icon {
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

    .cp-admin-kpi strong {
        display: block;
        font-size: 22px;
        color: var(--cu-text);
    }

    .cp-admin-kpi span {
        color: var(--cu-muted);
        font-size: 11px;
    }

    .cp-admin-shortcut {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 14px;
        min-height: 100%;
    }

    .cp-admin-shortcut-box {
        border: 1px dashed #d9e2f4;
        background: linear-gradient(180deg, #ffffff 0%, #fafcff 100%);
        border-radius: 16px;
        padding: 18px;
    }

    .cp-admin-shortcut-box h6 {
        margin: 0 0 8px;
        font-weight: 800;
        color: var(--cu-text);
    }

    .cp-admin-shortcut-box p {
        margin: 0;
        color: var(--cu-muted);
        line-height: 1.9;
        font-size: .84rem;
    }

    .cp-admin-table th {
        background: #f8fafc;
        color: #64748b;
        font-size: .78rem;
        white-space: nowrap;
        border-bottom-width: 1px;
    }

    .cp-admin-table td {
        vertical-align: middle;
        font-size: .84rem;
    }

    .cp-role {
        display: inline-flex;
        padding: .28rem .58rem;
        border-radius: 999px;
        background: #e8f0ff;
        color: #1d4ed8;
        font-weight: 700;
        font-size: .73rem;
    }

    .cp-perm-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .cp-perm-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
        border: 1px solid #e8edf3;
        border-radius: 12px;
        padding: 10px 12px;
        background: #fbfcfe;
    }

    .cp-perm-item small {
        line-height: 1.7;
        color: #475569;
    }

    .cp-audit-filter {
        background: #f8fafc;
        border: 1px solid #e7ebf1;
        border-radius: 14px;
        padding: 14px;
    }

    .cp-audit-json {
        direction: ltr;
        text-align: left;
        white-space: pre-wrap;
        word-break: break-word;
        font-size: .72rem;
        max-width: 380px;
    }

    .cp-admin-empty {
        text-align: center;
        color: var(--cu-muted);
        padding: 2rem 1rem;
    }

    @media (max-width: 1199px) {
        .cp-admin-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .cp-perm-grid,
        .cp-admin-kpi-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="content-page cp-admin-page"><div class="content"><div class="container-fluid"><div class="cp-admin-shell">
    <div class="cp-admin-hero">
        <div class="cp-admin-hero-main">
            <span class="cp-admin-hero-icon"><i class="ri-shield-user-line"></i></span>
            <div>
                <h3><?php echo _lang['cp_admin_title']; ?></h3>
                <p><?php echo _lang['cp_admin_subtitle']; ?></p>
            </div>
        </div>
        <div><span class="cp-admin-badge"><?php echo _lang['cp_admin_default_deny']; ?></span></div>
    </div>

    <?php if ($cpAdminFlash): ?><div class="alert alert-<?php echo cp_admin_e($cpAdminFlash['type']); ?> alert-dismissible fade show"><strong><?php echo cp_admin_e(cp_admin_msg($cpAdminFlash['message'])); ?></strong><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

    <div class="cp-admin-card mb-3"><div class="cp-admin-card-body">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-lg-5"><label class="form-label"><?php echo _lang['company']; ?></label><select class="form-select" name="company_id" onchange="this.form.submit()">
                <?php foreach ($cpCompanies as $company): ?><option value="<?php echo (int)$company['id']; ?>" <?php echo (int)$company['id']===(int)$selectedCompanyId?'selected':''; ?>><?php echo cp_admin_e($company['company_name']); ?> — <?php echo !empty($company['workflow_enabled']) ? _lang['active'] : _lang['inactive']; ?></option><?php endforeach; ?>
            </select></div>
            <div class="col-lg-7"><div class="cp-admin-note"><?php echo _lang['cp_admin_company_selector_note']; ?></div></div>
        </form>
    </div></div>

    <?php if ($selectedCompanyId > 0): ?>
    <div class="cp-admin-kpi-grid mb-3">
        <div class="cp-admin-kpi"><span class="icon"><i class="ri-building-4-line"></i></span><strong><?php echo number_format(count($cpCompanies)); ?></strong><span><?php echo _lang['company']; ?></span></div>
        <div class="cp-admin-kpi"><span class="icon"><i class="ri-team-line"></i></span><strong><?php echo number_format(count($cpPortalUsers)); ?></strong><span><?php echo _lang['users']; ?></span></div>
        <div class="cp-admin-kpi"><span class="icon"><i class="ri-shield-check-line"></i></span><strong><?php echo number_format(count($permissionLabels)); ?></strong><span><?php echo _lang['cp_admin_permissions']; ?></span></div>
        <div class="cp-admin-kpi"><span class="icon"><i class="ri-file-list-3-line"></i></span><strong><?php echo number_format(count($cpAdminAudit)); ?></strong><span><?php echo _lang['cp_admin_global_audit']; ?></span></div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-xxl-7 col-xl-8"><div class="cp-admin-card h-100"><div class="cp-admin-card-head"><h5><?php echo _lang['cp_admin_settings']; ?></h5><span class="cp-admin-status <?php echo !empty($cpSettings['customer_portal_workflow_enabled'])?'cp-enabled':'cp-disabled'; ?>"><?php echo !empty($cpSettings['customer_portal_workflow_enabled'])?_lang['active']:_lang['inactive']; ?></span></div><div class="cp-admin-card-body">
            <form method="post"><input type="hidden" name="csrf_token" value="<?php echo cp_admin_e($cpAdminCsrf); ?>"><input type="hidden" name="cp_admin_action" value="save_settings"><input type="hidden" name="company_id" value="<?php echo (int)$selectedCompanyId; ?>">
                <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" id="workflowEnabled" name="customer_portal_workflow_enabled" value="1" <?php echo !empty($cpSettings['customer_portal_workflow_enabled'])?'checked':''; ?> <?php echo !$canEditPortalAdmin?'disabled':''; ?>><label class="form-check-label fw-bold" for="workflowEnabled"><?php echo _lang['cp_admin_enable_workflow']; ?></label></div>
                <div class="row g-3">
                    <div class="col-lg-6"><label class="form-label"><?php echo _lang['cp_admin_aging_days']; ?></label><input class="form-control" type="number" min="1" max="365" name="draft_aging_days" value="<?php echo (int)($cpSettings['draft_aging_days']??14); ?>" <?php echo !$canEditPortalAdmin?'disabled':''; ?>></div>
                    <div class="col-lg-6"><label class="form-label"><?php echo _lang['cp_admin_portal_unit']; ?></label><select class="form-select" name="portal_unit_id" <?php echo !$canEditPortalAdmin?'disabled':''; ?>><option value=""><?php echo _lang['select']; ?></option><?php foreach ($cpLegacyOptions['units'] as $unit): ?><option value="<?php echo (int)$unit['id']; ?>" <?php echo (int)($cpSettings['portal_unit_id']??0)===(int)$unit['id']?'selected':''; ?>><?php echo cp_admin_e($unit['unit_name']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-lg-4"><label class="form-label"><?php echo _lang['cp_role_admin'] . ' — ' . _lang['role']; ?></label><select class="form-select" name="admin_rbac_id" <?php echo !$canEditPortalAdmin?'disabled':''; ?>><option value=""><?php echo _lang['select']; ?></option><?php foreach ($cpLegacyOptions['rbacs'] as $rbac): ?><option value="<?php echo (int)$rbac['id']; ?>" <?php echo (int)($cpSettings['admin_rbac_id']??0)===(int)$rbac['id']?'selected':''; ?>><?php echo cp_admin_e($rbac['rbac_name']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-lg-4"><label class="form-label"><?php echo _lang['cp_role_manager'] . ' — ' . _lang['role']; ?></label><select class="form-select" name="manager_rbac_id" <?php echo !$canEditPortalAdmin?'disabled':''; ?>><option value=""><?php echo _lang['select']; ?></option><?php foreach ($cpLegacyOptions['rbacs'] as $rbac): ?><option value="<?php echo (int)$rbac['id']; ?>" <?php echo (int)($cpSettings['manager_rbac_id']??0)===(int)$rbac['id']?'selected':''; ?>><?php echo cp_admin_e($rbac['rbac_name']); ?></option><?php endforeach; ?></select></div>
                    <div class="col-lg-4"><label class="form-label"><?php echo _lang['cp_role_expert'] . ' — ' . _lang['role']; ?></label><select class="form-select" name="expert_rbac_id" <?php echo !$canEditPortalAdmin?'disabled':''; ?>><option value=""><?php echo _lang['select']; ?></option><?php foreach ($cpLegacyOptions['rbacs'] as $rbac): ?><option value="<?php echo (int)$rbac['id']; ?>" <?php echo (int)($cpSettings['expert_rbac_id']??0)===(int)$rbac['id']?'selected':''; ?>><?php echo cp_admin_e($rbac['rbac_name']); ?></option><?php endforeach; ?></select></div>
                </div>
                <div class="cp-admin-note mt-3 mb-3"><?php echo _lang['cp_admin_mapping_note']; ?></div>
                <?php if ($canEditPortalAdmin): ?><button class="btn btn-primary"><i class="ri-save-line me-1"></i><?php echo _lang['save_changes']; ?></button><?php endif; ?>
            </form>
        </div></div></div>

        <div class="col-xxl-5 col-xl-4">
            <div class="cp-admin-card h-100">
                <div class="cp-admin-card-head">
                    <h5><?php echo _lang['users']; ?> / <?php echo _lang['role']; ?></h5>
                    <span class="text-muted small"><?php echo number_format(count($cpPortalUsers)); ?></span>
                </div>
                <div class="cp-admin-card-body">
                    <div class="cp-admin-shortcut">
                        <div class="cp-admin-shortcut-box">
                            <h6><?php echo _lang['cp_admin_profiles']; ?></h6>
                            <p>ویرایش نقش، بخش، وضعیت و ساختار کاربران در این صفحه انجام نمی‌شود و مرجع اصلی آن صفحه کاربران هر شرکت است.</p>
                        </div>
                        <div class="cp-admin-note">برای جلوگیری از تداخل، مدیریت User / Role / Section فقط از مسیر صفحه کاربران همان شرکت انجام می‌شود.</div>
                        <a class="btn btn-outline-primary" href="./users?company_id=<?php echo (int)$selectedCompanyId; ?>"><i class="ri-team-line me-1"></i> <?php echo _lang['users']; ?> / <?php echo _lang['role']; ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="cp-admin-card"><div class="cp-admin-card-head"><h5><?php echo _lang['cp_admin_permissions']; ?></h5><span class="text-muted small"><?php echo _lang['cp_admin_permissions_note']; ?></span></div><div class="cp-admin-card-body">
        <?php if (empty($cpPortalUsers)): ?><div class="text-center text-muted py-4"><?php echo _lang['cp_admin_no_profiles']; ?></div><?php endif; ?>
        <div class="accordion" id="permissionsAccordion">
        <?php foreach ($cpPortalUsers as $index=>$u): $uid=(int)$u['user_id']; $role=(string)$u['portal_role']; $map=$cpPermissionMap[$uid]??[]; ?><div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button <?php echo $index?'collapsed':''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#perm<?php echo $uid; ?>"><strong><?php echo cp_admin_e($u['name']); ?></strong><span class="cp-role ms-2"><?php echo cp_admin_e($role); ?></span></button></h2><div id="perm<?php echo $uid; ?>" class="accordion-collapse collapse <?php echo $index===0?'show':''; ?>" data-bs-parent="#permissionsAccordion"><div class="accordion-body"><div class="cp-perm-grid">
            <?php foreach ($permissionLabels as $perm=>$label): $applies=(strpos($perm,'customer_admin_')===0 && $role==='ADMIN') || (strpos($perm,'manager_')===0 && $role==='MANAGER'); if(!$applies) continue; $allowed=!empty($map[$perm]); ?><div class="cp-perm-item"><small><?php echo cp_admin_e($label); ?></small><form method="post" class="m-0"><input type="hidden" name="csrf_token" value="<?php echo cp_admin_e($cpAdminCsrf); ?>"><input type="hidden" name="cp_admin_action" value="set_permission"><input type="hidden" name="company_id" value="<?php echo (int)$selectedCompanyId; ?>"><input type="hidden" name="user_id" value="<?php echo $uid; ?>"><input type="hidden" name="permission_name" value="<?php echo cp_admin_e($perm); ?>"><input type="hidden" name="is_allowed" value="<?php echo $allowed?'0':'1'; ?>"><button class="btn btn-sm <?php echo $allowed?'btn-success':'btn-outline-secondary'; ?>" <?php echo !$canEditPortalAdmin?'disabled':''; ?>><?php echo $allowed?_lang['active']:_lang['inactive']; ?></button></form></div><?php endforeach; ?>
        </div></div></div></div><?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="cp-admin-card mt-3">
        <div class="cp-admin-card-head">
            <div>
                <h5><?php echo _lang['cp_admin_global_audit']; ?></h5>
                <div class="text-muted small mt-1"><?php echo _lang['cp_admin_audit_note']; ?></div>
            </div>
            <span class="badge bg-light text-dark"><?php echo number_format(count($cpAdminAudit)); ?></span>
        </div>
        <div class="cp-admin-card-body">
            <form method="get" class="cp-audit-filter row g-2 align-items-end mb-3">
                <input type="hidden" name="company_id" value="<?php echo (int)$selectedCompanyId; ?>">
                <div class="col-xl-2 col-md-4">
                    <label class="form-label"><?php echo _lang['company']; ?></label>
                    <select class="form-select" name="audit_company_id">
                        <option value="0" <?php echo (int)$cpAuditCompanyId===0?'selected':''; ?>><?php echo _lang['cp_admin_all_companies']; ?></option>
                        <?php foreach ($cpCompanies as $company): ?>
                            <option value="<?php echo (int)$company['id']; ?>" <?php echo (int)$cpAuditCompanyId===(int)$company['id']?'selected':''; ?>><?php echo cp_admin_e($company['company_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xl-2 col-md-4">
                    <label class="form-label"><?php echo _lang['users']; ?></label>
                    <select class="form-select" name="audit_user_id">
                        <option value="0"><?php echo _lang['select']; ?></option>
                        <?php foreach ($cpAuditUsers as $auditUser): ?>
                            <option value="<?php echo (int)$auditUser['user_id']; ?>" <?php echo (int)$cpAuditFilters['user_id']===(int)$auditUser['user_id']?'selected':''; ?>><?php echo cp_admin_e(($auditUser['company_name'] ? $auditUser['company_name'].' — ' : '').$auditUser['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xl-2 col-md-4">
                    <label class="form-label"><?php echo _lang['role']; ?></label>
                    <select class="form-select" name="audit_role">
                        <option value=""><?php echo _lang['select']; ?></option>
                        <?php foreach (['ADMIN'=>_lang['cp_role_admin'],'MANAGER'=>_lang['cp_role_manager'],'EXPERT'=>_lang['cp_role_expert'],'INTEK_ADMIN'=>(isset(_lang['cp_role_intek_admin']) ? _lang['cp_role_intek_admin'] : 'INTEK Admin')] as $auditRole=>$auditRoleLabel): ?>
                            <option value="<?php echo cp_admin_e($auditRole); ?>" <?php echo strtoupper((string)$cpAuditFilters['role'])===$auditRole?'selected':''; ?>><?php echo cp_admin_e($auditRoleLabel); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xl-2 col-md-4">
                    <label class="form-label"><?php echo _lang['cp_section']; ?></label>
                    <select class="form-select" name="audit_section_id">
                        <option value="0"><?php echo _lang['select']; ?></option>
                        <?php foreach ($cpAuditSections as $auditSection): ?>
                            <option value="<?php echo (int)$auditSection['id']; ?>" <?php echo (int)$cpAuditFilters['section_id']===(int)$auditSection['id']?'selected':''; ?>><?php echo cp_admin_e(($auditSection['company_name'] ? $auditSection['company_name'].' — ' : '').$auditSection['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xl-3 col-md-6">
                    <label class="form-label"><?php echo _lang['cp_event']; ?></label>
                    <select class="form-select" name="audit_action">
                        <option value=""><?php echo _lang['select']; ?></option>
                        <?php foreach ($cpAuditActions as $auditAction): ?>
                            <option value="<?php echo cp_admin_e($auditAction); ?>" <?php echo (string)$cpAuditFilters['action']===(string)$auditAction?'selected':''; ?>><?php echo cp_admin_e(cp_admin_audit_action_label($auditAction)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xl-1 col-md-2"><button class="btn btn-primary w-100"><i class="ri-filter-3-line"></i></button></div>
            </form>

            <div class="table-responsive">
                <table class="table cp-admin-table align-middle mb-0">
                    <thead><tr><th>#</th><th><?php echo _lang['added_date']; ?></th><th><?php echo _lang['company']; ?></th><th><?php echo _lang['cp_section']; ?></th><th><?php echo _lang['cp_actor']; ?></th><th><?php echo _lang['role']; ?></th><th><?php echo _lang['cp_event']; ?></th><th><?php echo _lang['cp_entity']; ?></th><th><?php echo _lang['cp_changes']; ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($cpAdminAudit as $auditRow): ?>
                        <tr>
                            <td><?php echo (int)$auditRow['id']; ?></td>
                            <td class="text-nowrap"><?php echo cp_admin_e($auditRow['created_at']); ?></td>
                            <td><?php echo cp_admin_e($auditRow['company_name'] ?: '#'.$auditRow['company_id']); ?></td>
                            <td><?php echo cp_admin_e($auditRow['section_name'] ?: '-'); ?></td>
                            <td><?php echo cp_admin_e($auditRow['actor_name']); ?><?php if (!empty($auditRow['target_user_name'])): ?><div class="text-muted small">→ <?php echo cp_admin_e($auditRow['target_user_name']); ?></div><?php endif; ?></td>
                            <td><span class="cp-role"><?php echo cp_admin_e(cp_admin_audit_role_label($auditRow['actor_role'])); ?></span></td>
                            <td><strong><?php echo cp_admin_e(cp_admin_audit_action_label($auditRow['action_type'])); ?></strong></td>
                            <td><?php echo cp_admin_e(cp_admin_audit_entity_label($auditRow['entity_type'])); ?><?php echo $auditRow['entity_id']!==null?' #'.(int)$auditRow['entity_id']:''; ?></td>
                            <td><?php if (!empty($auditRow['new_data']) || !empty($auditRow['old_data'])): ?><details><summary><?php echo _lang['view']; ?></summary><?php if (!empty($auditRow['old_data'])): ?><div class="cp-audit-json mt-2"><strong><?php echo cp_admin_e(_lang['cp_audit_before']); ?></strong> <?php echo cp_admin_e($auditRow['old_data']); ?></div><?php endif; ?><?php if (!empty($auditRow['new_data'])): ?><div class="cp-audit-json mt-1"><strong><?php echo cp_admin_e(_lang['cp_audit_after']); ?></strong> <?php echo cp_admin_e($auditRow['new_data']); ?></div><?php endif; ?></details><?php else: ?>-<?php endif; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$cpAdminAudit): ?><tr><td colspan="9" class="text-center text-muted py-4"><?php echo _lang['cp_admin_no_audit']; ?></td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div></div></div></div>
