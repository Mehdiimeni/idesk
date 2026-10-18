<?php
///template/member/admins.php

$esc = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$roleLabel = static function (string $role): string {
    if ($role === 'EXPERT') {
        return _lang['internal_admins_role_expert'];
    }
    if ($role === 'MANAGER') {
        return _lang['internal_admins_role_manager'];
    }
    return _lang['internal_admins_role_unassigned'];
};

$statusLabel = static function (string $status): string {
    return $status === 'Active' ? _lang['internal_admins_active'] : _lang['internal_admins_inactive'];
};
?>

<style>
.internal-admins-page{--ia-border:#e4e9f2;--ia-soft:#f7f9fc;--ia-muted:#6b7890;--ia-text:#17223b;--ia-shadow:0 8px 24px rgba(15,35,70,.06)}
.internal-admins-page .ia-hero{border:1px solid var(--ia-border);border-radius:18px;padding:18px 20px;background:linear-gradient(135deg,#fbfcff 0%,#f3f7ff 100%);box-shadow:var(--ia-shadow);margin-bottom:18px}
.internal-admins-page .ia-hero-title{font-size:18px;font-weight:800;color:var(--ia-text);margin:0 0 6px}
.internal-admins-page .ia-hero-note{margin:0;color:var(--ia-muted);font-size:12px;line-height:1.9}
.internal-admins-page .ia-summary{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin-bottom:18px}
.internal-admins-page .ia-stat{background:#fff;border:1px solid var(--ia-border);border-radius:16px;padding:15px 16px;box-shadow:var(--ia-shadow);display:flex;align-items:center;justify-content:space-between;gap:12px}
.internal-admins-page .ia-stat-label{font-size:11px;color:var(--ia-muted);margin-bottom:5px}.internal-admins-page .ia-stat-value{font-size:22px;font-weight:800;color:var(--ia-text)}
.internal-admins-page .ia-stat-icon{width:42px;height:42px;border-radius:13px;background:#f0f4fb;display:grid;place-items:center;font-size:20px;color:#4667a8}
.internal-admins-page .ia-panel{background:#fff;border:1px solid var(--ia-border);border-radius:18px;box-shadow:var(--ia-shadow);margin-bottom:18px;overflow:hidden}
.internal-admins-page .ia-panel-head{padding:16px 18px;border-bottom:1px solid var(--ia-border);display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
.internal-admins-page .ia-panel-title{font-size:14px;font-weight:800;color:var(--ia-text);margin:0 0 4px}.internal-admins-page .ia-panel-note{font-size:11px;color:var(--ia-muted);margin:0}
.internal-admins-page .ia-chart{padding:18px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
.internal-admins-page .ia-unit{border:1px solid var(--ia-border);border-radius:16px;background:#fbfcfe;overflow:hidden}
.internal-admins-page .ia-unit-head{padding:14px 15px;border-bottom:1px solid var(--ia-border);display:flex;justify-content:space-between;gap:10px;align-items:flex-start}
.internal-admins-page .ia-unit-name{font-size:13px;font-weight:800;color:var(--ia-text)}.internal-admins-page .ia-unit-type{font-size:10px;color:var(--ia-muted);margin-top:3px}
.internal-admins-page .ia-counts{display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end}.internal-admins-page .ia-count{font-size:10px;padding:4px 7px;border:1px solid var(--ia-border);border-radius:999px;background:#fff;color:#516078}
.internal-admins-page .ia-role-block{padding:14px 15px}.internal-admins-page .ia-role-block + .ia-role-block{border-top:1px dashed #dfe5ef}
.internal-admins-page .ia-role-title{font-size:11px;font-weight:800;color:#4d5d77;margin:0 0 10px;display:flex;align-items:center;gap:6px}
.internal-admins-page .ia-person{border:1px solid #e6ebf3;background:#fff;border-radius:13px;padding:11px 12px;margin-bottom:8px;display:flex;justify-content:space-between;gap:12px;align-items:center}
.internal-admins-page .ia-person:last-child{margin-bottom:0}.internal-admins-page .ia-person-name{font-size:12px;font-weight:800;color:var(--ia-text)}.internal-admins-page .ia-person-meta{font-size:10px;color:var(--ia-muted);line-height:1.8;margin-top:2px}
.internal-admins-page .ia-person-actions{display:flex;gap:5px;flex-shrink:0}.internal-admins-page .ia-icon-btn{width:31px;height:31px;padding:0;border-radius:8px;display:grid;place-items:center}
.internal-admins-page .ia-empty{font-size:10px;color:#8a96a9;padding:10px;border:1px dashed #dbe2ed;border-radius:10px;background:#fafbfd}
.internal-admins-page .ia-badge{display:inline-flex;align-items:center;gap:4px;font-size:9px;font-weight:700;padding:4px 7px;border-radius:999px;border:1px solid transparent}
.internal-admins-page .ia-badge-manager{background:#eef4ff;color:#315fa8;border-color:#dbe8ff}.internal-admins-page .ia-badge-expert{background:#f5f1ff;color:#644da1;border-color:#e5dcff}.internal-admins-page .ia-badge-active{background:#edfbf3;color:#14824a;border-color:#d7f4e4}.internal-admins-page .ia-badge-inactive{background:#f3f5f7;color:#687487;border-color:#e5e8ed}
.internal-admins-page .ia-toolbar{display:flex;gap:8px;flex-wrap:wrap;align-items:center}.internal-admins-page .ia-search{min-width:270px;position:relative}.internal-admins-page .ia-search input{padding-inline-end:35px}.internal-admins-page .ia-search i{position:absolute;inset-inline-end:11px;top:10px;color:#8190a7}
.internal-admins-page .ia-table-wrap{overflow:auto}.internal-admins-page table{margin:0}.internal-admins-page thead th{font-size:11px;color:#667188;background:#fbfcfe;border-bottom:1px solid var(--ia-border);white-space:nowrap}.internal-admins-page tbody td{font-size:12px;vertical-align:middle;border-color:#edf0f5}.internal-admins-page .ia-user-name{font-weight:700;color:var(--ia-text)}.internal-admins-page .ia-subline{font-size:10px;color:var(--ia-muted);margin-top:2px}.internal-admins-page .ia-table-role-badge{border-radius:999px;font-weight:600}
.internal-admins-page .modal-content{border:0;border-radius:18px;box-shadow:0 18px 50px rgba(17,35,70,.18)}.internal-admins-page .modal-header{border-bottom:1px solid var(--ia-border)}.internal-admins-page .modal-footer{border-top:1px solid var(--ia-border)}
.internal-admins-page .ia-form-note{font-size:10px;color:var(--ia-muted);background:#f7f9fc;border:1px solid var(--ia-border);border-radius:11px;padding:10px 11px;line-height:1.8}
.internal-admins-page .ia-role-preview{margin-top:7px}.internal-admins-page .ia-feedback{display:none;margin-bottom:14px}
@media(max-width:1199px){.internal-admins-page .ia-summary{grid-template-columns:repeat(3,minmax(0,1fr))}.internal-admins-page .ia-chart{grid-template-columns:1fr}}
@media(max-width:767px){.internal-admins-page .ia-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.internal-admins-page .ia-search{min-width:100%;width:100%}}
</style>

<div class="content-page internal-admins-page">
    <div class="content">
        <div class="container-fluid py-3">
            <div id="internalAdminsFeedback" class="alert ia-feedback" role="alert"></div>

            <div class="ia-hero">
                <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                    <div>
                        <h1 class="ia-hero-title"><?php echo $esc(_lang['internal_admins_management']); ?></h1>
                        <p class="ia-hero-note"><?php echo $esc(_lang['internal_admins_management_note']); ?></p>
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <span class="ia-badge ia-badge-manager"><i class="ri-building-4-line"></i><?php echo $esc($internalCompany['company_name'] ?? ''); ?></span>
                            <span class="ia-badge ia-badge-active"><i class="ri-shield-user-line"></i><?php echo $esc(_lang['internal_admins_role_from_access']); ?></span>
                        </div>
                    </div>
                    <?php if ($canAddAdmin) { ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#internalAdminAddModal">
                            <i class="ri-user-add-line me-1"></i><?php echo $esc(_lang['internal_admins_add']); ?>
                        </button>
                    <?php } ?>
                </div>
            </div>

            <div class="ia-summary">
                <div class="ia-stat"><div><div class="ia-stat-label"><?php echo $esc(_lang['internal_admins_total']); ?></div><div class="ia-stat-value"><?php echo (int) $adminStats['total']; ?></div></div><div class="ia-stat-icon"><i class="ri-team-line"></i></div></div>
                <div class="ia-stat"><div><div class="ia-stat-label"><?php echo $esc(_lang['internal_admins_active_total']); ?></div><div class="ia-stat-value"><?php echo (int) $adminStats['active']; ?></div></div><div class="ia-stat-icon"><i class="ri-checkbox-circle-line"></i></div></div>
                <div class="ia-stat"><div><div class="ia-stat-label"><?php echo $esc(_lang['internal_admins_manager_total']); ?></div><div class="ia-stat-value"><?php echo (int) $adminStats['managers']; ?></div></div><div class="ia-stat-icon"><i class="ri-user-star-line"></i></div></div>
                <div class="ia-stat"><div><div class="ia-stat-label"><?php echo $esc(_lang['internal_admins_expert_total']); ?></div><div class="ia-stat-value"><?php echo (int) $adminStats['experts']; ?></div></div><div class="ia-stat-icon"><i class="ri-user-settings-line"></i></div></div>
                <div class="ia-stat"><div><div class="ia-stat-label"><?php echo $esc(_lang['internal_admins_unit_total']); ?></div><div class="ia-stat-value"><?php echo (int) $adminStats['units']; ?></div></div><div class="ia-stat-icon"><i class="ri-node-tree"></i></div></div>
            </div>

            <div class="ia-panel">
                <div class="ia-panel-head">
                    <div>
                        <div class="ia-panel-title"><?php echo $esc(_lang['internal_admins_org_chart']); ?></div>
                        <p class="ia-panel-note"><?php echo $esc(_lang['internal_admins_org_chart_note']); ?></p>
                    </div>
                </div>
                <div class="ia-chart" id="internalAdminsChart">
                    <?php foreach ($adminChartUnits as $unitData) {
                        $unit = $unitData['unit'];
                        $unitId = (int) $unit['id'];
                        $unitSearch = mb_strtolower(trim((string) ($unit['unit_name'] . ' ' . $unit['unit_type'])));
                    ?>
                        <section class="ia-unit ia-unit-card" data-search="<?php echo $esc($unitSearch); ?>">
                            <div class="ia-unit-head">
                                <div>
                                    <div class="ia-unit-name"><?php echo $esc($unit['unit_name']); ?></div>
                                    <div class="ia-unit-type"><?php echo $esc(($unit['unit_type'] ?? '') !== '' ? $unit['unit_type'] : _lang['internal_admins_unit_type_empty']); ?></div>
                                </div>
                                <div class="ia-counts">
                                    <span class="ia-count"><?php echo $esc(_lang['internal_admins_role_manager']); ?>: <?php echo count($unitData['managers']); ?></span>
                                    <span class="ia-count"><?php echo $esc(_lang['internal_admins_role_expert']); ?>: <?php echo count($unitData['experts']); ?></span>
                                </div>
                            </div>

                            <div class="ia-role-block">
                                <div class="ia-role-title"><i class="ri-user-star-line"></i><?php echo $esc(_lang['internal_admins_managers']); ?></div>
                                <?php if (empty($unitData['managers'])) { ?>
                                    <div class="ia-empty"><?php echo $esc(_lang['internal_admins_no_manager']); ?></div>
                                <?php } ?>
                                <?php foreach ($unitData['managers'] as $person) {
                                    $personSearch = mb_strtolower(trim(implode(' ', [
                                        $person['name'] ?? '', $person['email'] ?? '', $person['mobile'] ?? '',
                                        $person['rbac_name'] ?? '', $person['unit_name'] ?? ''
                                    ])));
                                ?>
                                    <div class="ia-person ia-person-card" data-search="<?php echo $esc($personSearch); ?>" data-role="MANAGER" data-status="<?php echo $esc($person['status']); ?>">
                                        <div>
                                            <div class="ia-person-name"><?php echo $esc($person['name']); ?></div>
                                            <div class="ia-person-meta"><?php echo $esc($person['rbac_name'] ?: _lang['internal_admins_no_access']); ?><br><?php echo $esc($person['email']); ?></div>
                                            <div class="mt-1"><span class="ia-badge <?php echo $person['status'] === 'Active' ? 'ia-badge-active' : 'ia-badge-inactive'; ?>"><?php echo $esc($statusLabel($person['status'])); ?></span></div>
                                        </div>
                                        <div class="ia-person-actions">
                                            <?php if ($canEditAdmin) { ?>
                                                <button type="button" class="btn btn-light border ia-icon-btn js-admin-edit"
                                                    title="<?php echo $esc(_lang['edit_item']); ?>"
                                                    data-id="<?php echo (int) $person['id']; ?>"
                                                    data-name="<?php echo $esc($person['name']); ?>"
                                                    data-email="<?php echo $esc($person['email']); ?>"
                                                    data-mobile="<?php echo $esc($person['mobile']); ?>"
                                                    data-unit-id="<?php echo (int) $person['unit_id']; ?>"
                                                    data-rbac-id="<?php echo (int) $person['rbac_id']; ?>"
                                                    data-bs-toggle="modal" data-bs-target="#internalAdminEditModal"><i class="ri-edit-line"></i></button>
                                            <?php } ?>
                                            <?php if ($person['status'] === 'Active' && $canInactivateAdmin) { ?>
                                                <button type="button" class="btn btn-light border ia-icon-btn js-admin-status" title="<?php echo $esc(_lang['inactive']); ?>" data-id="<?php echo (int) $person['id']; ?>" data-action="inactivate" data-name="<?php echo $esc($person['name']); ?>"><i class="ri-user-unfollow-line"></i></button>
                                            <?php } elseif ($person['status'] !== 'Active' && $canActivateAdmin) { ?>
                                                <button type="button" class="btn btn-light border ia-icon-btn js-admin-status" title="<?php echo $esc(_lang['active']); ?>" data-id="<?php echo (int) $person['id']; ?>" data-action="activate" data-name="<?php echo $esc($person['name']); ?>"><i class="ri-user-follow-line"></i></button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="ia-role-block">
                                <div class="ia-role-title"><i class="ri-user-settings-line"></i><?php echo $esc(_lang['internal_admins_experts']); ?></div>
                                <?php if (empty($unitData['experts'])) { ?>
                                    <div class="ia-empty"><?php echo $esc(_lang['internal_admins_no_expert']); ?></div>
                                <?php } ?>
                                <?php foreach ($unitData['experts'] as $person) {
                                    $personSearch = mb_strtolower(trim(implode(' ', [
                                        $person['name'] ?? '', $person['email'] ?? '', $person['mobile'] ?? '',
                                        $person['rbac_name'] ?? '', $person['unit_name'] ?? ''
                                    ])));
                                ?>
                                    <div class="ia-person ia-person-card" data-search="<?php echo $esc($personSearch); ?>" data-role="EXPERT" data-status="<?php echo $esc($person['status']); ?>">
                                        <div>
                                            <div class="ia-person-name"><?php echo $esc($person['name']); ?></div>
                                            <div class="ia-person-meta"><?php echo $esc($person['rbac_name'] ?: _lang['internal_admins_no_access']); ?><br><?php echo $esc($person['email']); ?></div>
                                            <div class="mt-1"><span class="ia-badge <?php echo $person['status'] === 'Active' ? 'ia-badge-active' : 'ia-badge-inactive'; ?>"><?php echo $esc($statusLabel($person['status'])); ?></span></div>
                                        </div>
                                        <div class="ia-person-actions">
                                            <?php if ($canEditAdmin) { ?>
                                                <button type="button" class="btn btn-light border ia-icon-btn js-admin-edit"
                                                    title="<?php echo $esc(_lang['edit_item']); ?>"
                                                    data-id="<?php echo (int) $person['id']; ?>"
                                                    data-name="<?php echo $esc($person['name']); ?>"
                                                    data-email="<?php echo $esc($person['email']); ?>"
                                                    data-mobile="<?php echo $esc($person['mobile']); ?>"
                                                    data-unit-id="<?php echo (int) $person['unit_id']; ?>"
                                                    data-rbac-id="<?php echo (int) $person['rbac_id']; ?>"
                                                    data-bs-toggle="modal" data-bs-target="#internalAdminEditModal"><i class="ri-edit-line"></i></button>
                                            <?php } ?>
                                            <?php if ($person['status'] === 'Active' && $canInactivateAdmin) { ?>
                                                <button type="button" class="btn btn-light border ia-icon-btn js-admin-status" title="<?php echo $esc(_lang['inactive']); ?>" data-id="<?php echo (int) $person['id']; ?>" data-action="inactivate" data-name="<?php echo $esc($person['name']); ?>"><i class="ri-user-unfollow-line"></i></button>
                                            <?php } elseif ($person['status'] !== 'Active' && $canActivateAdmin) { ?>
                                                <button type="button" class="btn btn-light border ia-icon-btn js-admin-status" title="<?php echo $esc(_lang['active']); ?>" data-id="<?php echo (int) $person['id']; ?>" data-action="activate" data-name="<?php echo $esc($person['name']); ?>"><i class="ri-user-follow-line"></i></button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </section>
                    <?php } ?>
                </div>
            </div>

            <div class="ia-panel">
                <div class="ia-panel-head">
                    <div>
                        <div class="ia-panel-title"><?php echo $esc(_lang['internal_admins_list']); ?></div>
                        <p class="ia-panel-note"><?php echo $esc(_lang['internal_admins_list_note']); ?></p>
                    </div>
                    <div class="ia-toolbar">
                        <div class="ia-search">
                            <input type="text" class="form-control form-control-sm" id="internalAdminsSearch" placeholder="<?php echo $esc(_lang['internal_admins_search']); ?>">
                            <i class="ri-search-line"></i>
                        </div>
                        <select class="form-select form-select-sm" id="internalAdminsRoleFilter">
                            <option value="all"><?php echo $esc(_lang['internal_admins_filter_all_roles']); ?></option>
                            <option value="MANAGER"><?php echo $esc(_lang['internal_admins_role_manager']); ?></option>
                            <option value="EXPERT"><?php echo $esc(_lang['internal_admins_role_expert']); ?></option>
                            <option value="UNASSIGNED"><?php echo $esc(_lang['internal_admins_role_unassigned']); ?></option>
                        </select>
                        <select class="form-select form-select-sm" id="internalAdminsStatusFilter">
                            <option value="all"><?php echo $esc(_lang['internal_admins_filter_all_statuses']); ?></option>
                            <option value="Active"><?php echo $esc(_lang['internal_admins_active']); ?></option>
                            <option value="Inactive"><?php echo $esc(_lang['internal_admins_inactive']); ?></option>
                        </select>
                    </div>
                </div>

                <div class="ia-table-wrap">
                    <table class="table table-hover align-middle" id="internalAdminsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $esc(_lang['name']); ?></th>
                                <th><?php echo $esc(_lang['unit']); ?></th>
                                <th><?php echo $esc(_lang['internal_admins_org_role']); ?></th>
                                <th><?php echo $esc(_lang['access']); ?></th>
                                <th><?php echo $esc(_lang['internal_admins_contact']); ?></th>
                                <th><?php echo $esc(_lang['status']); ?></th>
                                <th><?php echo $esc(_lang['actions']); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($internalAdmins as $person) {
                            $searchText = mb_strtolower(trim(implode(' ', [
                                $person['name'] ?? '', $person['email'] ?? '', $person['mobile'] ?? '', $person['unit_name'] ?? '',
                                $person['unit_type'] ?? '', $person['rbac_name'] ?? '', $roleLabel($person['org_role'])
                            ])));
                        ?>
                            <tr class="js-admin-row" data-search="<?php echo $esc($searchText); ?>" data-role="<?php echo $esc($person['org_role']); ?>" data-status="<?php echo $esc($person['status']); ?>">
                                <td><?php echo (int) $person['id']; ?></td>
                                <td><div class="ia-user-name"><?php echo $esc($person['name']); ?></div><div class="ia-subline"><?php echo $esc($person['email']); ?></div></td>
                                <td><div><?php echo $esc($person['unit_name']); ?></div><div class="ia-subline"><?php echo $esc($person['unit_type']); ?></div></td>
                                <td><span class="badge <?php echo $person['org_role'] === 'EXPERT' ? 'bg-success' : 'bg-warning'; ?> ia-table-role-badge"><?php echo $esc($roleLabel($person['org_role'])); ?></span></td>
                                <td><div><?php echo $esc($person['rbac_name'] ?: _lang['internal_admins_no_access']); ?></div><?php if (!empty($person['parent_rbac_name'])) { ?><div class="ia-subline"><?php echo $esc(_lang['internal_admins_parent_access']); ?>: <?php echo $esc($person['parent_rbac_name']); ?></div><?php } ?></td>
                                <td><div><?php echo $esc($person['mobile']); ?></div><div class="ia-subline"><?php echo $esc($person['email']); ?></div></td>
                                <td><?php if ($person['status'] === 'Active') { ?><span class="badge bg-soft-success text-success border"><i class="ri-checkbox-circle-line me-1"></i><?php echo $esc($statusLabel($person['status'])); ?></span><?php } else { ?><span class="badge bg-light text-muted border"><i class="ri-forbid-line me-1"></i><?php echo $esc($statusLabel($person['status'])); ?></span><?php } ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <?php if ($canEditAdmin) { ?>
                                            <button type="button" class="btn btn-light border ia-icon-btn js-admin-edit"
                                                title="<?php echo $esc(_lang['edit_item']); ?>"
                                                data-id="<?php echo (int) $person['id']; ?>"
                                                data-name="<?php echo $esc($person['name']); ?>"
                                                data-email="<?php echo $esc($person['email']); ?>"
                                                data-mobile="<?php echo $esc($person['mobile']); ?>"
                                                data-unit-id="<?php echo (int) $person['unit_id']; ?>"
                                                data-rbac-id="<?php echo (int) $person['rbac_id']; ?>"
                                                data-bs-toggle="modal" data-bs-target="#internalAdminEditModal"><i class="ri-edit-line"></i></button>
                                        <?php } ?>
                                        <?php if ($person['status'] === 'Active' && $canInactivateAdmin) { ?>
                                            <button type="button" class="btn btn-light border ia-icon-btn js-admin-status" title="<?php echo $esc(_lang['inactive']); ?>" data-id="<?php echo (int) $person['id']; ?>" data-action="inactivate" data-name="<?php echo $esc($person['name']); ?>"><i class="ri-user-unfollow-line"></i></button>
                                        <?php } elseif ($person['status'] !== 'Active' && $canActivateAdmin) { ?>
                                            <button type="button" class="btn btn-light border ia-icon-btn js-admin-status" title="<?php echo $esc(_lang['active']); ?>" data-id="<?php echo (int) $person['id']; ?>" data-action="activate" data-name="<?php echo $esc($person['name']); ?>"><i class="ri-user-follow-line"></i></button>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <div id="internalAdminsNoResult" class="ia-empty m-3" style="display:none"><?php echo $esc(_lang['internal_admins_no_result']); ?></div>
                </div>
                <div class="px-3 py-2 border-top small text-muted"><i class="ri-information-line me-1"></i><?php echo $esc(_lang['internal_admins_no_physical_delete']); ?></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="internalAdminAddModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div><h5 class="modal-title"><?php echo $esc(_lang['internal_admins_add_title']); ?></h5><div class="small text-muted mt-1"><?php echo $esc(_lang['internal_admins_add_note']); ?></div></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="internalAdminAddForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $esc($adminsManagementCsrf); ?>">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label"><?php echo $esc(_lang['name']); ?></label><input type="text" class="form-control" name="name" maxlength="200" required></div>
                            <div class="col-md-6"><label class="form-label"><?php echo $esc(_lang['email']); ?></label><input type="email" class="form-control" name="email" maxlength="200" required></div>
                            <div class="col-md-6"><label class="form-label"><?php echo $esc(_lang['mobile']); ?></label><input type="text" class="form-control" name="mobile" maxlength="25"></div>
                            <div class="col-md-6"><label class="form-label"><?php echo $esc(_lang['password']); ?></label><input type="password" class="form-control" name="password" minlength="8" required></div>
                            <div class="col-md-6">
                                <label class="form-label"><?php echo $esc(_lang['unit']); ?></label>
                                <select class="form-select js-unit-select" name="unit_id" required>
                                    <?php foreach ($internalUnits as $unit) { ?><option value="<?php echo (int) $unit['id']; ?>"><?php echo $esc($unit['unit_name']); ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?php echo $esc(_lang['access']); ?></label>
                                <select class="form-select js-rbac-select" name="rbac_id" required>
                                    <?php foreach ($internalRbacs as $rbac) { ?>
                                        <option value="<?php echo (int) $rbac['id']; ?>" data-unit-id="<?php echo (int) $rbac['unit_id']; ?>" data-role="<?php echo $esc($rbac['org_role']); ?>"><?php echo $esc($rbac['rbac_name']); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="ia-role-preview"><span class="ia-badge ia-badge-manager js-role-preview"></span></div>
                            </div>
                        </div>
                        <div class="ia-form-note mt-3"><?php echo $esc(_lang['internal_admins_form_note']); ?></div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo $esc(_lang['close']); ?></button><button type="button" class="btn btn-primary" id="internalAdminAddSubmit"><?php echo $esc(_lang['add']); ?></button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="internalAdminEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div><h5 class="modal-title"><?php echo $esc(_lang['internal_admins_edit_title']); ?></h5><div class="small text-muted mt-1"><?php echo $esc(_lang['internal_admins_edit_note']); ?></div></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="internalAdminEditForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $esc($adminsManagementCsrf); ?>">
                        <input type="hidden" name="admin_id" value="">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label"><?php echo $esc(_lang['name']); ?></label><input type="text" class="form-control" name="name" maxlength="200" required></div>
                            <div class="col-md-6"><label class="form-label"><?php echo $esc(_lang['email']); ?></label><input type="email" class="form-control" name="email" maxlength="200" required></div>
                            <div class="col-md-6"><label class="form-label"><?php echo $esc(_lang['mobile']); ?></label><input type="text" class="form-control" name="mobile" maxlength="25"></div>
                            <div class="col-md-6"><label class="form-label"><?php echo $esc(_lang['password']); ?> <span class="text-muted small"><?php echo $esc(_lang['internal_admins_password_optional']); ?></span></label><input type="password" class="form-control" name="password" minlength="8"></div>
                            <div class="col-md-6"><label class="form-label"><?php echo $esc(_lang['unit']); ?></label><select class="form-select js-unit-select" name="unit_id" required><?php foreach ($internalUnits as $unit) { ?><option value="<?php echo (int) $unit['id']; ?>"><?php echo $esc($unit['unit_name']); ?></option><?php } ?></select></div>
                            <div class="col-md-6"><label class="form-label"><?php echo $esc(_lang['access']); ?></label><select class="form-select js-rbac-select" name="rbac_id" required><?php foreach ($internalRbacs as $rbac) { ?><option value="<?php echo (int) $rbac['id']; ?>" data-unit-id="<?php echo (int) $rbac['unit_id']; ?>" data-role="<?php echo $esc($rbac['org_role']); ?>"><?php echo $esc($rbac['rbac_name']); ?></option><?php } ?></select><div class="ia-role-preview"><span class="ia-badge ia-badge-manager js-role-preview"></span></div></div>
                        </div>
                        <div class="ia-form-note mt-3"><?php echo $esc(_lang['internal_admins_form_note']); ?></div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo $esc(_lang['close']); ?></button><button type="button" class="btn btn-primary" id="internalAdminEditSubmit"><?php echo $esc(_lang['save_changes']); ?></button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="internalAdminStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="internalAdminStatusTitle"></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><p class="mb-0" id="internalAdminStatusMessage"></p></div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo $esc(_lang['close']); ?></button><button type="button" class="btn btn-primary" id="internalAdminStatusSubmit"><?php echo $esc(_lang['internal_admins_confirm']); ?></button></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const endpoint = '../icore/json/admin_manage.php';
    const csrf = <?php echo json_encode($adminsManagementCsrf, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const labels = {
        manager: <?php echo json_encode(_lang['internal_admins_role_manager'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
        expert: <?php echo json_encode(_lang['internal_admins_role_expert'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
        unassigned: <?php echo json_encode(_lang['internal_admins_role_unassigned'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
        activateTitle: <?php echo json_encode(_lang['internal_admins_activate_title'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
        inactivateTitle: <?php echo json_encode(_lang['internal_admins_inactivate_title'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
        activateMessage: <?php echo json_encode(_lang['internal_admins_activate_confirm'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
        inactivateMessage: <?php echo json_encode(_lang['internal_admins_inactivate_confirm'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
    };

    const feedback = document.getElementById('internalAdminsFeedback');
    const showFeedback = (message, ok) => {
        if (!feedback) return;
        feedback.textContent = message || '';
        feedback.className = 'alert ia-feedback ' + (ok ? 'alert-success' : 'alert-danger');
        feedback.style.display = 'block';
        window.scrollTo({top: 0, behavior: 'smooth'});
    };

    const post = async (payload) => {
        const body = new URLSearchParams();
        Object.entries(payload).forEach(([key, value]) => body.append(key, value == null ? '' : value));
        const response = await fetch(endpoint, {method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}, body});
        const data = await response.json().catch(() => ({status: 'error', message: response.statusText}));
        if (!response.ok || data.status !== 'success') throw new Error(data.message || response.statusText);
        return data;
    };

    const getRoleLabel = (role) => role === 'EXPERT' ? labels.expert : (role === 'MANAGER' ? labels.manager : labels.unassigned);

    const filterRbac = (form, selectedRbacId = null) => {
        const unitSelect = form.querySelector('.js-unit-select');
        const rbacSelect = form.querySelector('.js-rbac-select');
        const preview = form.querySelector('.js-role-preview');
        if (!unitSelect || !rbacSelect) return;
        const unitId = String(unitSelect.value || '');
        let firstVisible = null;
        Array.from(rbacSelect.options).forEach(opt => {
            const visible = String(opt.dataset.unitId || '') === unitId;
            opt.hidden = !visible;
            opt.disabled = !visible;
            if (visible && firstVisible === null) firstVisible = opt;
        });
        const desired = selectedRbacId == null ? null : Array.from(rbacSelect.options).find(opt => String(opt.value) === String(selectedRbacId) && !opt.disabled);
        if (desired) desired.selected = true;
        else if (firstVisible) firstVisible.selected = true;
        if (preview) {
            const selected = rbacSelect.options[rbacSelect.selectedIndex];
            const role = selected ? selected.dataset.role : 'UNASSIGNED';
            preview.textContent = getRoleLabel(role);
            preview.className = 'ia-badge js-role-preview ' + (role === 'EXPERT' ? 'ia-badge-expert' : 'ia-badge-manager');
        }
    };

    document.querySelectorAll('.js-unit-select').forEach(select => select.addEventListener('change', () => filterRbac(select.closest('form'))));
    document.querySelectorAll('.js-rbac-select').forEach(select => select.addEventListener('change', () => filterRbac(select.closest('form'), select.value)));

    const addForm = document.getElementById('internalAdminAddForm');
    if (addForm) filterRbac(addForm);

    document.querySelectorAll('.js-admin-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = document.getElementById('internalAdminEditForm');
            if (!form) return;
            form.elements.admin_id.value = this.dataset.id || '';
            form.elements.name.value = this.dataset.name || '';
            form.elements.email.value = this.dataset.email || '';
            form.elements.mobile.value = this.dataset.mobile || '';
            form.elements.password.value = '';
            form.elements.unit_id.value = this.dataset.unitId || '';
            filterRbac(form, this.dataset.rbacId || '');
        });
    });

    const serializeForm = (form) => {
        const data = {};
        new FormData(form).forEach((value, key) => { data[key] = value; });
        return data;
    };

    const addSubmit = document.getElementById('internalAdminAddSubmit');
    if (addSubmit) addSubmit.addEventListener('click', async function () {
        if (!addForm || !addForm.reportValidity()) return;
        this.disabled = true;
        try {
            const payload = serializeForm(addForm);
            payload.action = 'add';
            payload.csrf_token = csrf;
            const data = await post(payload);
            bootstrap.Modal.getInstance(document.getElementById('internalAdminAddModal'))?.hide();
            showFeedback(data.message, true);
            setTimeout(() => location.reload(), 450);
        } catch (e) { showFeedback(e.message, false); }
        finally { this.disabled = false; }
    });

    const editForm = document.getElementById('internalAdminEditForm');
    const editSubmit = document.getElementById('internalAdminEditSubmit');
    if (editSubmit) editSubmit.addEventListener('click', async function () {
        if (!editForm || !editForm.reportValidity()) return;
        this.disabled = true;
        try {
            const payload = serializeForm(editForm);
            payload.action = 'edit';
            payload.csrf_token = csrf;
            const data = await post(payload);
            bootstrap.Modal.getInstance(document.getElementById('internalAdminEditModal'))?.hide();
            showFeedback(data.message, true);
            setTimeout(() => location.reload(), 450);
        } catch (e) { showFeedback(e.message, false); }
        finally { this.disabled = false; }
    });

    let pendingStatus = {admin_id: 0, action: ''};
    const statusModalEl = document.getElementById('internalAdminStatusModal');
    const statusModal = statusModalEl ? new bootstrap.Modal(statusModalEl) : null;
    document.querySelectorAll('.js-admin-status').forEach(btn => {
        btn.addEventListener('click', function () {
            pendingStatus = {admin_id: this.dataset.id || 0, action: this.dataset.action || ''};
            const name = this.dataset.name || '';
            const activate = pendingStatus.action === 'activate';
            document.getElementById('internalAdminStatusTitle').textContent = activate ? labels.activateTitle : labels.inactivateTitle;
            document.getElementById('internalAdminStatusMessage').textContent = (activate ? labels.activateMessage : labels.inactivateMessage).replace('%s', name);
            statusModal?.show();
        });
    });

    const statusSubmit = document.getElementById('internalAdminStatusSubmit');
    if (statusSubmit) statusSubmit.addEventListener('click', async function () {
        if (!pendingStatus.admin_id || !pendingStatus.action) return;
        this.disabled = true;
        try {
            const data = await post({action: pendingStatus.action, admin_id: pendingStatus.admin_id, csrf_token: csrf});
            statusModal?.hide();
            showFeedback(data.message, true);
            setTimeout(() => location.reload(), 450);
        } catch (e) { statusModal?.hide(); showFeedback(e.message, false); }
        finally { this.disabled = false; }
    });

    const search = document.getElementById('internalAdminsSearch');
    const roleFilter = document.getElementById('internalAdminsRoleFilter');
    const statusFilter = document.getElementById('internalAdminsStatusFilter');
    const applyFilters = () => {
        const q = (search?.value || '').trim().toLocaleLowerCase();
        const role = roleFilter?.value || 'all';
        const status = statusFilter?.value || 'all';
        let visibleRows = 0;
        document.querySelectorAll('.js-admin-row').forEach(row => {
            const ok = (!q || (row.dataset.search || '').includes(q)) && (role === 'all' || row.dataset.role === role) && (status === 'all' || row.dataset.status === status);
            row.style.display = ok ? '' : 'none';
            if (ok) visibleRows++;
        });
        const noResult = document.getElementById('internalAdminsNoResult');
        if (noResult) noResult.style.display = visibleRows ? 'none' : 'block';

        document.querySelectorAll('.ia-person-card').forEach(card => {
            const ok = (!q || (card.dataset.search || '').includes(q)) && (role === 'all' || card.dataset.role === role) && (status === 'all' || card.dataset.status === status);
            card.style.display = ok ? '' : 'none';
        });
        document.querySelectorAll('.ia-unit-card').forEach(unit => {
            const personVisible = Array.from(unit.querySelectorAll('.ia-person-card')).some(card => card.style.display !== 'none');
            const unitMatches = !q || (unit.dataset.search || '').includes(q);
            unit.style.display = (personVisible || unitMatches) && role === 'all' && status === 'all' ? '' : (personVisible ? '' : 'none');
        });
    };
    search?.addEventListener('input', applyFilters);
    roleFilter?.addEventListener('change', applyFilters);
    statusFilter?.addEventListener('change', applyFilters);
});
</script>
