<?php
///template/structure/user_stracture.php

$userStractureEsc = static function ($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$userStractureLang = static function ($key) {
    return _lang[$key] ?? $key;
};
$userStractureSystemBadge = static function ($scope) {
    switch ($scope) {
        case 'ipanel': return ['class' => 'primary', 'key' => 'access_system_ipanel', 'icon' => 'ri-shield-user-line'];
        case 'iweb': return ['class' => 'success', 'key' => 'access_system_iweb', 'icon' => 'ri-global-line'];
        case 'shared': return ['class' => 'info', 'key' => 'access_system_shared', 'icon' => 'ri-links-line'];
        default: return ['class' => 'secondary', 'key' => 'access_system_unassigned', 'icon' => 'ri-question-line'];
    }
};
?>

<style>
.user-stracture-v2{--us-border:#e8edf4;--us-muted:#6c7a91;--us-primary:#3159d8;--us-soft:#f7f9fc}
.user-stracture-v2 .us-hero{border:1px solid var(--us-border);background:linear-gradient(135deg,#fff 0%,#f6f8ff 55%,#eef4ff 100%);border-radius:20px;padding:24px;box-shadow:0 10px 30px rgba(31,45,79,.06);position:relative;overflow:hidden}
.user-stracture-v2 .us-hero:after{content:"";position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(49,89,216,.06);left:-80px;top:-95px;pointer-events:none}
.user-stracture-v2 .us-title-icon{width:48px;height:48px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:rgba(49,89,216,.1);color:var(--us-primary);font-size:23px;flex:0 0 auto}
.user-stracture-v2 .us-subtitle{color:var(--us-muted);margin:5px 0 0;line-height:1.8}
.user-stracture-v2 .us-switch{background:#eef2f8;padding:5px;border-radius:13px;display:inline-flex;gap:4px;flex-wrap:wrap}
.user-stracture-v2 .us-switch a{border-radius:10px;padding:9px 15px;color:#536176;text-decoration:none;font-weight:600}
.user-stracture-v2 .us-switch a.active{background:#fff;color:var(--us-primary);box-shadow:0 3px 12px rgba(31,45,79,.09)}
.user-stracture-v2 .us-nav{border:1px solid var(--us-border);background:#fff;border-radius:16px;padding:8px;display:flex;gap:6px;flex-wrap:wrap}
.user-stracture-v2 .us-nav a{text-decoration:none;color:#596579;border-radius:11px;padding:10px 14px;font-weight:600;display:inline-flex;gap:7px;align-items:center}
.user-stracture-v2 .us-nav a.active{background:rgba(49,89,216,.09);color:var(--us-primary)}
.user-stracture-v2 .us-stat{height:100%;border:1px solid var(--us-border);background:#fff;border-radius:16px;padding:18px;box-shadow:0 7px 22px rgba(31,45,79,.045)}
.user-stracture-v2 .us-stat-icon{width:42px;height:42px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-size:20px;background:#f1f5ff;color:var(--us-primary)}
.user-stracture-v2 .us-stat-value{font-size:24px;font-weight:800;margin-top:12px;color:#27364b}
.user-stracture-v2 .us-stat-label{margin-top:3px;color:var(--us-muted);font-size:13px}
.user-stracture-v2 .us-main{border:1px solid var(--us-border);border-radius:18px;overflow:hidden;box-shadow:0 9px 28px rgba(31,45,79,.05)}
.user-stracture-v2 .us-main .card-header{background:#fff;border-bottom:1px solid var(--us-border);padding:17px 20px}
.user-stracture-v2 .us-table thead th{background:#f8fafc;color:#566277;border-bottom:1px solid var(--us-border);font-weight:700;vertical-align:middle;white-space:nowrap}
.user-stracture-v2 .us-table tbody td{vertical-align:middle;border-color:#f0f2f6}
.user-stracture-v2 .us-table tbody tr:hover{background:#fbfcff}
.user-stracture-v2 .us-role{font-weight:700;color:#253247}.user-stracture-v2 .us-meta{color:var(--us-muted);font-size:12px;line-height:1.7}
.user-stracture-v2 .us-chip{display:inline-flex;align-items:center;gap:5px;border-radius:9px;padding:5px 8px;background:#f5f7fb;border:1px solid #edf0f5;color:#566277;margin:2px;font-size:12px}
.user-stracture-v2 .us-chip.warning{background:#fff7e8;border-color:#ffe0a7;color:#946200}
.user-stracture-v2 .us-action{width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;border-radius:9px;border:1px solid #e8ecf2;background:#fff;text-decoration:none;margin:2px}
.user-stracture-v2 .us-action:hover{background:#f5f7fb}
.user-stracture-v2 .us-note{border-radius:12px;padding:12px 14px;background:#f7f9fd;border:1px solid #e8edf4;color:#596579;line-height:1.8}
.user-stracture-v2 .us-danger-note{border-radius:12px;padding:12px 14px;background:#fff5f5;border:1px solid #ffdede;color:#a73a3a;line-height:1.8}
.user-stracture-v2 .us-company-group{border:1px solid #e8edf4;border-radius:14px;margin-bottom:12px;overflow:hidden;background:#fff}
.user-stracture-v2 .us-company-head{padding:11px 13px;background:#f8fafc;border-bottom:1px solid #edf0f5;display:flex;align-items:center;justify-content:space-between;gap:10px}
.user-stracture-v2 .us-unit-list{padding:8px 12px}.user-stracture-v2 .us-unit-row{padding:9px 4px;border-bottom:1px dashed #eef1f5}.user-stracture-v2 .us-unit-row:last-child{border-bottom:0}
.user-stracture-v2 .us-unit-row.out-scope{background:#fffaf0;border-radius:8px;padding-left:8px;padding-right:8px}
.user-stracture-v2 .us-search{max-width:340px}.user-stracture-v2 .us-empty{text-align:center;padding:45px 20px;color:var(--us-muted)}
</style>

<div class="content-page user-stracture-v2">
<div class="content"><div class="container-fluid py-3">

    <div class="us-hero mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 position-relative" style="z-index:1">
            <div class="d-flex align-items-start gap-3">
                <span class="us-title-icon"><i class="ri-node-tree"></i></span>
                <div>
                    <h4 class="mb-1"><?php echo $userStractureLang('structure_access_management'); ?></h4>
                    <p class="us-subtitle"><?php echo $userStractureLang('structure_access_management_note'); ?></p>
                </div>
            </div>
            <div class="us-switch">
                <a class="<?php echo $userStractureSystemFilter === 'all' ? 'active' : ''; ?>" href="./user_stracture?system=all"><?php echo $userStractureLang('access_system_all'); ?></a>
                <a class="<?php echo $userStractureSystemFilter === 'ipanel' ? 'active' : ''; ?>" href="./user_stracture?system=ipanel">iPanel</a>
                <a class="<?php echo $userStractureSystemFilter === 'iweb' ? 'active' : ''; ?>" href="./user_stracture?system=iweb">iWeb</a>
            </div>
        </div>
    </div>

    <div class="us-nav mb-3">
        <a href="./user_access"><i class="ri-shield-keyhole-line"></i><?php echo _lang['user_access']; ?></a>
        <a href="./user_stracture" class="active"><i class="ri-node-tree"></i><?php echo _lang['user_stracture']; ?></a>
        <a href="./user_operation"><i class="ri-settings-5-line"></i><?php echo _lang['user_operation']; ?></a>
        <a href="./user_view"><i class="ri-eye-line"></i><?php echo _lang['user_view']; ?></a>
    </div>

    <div id="userStractureFeedback" class="alert d-none mb-3" role="alert"></div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3"><div class="us-stat"><span class="us-stat-icon"><i class="ri-shield-user-line"></i></span><div class="us-stat-value"><?php echo (int) $userStractureStats['total_roles']; ?></div><div class="us-stat-label"><?php echo $userStractureLang('structure_access_total_roles'); ?></div></div></div>
        <div class="col-6 col-xl-3"><div class="us-stat"><span class="us-stat-icon"><i class="ri-checkbox-circle-line"></i></span><div class="us-stat-value"><?php echo (int) $userStractureStats['configured_roles']; ?></div><div class="us-stat-label"><?php echo $userStractureLang('structure_access_configured_roles'); ?></div></div></div>
        <div class="col-6 col-xl-3"><div class="us-stat"><span class="us-stat-icon"><i class="ri-building-4-line"></i></span><div class="us-stat-value"><?php echo (int) $userStractureStats['multi_company_roles']; ?></div><div class="us-stat-label"><?php echo $userStractureLang('structure_access_multi_company_roles'); ?></div></div></div>
        <div class="col-6 col-xl-3"><div class="us-stat"><span class="us-stat-icon"><i class="ri-error-warning-line"></i></span><div class="us-stat-value"><?php echo (int) $userStractureStats['attention_roles']; ?></div><div class="us-stat-label"><?php echo $userStractureLang('structure_access_attention_roles'); ?></div></div></div>
    </div>

    <div class="us-note mb-3"><i class="ri-information-line me-1"></i><?php echo $userStractureLang('structure_access_derived_note'); ?></div>

    <div class="card us-main">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div><h5 class="mb-1"><?php echo $userStractureLang('structure_access_role_list'); ?></h5><div class="us-meta"><?php echo $userStractureLang('structure_access_role_list_note'); ?></div></div>
            <div class="input-group us-search"><span class="input-group-text"><i class="ri-search-line"></i></span><input type="search" id="userStractureSearch" class="form-control" placeholder="<?php echo $userStractureEsc($userStractureLang('structure_access_search')); ?>"></div>
        </div>
        <div class="table-responsive">
            <table class="table us-table mb-0" id="userStractureTable">
                <thead><tr>
                    <th><?php echo _lang['role']; ?></th>
                    <th><?php echo $userStractureLang('access_system'); ?></th>
                    <th><?php echo $userStractureLang('structure_access_origin'); ?></th>
                    <th><?php echo $userStractureLang('structure_access_allowed_units'); ?></th>
                    <th><?php echo $userStractureLang('status'); ?></th>
                    <th><?php echo _lang['last_updated_date']; ?></th>
                    <th style="width:110px"><?php echo _lang['actions']; ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ($roles as $role) {
                    $badge = $userStractureSystemBadge($role['system_scope']);
                    $searchText = strtolower(trim((string)($role['rbac_name'] ?? '') . ' ' . (string)($role['company_name'] ?? '') . ' ' . (string)($role['unit_name'] ?? '') . ' ' . (string)($role['activity_name'] ?? '')));
                ?>
                    <tr data-search="<?php echo $userStractureEsc($searchText); ?>">
                        <td>
                            <div class="us-role"><?php echo $userStractureEsc($role['rbac_name']); ?></div>
                            <div class="us-meta">#<?php echo (int)$role['id']; ?><?php if ($role['has_duplicate_rows']) { ?> · <span class="text-warning"><?php echo $userStractureLang('structure_access_duplicate_legacy'); ?>: <?php echo (int)$role['structure_row_count']; ?></span><?php } ?></div>
                        </td>
                        <td><span class="badge bg-<?php echo $badge['class']; ?>-subtle text-<?php echo $badge['class']; ?>"><i class="<?php echo $badge['icon']; ?> me-1"></i><?php echo $userStractureLang($badge['key']); ?></span></td>
                        <td>
                            <div class="fw-semibold"><?php echo $userStractureEsc($role['company_name'] ?: '—'); ?></div>
                            <div class="us-meta"><?php echo $userStractureEsc($role['unit_name'] ?: $userStractureLang('structure_access_no_home_unit')); ?></div>
                            <?php if (!empty($role['activity_name'])) { ?><div class="us-meta"><?php echo $userStractureEsc($role['activity_name']); ?></div><?php } ?>
                        </td>
                        <td style="min-width:270px">
                            <?php if ($role['is_protected']) { ?>
                                <span class="us-chip"><i class="ri-global-line"></i><?php echo $userStractureLang('structure_access_full_access'); ?></span>
                            <?php } elseif (empty($role['selected_unit_ids'])) { ?>
                                <span class="text-muted"><?php echo $userStractureLang('structure_access_not_configured'); ?></span>
                            <?php } else { foreach ($role['selected_unit_ids'] as $unitId) {
                                $unit = $unitById[$unitId] ?? null;
                                $invalid = in_array($unitId, $role['invalid_selected_unit_ids'], true);
                                if (!$unit) { ?>
                                    <span class="us-chip warning">#<?php echo (int)$unitId; ?> · <?php echo $userStractureLang('structure_access_missing_unit'); ?></span>
                                <?php } else { ?>
                                    <span class="us-chip <?php echo $invalid ? 'warning' : ''; ?>"><?php echo $userStractureEsc($unit['company_name']); ?> / <?php echo $userStractureEsc($unit['unit_name']); ?></span>
                                <?php }
                            }} ?>
                            <?php if ($role['has_scope_issue']) { ?><div class="us-meta text-warning mt-1"><i class="ri-error-warning-line me-1"></i><?php echo $userStractureLang('structure_access_scope_issue'); ?></div><?php } ?>
                        </td>
                        <td>
                            <?php if ($role['is_protected']) { ?>
                                <span class="badge bg-primary-subtle text-primary"><?php echo $userStractureLang('structure_access_protected'); ?></span>
                            <?php } elseif ($role['structure_configured']) { ?>
                                <span class="badge bg-success-subtle text-success"><?php echo $userStractureLang('structure_access_configured'); ?></span>
                            <?php } else { ?>
                                <span class="badge bg-secondary-subtle text-secondary"><?php echo $userStractureLang('structure_access_not_configured'); ?></span>
                            <?php } ?>
                        </td>
                        <td><span class="us-meta"><?php echo $userStractureEsc($role['last_updated_display']); ?></span></td>
                        <td>
                            <?php
                                $canConfigureThis = !$role['is_protected'] && (($role['structure_configured'] && $canEditUserStracture) || (!$role['structure_configured'] && $canAddUserStracture));
                                if ($canConfigureThis) { ?>
                                <button type="button" class="us-action text-primary user-stracture-edit" data-id="<?php echo (int)$role['id']; ?>" title="<?php echo $userStractureEsc($userStractureLang('structure_access_configure')); ?>"><i class="ri-equalizer-line"></i></button>
                            <?php } ?>
                            <?php if (!$role['is_protected'] && $role['structure_configured'] && !empty($role['selected_unit_ids']) && $canDeleteUserStracture) { ?>
                                <button type="button" class="us-action text-danger user-stracture-clear" data-id="<?php echo (int)$role['id']; ?>" data-name="<?php echo $userStractureEsc($role['rbac_name']); ?>" title="<?php echo $userStractureEsc($userStractureLang('structure_access_clear')); ?>"><i class="ri-close-circle-line"></i></button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php if (empty($roles)) { ?><div class="us-empty"><i class="ri-node-tree"></i><?php echo $userStractureLang('structure_access_no_roles'); ?></div><?php } ?>
        </div>
    </div>

    <div class="modal fade" id="userStractureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="ri-node-tree me-1"></i><?php echo $userStractureLang('structure_access_configure'); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $userStractureEsc(_lang['close']); ?>"></button></div>
            <div class="modal-body">
                <input type="hidden" id="userStractureRoleId">
                <div class="row g-3 mb-3">
                    <div class="col-md-4"><label class="form-label"><?php echo _lang['role']; ?></label><div class="form-control bg-light" id="userStractureRoleName"></div></div>
                    <div class="col-md-4"><label class="form-label"><?php echo $userStractureLang('structure_access_role_company'); ?></label><div class="form-control bg-light" id="userStractureCompany"></div></div>
                    <div class="col-md-4"><label class="form-label"><?php echo $userStractureLang('structure_access_home_unit'); ?></label><div class="form-control bg-light" id="userStractureHomeUnit"></div></div>
                </div>
                <div class="us-note mb-3" id="userStractureScopeNote"></div>
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div class="input-group us-search"><span class="input-group-text"><i class="ri-search-line"></i></span><input type="search" id="userStractureUnitSearch" class="form-control" placeholder="<?php echo $userStractureEsc($userStractureLang('structure_access_unit_search')); ?>"></div>
                    <div class="d-flex gap-2"><button type="button" class="btn btn-light btn-sm" id="userStractureSelectAll"><i class="ri-checkbox-multiple-line me-1"></i><?php echo $userStractureLang('structure_access_select_all_allowed'); ?></button><button type="button" class="btn btn-light btn-sm" id="userStractureClearOptional"><i class="ri-checkbox-blank-line me-1"></i><?php echo $userStractureLang('structure_access_clear_optional'); ?></button></div>
                </div>
                <div id="userStractureUnitGroups"></div>
                <div class="us-meta mt-2"><span id="userStractureSelectedCount">0</span> <?php echo $userStractureLang('structure_access_selected_count_suffix'); ?></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button><button type="button" class="btn btn-primary" id="userStractureSave"><i class="ri-save-line me-1"></i><?php echo _lang['save_changes']; ?></button></div>
        </div></div>
    </div>

    <div class="modal fade" id="userStractureClearModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title text-danger"><i class="ri-error-warning-line me-1"></i><?php echo $userStractureLang('structure_access_clear'); ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><p><?php echo $userStractureLang('structure_access_clear_confirm'); ?></p><div class="fw-semibold mb-3" id="userStractureClearName"></div><div class="us-danger-note"><?php echo $userStractureLang('structure_access_clear_note'); ?></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button><button type="button" class="btn btn-danger" id="userStractureClearConfirm"><?php echo $userStractureLang('structure_access_clear'); ?></button></div>
    </div></div></div>

</div></div></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const endpoint = '../icore/json/user_stracture_manage.php';
    const csrf = <?php echo json_encode($userStractureCsrf, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const roleData = <?php echo json_encode($userStractureRoleData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    const units = <?php echo json_encode($unitOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    const feedback = document.getElementById('userStractureFeedback');
    const modalEl = document.getElementById('userStractureModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const clearModalEl = document.getElementById('userStractureClearModal');
    const clearModal = clearModalEl ? new bootstrap.Modal(clearModalEl) : null;
    let activeRole = null;
    let clearRoleId = 0;

    const labels = {
        ownCompany: <?php echo json_encode($userStractureLang('structure_access_scope_own_company')); ?>,
        multiCompany: <?php echo json_encode($userStractureLang('structure_access_scope_multi_company')); ?>,
        ownUnit: <?php echo json_encode($userStractureLang('structure_access_home_unit_locked')); ?>,
        legacyOut: <?php echo json_encode($userStractureLang('structure_access_legacy_out_scope')); ?>,
        unavailable: <?php echo json_encode($userStractureLang('structure_access_unit_not_allowed')); ?>,
        unexpected: <?php echo json_encode($userStractureLang('structure_access_unexpected_error')); ?>
    };

    function showFeedback(message, ok) {
        if (!feedback) return;
        feedback.className = 'alert mb-3 ' + (ok ? 'alert-success' : 'alert-danger');
        feedback.textContent = message || '';
        feedback.classList.remove('d-none');
        window.scrollTo({top: Math.max(0, feedback.offsetTop - 120), behavior: 'smooth'});
    }

    async function request(payload) {
        const body = new URLSearchParams();
        Object.keys(payload).forEach(key => body.append(key, payload[key] ?? ''));
        body.append('csrf', csrf);
        const response = await fetch(endpoint, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}, body:body.toString(), credentials:'same-origin'});
        let data;
        try { data = await response.json(); } catch (e) { data = {status:'error', message:labels.unexpected}; }
        if (!response.ok || data.status !== 'success') throw new Error(data.message || labels.unexpected);
        return data;
    }

    function updateSelectedCount() {
        const count = document.querySelectorAll('#userStractureUnitGroups .us-unit-check:checked').length;
        const el = document.getElementById('userStractureSelectedCount');
        if (el) el.textContent = String(count);
    }

    function renderUnits(role) {
        const wrap = document.getElementById('userStractureUnitGroups');
        wrap.innerHTML = '';
        const selected = new Set((role.selected_unit_ids || []).map(Number));
        const allowed = new Set((role.allowed_unit_ids || []).map(Number));
        const invalid = new Set((role.invalid_selected_unit_ids || []).map(Number));
        const byCompany = {};

        units.forEach(unit => {
            const unitId = Number(unit.id || 0);
            const isSelected = selected.has(unitId);
            const isAllowed = allowed.has(unitId);
            if (!isAllowed && !isSelected) return;
            const companyId = Number(unit.company_id || 0);
            if (!byCompany[companyId]) byCompany[companyId] = {companyName:unit.company_name || '', activityName:unit.activity_name || '', units:[]};
            byCompany[companyId].units.push({unit, isSelected, isAllowed, invalid:invalid.has(unitId)});
        });

        Object.values(byCompany).forEach(group => {
            const box = document.createElement('div');
            box.className = 'us-company-group';
            const head = document.createElement('div');
            head.className = 'us-company-head';
            const left = document.createElement('div');
            left.innerHTML = '<div class="fw-semibold"></div><div class="us-meta"></div>';
            left.children[0].textContent = group.companyName || '—';
            left.children[1].textContent = group.activityName || '';
            head.appendChild(left);
            box.appendChild(head);

            const list = document.createElement('div');
            list.className = 'us-unit-list';
            group.units.forEach(item => {
                const unitId = Number(item.unit.id || 0);
                const row = document.createElement('label');
                row.className = 'us-unit-row d-flex align-items-start gap-2 ' + ((!item.isAllowed && item.isSelected) ? 'out-scope' : '');
                row.dataset.search = ((item.unit.unit_name || '') + ' ' + (item.unit.company_name || '') + ' ' + (item.unit.unit_type || '')).toLowerCase();

                const cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'form-check-input mt-1 us-unit-check';
                cb.value = String(unitId);
                cb.checked = item.isSelected || (Number(role.unit_id || 0) === unitId && unitId > 0);
                cb.dataset.allowed = item.isAllowed ? '1' : '0';
                cb.dataset.home = Number(role.unit_id || 0) === unitId && unitId > 0 ? '1' : '0';
                if (!item.isAllowed && !item.isSelected) cb.disabled = true;
                cb.addEventListener('change', function () {
                    if (this.dataset.home === '1' && !this.checked) this.checked = true;
                    updateSelectedCount();
                });

                const info = document.createElement('div');
                info.className = 'flex-grow-1';
                const title = document.createElement('div'); title.className='fw-semibold'; title.textContent = item.unit.unit_name || '';
                const meta = document.createElement('div'); meta.className='us-meta'; meta.textContent = item.unit.unit_type || '';
                info.appendChild(title); info.appendChild(meta);
                if (cb.dataset.home === '1') { const n=document.createElement('div'); n.className='us-meta text-primary'; n.textContent=labels.ownUnit; info.appendChild(n); }
                if (!item.isAllowed && item.isSelected) { const n=document.createElement('div'); n.className='us-meta text-warning'; n.textContent=labels.legacyOut; info.appendChild(n); }
                row.appendChild(cb); row.appendChild(info); list.appendChild(row);
            });
            box.appendChild(list); wrap.appendChild(box);
        });
        updateSelectedCount();
    }

    document.querySelectorAll('.user-stracture-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = Number(this.dataset.id || 0);
            activeRole = roleData[id] || roleData[String(id)] || null;
            if (!activeRole) return;
            document.getElementById('userStractureRoleId').value = String(activeRole.id || '');
            document.getElementById('userStractureRoleName').textContent = activeRole.rbac_name || '—';
            document.getElementById('userStractureCompany').textContent = activeRole.company_name || '—';
            document.getElementById('userStractureHomeUnit').textContent = activeRole.unit_name || '—';
            document.getElementById('userStractureScopeNote').textContent = activeRole.can_cross_company ? labels.multiCompany : labels.ownCompany;
            document.getElementById('userStractureUnitSearch').value = '';
            renderUnits(activeRole);
            modal?.show();
        });
    });

    document.getElementById('userStractureUnitSearch')?.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('#userStractureUnitGroups .us-unit-row').forEach(row => {
            row.style.display = !q || (row.dataset.search || '').includes(q) ? '' : 'none';
        });
    });

    document.getElementById('userStractureSelectAll')?.addEventListener('click', function () {
        document.querySelectorAll('#userStractureUnitGroups .us-unit-check').forEach(cb => { if (cb.dataset.allowed === '1' && !cb.disabled) cb.checked = true; });
        updateSelectedCount();
    });
    document.getElementById('userStractureClearOptional')?.addEventListener('click', function () {
        document.querySelectorAll('#userStractureUnitGroups .us-unit-check').forEach(cb => { cb.checked = cb.dataset.home === '1'; });
        updateSelectedCount();
    });

    document.getElementById('userStractureSave')?.addEventListener('click', async function () {
        if (!activeRole) return;
        const selected = Array.from(document.querySelectorAll('#userStractureUnitGroups .us-unit-check:checked')).map(cb => cb.value);
        this.disabled = true;
        try {
            const data = await request({action:'save', rbac_id:activeRole.id, unit_ids:selected.join(',')});
            modal?.hide(); showFeedback(data.message, true); setTimeout(() => location.reload(), 500);
        } catch (err) { showFeedback(err.message, false); }
        finally { this.disabled = false; }
    });

    document.querySelectorAll('.user-stracture-clear').forEach(btn => {
        btn.addEventListener('click', function () {
            clearRoleId = Number(this.dataset.id || 0);
            document.getElementById('userStractureClearName').textContent = this.dataset.name || '';
            clearModal?.show();
        });
    });
    document.getElementById('userStractureClearConfirm')?.addEventListener('click', async function () {
        if (!clearRoleId) return;
        this.disabled = true;
        try {
            const data = await request({action:'clear', rbac_id:clearRoleId});
            clearModal?.hide(); showFeedback(data.message, true); setTimeout(() => location.reload(), 500);
        } catch (err) { showFeedback(err.message, false); }
        finally { this.disabled = false; }
    });

    document.getElementById('userStractureSearch')?.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('#userStractureTable tbody tr').forEach(row => { row.style.display = !q || (row.dataset.search || '').includes(q) ? '' : 'none'; });
    });
});
</script>
