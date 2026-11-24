<?php
if (!function_exists('cp_e')) {
    function cp_e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('cp_audit_action_codes')) {
    function cp_audit_action_codes(): array
    {
        return ['SECTION_CREATED', 'SECTION_UPDATED', 'SECTION_ACTIVATED', 'SECTION_DEACTIVATED', 'MANAGER_ASSIGNED', 'EXPERT_ASSIGNED', 'EXPERT_REASSIGNED', 'USER_CREATED', 'USER_UPDATED', 'USER_ACTIVATED', 'USER_DEACTIVATED', 'PORTAL_USER_ROLE_UPDATED', 'MANAGER_CREATED', 'EXPERT_CREATED', 'TICKET_DRAFT_CREATED', 'TICKET_EDITED', 'TICKET_PRIORITY_CHANGED', 'TICKET_SENT_TO_MANAGER', 'TICKET_SENT_TO_EXPERT', 'TICKET_ASSIGNED_TO_EXPERT', 'TICKET_APPROVED', 'TICKET_REJECTED', 'TICKET_CANCELLED', 'TICKET_SENT_TO_INTEK', 'TICKET_FIRST_VIEWED', 'COMMENT_CREATED', 'FILE_UPLOADED', 'FILE_REMOVED', 'PORTAL_SETTINGS_UPDATED', 'PORTAL_PROFILE_SAVED'];
    }
}

/** Localize stored audit codes at presentation time; never alter persisted codes. */
if (!function_exists('cp_audit_action_label')) {
    function cp_audit_action_label($action): string
    {
        $key = 'cp_audit_action_' . strtolower((string)$action);
        return isset(_lang[$key]) ? (string)_lang[$key] : (string)$action;
    }
}
if (!function_exists('cp_audit_entity_label')) {
    function cp_audit_entity_label($entity): string
    {
        $key = 'cp_audit_entity_' . strtolower((string)$entity);
        return isset(_lang[$key]) ? (string)_lang[$key] : (string)$entity;
    }
}
if (!function_exists('cp_audit_actor_role_label')) {
    function cp_audit_actor_role_label($role): string
    {
        $key = 'cp_role_' . strtolower((string)$role);
        return isset(_lang[$key]) ? (string)_lang[$key] : (string)$role;
    }
}

if (!function_exists('cp_flash_text')) {
    function cp_flash_text($code): string
    {
        $map = [
            'SECTION_CREATED' => _lang['cp_msg_section_created'],
            'SECTION_UPDATED' => _lang['cp_msg_section_updated'],
            'SECTION_STATUS_UPDATED' => _lang['cp_msg_section_status_updated'],
            'USER_CREATED' => _lang['cp_msg_user_created'],
            'USER_UPDATED' => _lang['cp_msg_user_updated'],
            'USER_STATUS_UPDATED' => _lang['cp_msg_user_status_updated'],
            'MANAGER_ASSIGNED' => _lang['cp_msg_manager_assigned'],
            'EXPERT_ASSIGNED' => _lang['cp_msg_expert_assigned'],
            'DRAFT_CREATED' => _lang['cp_msg_draft_created'],
            'DRAFT_UPDATED' => _lang['cp_msg_draft_updated'],
            'DRAFT_SENT_TO_MANAGER' => _lang['cp_msg_sent_manager'],
            'DRAFT_SENT_TO_EXPERT' => _lang['cp_msg_sent_expert'],
            'DRAFT_CANCELLED' => _lang['cp_msg_cancelled'],
            'DRAFT_PUBLISHED' => _lang['cp_msg_published'],
            'COMMENT_CREATED' => _lang['cp_msg_comment_created'],
            'FILE_UPLOADED' => _lang['cp_msg_file_uploaded'],
            'FILE_REMOVED' => _lang['cp_msg_file_removed'],
            'CUSTOMER_PORTAL_DISABLED' => _lang['cp_error_disabled'],
            'CUSTOMER_PORTAL_PROFILE_REQUIRED' => _lang['cp_error_profile_required'],
            'CUSTOMER_PORTAL_SECTION_REQUIRED' => _lang['cp_error_section_required'],
            'CUSTOMER_PORTAL_PROFILE_NOT_OPERATIONAL' => _lang['cp_error_section_required'],
            'CUSTOMER_PORTAL_ACCESS_DENIED' => _lang['cp_error_access_denied'],
            'PORTAL_PERMISSION_DENIED' => _lang['cp_error_permission_denied'],
            'PORTAL_LEGACY_MAPPING_NOT_CONFIGURED' => _lang['cp_error_mapping'],
            'PORTAL_MAPPING_REQUIRED_BEFORE_ENABLE' => _lang['cp_error_mapping'],
            'PORTAL_ADMIN_REQUIRED_BEFORE_ENABLE' => _lang['cp_error_admin_required'],
            'LAST_PORTAL_ADMIN_REQUIRED' => _lang['cp_error_last_portal_admin'],
            'SECTION_ALREADY_HAS_MANAGER' => _lang['cp_error_section_manager_exists'],
            'SECTION_MANAGER_REQUIRED' => _lang['cp_error_section_manager_required'],
            'SECTION_HAS_OPEN_DRAFTS' => _lang['cp_error_section_open_drafts'],
            'LAST_MANAGER_WITH_ACTIVE_EXPERTS' => _lang['cp_error_last_manager'],
            'SELF_INACTIVATE_DENIED' => _lang['cp_error_self_inactivate'],
            'USER_HAS_OPEN_DRAFTS' => _lang['cp_error_user_open_drafts'],
            'DRAFT_CREATE_DENIED' => _lang['cp_error_draft_create_denied'],
            'DRAFT_NOT_CURRENT_OWNER' => _lang['cp_error_not_owner'],
            'DRAFT_LOCKED' => _lang['cp_error_draft_locked'],
            'PUBLISH_DENIED' => _lang['cp_error_publish_denied'],
            'DRAFT_ALREADY_PUBLISHED' => _lang['cp_error_already_published'],
            'CSRF_INVALID' => _lang['cp_error_security'],
            'USER_EMAIL_EXISTS' => _lang['cp_error_email_exists'],
            'INVALID_USER_DATA' => _lang['cp_error_invalid_user'],
            'FILE_REQUIRED' => _lang['cp_error_file_required'],
            'FILE_UPLOAD_FAILED' => _lang['cp_error_file_upload'],
            'INVALID_DRAFT_STATE' => _lang['cp_error_invalid_state'],
            'DRAFT_ACTION_DENIED' => _lang['cp_error_access_denied'],
            'DRAFT_EDIT_DENIED' => _lang['cp_error_access_denied'],
            'DRAFT_NOT_FOUND' => _lang['cp_error_not_found'],
            'USER_NOT_FOUND' => _lang['cp_error_not_found'],
            'SECTION_NOT_FOUND' => _lang['cp_error_not_found'],
            'FILE_NOT_FOUND' => _lang['cp_error_not_found'],
            'INVALID_DRAFT_DATA' => _lang['cp_error_invalid_data'],
            'INVALID_SECTION_NAME' => _lang['cp_error_invalid_data'],
            'SECTION_REQUIRED' => _lang['cp_error_section_required'],
            'SECTION_NOT_ACTIVE' => _lang['cp_error_section_required'],
            'INVALID_MANAGER_ASSIGNMENT' => _lang['cp_error_invalid_user'],
            'INVALID_EXPERT_ASSIGNMENT' => _lang['cp_error_invalid_user'],
            'COMMENT_EMPTY' => _lang['cp_error_invalid_data'],
            'AUDIT_MANAGEMENT_DENIED' => _lang['cp_error_access_denied'],
            'REPORT_DENIED' => _lang['cp_error_access_denied'],
            'AUDIT_WRITE_FAILED' => _lang['cp_error_system'],
            'DRAFT_CREATE_FAILED' => _lang['cp_error_system'],
            'PUBLISH_MAP_FAILED' => _lang['cp_error_system'],
            'PUBLISH_STATUS_FAILED' => _lang['cp_error_system'],
            'PUBLISH_TICKET_INSERT_FAILED' => _lang['cp_error_system'],
            'PORTAL_PROFILE_CREATE_FAILED' => _lang['cp_error_system'],
            'USER_CREATE_FAILED' => _lang['cp_error_system'],
            'SECTION_NAME_DUPLICATE' => _lang['cp_error_invalid_data'],
            'SECTION_CREATE_FAILED' => _lang['cp_error_system'],
            'SECTION_CREATE_PREPARE_FAILED' => _lang['cp_error_system'],
            'SECTION_UPDATE_FAILED' => _lang['cp_error_system'],
            'PORTAL_SETTINGS_SAVE_FAILED' => _lang['cp_error_system'],
            'INVALID_PORTAL_PERMISSION' => _lang['cp_error_security'],
            'INVALID_PORTAL_RBAC' => _lang['cp_error_invalid_data'],
            'INVALID_PORTAL_ROLE' => _lang['cp_error_invalid_data'],
            'INVALID_PORTAL_UNIT' => _lang['cp_error_invalid_data'],
            'INVALID_COMPANY' => _lang['cp_error_security'],
            'USER_COMPANY_MISMATCH' => _lang['cp_error_security'],
            'PERMISSION_ROLE_MISMATCH' => _lang['cp_error_permission_denied'],
            'INVALID_ACTION' => _lang['cp_error_security'],
        ];
        return $map[(string) $code] ?? (string) $code;
    }
}
if (!function_exists('cp_status_label')) {
    function cp_status_label(string $status): string
    {
        $map = [
            'EXPERT_DRAFT' => _lang['cp_status_expert_draft'],
            'MANAGER_REVIEW' => _lang['cp_status_manager_review'],
            'EXPERT_REVISION' => _lang['cp_status_expert_revision'],
            'SENT_TO_INTEK' => _lang['cp_status_sent'],
            'CANCELLED' => _lang['cp_status_cancelled'],
        ];
        return $map[$status] ?? $status;
    }
}
if (!function_exists('cp_status_class')) {
    function cp_status_class(string $status): string
    {
        $map = [
            'EXPERT_DRAFT' => 'secondary',
            'MANAGER_REVIEW' => 'primary',
            'EXPERT_REVISION' => 'warning',
            'SENT_TO_INTEK' => 'success',
            'CANCELLED' => 'danger',
        ];
        return $map[$status] ?? 'secondary';
    }
}
if (!function_exists('cp_date')) {
    function cp_date($value, $config): string
    {
        if (!$value) return '-';
        try {
            return (new DateConverter($value, $config->getNowLanguage('a')))->convertToShamsi();
        } catch (Throwable $e) {
            return (string) $value;
        }
    }
}

$role = $customerPortalContext['portal_role'] ?? '';
$isAdmin = $role === \iweb\model\CustomerPortalService::ROLE_ADMIN;
$isManager = $role === \iweb\model\CustomerPortalService::ROLE_MANAGER;
$isExpert = $role === \iweb\model\CustomerPortalService::ROLE_EXPERT;
?>
<style>
.customer-portal-page{background:#f6f8fb;min-height:calc(100vh - 130px)}
.cp-shell{width:100%;max-width:none;margin:0;padding-bottom:2rem}
.cp-hero{background:linear-gradient(135deg,#14213d 0%,#244978 58%,#2f6eb3 100%);color:#fff;border-radius:22px;padding:22px 26px;box-shadow:0 18px 45px rgba(24,54,91,.18);position:relative;overflow:hidden}
.cp-hero:after{content:"";position:absolute;width:280px;height:280px;border-radius:50%;background:rgba(255,255,255,.07);left:-70px;top:-130px}.cp-hero h3{color:#fff;margin:0;font-weight:800}.cp-hero p{margin:.45rem 0 0;color:rgba(255,255,255,.78)}
.cp-role-pill{display:inline-flex;align-items:center;gap:.45rem;border:1px solid rgba(255,255,255,.28);background:rgba(255,255,255,.12);border-radius:999px;padding:.45rem .8rem;font-size:.82rem}
.cp-nav{display:flex;gap:.45rem;flex-wrap:wrap;margin:16px 0 20px}.cp-nav a{display:inline-flex;align-items:center;gap:.4rem;text-decoration:none;color:#526174;background:#fff;border:1px solid #e4e9f0;border-radius:11px;padding:.55rem .8rem;font-weight:600;box-shadow:0 4px 14px rgba(15,23,42,.035)}.cp-nav a.active,.cp-nav a:hover{color:#1d4ed8;border-color:#bfdbfe;background:#eff6ff}
.cp-card{background:#fff;border:1px solid #e7ebf1;border-radius:18px;box-shadow:0 10px 28px rgba(15,23,42,.045);overflow:hidden}.cp-card-header{padding:16px 18px;border-bottom:1px solid #eef1f5;display:flex;align-items:center;justify-content:space-between;gap:10px}.cp-card-body{padding:18px}.cp-card-title{margin:0;font-size:1rem;font-weight:800;color:#26364a}.cp-muted{color:#7a889b;font-size:.86rem}.cp-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.cp-stat{background:#fff;border:1px solid #e7ebf1;border-radius:17px;padding:16px;box-shadow:0 8px 20px rgba(15,23,42,.04)}.cp-stat .value{font-size:1.65rem;font-weight:800;color:#1f3552}.cp-stat .label{color:#748399;font-size:.82rem;margin-top:.2rem}.cp-stat.primary{border-top:3px solid #3b82f6}.cp-stat.success{border-top:3px solid #22c55e}.cp-stat.warning{border-top:3px solid #f59e0b}.cp-stat.danger{border-top:3px solid #ef4444}
.cp-table{margin:0}.cp-table thead th{font-size:.78rem;color:#6b7789;background:#f8fafc;border-bottom:1px solid #e7ebf1;white-space:nowrap}.cp-table td{vertical-align:middle;color:#344258;font-size:.86rem}.cp-actions{display:flex;gap:.35rem;flex-wrap:wrap}.cp-actions form{margin:0}.cp-section-name{font-weight:800;color:#26364a}.cp-badge{display:inline-flex;align-items:center;border-radius:999px;padding:.28rem .58rem;font-size:.74rem;font-weight:700}.cp-badge.active{background:#dcfce7;color:#15803d}.cp-badge.inactive{background:#fee2e2;color:#b91c1c}.cp-badge.role{background:#e0ecff;color:#1d4ed8}
.cp-draft-card{display:block;text-decoration:none;color:inherit;border:1px solid #e7ebf1;border-radius:16px;background:#fff;padding:15px;transition:.15s ease;height:100%}.cp-draft-card:hover{transform:translateY(-2px);box-shadow:0 12px 25px rgba(15,23,42,.07);border-color:#cbd5e1}.cp-draft-title{font-weight:800;color:#26364a;line-height:1.7;margin:.55rem 0}.cp-draft-meta{display:flex;gap:.4rem;flex-wrap:wrap;color:#7a889b;font-size:.78rem}.cp-status{display:inline-flex;border-radius:999px;padding:.28rem .6rem;font-weight:700;font-size:.74rem}.cp-status.primary{background:#dbeafe;color:#1d4ed8}.cp-status.warning{background:#fef3c7;color:#b45309}.cp-status.success{background:#dcfce7;color:#15803d}.cp-status.danger{background:#fee2e2;color:#b91c1c}.cp-status.secondary{background:#e9eef5;color:#526174}
.cp-side-info{display:grid;gap:.65rem}.cp-info-row{display:flex;justify-content:space-between;gap:1rem;padding:.58rem 0;border-bottom:1px dashed #e7ebf1}.cp-info-row:last-child{border-bottom:0}.cp-info-row span{color:#7a889b;font-size:.82rem}.cp-info-row strong{font-size:.85rem;color:#344258;text-align:end}.cp-history{position:relative;padding-inline-start:24px}.cp-history:before{content:"";position:absolute;top:3px;bottom:3px;inset-inline-start:7px;width:2px;background:#e2e8f0}.cp-history-item{position:relative;padding:0 0 17px}.cp-history-item:before{content:"";position:absolute;inset-inline-start:-21px;top:5px;width:10px;height:10px;border-radius:50%;background:#3b82f6;box-shadow:0 0 0 4px #dbeafe}.cp-history-item strong{font-size:.83rem;color:#334155}.cp-history-item small{display:block;color:#94a3b8;margin-top:3px}.cp-comment{padding:12px 14px;background:#f8fafc;border:1px solid #edf1f5;border-radius:13px;margin-bottom:10px}.cp-comment-head{display:flex;justify-content:space-between;gap:10px;font-size:.78rem;color:#7a889b;margin-bottom:.45rem}.cp-comment-body{color:#344258;line-height:1.8;white-space:pre-wrap}.cp-empty{text-align:center;padding:34px 20px;color:#94a3b8}.cp-empty i{font-size:2rem;display:block;margin-bottom:.5rem}.cp-form-note{background:#f8fafc;border:1px solid #e7ebf1;border-radius:12px;padding:10px 12px;color:#64748b;font-size:.82rem}.cp-owner-banner{background:linear-gradient(135deg,#eff6ff,#f8fbff);border:1px solid #bfdbfe;border-radius:14px;padding:12px 14px;color:#31527c}.cp-danger-zone{border:1px solid #fecaca;background:#fff7f7;border-radius:14px;padding:12px}.cp-json{direction:ltr;text-align:left;white-space:pre-wrap;word-break:break-all;font-size:.72rem;background:#f8fafc;border-radius:8px;padding:6px}
@media(max-width:1200px){.cp-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:767px){.cp-grid{grid-template-columns:1fr}.cp-hero{padding:18px}.cp-card-body{padding:14px}.cp-table{min-width:850px}.cp-nav{overflow-x:auto;flex-wrap:nowrap;padding-bottom:4px}.cp-nav a{white-space:nowrap}}


/* Customer Portal admin UX v2.4 - visual alignment with IWEB/IPanel management pages */
.customer-portal-page {
    --cu-border: #e5eaf2;
    --cu-text: #172033;
    --cu-muted: #7a8699;
    --cu-soft: #f6f8fc;
    --cu-primary: #356df3;
    background: #f8fbff;
}
.customer-portal-page .cp-shell {
    width: 100%;
    max-width: none;
    /* Inherit standard Bootstrap container-fluid gutters. */
}
.customer-portal-page .cp-hero {
    border: 1px solid #e2e8f5;
    border-radius: 20px;
    padding: 22px 24px;
    background:
        radial-gradient(circle at top left, rgba(53,109,243,.11), transparent 34%),
        linear-gradient(135deg, #ffffff 0%, #f6f9ff 100%);
    box-shadow: 0 12px 35px rgba(37, 54, 89, .06);
    color: var(--cu-text);
}
.customer-portal-page .cp-hero:after { display:none; }
.customer-portal-page .cp-hero h3 { color: var(--cu-text); }
.customer-portal-page .cp-hero p { color: var(--cu-muted); }
.customer-portal-page .cp-role-pill {
    border: 1px solid #d8e5ff;
    background: #eef3ff;
    color: var(--cu-primary);
}
.customer-portal-page .cp-nav {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(135px, 1fr));
    gap: 10px;
    padding: 12px;
    margin: 0 0 18px;
    background: #fff;
    border: 1px solid var(--cu-border);
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(29,42,69,.04);
}
.customer-portal-page .cp-nav a {
    min-height: 48px;
    justify-content: center;
    border-radius: 12px;
    box-shadow: none;
    border-color: transparent;
    background: var(--cu-soft);
    color: #526174;
}
.customer-portal-page .cp-nav a i { font-size: 17px; }
.customer-portal-page .cp-nav a.active,
.customer-portal-page .cp-nav a:hover {
    color: var(--cu-primary);
    border-color: #d8e5ff;
    background: #eef3ff;
}
.customer-portal-page .cp-card,
.customer-portal-page .cp-stat {
    border-color: var(--cu-border);
    box-shadow: 0 8px 25px rgba(29,42,69,.04);
}
.customer-portal-page .cp-card-header {
    background: linear-gradient(180deg,#fff 0%,#fbfdff 100%);
}
.customer-portal-page .cp-card-title { color: var(--cu-text); }
.customer-portal-page .cp-muted { color: var(--cu-muted); }
.customer-portal-page .cp-stat {
    border-top-width: 1px;
    position: relative;
    overflow: hidden;
}
.customer-portal-page .cp-stat:before {
    content:"";
    position:absolute;
    top:0;
    right:0;
    left:0;
    height:3px;
    background:#356df3;
}
.customer-portal-page .cp-stat.success:before { background:#22c55e; }
.customer-portal-page .cp-stat.warning:before { background:#f59e0b; }
.customer-portal-page .cp-stat.danger:before { background:#ef4444; }
@media(max-width:767.98px){
    /* Keep responsive Bootstrap container-fluid gutters. */
    .customer-portal-page .cp-nav{grid-template-columns:1fr 1fr;overflow:visible}
    .customer-portal-page .cp-nav a{white-space:normal;text-align:center}
}


.customer-portal-page .cp-file-picker { position:relative; display:flex; align-items:center; flex-wrap:wrap; gap:10px; min-height:44px; padding:5px 9px; border:1px solid #dee5ee; border-radius:9px; background:#fff; }
.customer-portal-page .cp-file-picker:focus-within { outline:2px solid #c2d7ff; outline-offset:1px; }
.customer-portal-page .cp-file-picker input[type=file] { position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; z-index:2; }
.customer-portal-page .cp-file-picker .cp-file-trigger { display:inline-flex; align-items:center; margin:0; padding:7px 10px; border-radius:7px; background:#eff4ff; border:1px solid #d1ddfb; color:#2a5cb8; font-size:.8rem; font-weight:700; }
.customer-portal-page .cp-file-picker .cp-file-name { flex:1; min-width:0; color:#64748b; overflow-wrap:anywhere; font-size:.77rem; }

/* IWEB Customer Portal: shared IPanel management visual language + mobile-first layout.
   Scoped to this page; no legacy tickets or global admin style changes. */
.customer-portal-page, .customer-portal-page .content,
.customer-portal-page .cp-shell, .customer-portal-page .row > * { min-width:0; }
.customer-portal-page .cp-shell {
    width:100%; max-width:1320px; margin-inline:auto;
    padding:clamp(12px, 2.2vw, 22px) clamp(12px, 2vw, 24px) 30px;
}
.customer-portal-page .cp-hero { border-radius:18px; padding:18px 20px; box-shadow:0 8px 24px rgba(15,35,70,.06); }
.customer-portal-page .cp-hero h3 { font-size:clamp(1.03rem,2.1vw,1.25rem); line-height:1.55; }
.customer-portal-page .cp-hero p { font-size:.84rem; line-height:1.8; }
.customer-portal-page .cp-nav { grid-template-columns:repeat(auto-fit,minmax(142px,1fr)); }
.customer-portal-page .cp-nav a { min-height:44px; padding:.55rem .65rem; font-size:.83rem; }
.customer-portal-page .cp-card, .customer-portal-page .cp-stat { border-radius:16px; }
.customer-portal-page .cp-card-header { flex-wrap:wrap; align-items:flex-start; min-width:0; }
.customer-portal-page .cp-card-header > div { min-width:0; }
.customer-portal-page .cp-card-title { font-size:.93rem; line-height:1.7; }
.customer-portal-page .cp-card-body { min-width:0; }
.customer-portal-page .cp-table { min-width:0; }
.customer-portal-page .table-responsive { min-width:0; max-width:100%; -webkit-overflow-scrolling:touch; }
.customer-portal-page .cp-table td { overflow-wrap:anywhere; }
.customer-portal-page .cp-actions { align-items:center; }
.customer-portal-page .cp-draft-card, .customer-portal-page .cp-draft-title { overflow-wrap:anywhere; }
.customer-portal-page input, .customer-portal-page select,
.customer-portal-page textarea { max-width:100%; min-width:0; }
.customer-portal-page textarea { resize:vertical; }
.customer-portal-page .form-control[type=file] { width:100%; min-height:44px; }
.customer-portal-page .cp-info-row { min-width:0; }
.customer-portal-page .cp-info-row strong { overflow-wrap:anywhere; }
.customer-portal-page .list-group-item { flex-wrap:wrap; min-width:0; }
.customer-portal-page .list-group-item > div { min-width:0; overflow-wrap:anywhere; }
.customer-portal-page .cp-comment-body { overflow-wrap:anywhere; }
.customer-portal-page .modal-dialog { max-width:min(560px,calc(100vw - 24px)); margin-inline:auto; }
.customer-portal-page .modal-content { max-width:100%; }
.customer-portal-page .btn:not(.btn-close) { min-height:36px; }
@media(max-width:991.98px) {
    .customer-portal-page .cp-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
    .customer-portal-page .cp-nav { grid-template-columns:repeat(3,minmax(0,1fr)); }
}
@media(max-width:767.98px) {
    .customer-portal-page .cp-shell { padding:12px 12px 24px; }
    .customer-portal-page .cp-hero { padding:14px 16px; }
    .customer-portal-page .cp-hero > div { align-items:flex-start!important; }
    .customer-portal-page .cp-nav { grid-template-columns:repeat(2,minmax(0,1fr)); padding:8px; gap:6px; }
    .customer-portal-page .cp-nav a { min-height:44px; white-space:normal; text-align:center; }
    .customer-portal-page .cp-card-header { padding:12px 14px; }
    .customer-portal-page .cp-card-body { padding:14px; }
    .customer-portal-page .cp-card-header > .btn,
    .customer-portal-page .cp-card-header > a.btn { width:100%; }
    .customer-portal-page .cp-actions .btn { min-width:36px; }
    .customer-portal-page .cp-role-pill { max-width:100%; white-space:normal; }
}
@media(max-width:575.98px) {
    .customer-portal-page .cp-grid { grid-template-columns:1fr; gap:10px; }
    .customer-portal-page .cp-stat { padding:12px 14px; }
    .customer-portal-page .cp-stat .value { font-size:1.4rem; }
    .customer-portal-page .cp-card-header { display:flex; flex-direction:column; align-items:stretch; gap:6px; }
    .customer-portal-page .cp-card-header .cp-muted { font-size:.78rem; }
    .customer-portal-page .cp-draft-card { padding:13px; }
    /* Cards instead of wide, sideways-scrolling management tables on phones. */
    .customer-portal-page .table-responsive:has(.cp-table.cp-mobile-cards) { overflow:visible; }
    .customer-portal-page .cp-table.cp-mobile-cards,
    .customer-portal-page .cp-table.cp-mobile-cards tbody,
    .customer-portal-page .cp-table.cp-mobile-cards tr,
    .customer-portal-page .cp-table.cp-mobile-cards td { display:block; width:100%; min-width:0; }
    .customer-portal-page .cp-table.cp-mobile-cards thead { display:none; }
    .customer-portal-page .cp-table.cp-mobile-cards tbody { padding:10px; }
    .customer-portal-page .cp-table.cp-mobile-cards tbody tr { margin-bottom:10px; padding:8px 10px; background:#fff; border:1px solid #e5eaf2; border-radius:12px; box-shadow:0 3px 10px rgba(15,35,70,.035); }
    .customer-portal-page .cp-table.cp-mobile-cards tbody tr:last-child { margin-bottom:0; }
    .customer-portal-page .cp-table.cp-mobile-cards td { padding:8px 0; border-bottom:1px dashed #e6eaf1; text-align:start!important; overflow-wrap:anywhere; }
    .customer-portal-page .cp-table.cp-mobile-cards td:last-child { border-bottom:0; }
    .customer-portal-page .cp-table.cp-mobile-cards td[data-label]:not([colspan]) { display:grid; grid-template-columns:minmax(84px,36%) minmax(0,1fr); align-items:center; gap:10px; }
    .customer-portal-page .cp-table.cp-mobile-cards td[data-label]:not([colspan])::before { content:attr(data-label); font-size:.74rem; font-weight:700; color:#718099; }
    .customer-portal-page .cp-table.cp-mobile-cards td[colspan] { padding:18px 4px; text-align:center!important; }
    .customer-portal-page .cp-table.cp-mobile-cards .cp-actions { justify-content:flex-start; flex-wrap:wrap; }
    .customer-portal-page .cp-table.cp-mobile-cards td .cp-json { max-width:100%; }
    .customer-portal-page .cp-table.cp-mobile-cards td > a { overflow-wrap:anywhere; }
    .customer-portal-page .cp-info-row { flex-direction:column; align-items:flex-start; gap:4px; }
    .customer-portal-page .cp-info-row strong { text-align:start; }
    .customer-portal-page .cp-card-body .input-group { flex-wrap:wrap; gap:8px; }
    .customer-portal-page .cp-card-body .input-group > .form-select,
    .customer-portal-page .cp-card-body .input-group > .btn { width:100%; flex:0 0 100%; border-radius:8px!important; }
    .customer-portal-page .cp-role-pill { font-size:.74rem; }
    .customer-portal-page .list-group-item .cp-actions { width:100%; }
}
@media(max-width:360px) {
    .customer-portal-page .cp-nav { grid-template-columns:1fr; }
    .customer-portal-page .cp-table.cp-mobile-cards td[data-label]:not([colspan]) { grid-template-columns:minmax(66px,34%) minmax(0,1fr); }
}

/* Portal organizational chart, scoped to its own component. */
.cp-org-chart .cp-org-top{text-align:center;margin-bottom:20px}
.cp-org-chart .cp-org-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px}
.cp-org-chart .cp-org-section{border:1px solid #e2e9f3;border-radius:14px;padding:15px;background:#fbfcff;min-width:0}
.cp-org-chart .cp-org-heading{font-weight:800;color:#243955;margin-bottom:12px;font-size:.92rem}
.cp-org-chart .cp-org-tier{font-size:.78rem;color:#64748b;margin:12px 0 8px;font-weight:700}
.cp-org-chart .cp-org-people{display:flex;flex-wrap:wrap;gap:8px;justify-content:flex-start}
.cp-org-chart .cp-org-top .cp-org-people{justify-content:center}
.cp-org-chart .cp-org-person{display:flex;flex-direction:column;gap:3px;padding:9px 12px;border:1px solid #dce5f0;border-radius:11px;background:#fff;min-width:110px;max-width:100%;overflow-wrap:anywhere;font-size:.82rem}
.cp-org-chart .cp-org-person small{color:#7b8797;font-size:.72rem}
.cp-org-chart .cp-org-person.admin{border-color:#bdd3ff;background:#eef4ff}
.cp-org-chart .cp-org-person.manager{border-color:#c0d7f2;background:#f0f7ff}
.cp-org-chart .cp-org-unassigned{border-top:1px solid #e5ebf2;margin-top:20px;padding-top:8px}
@media(max-width:575px){.cp-org-chart .cp-org-grid{grid-template-columns:1fr}}

/* Portal theme: honor the shell's light/dark mode, scoped to this portal only. */
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page {
    --cu-border:#344255;
    --cu-text:#e8eef7;
    --cu-muted:#adbbca;
    --cu-soft:#283547;
    --cu-primary:#9abaff;
    --cp-dark-panel:#202b39;
    --cp-dark-panel-soft:#263344;
    --cp-dark-border:#344255;
    background:#17212d;
    color:#dbe5ef;
}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page .cp-hero {
    background:linear-gradient(135deg,#1c3049,#263e5c);
    border-color:#3a5068;
    color:#edf3fb;
    box-shadow:0 8px 24px rgba(0,0,0,.15);
}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page .cp-hero h3 {color:#f0f5ff;}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page .cp-hero p {color:#b8c9df;}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page :is(.cp-card,.cp-stat,.cp-nav,.cp-draft-card,.cp-comment,.cp-form-note,.cp-json,.cp-org-section,.cp-org-person,.cp-file-picker,.cp-danger-zone,.cp-owner-banner) {
    background:var(--cp-dark-panel);
    border-color:var(--cp-dark-border);
    color:#dbe5ef;
    box-shadow:none;
}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page :is(.cp-card-header,.cp-table thead th,.cp-nav a,.cp-org-person.admin,.cp-org-person.manager,.cp-file-picker .cp-file-trigger) {
    background:var(--cp-dark-panel-soft);
    color:#dbe5ef;
    border-color:var(--cp-dark-border);
}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page :is(.cp-card-title,.cp-section-name,.cp-draft-title,.cp-stat .value,.cp-org-heading,.cp-info-row strong,.cp-history-item strong,.cp-comment-body) {color:#ecf3ff;}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page :is(.cp-muted,.cp-stat .label,.cp-table thead th,.cp-org-tier,.cp-org-person small,.cp-info-row span,.cp-draft-meta,.cp-comment-head,.cp-empty) {color:#b2c0d1;}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page .cp-table :is(td,th) {color:#dbe5ef;border-color:#394859;background:transparent;}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page .cp-table tbody tr:hover td {background:#29394c;}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page :is(.form-control,.form-select,.input-group-text) {background-color:#263344;color:#eef4fc;border-color:#47586a;}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page .form-control::placeholder {color:#a3b0bf;}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page .cp-nav a:is(.active,:hover) {background:#314968;border-color:#607b9d;color:#e0ecff;}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page :is(.cp-role-pill,.cp-badge.role) {background:#284568;color:#bcd5ff;border-color:#52739c;}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page .cp-org-unassigned {border-color:#344255;}
@media(max-width:575.98px){
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page .cp-table.cp-mobile-cards tbody tr {background:#202b39;border-color:#344255;}
:is(html[data-bs-theme="dark"],body[data-bs-theme="dark"],html[data-layout-color="dark"],body[data-layout-color="dark"]) .customer-portal-page .cp-table.cp-mobile-cards td {border-color:#344255;}
}

/* The role pill must stay on the opposite side of the hero title in RTL/LTR. */
.customer-portal-page .cp-hero > .cp-hero-layout {
    display:flex !important;
    flex-direction:row !important;
    align-items:center !important;
    justify-content:space-between !important;
    flex-wrap:wrap !important;
    width:100%;
    gap:12px;
}
.customer-portal-page .cp-hero-layout > .cp-hero-text {flex:1 1 220px;min-width:0;}
.customer-portal-page .cp-hero-layout > .cp-role-pill {
    position:relative !important;
    inset:auto !important;
    order:1;
    flex:0 0 auto;
    margin-inline-start:auto !important;
    max-width:100%;
    white-space:normal;
    background:#fff !important;
    color:#195177 !important;
    border-color:rgba(255,255,255,.8) !important;
    box-shadow:0 4px 15px rgba(0,0,0,.08);
}
@media(max-width:575px){
    .customer-portal-page .cp-hero-layout > .cp-role-pill {margin-inline-start:0 !important;}
}
</style>
<div class="content-page customer-portal-page">
    <div class="content">
        <div class="container-fluid cp-shell">
            <div class="cp-hero mb-3">
                <div class="cp-hero-layout position-relative" style="z-index:1">
                    <div class="cp-hero-text">
                        <h3><i class="ri-building-4-line me-1"></i><?php echo _lang['cp_title']; ?></h3>
                        <p><?php echo _lang['cp_subtitle_' . strtolower($role)] ?? _lang['cp_subtitle']; ?></p>
                    </div>
                    <?php if (!empty($customerPortalContext['portal_role'])): ?>
                        <div class="cp-role-pill"><i class="ri-shield-user-line"></i><?php echo cp_e(_lang['cp_role_' . strtolower($customerPortalContext['portal_role'])]); ?><?php if (!empty($customerPortalContext['section_name'])): ?> · <?php echo cp_e($customerPortalContext['section_name']); ?><?php endif; ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($customerPortalFlash): ?>
                <div class="alert alert-<?php echo cp_e($customerPortalFlash['type']); ?> alert-dismissible fade show" role="alert">
                    <?php echo cp_e(cp_flash_text($customerPortalFlash['message'])); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($customerPortalBlockedReason !== null): ?>
                <div class="cp-card">
                    <div class="cp-empty py-5">
                        <i class="ri-lock-2-line"></i>
                        <h5><?php echo cp_e(cp_flash_text($customerPortalBlockedReason)); ?></h5>
                        <?php if ($customerPortalBlockedReason === 'CUSTOMER_PORTAL_SECTION_REQUIRED'): ?>
                            <p class="mb-0"><?php echo _lang['cp_pending_assignment_note']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="cp-nav">
                    <a href="./customer_portal?view=dashboard" class="<?php echo $customerPortalView === 'dashboard' ? 'active' : ''; ?>"><i class="ri-dashboard-line"></i><?php echo _lang['cp_dashboard']; ?></a>
                    <a href="./customer_portal?view=drafts" class="<?php echo in_array($customerPortalView, ['drafts','draft'], true) ? 'active' : ''; ?>"><i class="ri-file-list-3-line"></i><?php echo _lang['cp_requests']; ?></a>
                    <?php if (!empty($customerPortalContext['can_create_draft'])): ?>
                        <a href="./customer_portal?view=new" class="<?php echo $customerPortalView === 'new' ? 'active' : ''; ?>"><i class="ri-add-circle-line"></i><?php echo _lang['cp_new_request']; ?></a>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <a href="./customer_portal?view=sections" class="<?php echo $customerPortalView === 'sections' ? 'active' : ''; ?>"><i class="ri-node-tree"></i><?php echo _lang['cp_sections']; ?></a>
                        <a href="./customer_portal?view=company_tickets" class="<?php echo $customerPortalView === 'company_tickets' ? 'active' : ''; ?>"><i class="ri-ticket-2-line"></i><?php echo _lang['cp_company_tickets']; ?></a>
                    <?php endif; ?>
                    <?php if ($isAdmin || $isManager): ?>
                        <a href="./customer_portal?view=users" class="<?php echo $customerPortalView === 'users' ? 'active' : ''; ?>"><i class="ri-team-line"></i><?php echo _lang['cp_users']; ?></a>
                        <a href="./customer_portal?view=reports" class="<?php echo $customerPortalView === 'reports' ? 'active' : ''; ?>"><i class="ri-bar-chart-box-line"></i><?php echo _lang['cp_reports']; ?></a>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <a href="./customer_portal?view=audit" class="<?php echo $customerPortalView === 'audit' ? 'active' : ''; ?>"><i class="ri-history-line"></i><?php echo _lang['cp_audit']; ?></a>
                    <?php endif; ?>
                </div>

                <?php if ($customerPortalView === 'dashboard'): ?>
                    <?php
                    $reportSummary = $customerPortalDashboard['report']['summary'] ?? [];
                    $recentDrafts = $customerPortalDashboard['recent_drafts'] ?? [];
                    ?>
                    <?php if ($isAdmin): ?>
                        <?php $overview = $customerPortalDashboard['company_overview'] ?? []; ?>
                        <div class="cp-card mb-3"><div class="cp-card-header"><div><h5 class="cp-card-title"><?php echo _lang['cp_company_overview']; ?></h5><div class="cp-muted"><?php echo _lang['cp_company_overview_note']; ?></div></div><a href="./customer_portal?view=company_tickets" class="btn btn-sm btn-light"><?php echo _lang['cp_company_tickets']; ?></a></div>
                            <div class="cp-card-body"><div class="cp-grid">
                                <div class="cp-stat primary"><div class="value"><?php echo number_format((int)($overview['total_tickets'] ?? 0)); ?></div><div class="label"><?php echo _lang['cp_total_registered_tickets']; ?></div></div>
                                <div class="cp-stat success"><div class="value"><?php echo number_format((int)($overview['tickets_30d'] ?? 0)); ?></div><div class="label"><?php echo _lang['cp_tickets_last30']; ?></div></div>
                                <div class="cp-stat warning"><div class="value"><?php echo number_format((int)($overview['active_sections'] ?? 0)); ?></div><div class="label"><?php echo _lang['cp_active_sections']; ?></div></div>
                                <div class="cp-stat primary"><div class="value"><?php echo number_format((int)($overview['active_users'] ?? 0)); ?></div><div class="label"><?php echo _lang['cp_active_portal_users']; ?></div><div class="cp-muted"><?php echo _lang['cp_active_managers']; ?>: <?php echo (int)($overview['active_managers'] ?? 0); ?> · <?php echo _lang['cp_active_experts']; ?>: <?php echo (int)($overview['active_experts'] ?? 0); ?></div></div>
                            </div></div>
                        </div>
                    <?php endif; ?>
                    <div class="cp-muted mb-2"><?php echo _lang['cp_draft_metrics_note']; ?></div>
                    <div class="cp-grid mb-3">
                        <div class="cp-stat primary"><div class="value"><?php echo number_format((int)($reportSummary['total'] ?? 0)); ?></div><div class="label"><?php echo _lang['cp_metric_total']; ?></div></div>
                        <div class="cp-stat warning"><div class="value"><?php echo number_format((int)($reportSummary['open_count'] ?? 0)); ?></div><div class="label"><?php echo _lang['cp_metric_open']; ?></div></div>
                        <div class="cp-stat success"><div class="value"><?php echo number_format((int)($reportSummary['sent'] ?? 0)); ?></div><div class="label"><?php echo _lang['cp_metric_sent']; ?></div></div>
                        <div class="cp-stat danger"><div class="value"><?php echo number_format((int)($reportSummary['aging_count'] ?? 0)); ?></div><div class="label"><?php echo $isExpert ? _lang['cp_metric_owned'] : _lang['cp_metric_aging']; ?></div></div>
                    </div>
                    <div class="cp-card">
                        <div class="cp-card-header"><div><h5 class="cp-card-title"><?php echo _lang['cp_recent_requests']; ?></h5><div class="cp-muted"><?php echo _lang['cp_recent_requests_note']; ?></div></div><a href="./customer_portal?view=drafts" class="btn btn-sm btn-light"><?php echo _lang['view_all']; ?></a></div>
                        <div class="cp-card-body">
                            <?php if (empty($recentDrafts)): ?><div class="cp-empty"><i class="ri-inbox-line"></i><?php echo _lang['cp_no_requests']; ?></div><?php else: ?>
                                <div class="row g-3">
                                    <?php foreach ($recentDrafts as $draft): ?>
                                        <div class="col-xl-3 col-lg-4 col-md-6"><a class="cp-draft-card" href="./customer_portal?view=draft&draft_id=<?php echo (int)$draft['id']; ?>">
                                            <span class="cp-status <?php echo cp_status_class($draft['workflow_status']); ?>"><?php echo cp_e(cp_status_label($draft['workflow_status'])); ?></span>
                                            <div class="cp-draft-title"><?php echo cp_e($draft['title']); ?></div>
                                            <div class="cp-draft-meta"><span><i class="ri-building-line"></i> <?php echo cp_e($draft['section_name']); ?></span><span><i class="ri-user-line"></i> <?php echo cp_e($draft['owner_name']); ?></span></div>
                                        </a></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($customerPortalView === 'company_tickets' && $isAdmin): ?>
                    <?php $ct = $customerPortalCompanyTickets ?? ['rows'=>[],'total'=>0,'page'=>1,'pages'=>1]; ?>
                    <?php
                    $ticketSearch = (string)($ct['q'] ?? '');
                    $ticketPriority = (string)($ct['priority'] ?? '');
                    $ticketBase = './customer_portal?view=company_tickets&amp;q=' . rawurlencode($ticketSearch) . '&amp;priority=' . rawurlencode($ticketPriority);
                    ?>
                    <div class="cp-card mb-3">
                        <div class="cp-card-body">
                            <form action="./customer_portal" method="get" class="row g-2 align-items-end">
                                <input type="hidden" name="view" value="company_tickets">
                                <div class="col-lg-7"><label class="form-label"><?php echo _lang['cp_ticket_search_label']; ?></label><input type="search" class="form-control" name="q" value="<?php echo cp_e($ticketSearch); ?>" placeholder="<?php echo cp_e(_lang['cp_ticket_search_placeholder']); ?>"></div>
                                <div class="col-lg-3"><label class="form-label"><?php echo _lang['priority']; ?></label><select class="form-select" name="priority">
                                <option value=""><?php echo _lang['cp_ticket_all_priorities']; ?></option>
                                <?php foreach (['low','medium','high'] as $value): ?>
                                    <option value="<?php echo $value; ?>" <?php echo $ticketPriority === $value ? 'selected' : ''; ?>><?php echo cp_e(_lang[$value] ?? $value); ?></option>
                                <?php endforeach; ?></select></div>
                                <div class="col-lg-2"><button class="btn btn-primary w-100"><i class="ri-search-line me-1"></i><?php echo _lang['search']; ?></button></div>
                            </form>
                            <div class="d-flex gap-2 mt-3 flex-wrap">
                                <a href="<?php echo $ticketBase; ?>&amp;export=csv" class="btn btn-sm btn-outline-success"><i class="ri-file-excel-2-line me-1"></i><?php echo _lang['cp_ticket_export_csv']; ?></a>
                                <a href="./customer_portal?view=company_tickets" class="btn btn-sm btn-outline-secondary"><?php echo _lang['cp_ticket_clear_filters']; ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="cp-card">
                        <div class="cp-card-header"><div><h5 class="cp-card-title"><?php echo _lang['cp_company_tickets']; ?></h5><div class="cp-muted"><?php echo _lang['cp_company_tickets_note']; ?></div></div><strong><?php echo number_format((int)$ct['total']); ?></strong></div>
                        <div class="table-responsive"><table class="table cp-table"><thead><tr>
                            <th><?php echo _lang['cp_ticket_number']; ?></th><th><?php echo _lang['cp_ticket_subject']; ?></th><th><?php echo _lang['added_date']; ?></th><th><?php echo _lang['priority']; ?></th>
                        </tr></thead><tbody>
                        <?php if (empty($ct['rows'])): ?><tr><td colspan="4" class="text-center p-4"><?php echo _lang['cp_no_company_tickets']; ?></td></tr><?php else: ?>
                            <?php foreach ($ct['rows'] as $t): ?><tr>
                                <td><?php echo cp_e($t['ticket_number'] ?: $t['ticket_id']); ?></td>
                                <td><a class="text-decoration-none fw-semibold" href="./customer_portal?view=company_ticket&amp;ticket_id=<?php echo (int)$t['ticket_id']; ?>"><?php echo cp_e($t['ticket_title']); ?></a></td>
                                <td><?php echo cp_e(cp_date($t['ticket_creation_date'], $config)); ?></td>
                                <td><?php echo cp_e(_lang[(string)$t['priority']] ?? (string)$t['priority']); ?></td>
                            </tr><?php endforeach; ?>
                        <?php endif; ?>
                        </tbody></table></div>
                        <?php if ((int)$ct['pages'] > 1): ?>
                            <div class="cp-card-body d-flex justify-content-between align-items-center gap-2">
                                <span class="cp-muted"><?php echo _lang['cp_page']; ?> <?php echo (int)$ct['page']; ?> / <?php echo (int)$ct['pages']; ?></span>
                                <div class="d-flex gap-2">
                                    <?php if ((int)$ct['page'] > 1): ?><a class="btn btn-sm btn-outline-secondary" href="<?php echo $ticketBase; ?>&amp;page_num=<?php echo (int)$ct['page']-1; ?>"><?php echo _lang['cp_prev_page']; ?></a><?php endif; ?>
                                    <?php if ((int)$ct['page'] < (int)$ct['pages']): ?><a class="btn btn-sm btn-outline-primary" href="<?php echo $ticketBase; ?>&amp;page_num=<?php echo (int)$ct['page']+1; ?>"><?php echo _lang['cp_next_page']; ?></a><?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($customerPortalView === 'company_ticket' && $isAdmin): ?>
                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
                        <h5 class="mb-0"><i class="ri-file-text-line me-1"></i><?php echo _lang['cp_company_ticket_detail']; ?></h5>
                        <a class="btn btn-sm btn-outline-secondary" href="./customer_portal?view=company_tickets"><i class="ri-arrow-go-back-line me-1"></i><?php echo _lang['cp_ticket_detail_back']; ?></a>
                    </div>
                    <?php if (empty($customerPortalCompanyTicketDetail)): ?>
                        <div class="cp-card"><div class="cp-empty"><i class="ri-lock-line"></i><?php echo _lang['cp_ticket_detail_not_found']; ?></div></div>
                    <?php else: ?>
                        <?php $ctd = $customerPortalCompanyTicketDetail; ?>
                        <div class="row g-3">
                            <div class="col-xl-8">
                                <div class="cp-card mb-3">
                                    <div class="cp-card-header"><h5 class="cp-card-title"><?php echo cp_e($ctd['ticket_title']); ?></h5><span class="cp-badge role">#<?php echo cp_e($ctd['ticket_number'] ?: $ctd['ticket_id']); ?></span></div>
                                    <div class="cp-card-body"><div class="cp-muted mb-2"><?php echo _lang['cp_ticket_description']; ?></div><div style="white-space:pre-wrap;overflow-wrap:anywhere;line-height:1.9"><?php echo cp_e(html_entity_decode((string)($ctd['ticket_description'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?></div></div>
                                </div>
                                <div class="cp-card">
                                    <div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['cp_ticket_comments']; ?></h5><span class="cp-muted"><?php echo _lang['cp_ticket_comments_note']; ?></span></div>
                                    <div class="cp-card-body">
                                        <?php if (empty($ctd['comments'])): ?><div class="cp-empty"><?php echo _lang['cp_ticket_no_comments']; ?></div><?php else: ?>
                                            <?php foreach ($ctd['comments'] as $comment): ?>
                                                <div class="cp-comment"><div class="cp-comment-head"><strong><?php echo cp_e($comment['author_name']); ?></strong><span><?php echo cp_e(cp_date($comment['creation_date'], $config)); ?></span></div><div class="cp-comment-body"><?php echo cp_e(html_entity_decode((string)$comment['comment_text'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?></div></div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4"><div class="cp-card"><div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['cp_request_info']; ?></h5></div>
                                <div class="cp-card-body cp-side-info">
                                    <div class="cp-info-row"><span><?php echo _lang['cp_ticket_number']; ?></span><strong><?php echo cp_e($ctd['ticket_number'] ?: $ctd['ticket_id']); ?></strong></div>
                                    <div class="cp-info-row"><span><?php echo _lang['added_date']; ?></span><strong><?php echo cp_e(cp_date($ctd['ticket_creation_date'], $config)); ?></strong></div>
                                    <div class="cp-info-row"><span><?php echo _lang['priority']; ?></span><strong><?php $ctdPriority=(string)($ctd['priority'] ?? ''); echo cp_e(_lang[$ctdPriority] ?? $ctdPriority); ?></strong></div>
                                    <div class="cp-info-row"><span><?php echo _lang['cp_ticket_status']; ?></span><strong><?php $ctdStatus=(string)($ctd['status'] ?? ''); echo cp_e(_lang[$ctdStatus] ?? $ctdStatus); ?></strong></div>
                                    <div class="cp-info-row"><span><?php echo _lang['type']; ?></span><strong><?php echo cp_e(trim((string)($ctd['type_group'] ?? '') . ' / ' . (string)($ctd['type_name'] ?? ''), ' /')); ?></strong></div>
                                    <?php if (!empty($ctd['indicator_number'])): ?><div class="cp-info-row"><span><?php echo _lang['cp_ticket_indicator']; ?></span><strong><?php echo cp_e($ctd['indicator_number']); ?></strong></div><?php endif; ?>
                                </div></div></div>
                        </div>
                    <?php endif; ?>

                <?php elseif ($customerPortalView === 'sections' && $isAdmin): ?>
                    <div class="row g-3">
                        <div class="col-xl-4">
                            <div class="cp-card"><div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['cp_add_section']; ?></h5></div><div class="cp-card-body">
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="section_create"><input type="hidden" name="return_view" value="sections">
                                    <label class="form-label"><?php echo _lang['cp_section_name']; ?></label><input class="form-control mb-3" name="section_name" maxlength="150" required>
                                    <button class="btn btn-primary w-100"><i class="ri-add-line me-1"></i><?php echo _lang['add']; ?></button>
                                </form>
                            </div></div>
                        </div>
                        <div class="col-xl-8">
                            <div class="cp-card"><div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['cp_sections']; ?></h5><span class="cp-muted"><?php echo number_format(count($customerPortalSections)); ?></span></div><div class="table-responsive">
                                <table class="table cp-table"><thead><tr><th><?php echo _lang['name']; ?></th><th><?php echo _lang['status']; ?></th><th><?php echo _lang['cp_manager']; ?></th><th><?php echo _lang['cp_experts']; ?></th><th><?php echo _lang['action']; ?></th></tr></thead><tbody>
                                <?php foreach ($customerPortalSections as $section): ?>
                                    <tr><td><span class="cp-section-name"><?php echo cp_e($section['name']); ?></span></td><td><span class="cp-badge <?php echo $section['is_active'] ? 'active':'inactive'; ?>"><?php echo $section['is_active'] ? _lang['active']:_lang['inactive']; ?></span></td><td><?php echo cp_e($section['manager_name'] ?: '-'); ?></td><td><?php echo number_format((int)$section['expert_count']); ?></td><td><div class="cp-actions">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSection<?php echo (int)$section['id']; ?>"><i class="ri-edit-line"></i></button>
                                        <?php if (!empty($customerPortalContext['permissions']['customer_admin_assign_manager_to_section'])): ?><button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#managerSection<?php echo (int)$section['id']; ?>"><i class="ri-user-settings-line"></i></button><?php endif; ?>
                                        <form method="post"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="section_toggle"><input type="hidden" name="section_id" value="<?php echo (int)$section['id']; ?>"><input type="hidden" name="set_active" value="<?php echo $section['is_active'] ? '0':'1'; ?>"><input type="hidden" name="return_view" value="sections"><button class="btn btn-sm <?php echo $section['is_active']?'btn-outline-danger':'btn-outline-success'; ?>"><i class="ri-<?php echo $section['is_active']?'stop':'play'; ?>-circle-line"></i></button></form>
                                    </div></td></tr>
                                    <div class="modal fade" id="editSection<?php echo (int)$section['id']; ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="post"><div class="modal-header"><h5 class="modal-title"><?php echo _lang['cp_edit_section']; ?></h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="section_update"><input type="hidden" name="return_view" value="sections"><input type="hidden" name="section_id" value="<?php echo (int)$section['id']; ?>"><input class="form-control" name="section_name" value="<?php echo cp_e($section['name']); ?>" required></div><div class="modal-footer"><button class="btn btn-primary"><?php echo _lang['save_changes']; ?></button></div></form></div></div></div>
                                    <?php if (!empty($customerPortalContext['permissions']['customer_admin_assign_manager_to_section'])): ?><div class="modal fade" id="managerSection<?php echo (int)$section['id']; ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="post"><div class="modal-header"><h5 class="modal-title"><?php echo _lang['cp_assign_manager']; ?></h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="assign_manager"><input type="hidden" name="return_view" value="sections"><input type="hidden" name="section_id" value="<?php echo (int)$section['id']; ?>"><select class="form-select" name="manager_user_id" required><option value=""><?php echo _lang['select']; ?></option><?php foreach ($customerPortalUsers as $u): if ($u['portal_role'] !== 'MANAGER' || !$u['profile_active'] || $u['status'] !== 'Active'
                                        || ($u['section_id'] !== null && (int)$u['section_id'] !== (int)$section['id'])) continue; ?><option value="<?php echo (int)$u['user_id']; ?>" <?php echo (int)$section['manager_user_id']===(int)$u['user_id']?'selected':''; ?>><?php echo cp_e($u['name']); ?><?php if ($u['section_name']): ?> - <?php echo cp_e($u['section_name']); ?><?php endif; ?></option><?php endforeach; ?></select><div class="cp-form-note mt-2"><?php echo _lang['cp_assign_manager_note']; ?></div></div><div class="modal-footer"><button class="btn btn-primary"><?php echo _lang['cp_assign']; ?></button></div></form></div></div></div><?php endif; ?>
                                <?php endforeach; ?>
                                </tbody></table>
                            </div></div>
                        </div>
                    </div>

                <?php elseif ($customerPortalView === 'users' && ($isAdmin || $isManager)): ?>
                    <?php
                    $canAdminManager = $isAdmin && !empty($customerPortalContext['permissions']['customer_admin_create_manager']);
                    $canAdminExpert = $isAdmin && !empty($customerPortalContext['permissions']['customer_admin_create_expert']);
                    $canManagerExpert = $isManager && !empty($customerPortalContext['permissions']['manager_create_expert']);
                    $canCreateUser = $canAdminManager || $canAdminExpert || $canManagerExpert;
                    ?>
                    <?php if ($canCreateUser): ?><div class="cp-card mb-3"><div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['cp_create_user']; ?></h5></div><div class="cp-card-body"><form method="post"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="user_create"><input type="hidden" name="return_view" value="users"><div class="row g-3">
                        <div class="col-lg-3"><label class="form-label"><?php echo _lang['name']; ?></label><input class="form-control" name="name" required></div>
                        <div class="col-lg-3"><label class="form-label"><?php echo _lang['email']; ?></label><input class="form-control" type="email" name="email" required></div>
                        <div class="col-lg-2"><label class="form-label"><?php echo _lang['mobile']; ?></label><input class="form-control" name="mobile"></div>
                        <div class="col-lg-2"><label class="form-label"><?php echo _lang['password']; ?></label><input class="form-control" type="password" name="password" minlength="8" required></div>
                        <div class="col-lg-2"><label class="form-label"><?php echo _lang['role']; ?></label><select class="form-select" name="portal_role" required><?php if ($canAdminManager): ?><option value="MANAGER"><?php echo _lang['cp_role_manager']; ?></option><?php endif; ?><?php if ($canAdminExpert || $canManagerExpert): ?><option value="EXPERT"><?php echo _lang['cp_role_expert']; ?></option><?php endif; ?></select></div>
                        <div class="col-lg-4"><label class="form-label"><?php echo _lang['cp_section']; ?></label><?php if ($isManager): ?><input type="hidden" name="section_id" value="<?php echo (int)$customerPortalContext['section_id']; ?>"><input class="form-control" value="<?php echo cp_e($customerPortalContext['section_name']); ?>" disabled><?php else: ?><select class="form-select" name="section_id" required><option value=""><?php echo _lang['select']; ?></option><?php foreach ($customerPortalSections as $s): if (!$s['is_active']) continue; ?>
                            <option value="<?php echo (int)$s['id']; ?>" data-available-manager="<?php echo empty($s['manager_user_id']) ? '1' : '0'; ?>" data-available-expert="<?php echo !empty($s['manager_user_id']) ? '1' : '0'; ?>"><?php echo cp_e($s['name']); ?></option><?php endforeach; ?></select><?php endif; ?></div>
                        <div class="col-lg-2 d-flex align-items-end"><button class="btn btn-primary w-100"><?php echo _lang['cp_create_user']; ?></button></div>
                    </div></form></div></div>
                    <script>
                    (function () {
                        var form = document.querySelector('form input[name="cp_action"][value="user_create"]');
                        form = form ? form.closest('form') : null;
                        if (!form) return;
                        var role = form.querySelector('select[name="portal_role"]');
                        var section = form.querySelector('select[name="section_id"]');
                        if (!role || !section) return;
                        function filterSections() {
                            var capability = role.value === 'MANAGER' ? 'availableManager' : 'availableExpert';
                            Array.prototype.forEach.call(section.options, function (option) {
                                if (!option.value) return;
                                option.disabled = option.dataset[capability] !== '1';
                                option.hidden = option.disabled;
                            });
                            if (section.selectedOptions.length && section.selectedOptions[0].disabled) section.value = '';
                        }
                        role.addEventListener('change', filterSections);
                        filterSections();
                    })();
                    </script><?php endif; ?>
                    <div class="cp-card"><div class="cp-card-header"><div><h5 class="cp-card-title"><?php echo _lang['cp_users']; ?></h5><div class="cp-muted"><?php echo _lang['cp_users_note']; ?></div></div></div><div class="table-responsive"><table class="table cp-table"><thead><tr><th><?php echo _lang['name']; ?></th><th><?php echo _lang['role']; ?></th><th><?php echo _lang['cp_section']; ?></th><th><?php echo _lang['email']; ?></th><th><?php echo _lang['status']; ?></th><th><?php echo _lang['action']; ?></th></tr></thead><tbody>
                    <?php foreach ($customerPortalUsers as $u): if ($isManager && $u['portal_role'] !== 'EXPERT') continue; ?><tr><td><strong><?php echo cp_e($u['name']); ?></strong></td><td><span class="cp-badge role"><?php echo cp_e(_lang['cp_role_' . strtolower($u['portal_role'])]); ?></span><?php if ($u['portal_role']==='MANAGER'): ?><div class="mt-1"><span class="badge <?php echo !empty($customerPortalFinancePermissions[(int)$u['user_id']]) ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>"><i class="ri-bank-card-line me-1"></i><?php echo cp_e(!empty($customerPortalFinancePermissions[(int)$u['user_id']]) ? _lang['cp_finance_status_enabled'] : _lang['cp_finance_status_disabled']); ?></span></div><?php endif; ?></td><td><?php echo cp_e($u['section_name'] ?: _lang['cp_unassigned']); ?></td><td><?php echo cp_e($u['email']); ?></td><td><span class="cp-badge <?php echo ($u['profile_active'] && $u['status']==='Active')?'active':'inactive'; ?>"><?php echo ($u['profile_active'] && $u['status']==='Active')?_lang['active']:_lang['inactive']; ?></span></td><td><div class="cp-actions">
                        <?php if ($isAdmin && $u['portal_role']==='MANAGER'): ?>
                        <button class="btn btn-sm btn-outline-warning" type="button" data-bs-toggle="modal" data-bs-target="#financeAccess<?php echo (int)$u['user_id']; ?>" title="<?php echo cp_e(_lang['cp_finance_permission']); ?>"><i class="ri-bank-card-line"></i></button>
                        <?php endif; ?>
                        <?php if ($isAdmin): ?><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUser<?php echo (int)$u['user_id']; ?>"><i class="ri-edit-line"></i></button><form method="post"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="user_toggle"><input type="hidden" name="return_view" value="users"><input type="hidden" name="user_id" value="<?php echo (int)$u['user_id']; ?>"><input type="hidden" name="set_active" value="<?php echo ($u['profile_active'] && $u['status']==='Active')?'0':'1'; ?>"><button class="btn btn-sm <?php echo ($u['profile_active'] && $u['status']==='Active')?'btn-outline-danger':'btn-outline-success'; ?>"><i class="ri-user-<?php echo ($u['profile_active'] && $u['status']==='Active')?'unfollow':'follow'; ?>-line"></i></button></form><?php endif; ?>
                        <?php $canAssignThisExpert = $u['portal_role']==='EXPERT' && (($isManager && empty($u['section_id']) && !empty($customerPortalContext['permissions']['manager_assign_unassigned_expert'])) || ($isAdmin && !empty($customerPortalContext['permissions']['customer_admin_reassign_expert']))); if ($canAssignThisExpert): ?><button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#assignExpert<?php echo (int)$u['user_id']; ?>"><i class="ri-node-tree"></i></button><?php endif; ?>
                    </div></td></tr>
                    <?php if ($isAdmin && $u['portal_role']==='MANAGER'): ?>
                    <div class="modal fade" id="financeAccess<?php echo (int)$u['user_id']; ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog"><div class="modal-content"><form method="post">
                        <div class="modal-header"><h5 class="modal-title"><?php echo cp_e(_lang['cp_finance_permission']); ?> — <?php echo cp_e($u['name']); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                          <input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>">
                          <input type="hidden" name="cp_action" value="manager_finance_permission">
                          <input type="hidden" name="return_view" value="users">
                          <input type="hidden" name="user_id" value="<?php echo (int)$u['user_id']; ?>">
                          <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" name="finance_allowed" value="1" id="financeAllowed<?php echo (int)$u['user_id']; ?>" <?php echo !empty($customerPortalFinancePermissions[(int)$u['user_id']])?'checked':''; ?>>
                            <label class="form-check-label" for="financeAllowed<?php echo (int)$u['user_id']; ?>"><?php echo cp_e(_lang['cp_finance_enable']); ?></label></div>
                          <p class="small text-muted mt-3"><?php echo cp_e(_lang['cp_finance_note']); ?></p>
                        </div><div class="modal-footer"><button type="submit" class="btn btn-primary"><?php echo _lang['save_changes']; ?></button></div>
                      </form></div></div>
                    </div>
                    <?php endif; ?>
                                        <?php if ($isAdmin): ?><div class="modal fade" id="editUser<?php echo (int)$u['user_id']; ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="post"><div class="modal-header"><h5 class="modal-title"><?php echo _lang['cp_edit_user']; ?></h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="user_update"><input type="hidden" name="return_view" value="users"><input type="hidden" name="user_id" value="<?php echo (int)$u['user_id']; ?>"><div class="mb-2"><label class="form-label"><?php echo _lang['name']; ?></label><input class="form-control" name="name" value="<?php echo cp_e($u['name']); ?>" required></div><div class="mb-2"><label class="form-label"><?php echo _lang['email']; ?></label><input class="form-control" type="email" name="email" value="<?php echo cp_e($u['email']); ?>" required></div><div><label class="form-label"><?php echo _lang['mobile']; ?></label><input class="form-control" name="mobile" value="<?php echo cp_e($u['mobile']); ?>"></div>
<?php if (in_array($u['portal_role'], ['MANAGER','EXPERT'], true)): ?>
<div class="mt-3"><label class="form-label"><?php echo _lang['role']; ?></label>
<select class="form-select" name="portal_role" required>
<option value="MANAGER" <?php echo $u['portal_role']==='MANAGER'?'selected':''; ?>><?php echo _lang['cp_role_manager']; ?></option>
<option value="EXPERT" <?php echo $u['portal_role']==='EXPERT'?'selected':''; ?>><?php echo _lang['cp_role_expert']; ?></option>
</select></div>
<div class="mt-2"><label class="form-label"><?php echo _lang['cp_section']; ?></label>
<select class="form-select" name="section_id" required>
<?php foreach ($customerPortalSections as $editSection): if (empty($editSection['is_active'])) continue; ?>
<option value="<?php echo (int)$editSection['id']; ?>" <?php echo (int)$u['section_id']===(int)$editSection['id']?'selected':''; ?>><?php echo cp_e($editSection['name']); ?></option>
<?php endforeach; ?>
</select></div>
<div class="cp-form-note mt-2">تغییر نقش فقط در همین شرکت انجام می‌شود. هر بخش یک مدیر فعال دارد؛ تغییر نقش مدیر دارای کارشناسان فعال نیازمند تعیین مدیر جایگزین است.</div>
<?php endif; ?>
</div><div class="modal-footer"><button class="btn btn-primary"><?php echo _lang['save_changes']; ?></button></div></form></div></div></div><?php endif; ?>
                    <?php if ($canAssignThisExpert): ?><div class="modal fade" id="assignExpert<?php echo (int)$u['user_id']; ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="post"><div class="modal-header"><h5 class="modal-title"><?php echo _lang['cp_assign_expert']; ?></h5><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="assign_expert"><input type="hidden" name="return_view" value="users"><input type="hidden" name="expert_user_id" value="<?php echo (int)$u['user_id']; ?>"><?php if ($isManager): ?><input type="hidden" name="section_id" value="<?php echo (int)$customerPortalContext['section_id']; ?>"><div class="cp-form-note"><?php echo cp_e($customerPortalContext['section_name']); ?></div><?php else: ?><select class="form-select" name="section_id" required><?php foreach ($customerPortalSections as $s): if (!$s['is_active'] || empty($s['manager_user_id'])) continue; ?><option value="<?php echo (int)$s['id']; ?>" <?php echo (int)$u['section_id']===(int)$s['id']?'selected':''; ?>><?php echo cp_e($s['name']); ?></option><?php endforeach; ?></select><?php endif; ?></div><div class="modal-footer"><button class="btn btn-primary"><?php echo _lang['cp_assign']; ?></button></div></form></div></div></div><?php endif; ?>
                    <?php endforeach; ?>
                    </tbody></table></div></div>

                    <?php
                    // Organizational tree is derived from current scoped sections and users, no extra database table.
                    $orgSections = [];
                    foreach ($customerPortalSections as $section) {
                        if ($isManager && (int)$section['id'] !== (int)$customerPortalContext['section_id']) continue;
                        $orgSections[(int)$section['id']] = $section;
                    }
                    $orgUsers = $customerPortalUsers;
                    // Manager's user listing contains experts only. Include the signed-in
                    // manager in the chart without broadening user-management permissions.
                    if ($isManager && !empty($customerPortalContext['section_id'])) {
                        $currentManagerId = (int) $customerPortalContext['user_id'];
                        $managerExistsInChart = false;
                        foreach ($orgUsers as $chartUser) {
                            if ((int)$chartUser['user_id'] === $currentManagerId) {
                                $managerExistsInChart = true;
                                break;
                            }
                        }
                        if (!$managerExistsInChart) {
                            $orgUsers[] = [
                                'user_id' => $currentManagerId,
                                'name' => (string)($_SESSION['name'] ?? ''),
                                'portal_role' => 'MANAGER',
                                'section_id' => (int)$customerPortalContext['section_id'],
                                'profile_active' => (int)($customerPortalContext['profile_active'] ?? 0),
                                'status' => (string)($customerPortalContext['user_status'] ?? ''),
                            ];
                        }
                    }
                    $orgAdmins = array_values(array_filter($orgUsers, static function ($u) { return $u['portal_role'] === 'ADMIN'; }));
                    $orgUnassigned = array_values(array_filter($orgUsers, static function ($u) { return $u['portal_role'] !== 'ADMIN' && empty($u['section_id']); }));
                    ?>
                    <div class="cp-card mt-3 cp-org-chart">
                        <div class="cp-card-header"><div><h5 class="cp-card-title"><i class="ri-node-tree me-1"></i>چارت سازمانی</h5><div class="cp-muted">ساختار بخش‌ها، مدیران و کارشناسان بر اساس اطلاعات فعلی پرتال</div></div></div>
                        <div class="cp-card-body">
                            <?php if ($isAdmin): ?>
                                <div class="cp-org-top">
                                    <div class="cp-org-heading">مدیریت شرکت</div>
                                    <div class="cp-org-people">
                                        <?php foreach ($orgAdmins as $u): ?><div class="cp-org-person admin"><strong><?php echo cp_e($u['name']); ?></strong><small>ادمین شرکت<?php echo ($u['profile_active'] && $u['status']==='Active') ? '' : ' · غیرفعال'; ?></small></div><?php endforeach; ?>
                                        <?php if (!$orgAdmins): ?><span class="cp-muted">ادمینی ثبت نشده است.</span><?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="cp-org-grid">
                                <?php foreach ($orgSections as $section):
                                    $sid = (int)$section['id'];
                                    $managers = array_values(array_filter($orgUsers, static function($u) use ($sid) { return $u['portal_role'] === 'MANAGER' && (int)$u['section_id'] === $sid; }));
                                    $experts = array_values(array_filter($orgUsers, static function($u) use ($sid) { return $u['portal_role'] === 'EXPERT' && (int)$u['section_id'] === $sid; }));
                                ?>
                                    <div class="cp-org-section">
                                        <div class="cp-org-heading"><i class="ri-building-2-line me-1"></i><?php echo cp_e($section['name']); ?><?php if (!$section['is_active']): ?><small class="ms-2">(غیرفعال)</small><?php endif; ?></div>
                                        <div class="cp-org-tier">مدیر بخش</div>
                                        <div class="cp-org-people">
                                            <?php foreach ($managers as $u): ?><div class="cp-org-person manager"><strong><?php echo cp_e($u['name']); ?></strong><?php if (!$u['profile_active'] || $u['status'] !== 'Active'): ?><small>غیرفعال</small><?php endif; ?></div><?php endforeach; ?>
                                            <?php if (!$managers): ?><span class="cp-muted">بدون مدیر</span><?php endif; ?>
                                        </div>
                                        <div class="cp-org-tier">کارشناسان (<?php echo count($experts); ?>)</div>
                                        <div class="cp-org-people">
                                            <?php foreach ($experts as $u): ?><div class="cp-org-person expert"><strong><?php echo cp_e($u['name']); ?></strong><?php if (!$u['profile_active'] || $u['status'] !== 'Active'): ?><small>غیرفعال</small><?php endif; ?></div><?php endforeach; ?>
                                            <?php if (!$experts): ?><span class="cp-muted">بدون کارشناس</span><?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (!$orgSections): ?><div class="cp-muted">هنوز بخشی تعریف نشده است.</div><?php endif; ?>
                            </div>
                            <?php if ($isAdmin && $orgUnassigned): ?>
                                <div class="cp-org-unassigned"><div class="cp-org-tier">کاربران فاقد بخش</div><div class="cp-org-people"><?php foreach ($orgUnassigned as $u): ?><div class="cp-org-person"><strong><?php echo cp_e($u['name']); ?></strong><small><?php echo $u['portal_role']==='MANAGER'?'مدیر':'کارشناس'; ?></small></div><?php endforeach; ?></div></div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($customerPortalView === 'drafts'): ?>
                    <div class="cp-card"><div class="cp-card-header"><div><h5 class="cp-card-title"><?php echo _lang['cp_requests']; ?></h5><div class="cp-muted"><?php echo _lang['cp_requests_note']; ?></div></div><?php if (!empty($customerPortalContext['can_create_draft'])): ?><a href="./customer_portal?view=new" class="btn btn-primary btn-sm"><i class="ri-add-line me-1"></i><?php echo _lang['cp_new_request']; ?></a><?php endif; ?></div><div class="cp-card-body">
                        <div class="d-flex gap-2 flex-wrap mb-3"><a href="./customer_portal?view=drafts" class="btn btn-sm btn-light"><?php echo _lang['all']; ?></a><?php foreach (['EXPERT_DRAFT','MANAGER_REVIEW','EXPERT_REVISION','SENT_TO_INTEK','CANCELLED'] as $sf): ?><a href="./customer_portal?view=drafts&status=<?php echo $sf; ?>" class="btn btn-sm btn-outline-<?php echo cp_status_class($sf); ?>"><?php echo cp_e(cp_status_label($sf)); ?></a><?php endforeach; ?></div>
                        <?php if (empty($customerPortalDrafts)): ?><div class="cp-empty"><i class="ri-inbox-line"></i><?php echo _lang['cp_no_requests']; ?></div><?php else: ?><div class="row g-3"><?php foreach ($customerPortalDrafts as $draft): ?><div class="col-xl-3 col-lg-4 col-md-6"><a class="cp-draft-card" href="./customer_portal?view=draft&draft_id=<?php echo (int)$draft['id']; ?>"><div class="d-flex justify-content-between gap-2"><span class="cp-status <?php echo cp_status_class($draft['workflow_status']); ?>"><?php echo cp_e(cp_status_label($draft['workflow_status'])); ?></span><small class="text-muted"><?php echo !empty($draft['published_ticket_number']) ? '#'.cp_e($draft['published_ticket_number']) : 'بدون شماره تیکت'; ?></small></div><div class="cp-draft-title"><?php echo cp_e($draft['title']); ?></div><div class="cp-draft-meta"><span><?php echo cp_e($draft['section_name']); ?></span><span><?php echo cp_e($draft['owner_name']); ?></span><span><?php echo cp_e(cp_date($draft['updated_at'],$config)); ?></span></div></a></div><?php endforeach; ?></div><?php endif; ?>
                    </div></div>

                <?php elseif ($customerPortalView === 'new'): ?>
                    <div class="cp-card"><div class="cp-card-header"><div><h5 class="cp-card-title"><?php echo _lang['cp_new_request']; ?></h5><div class="cp-muted"><?php echo _lang['cp_new_request_note']; ?></div></div><div class="cp-card-body">
                    <?php if (empty($customerPortalContext['can_create_draft'])): ?><div class="alert alert-warning mb-0"><?php echo _lang['cp_error_draft_create_denied']; ?></div><?php else: ?><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="draft_create"><input type="hidden" name="return_view" value="new"><div class="row g-3"><div class="col-lg-8"><label class="form-label"><?php echo _lang['title']; ?></label><input class="form-control" name="title" required></div><div class="col-lg-4"><label class="form-label"><?php echo _lang['type']; ?></label><select class="form-select" name="type_id" required><option value=""><?php echo _lang['select']; ?></option><?php foreach ($customerPortalTypes as $group=>$typeList): ?><optgroup label="<?php echo cp_e($group); ?>"><?php foreach ($typeList as $t): ?><option value="<?php echo (int)$t['id']; ?>"><?php echo cp_e($group.' - '.$t['type_name']); ?></option><?php endforeach; ?></optgroup><?php endforeach; ?></select></div><?php if ($isManager): ?><div class="col-lg-3"><label class="form-label"><?php echo _lang['priority']; ?></label><select class="form-select" name="priority"><option value="low"><?php echo _lang['low']; ?></option><option value="medium"><?php echo _lang['medium']; ?></option><option value="high"><?php echo _lang['high']; ?></option></select></div><?php endif; ?><div class="col-lg-3"><label class="form-label"><?php echo _lang['indicator_number']; ?></label><input class="form-control" name="indicator_number"></div><div class="col-lg-<?php echo $isManager?'6':'9'; ?>"><label class="form-label"><?php echo _lang['attach_file']; ?></label><div class="cp-file-picker"><input type="file" id="cp-new-file" name="attach_file" aria-label="<?php echo cp_e(_lang['cp_choose_file']); ?>"><label for="cp-new-file" class="cp-file-trigger"><?php echo _lang['cp_choose_file']; ?></label><span class="cp-file-name" aria-live="polite"><?php echo _lang['cp_no_file_selected']; ?></span></div></div><div class="col-12"><label class="form-label"><?php echo _lang['description']; ?></label><textarea class="form-control" name="description" rows="6" required></textarea></div><div class="col-12"><div class="cp-form-note mb-3"><?php echo $isExpert?_lang['cp_expert_create_note']:_lang['cp_manager_create_note']; ?></div><button class="btn btn-primary"><i class="ri-save-line me-1"></i><?php echo _lang['submit']; ?></button></div></div></form><?php endif; ?>
                    </div></div>

                <?php elseif ($customerPortalView === 'draft'): ?>
                    <?php if (!$customerPortalDraft): ?><div class="cp-card"><div class="cp-empty"><i class="ri-file-forbid-line"></i><?php echo _lang['cp_request_not_found']; ?></div></div><?php else:
                        $draft = $customerPortalDraft;
                        $isOwner = (int)$draft['current_owner_user_id'] === (int)$customerPortalContext['user_id'];
                        $isLocked = in_array($draft['workflow_status'], ['SENT_TO_INTEK','CANCELLED'], true);
                        $canEdit = $isOwner && !$isLocked && !$isAdmin;
                    ?>
                    <div class="row g-3">
                        <div class="col-xl-8">
                            <div class="cp-card mb-3"><div class="cp-card-header"><div><div class="d-flex align-items-center gap-2 flex-wrap"><h5 class="cp-card-title"><?php echo !empty($draft['published_ticket_number']) ? '#'.cp_e($draft['published_ticket_number']) : 'بدون شماره تیکت'; ?> · <?php echo cp_e($draft['title']); ?></h5><span class="cp-status <?php echo cp_status_class($draft['workflow_status']); ?>"><?php echo cp_e(cp_status_label($draft['workflow_status'])); ?></span></div><div class="cp-muted mt-1"><?php echo _lang['cp_current_owner']; ?>: <?php echo cp_e($draft['owner_name']); ?></div></div></div><div class="cp-card-body">
                                <?php if ($canEdit): ?><form method="post"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="draft_update"><input type="hidden" name="draft_id" value="<?php echo (int)$draft['id']; ?>"><input type="hidden" name="return_draft_id" value="<?php echo (int)$draft['id']; ?>"><div class="row g-3"><div class="col-lg-8"><label class="form-label"><?php echo _lang['title']; ?></label><input class="form-control" name="title" value="<?php echo cp_e($draft['title']); ?>" required></div><div class="col-lg-4"><label class="form-label"><?php echo _lang['type']; ?></label><select class="form-select" name="type_id" required><?php foreach ($customerPortalTypes as $group=>$typeList): ?><optgroup label="<?php echo cp_e($group); ?>"><?php foreach ($typeList as $t): ?><option value="<?php echo (int)$t['id']; ?>" <?php echo (int)$draft['type_id']===(int)$t['id']?'selected':''; ?>><?php echo cp_e($group.' - '.$t['type_name']); ?></option><?php endforeach; ?></optgroup><?php endforeach; ?></select></div><?php if ($isManager): ?><div class="col-lg-3"><label class="form-label"><?php echo _lang['priority']; ?></label><select class="form-select" name="priority"><?php foreach (['low','medium','high'] as $pr): ?><option value="<?php echo $pr; ?>" <?php echo $draft['priority']===$pr?'selected':''; ?>><?php echo _lang[$pr]; ?></option><?php endforeach; ?></select></div><?php endif; ?><div class="col-lg-<?php echo $isManager?'9':'12'; ?>"><label class="form-label"><?php echo _lang['indicator_number']; ?></label><input class="form-control" name="indicator_number" value="<?php echo cp_e($draft['indicator_number']); ?>"></div><div class="col-12"><label class="form-label"><?php echo _lang['description']; ?></label><textarea class="form-control" name="description" rows="7" required><?php echo cp_e($draft['description']); ?></textarea></div><div class="col-12"><button class="btn btn-primary"><i class="ri-save-line me-1"></i><?php echo _lang['save_changes']; ?></button></div></div></form><?php else: ?><div class="cp-owner-banner mb-3"><i class="ri-lock-2-line me-1"></i><?php echo $isLocked?_lang['cp_locked_note']:_lang['cp_not_owner_note']; ?></div><h5 class="mb-3"><?php echo cp_e($draft['title']); ?></h5><div style="line-height:2;white-space:pre-wrap"><?php echo cp_e($draft['description']); ?></div><?php endif; ?>
                            </div></div>

                            <div class="cp-card mb-3"><div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['comments']; ?></h5></div><div class="cp-card-body"><?php foreach ($customerPortalDraftComments as $c): ?><div class="cp-comment"><div class="cp-comment-head"><strong><?php echo cp_e($c['user_name'] ?: '-'); ?></strong><span><?php echo cp_e(cp_date($c['creation_date'],$config)); ?></span></div><div class="cp-comment-body"><?php echo cp_e($c['comment_text']); ?></div></div><?php endforeach; ?><?php if (empty($customerPortalDraftComments)): ?><div class="cp-empty py-3"><?php echo _lang['cp_no_comments']; ?></div><?php endif; ?><?php if (!$isLocked && ($isManager || $isExpert)): ?><form method="post" class="mt-3"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="draft_comment"><input type="hidden" name="draft_id" value="<?php echo (int)$draft['id']; ?>"><input type="hidden" name="return_draft_id" value="<?php echo (int)$draft['id']; ?>"><textarea class="form-control mb-2" name="comment_text" rows="3" required></textarea><button class="btn btn-sm btn-primary"><?php echo _lang['cp_add_comment']; ?></button></form><?php endif; ?></div></div>

                            <div class="cp-card"><div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['cp_files']; ?></h5></div><div class="cp-card-body"><?php if ($customerPortalDraftFiles): ?><div class="list-group mb-3"><?php foreach ($customerPortalDraftFiles as $f): ?><div class="list-group-item d-flex justify-content-between align-items-center gap-2"><div><i class="ri-attachment-2 me-1"></i><strong><?php echo cp_e($f['file_title']); ?></strong><small class="text-muted ms-2"><?php echo cp_e($f['file_name']); ?></small></div><div class="cp-actions"><a class="btn btn-sm btn-outline-primary" href="./customer_portal?view=draft&draft_id=<?php echo (int)$draft['id']; ?>&download_file=<?php echo (int)$f['id']; ?>"><i class="ri-download-line"></i></a><?php if ($canEdit): ?><form method="post"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="draft_file_remove"><input type="hidden" name="draft_id" value="<?php echo (int)$draft['id']; ?>"><input type="hidden" name="file_id" value="<?php echo (int)$f['id']; ?>"><input type="hidden" name="return_draft_id" value="<?php echo (int)$draft['id']; ?>"><button class="btn btn-sm btn-outline-danger"><i class="ri-delete-bin-line"></i></button></form><?php endif; ?></div></div><?php endforeach; ?></div><?php else: ?><div class="cp-empty py-3"><?php echo _lang['cp_no_files']; ?></div><?php endif; ?><?php if ($canEdit): ?><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="draft_file_upload"><input type="hidden" name="draft_id" value="<?php echo (int)$draft['id']; ?>"><input type="hidden" name="return_draft_id" value="<?php echo (int)$draft['id']; ?>"><div class="row g-2"><div class="col-md-5"><input class="form-control" name="file_title" placeholder="<?php echo cp_e(_lang['file_title']); ?>"></div><div class="col-md-5"><div class="cp-file-picker"><input type="file" id="cp-detail-file" name="attach_file" aria-label="<?php echo cp_e(_lang['cp_choose_file']); ?>" required><label for="cp-detail-file" class="cp-file-trigger"><?php echo _lang['cp_choose_file']; ?></label><span class="cp-file-name" aria-live="polite"><?php echo _lang['cp_no_file_selected']; ?></span></div></div><div class="col-md-2"><button class="btn btn-primary w-100"><?php echo cp_e(_lang['upload'] ?? 'بارگذاری'); ?></button></div></div></form><?php endif; ?></div></div>
                        </div>
                        <div class="col-xl-4">
                            <div class="cp-card mb-3"><div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['cp_request_info']; ?></h5></div><div class="cp-card-body cp-side-info"><div class="cp-info-row"><span><?php echo _lang['cp_section']; ?></span><strong><?php echo cp_e($draft['section_name']); ?></strong></div><div class="cp-info-row"><span><?php echo _lang['cp_creator']; ?></span><strong><?php echo cp_e($draft['creator_name']); ?></strong></div><div class="cp-info-row"><span><?php echo _lang['cp_manager']; ?></span><strong><?php echo cp_e($draft['manager_name']); ?></strong></div><div class="cp-info-row"><span><?php echo _lang['priority']; ?></span><strong><?php echo cp_e(_lang[$draft['priority']]); ?></strong></div><div class="cp-info-row"><span><?php echo _lang['cp_revision_count']; ?></span><strong><?php echo number_format((int)$draft['revision_count']); ?></strong></div><div class="cp-info-row"><span><?php echo _lang['added_date']; ?></span><strong><?php echo cp_e(cp_date($draft['created_at'],$config)); ?></strong></div><?php if ($draft['published_ticket_id']): ?><div class="cp-info-row"><span><?php echo _lang['ticket']; ?></span><strong><a href="./tickets?ticket_id=<?php echo urlencode($encryptorClass->encrypt((int)$draft['published_ticket_id'])); ?>"><?php echo cp_e($draft['published_ticket_number'] ?? ''); ?></a></strong></div><?php endif; ?></div></div>

                            <?php if (!$isLocked && $isOwner): ?><div class="cp-card mb-3"><div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['cp_actions']; ?></h5></div><div class="cp-card-body d-grid gap-2">
                                <?php if ($isExpert && in_array($draft['workflow_status'], ['EXPERT_DRAFT','EXPERT_REVISION'], true)): ?><form method="post"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="draft_send_manager"><input type="hidden" name="draft_id" value="<?php echo (int)$draft['id']; ?>"><input type="hidden" name="return_draft_id" value="<?php echo (int)$draft['id']; ?>"><button class="btn btn-primary w-100"><i class="ri-send-plane-line me-1"></i><?php echo _lang['cp_send_to_manager']; ?></button></form><?php endif; ?>
                                <?php if ($isManager && $draft['workflow_status']==='MANAGER_REVIEW'): ?><form method="post"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="draft_publish"><input type="hidden" name="draft_id" value="<?php echo (int)$draft['id']; ?>"><input type="hidden" name="return_draft_id" value="<?php echo (int)$draft['id']; ?>"><button class="btn btn-success w-100"><i class="ri-send-plane-fill me-1"></i><?php echo _lang['cp_send_to_intek']; ?></button></form><?php if (!empty($customerPortalContext['permissions']['manager_return_ticket_to_expert'])): ?><form method="post"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="draft_assign_expert"><input type="hidden" name="draft_id" value="<?php echo (int)$draft['id']; ?>"><input type="hidden" name="return_draft_id" value="<?php echo (int)$draft['id']; ?>"><div class="input-group"><select class="form-select" name="expert_user_id" required><option value=""><?php echo _lang['cp_choose_expert']; ?></option><?php foreach ($customerPortalExperts as $ex): ?><option value="<?php echo (int)$ex['user_id']; ?>"><?php echo cp_e($ex['name']); ?></option><?php endforeach; ?></select><button class="btn btn-outline-warning"><?php echo _lang['cp_return_for_revision']; ?></button></div></form><?php endif; ?><?php endif; ?>
                                <div class="cp-danger-zone"><form method="post" onsubmit="return confirm('<?php echo cp_e(_lang['cp_cancel_confirm']); ?>')"><input type="hidden" name="csrf_token" value="<?php echo cp_e($customerPortalCsrf); ?>"><input type="hidden" name="cp_action" value="draft_cancel"><input type="hidden" name="draft_id" value="<?php echo (int)$draft['id']; ?>"><input type="hidden" name="return_draft_id" value="<?php echo (int)$draft['id']; ?>"><button class="btn btn-sm btn-outline-danger w-100"><?php echo _lang['cp_cancel_request']; ?></button></form></div>
                            </div></div><?php endif; ?>

                            <?php if ($isAdmin): ?>
                            <div class="cp-card"><div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['cp_history']; ?></h5></div><div class="cp-card-body"><div class="cp-history"><?php foreach ($customerPortalDraftHistory as $h): ?><div class="cp-history-item"><strong><?php echo cp_e($h['action_type']); ?></strong><small><?php echo cp_e($h['actor_name']); ?> · <?php echo cp_e(cp_date($h['created_at'],$config)); ?></small></div><?php endforeach; ?><?php if (!$customerPortalDraftHistory): ?><div class="cp-muted"><?php echo _lang['cp_no_history']; ?></div><?php endif; ?></div></div></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                <?php elseif ($customerPortalView === 'audit' && $isAdmin): ?>
                    <?php $auditFilters = $customerPortalAuditPage['filters'] ?? []; ?>
                    <div class="cp-card mb-3">
                        <div class="cp-card-header"><div><h5 class="cp-card-title"><?php echo _lang['cp_audit']; ?></h5><div class="cp-muted"><?php echo _lang['cp_audit_note']; ?></div></div><span class="cp-badge role"><?php echo number_format((int)($customerPortalAuditPage['total'] ?? 0)); ?> <?php echo _lang['cp_audit_records']; ?></span></div>
                        <div class="cp-card-body">
                            <form method="get" action="./customer_portal" class="row g-2 align-items-end">
                                <input type="hidden" name="view" value="audit">
                                <?php if ($isAdmin): ?>
                                    <div class="col-md-3"><label class="form-label"><?php echo _lang['cp_section']; ?></label><select name="section_id" class="form-select"><option value="0"><?php echo _lang['cp_audit_all_sections']; ?></option><?php foreach ($customerPortalAuditSections as $sec): ?><option value="<?php echo (int)$sec['id']; ?>" <?php echo (int)($auditFilters['section_id'] ?? 0)===(int)$sec['id'] ? 'selected' : ''; ?>><?php echo cp_e($sec['name']); ?></option><?php endforeach; ?></select></div>
                                <?php endif; ?>
                                <div class="col-md-3"><label class="form-label"><?php echo _lang['cp_event']; ?></label><select name="event" class="form-select">
                                    <option value=""><?php echo _lang['cp_audit_all_events']; ?></option>
                                    <?php foreach (cp_audit_action_codes() as $eventCode): ?>
                                        <option value="<?php echo cp_e($eventCode); ?>" <?php echo (string)($auditFilters['event'] ?? '') === $eventCode ? 'selected' : ''; ?>><?php echo cp_e(cp_audit_action_label($eventCode)); ?></option>
                                    <?php endforeach; ?>
                                    <?php if (!empty($auditFilters['event']) && !in_array((string)$auditFilters['event'], cp_audit_action_codes(), true)): ?>
                                        <option value="<?php echo cp_e($auditFilters['event']); ?>" selected><?php echo cp_e(cp_audit_action_label($auditFilters['event'])); ?></option>
                                    <?php endif; ?>
                                </select></div>
                                <div class="col-md-2"><label class="form-label"><?php echo _lang['cp_audit_date_from']; ?></label><input type="date" name="date_from" class="form-control" value="<?php echo cp_e($auditFilters['date_from'] ?? ''); ?>"></div>
                                <div class="col-md-2"><label class="form-label"><?php echo _lang['cp_audit_date_to']; ?></label><input type="date" name="date_to" class="form-control" value="<?php echo cp_e($auditFilters['date_to'] ?? ''); ?>"></div>
                                <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-fill" type="submit"><?php echo _lang['search']; ?></button><a href="./customer_portal?view=audit" class="btn btn-outline-secondary"><?php echo _lang['cp_audit_clear']; ?></a></div>
                            </form>
                            <div class="cp-muted mt-2"><?php echo _lang['cp_audit_date_hint']; ?></div>
                        </div>
                    </div>
                    <div class="cp-card">
                        <div class="table-responsive"><table class="table cp-table"><thead><tr><th>#</th><th><?php echo _lang['added_date']; ?></th><th><?php echo _lang['cp_actor']; ?></th><th><?php echo _lang['cp_section']; ?></th><th><?php echo _lang['cp_event']; ?></th><th><?php echo _lang['cp_entity']; ?></th><th><?php echo _lang['cp_changes']; ?></th></tr></thead>
                        <tbody>
                        <?php if (!$customerPortalAudit): ?><tr><td colspan="7" class="text-center p-4 cp-muted"><?php echo _lang['cp_audit_empty']; ?></td></tr><?php endif; ?>
                        <?php foreach ($customerPortalAudit as $a): ?>
                            <tr>
                                <td><?php echo (int)$a['id']; ?></td>
                                <td><?php echo cp_e(cp_date($a['created_at'],$config)); ?></td>
                                <td><?php echo cp_e($a['actor_name']); ?><div class="cp-muted"><?php echo cp_e(cp_audit_actor_role_label($a['actor_role'])); ?></div></td>
                                <td><?php echo cp_e($a['section_name'] ?: '-'); ?></td>
                                <td><strong><?php echo cp_e(cp_audit_action_label($a['action_type'])); ?></strong></td>
                                <td><?php echo cp_e(cp_audit_entity_label($a['entity_type'])); ?> #<?php echo (int)$a['entity_id']; ?></td>
                                <td>
                                    <?php if (!empty($a['old_data']) || !empty($a['new_data'])): ?>
                                        <details><summary><?php echo _lang['view']; ?></summary>
                                        <?php if (!empty($a['old_data'])): ?><div class="cp-muted mt-2"><?php echo _lang['cp_audit_before']; ?></div><div class="cp-json"><?php echo cp_e($a['old_data']); ?></div><?php endif; ?>
                                        <?php if (!empty($a['new_data'])): ?><div class="cp-muted mt-2"><?php echo _lang['cp_audit_after']; ?></div><div class="cp-json"><?php echo cp_e($a['new_data']); ?></div><?php endif; ?>
                                        </details>
                                    <?php else: ?>-<?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody></table></div>
                        <?php if (!empty($customerPortalAuditPage) && $customerPortalAuditPage['pages'] > 1): ?>
                            <div class="cp-card-body d-flex justify-content-between align-items-center flex-wrap gap-2 border-top">
                                <span class="cp-muted"><?php echo _lang['cp_page']; ?> <?php echo (int)$customerPortalAuditPage['page']; ?> / <?php echo (int)$customerPortalAuditPage['pages']; ?></span>
                                <div class="d-flex gap-2">
                                    <?php if ($customerPortalAuditPage['page'] > 1): ?><a class="btn btn-sm btn-outline-secondary" href="./customer_portal?<?php echo cp_e(http_build_query(array_merge($auditFilters, ['view'=>'audit','audit_page'=>$customerPortalAuditPage['page']-1]))); ?>"><?php echo _lang['cp_prev_page']; ?></a><?php endif; ?>
                                    <?php if ($customerPortalAuditPage['page'] < $customerPortalAuditPage['pages']): ?><a class="btn btn-sm btn-outline-primary" href="./customer_portal?<?php echo cp_e(http_build_query(array_merge($auditFilters, ['view'=>'audit','audit_page'=>$customerPortalAuditPage['page']+1]))); ?>"><?php echo _lang['cp_next_page']; ?></a><?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($customerPortalView === 'reports' && ($isAdmin || $isManager)): ?>
                    <?php $sum=$customerPortalReport['summary']??[]; ?>
                    <?php if ($isAdmin && $customerPortalCompanyReportOverview): ?>
                        <div class="cp-card mb-3"><div class="cp-card-header"><div><h5 class="cp-card-title"><?php echo _lang['cp_reports_company_tickets']; ?></h5><div class="cp-muted"><?php echo _lang['cp_reports_company_tickets_note']; ?></div></div><a href="./customer_portal?view=company_tickets" class="btn btn-sm btn-outline-primary"><?php echo _lang['cp_company_tickets']; ?></a></div>
                            <div class="cp-card-body"><div class="cp-grid">
                                <div class="cp-stat primary"><div class="value"><?php echo number_format((int)$customerPortalCompanyReportOverview['total_tickets']); ?></div><div class="label"><?php echo _lang['cp_total_registered_tickets']; ?></div></div>
                                <div class="cp-stat success"><div class="value"><?php echo number_format((int)$customerPortalCompanyReportOverview['tickets_30d']); ?></div><div class="label"><?php echo _lang['cp_tickets_last30']; ?></div></div>
                                <div class="cp-stat warning"><div class="value"><?php echo number_format((int)$customerPortalCompanyReportOverview['active_managers']); ?></div><div class="label"><?php echo _lang['cp_active_managers']; ?></div></div>
                                <div class="cp-stat"><div class="value"><?php echo number_format((int)$customerPortalCompanyReportOverview['active_experts']); ?></div><div class="label"><?php echo _lang['cp_active_experts']; ?></div></div>
                            </div></div></div>
                    <?php endif; ?>
                    <div class="cp-muted mb-2"><?php echo _lang['cp_reports_drafts_only']; ?></div>
                    <div class="cp-grid mb-3"><div class="cp-stat primary"><div class="value"><?php echo number_format((int)($sum['total']??0)); ?></div><div class="label"><?php echo _lang['cp_metric_total']; ?></div></div><div class="cp-stat success"><div class="value"><?php echo number_format((int)($sum['sent']??0)); ?></div><div class="label"><?php echo _lang['cp_metric_sent']; ?></div></div><div class="cp-stat warning"><div class="value"><?php echo number_format((int)($sum['open_count']??0)); ?></div><div class="label"><?php echo _lang['cp_metric_open']; ?></div></div><div class="cp-stat danger"><div class="value"><?php echo number_format((int)($sum['aging_count']??0)); ?></div><div class="label"><?php echo _lang['cp_metric_aging']; ?> (<?php echo (int)($customerPortalReport['aging_days']??14); ?>)</div></div></div>
                    <div class="row g-3"><div class="col-xl-6"><div class="cp-card h-100"><div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['cp_report_sections']; ?></h5></div><div class="table-responsive"><table class="table cp-table"><thead><tr><th><?php echo _lang['cp_section']; ?></th><th><?php echo _lang['cp_metric_total']; ?></th><th><?php echo _lang['cp_metric_sent']; ?></th><th><?php echo _lang['cp_metric_open']; ?></th><th><?php echo _lang['cp_revision_count']; ?></th></tr></thead><tbody><?php foreach (($customerPortalReport['sections']??[]) as $r): ?><tr><td><strong><?php echo cp_e($r['section_name']); ?></strong></td><td><?php echo number_format((int)$r['total']); ?></td><td><?php echo number_format((int)$r['sent']); ?></td><td><?php echo number_format((int)$r['open_count']); ?></td><td><?php echo number_format((int)$r['revisions']); ?></td></tr><?php endforeach; ?></tbody></table></div></div></div><div class="col-xl-6"><div class="cp-card h-100"><div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['cp_report_users']; ?></h5></div><div class="table-responsive"><table class="table cp-table"><thead><tr><th><?php echo _lang['name']; ?></th><th><?php echo _lang['role']; ?></th><th><?php echo _lang['cp_section']; ?></th><th><?php echo _lang['cp_metric_total']; ?></th><th><?php echo _lang['cp_metric_sent']; ?></th></tr></thead><tbody><?php foreach (($customerPortalReport['users']??[]) as $r): ?><tr><td><strong><?php echo cp_e($r['name']); ?></strong></td><td><?php echo cp_e(_lang['cp_role_'.strtolower($r['portal_role'])]); ?></td><td><?php echo cp_e($r['section_name']?:'-'); ?></td><td><?php echo number_format((int)$r['created_drafts']); ?></td><td><?php echo number_format((int)$r['sent_drafts']); ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
                    <div class="cp-card mt-3"><div class="cp-card-header"><h5 class="cp-card-title"><?php echo _lang['cp_report_timing']; ?></h5></div><div class="cp-card-body"><div class="row g-3"><div class="col-md-4"><div class="cp-stat"><div class="value"><?php echo number_format((float)($sum['avg_minutes_to_intek']??0),1); ?></div><div class="label"><?php echo _lang['cp_avg_minutes_to_intek']; ?></div></div></div><div class="col-md-4"><div class="cp-stat"><div class="value"><?php echo number_format((int)($sum['revision_count']??0)); ?></div><div class="label"><?php echo _lang['cp_revision_count']; ?></div></div></div><div class="col-md-4"><div class="cp-stat"><div class="value"><?php echo number_format((int)($sum['cancelled']??0)); ?></div><div class="label"><?php echo _lang['cp_metric_cancelled']; ?></div></div></div></div></div></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
(function () {
    function preparePortalMobileTables() {
        document.querySelectorAll('.customer-portal-page .cp-file-picker').forEach(function (picker) {
            var input = picker.querySelector('input[type="file"]');
            var name = picker.querySelector('.cp-file-name');
            if (!input || !name) return;
            var emptyText = name.textContent;
            input.addEventListener('change', function () {
                name.textContent = input.files && input.files.length ?
                    Array.prototype.map.call(input.files, function (file) { return file.name; }).join('، ') : emptyText;
            });
        });
        document.querySelectorAll('.customer-portal-page table.cp-table').forEach(function (table) {
            var headings = Array.prototype.map.call(table.querySelectorAll('thead th'), function (th) {
                return (th.textContent || '').trim();
            });
            if (!headings.length) return;
            table.classList.add('cp-mobile-cards');
            Array.prototype.forEach.call(table.querySelectorAll('tbody tr'), function (row) {
                Array.prototype.forEach.call(row.children, function (cell, index) {
                    if (cell.tagName === 'TD' && !cell.hasAttribute('colspan')) {
                        cell.setAttribute('data-label', headings[index] || '');
                    }
                });
            });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', preparePortalMobileTables);
    } else {
        preparePortalMobileTables();
    }
})();
</script>
