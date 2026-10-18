<?php
///template/structure/type.php

$typeEsc = static function ($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$typeLang = static function ($key) {
    return _lang[$key] ?? $key;
};
?>

<style>
    .type-page {
        --type-primary: #3157a4;
        --type-primary-soft: #eef3ff;
        --type-border: #e6eaf1;
        --type-text: #24324a;
        --type-muted: #7a8699;
    }
    .type-hero {
        position: relative; overflow: hidden; border: 1px solid #e5eaf3; border-radius: 18px;
        background: linear-gradient(135deg, #f8faff 0%, #eef4ff 100%); padding: 22px 24px;
        box-shadow: 0 8px 28px rgba(31,51,89,.06);
    }
    .type-hero::after {
        content: ""; position: absolute; width: 190px; height: 190px; border-radius: 50%;
        background: rgba(49,87,164,.06); inset-inline-start: -70px; top: -100px;
    }
    .type-hero-icon,.type-stat-icon {
        width:46px;height:46px;border-radius:13px;display:inline-flex;align-items:center;justify-content:center;
        background:var(--type-primary-soft);color:var(--type-primary);font-size:22px;flex:0 0 auto;
    }
    .type-hero h4 { margin:0;color:var(--type-text);font-weight:700; }
    .type-hero p { margin:6px 0 0;color:var(--type-muted);max-width:900px; }
    .type-stat-card { border:1px solid var(--type-border);border-radius:16px;background:#fff;padding:17px;min-height:104px;box-shadow:0 6px 22px rgba(31,51,89,.045); }
    .type-stat-label { color:var(--type-muted);font-size:12px;margin-bottom:5px; }
    .type-stat-value { color:var(--type-text);font-weight:800;font-size:24px;line-height:1; }
    .type-main-card { border:1px solid var(--type-border);border-radius:18px;box-shadow:0 8px 28px rgba(31,51,89,.055);overflow:hidden; }
    .type-toolbar { display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px;border-bottom:1px solid var(--type-border);background:#fff; }
    .type-search-wrap { position:relative;width:min(100%,390px); }
    .type-search-wrap i { position:absolute;top:50%;transform:translateY(-50%);inset-inline-start:13px;color:#95a0b3;font-size:18px; }
    .type-search-wrap .form-control { min-height:43px;border-radius:12px;padding-inline-start:40px;border-color:#dfe5ef; }
    .type-add-btn { min-height:43px;border-radius:12px;padding-inline:16px; }
    .type-table thead th { background:#f8fafc;color:#667085;font-size:12px;font-weight:700;white-space:nowrap;border-bottom:1px solid #e9edf4; }
    .type-table tbody td { vertical-align:middle;border-color:#edf0f5;color:#344054; }
    .type-name { font-weight:700;color:#253858; }
    .type-subtext { color:#98a2b3;font-size:11px;margin-top:3px; }
    .type-group-badge,.type-count-badge,.type-state-badge { display:inline-flex;align-items:center;gap:4px;border-radius:9px;font-size:11px;font-weight:700;padding:5px 8px;white-space:nowrap; }
    .type-group-badge { background:#eef3ff;color:#3157a4; }
    .type-count-badge { background:#f4f6f9;color:#475467; }
    .type-state-badge.used { background:#ecfdf3;color:#027a48; }
    .type-state-badge.free { background:#f2f4f7;color:#667085; }
    .type-action-btn { width:34px;height:34px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #e4e8ef;background:#fff;color:#667085;transition:.18s ease; }
    .type-action-btn:hover { background:#f7f9fc;color:var(--type-primary);border-color:#d7dfec; }
    .type-action-btn.delete:hover { color:#d92d20;background:#fff5f4;border-color:#f4cbc7; }
    .type-action-btn.locked { cursor:not-allowed;opacity:.4; }
    .type-empty { padding:44px 20px!important;text-align:center;color:#98a2b3!important; }
    .type-modal .modal-content { border:0;border-radius:18px;box-shadow:0 18px 55px rgba(16,24,40,.18); }
    .type-modal .modal-header { border-bottom:1px solid #edf0f5;padding:18px 20px; }
    .type-modal .modal-body { padding:20px; }
    .type-modal .form-control { border-radius:11px;border-color:#dfe5ef; }
    .type-safety-note { border-radius:12px;background:#fff8eb;border:1px solid #fde3b1;color:#8a6116;padding:12px 14px;line-height:1.8; }
    .type-info-note { border-radius:12px;background:#f5f8ff;border:1px solid #dce6fb;color:#36558d;padding:11px 13px;line-height:1.75;font-size:12px; }
    @media (max-width:767.98px) {
        .type-toolbar { align-items:stretch;flex-direction:column; }
        .type-search-wrap,.type-add-btn { width:100%; }
    }
</style>

<div class="content-page type-page">
    <div class="content">
        <div class="container-fluid">

            <div class="type-hero mb-3">
                <div class="row align-items-center g-3 position-relative" style="z-index:1;">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-start gap-3">
                            <span class="type-hero-icon"><i class="ri-list-settings-line"></i></span>
                            <div>
                                <h4><?php echo $typeLang('type_management'); ?></h4>
                                <p><?php echo $typeLang('type_management_note'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php if ($canAddType) { ?>
                        <div class="col-lg-4 text-lg-end">
                            <button type="button" class="btn btn-primary type-add-btn" data-bs-toggle="modal" data-bs-target="#typeAddModal">
                                <i class="ri-add-line me-1"></i><?php echo _lang['add']; ?>
                            </button>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div id="typeFeedback" class="alert d-none mb-3" role="alert"></div>

            <div class="row g-3 mb-3">
                <div class="col-xl-3 col-md-6"><div class="type-stat-card d-flex align-items-center justify-content-between gap-3"><div><div class="type-stat-label"><?php echo $typeLang('type_total'); ?></div><div class="type-stat-value"><?php echo number_format($typeSummary['total']); ?></div></div><span class="type-stat-icon"><i class="ri-list-check-2"></i></span></div></div>
                <div class="col-xl-3 col-md-6"><div class="type-stat-card d-flex align-items-center justify-content-between gap-3"><div><div class="type-stat-label"><?php echo $typeLang('type_groups_total'); ?></div><div class="type-stat-value"><?php echo number_format($typeSummary['groups']); ?></div></div><span class="type-stat-icon"><i class="ri-stack-line"></i></span></div></div>
                <div class="col-xl-3 col-md-6"><div class="type-stat-card d-flex align-items-center justify-content-between gap-3"><div><div class="type-stat-label"><?php echo $typeLang('type_used_total'); ?></div><div class="type-stat-value"><?php echo number_format($typeSummary['used']); ?></div></div><span class="type-stat-icon"><i class="ri-links-line"></i></span></div></div>
                <div class="col-xl-3 col-md-6"><div class="type-stat-card d-flex align-items-center justify-content-between gap-3"><div><div class="type-stat-label"><?php echo $typeLang('type_tickets_total'); ?></div><div class="type-stat-value"><?php echo number_format($typeSummary['tickets']); ?></div></div><span class="type-stat-icon"><i class="ri-ticket-2-line"></i></span></div></div>
            </div>

            <div class="type-main-card bg-white">
                <div class="type-toolbar">
                    <div>
                        <div class="fw-bold text-dark"><?php echo _lang['types']; ?></div>
                        <div class="small text-muted mt-1"><?php echo $typeLang('type_list_note'); ?></div>
                    </div>
                    <div class="type-search-wrap">
                        <i class="ri-search-line"></i>
                        <input type="search" class="form-control" id="typeSearch" placeholder="<?php echo $typeEsc($typeLang('type_search_placeholder')); ?>" autocomplete="off">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table type-table mb-0" id="typeTable">
                        <thead><tr>
                            <th>#</th><th><?php echo _lang['name']; ?></th><th><?php echo _lang['group']; ?></th><th><?php echo _lang['description']; ?></th>
                            <th><?php echo $typeLang('type_business_usage'); ?></th><th><?php echo $typeLang('type_usage'); ?></th><th><?php echo _lang['last_updated_date']; ?></th><th><?php echo _lang['actions']; ?></th>
                        </tr></thead>
                        <tbody>
                        <?php foreach ($typeRows as $type) {
                            $searchText = implode(' ', [$type['type_name'] ?? '', $type['type_group'] ?? '', $type['type_description'] ?? '']);
                        ?>
                            <tr class="type-row" data-search="<?php echo $typeEsc($searchText); ?>">
                                <td><?php echo (int) $type['id']; ?></td>
                                <td><div class="type-name"><?php echo $typeEsc($type['type_name']); ?></div><div class="type-subtext"><?php echo $typeLang('type_id_label'); ?>: <?php echo (int) $type['id']; ?></div></td>
                                <td><span class="type-group-badge"><i class="ri-stack-line"></i><?php echo $typeEsc($type['type_group']); ?></span></td>
                                <td><?php echo $typeEsc(trim((string) ($type['type_description'] ?? '')) !== '' ? $type['type_description'] : '-'); ?></td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="type-count-badge"><i class="ri-ticket-2-line"></i><?php echo $typeLang('type_linked_tickets'); ?>: <?php echo (int) $type['ticket_count']; ?></span>
                                        <span class="type-count-badge"><i class="ri-list-check-3"></i><?php echo $typeLang('type_group_types'); ?>: <?php echo (int) $type['group_type_count']; ?></span>
                                        <?php if ((int) $type['priority_list_count'] > 0) { ?><span class="type-count-badge"><i class="ri-sort-desc"></i><?php echo $typeLang('type_priority_lists'); ?>: <?php echo (int) $type['priority_list_count']; ?></span><?php } ?>
                                    </div>
                                </td>
                                <td><span class="type-state-badge <?php echo $type['is_in_use'] ? 'used' : 'free'; ?>"><i class="<?php echo $type['is_in_use'] ? 'ri-checkbox-circle-line' : 'ri-checkbox-blank-circle-line'; ?>"></i><?php echo $typeLang($type['is_in_use'] ? 'type_used' : 'type_unused'); ?></span></td>
                                <td class="text-nowrap"><?php echo $typeEsc($type['last_updated_display']); ?></td>
                                <td><div class="d-flex align-items-center gap-1">
                                    <?php if ($canEditType) { ?>
                                        <button type="button" class="type-action-btn type-edit-btn"
                                            data-id="<?php echo (int) $type['id']; ?>"
                                            data-name="<?php echo $typeEsc($type['type_name']); ?>"
                                            data-group="<?php echo $typeEsc($type['type_group']); ?>"
                                            data-description="<?php echo $typeEsc($type['type_description']); ?>"
                                            data-group-locked="<?php echo $type['group_locked'] ? '1' : '0'; ?>"
                                            title="<?php echo $typeEsc(_lang['edit_item']); ?>"><i class="ri-edit-line"></i></button>
                                    <?php } ?>
                                    <?php if ($canDeleteType) {
                                        if ($type['delete_locked']) { ?>
                                            <button type="button" class="type-action-btn locked" disabled title="<?php echo $typeEsc($typeLang('type_delete_blocked_title')); ?>"><i class="ri-lock-line"></i></button>
                                        <?php } else { ?>
                                            <button type="button" class="type-action-btn delete type-delete-btn" data-id="<?php echo (int) $type['id']; ?>" data-name="<?php echo $typeEsc($type['type_name']); ?>" title="<?php echo $typeEsc(_lang['delete_item']); ?>"><i class="ri-delete-bin-line"></i></button>
                                        <?php }
                                    } ?>
                                </div></td>
                            </tr>
                        <?php } ?>
                            <tr id="typeNoResults" class="d-none"><td colspan="8" class="type-empty"><i class="ri-search-eye-line fs-3 d-block mb-2"></i><?php echo $typeLang('type_no_results'); ?></td></tr>
                            <?php if (!$typeRows) { ?><tr><td colspan="8" class="type-empty"><i class="ri-inbox-line fs-3 d-block mb-2"></i><?php echo $typeLang('type_no_results'); ?></td></tr><?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<datalist id="typeGroupOptions"><?php foreach ($typeGroupNames as $groupName) { ?><option value="<?php echo $typeEsc($groupName); ?>"><?php } ?></datalist>

<?php if ($canAddType) { ?>
<div class="modal fade type-modal" id="typeAddModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><div><h5 class="modal-title mb-1"><?php echo $typeLang('type_add_title'); ?></h5><div class="small text-muted"><?php echo $typeLang('type_add_note'); ?></div></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $typeEsc(_lang['close']); ?>"></button></div>
    <div class="modal-body"><form id="typeAddForm">
        <div class="mb-3"><label class="form-label" for="typeAddName"><?php echo _lang['name']; ?></label><input type="text" class="form-control" id="typeAddName" maxlength="255" required></div>
        <div class="mb-3"><label class="form-label" for="typeAddGroup"><?php echo _lang['group']; ?></label><input type="text" class="form-control" id="typeAddGroup" list="typeGroupOptions" maxlength="255" required><div class="small text-muted mt-2"><?php echo $typeLang('type_group_hint'); ?></div></div>
        <div class="mb-3"><label class="form-label" for="typeAddDescription"><?php echo _lang['description']; ?></label><textarea class="form-control" id="typeAddDescription" rows="3"></textarea></div>
        <div class="type-info-note"><i class="ri-information-line me-1"></i><?php echo $typeLang('type_unique_note'); ?></div>
        <div class="d-flex justify-content-end gap-2 mt-4"><button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button><button type="submit" class="btn btn-primary"><?php echo _lang['add']; ?></button></div>
    </form></div>
</div></div></div>
<?php } ?>

<?php if ($canEditType) { ?>
<div class="modal fade type-modal" id="typeEditModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><div><h5 class="modal-title mb-1"><?php echo $typeLang('type_edit_title'); ?></h5><div class="small text-muted"><?php echo $typeLang('type_edit_note'); ?></div></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $typeEsc(_lang['close']); ?>"></button></div>
    <div class="modal-body"><form id="typeEditForm">
        <input type="hidden" id="typeEditId">
        <div class="mb-3"><label class="form-label" for="typeEditName"><?php echo _lang['name']; ?></label><input type="text" class="form-control" id="typeEditName" maxlength="255" required></div>
        <div class="mb-3"><label class="form-label" for="typeEditGroup"><?php echo _lang['group']; ?></label><input type="text" class="form-control" id="typeEditGroup" list="typeGroupOptions" maxlength="255" required><div class="small text-muted mt-2 d-none" id="typeEditGroupLockNote"><i class="ri-lock-line me-1"></i><?php echo $typeLang('type_group_locked_note'); ?></div></div>
        <div class="mb-3"><label class="form-label" for="typeEditDescription"><?php echo _lang['description']; ?></label><textarea class="form-control" id="typeEditDescription" rows="3"></textarea></div>
        <div class="d-flex justify-content-end gap-2 mt-4"><button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button><button type="submit" class="btn btn-primary"><?php echo _lang['save_changes']; ?></button></div>
    </form></div>
</div></div></div>
<?php } ?>

<?php if ($canDeleteType) { ?>
<div class="modal fade type-modal" id="typeDeleteModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title"><?php echo _lang['delete_item']; ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo $typeEsc(_lang['close']); ?>"></button></div>
    <div class="modal-body"><input type="hidden" id="typeDeleteId"><p class="mb-2"><?php echo _lang['delete_confirm']; ?></p><div class="fw-semibold mb-3" id="typeDeleteName"></div><div class="type-safety-note"><i class="ri-information-line me-1"></i><?php echo $typeLang('type_delete_safe_note'); ?></div><div class="d-flex justify-content-end gap-2 mt-4"><button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo _lang['close']; ?></button><button type="button" class="btn btn-danger" id="typeDeleteConfirm"><?php echo _lang['delete']; ?></button></div></div>
</div></div></div>
<?php } ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const endpoint = '../icore/json/type_manage.php';
    const csrf = <?php echo json_encode($typeCsrf, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const feedback = document.getElementById('typeFeedback');

    function showFeedback(message, ok) {
        if (!feedback) return;
        feedback.className = 'alert mb-3 ' + (ok ? 'alert-success' : 'alert-danger');
        feedback.textContent = message || '';
        feedback.classList.remove('d-none');
        window.scrollTo({top:Math.max(0, feedback.offsetTop - 120),behavior:'smooth'});
    }
    async function typeRequest(payload) {
        const body = new URLSearchParams();
        Object.keys(payload).forEach(key => body.append(key, payload[key] ?? ''));
        body.append('csrf', csrf);
        const response = await fetch(endpoint,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:body.toString(),credentials:'same-origin'});
        let data;
        try { data = await response.json(); } catch(e) { data = {status:'error',message:<?php echo json_encode($typeLang('type_unexpected_error')); ?>}; }
        if (!response.ok || data.status !== 'success') throw new Error(data.message || <?php echo json_encode($typeLang('type_unexpected_error')); ?>);
        return data;
    }

    const search = document.getElementById('typeSearch');
    const noResults = document.getElementById('typeNoResults');
    if (search) search.addEventListener('input', function () {
        const q = (this.value || '').trim().toLocaleLowerCase('fa');
        const rows = Array.from(document.querySelectorAll('#typeTable tbody .type-row'));
        let visible = 0;
        rows.forEach(row => { const show = q === '' || (row.dataset.search || '').toLocaleLowerCase('fa').includes(q); row.classList.toggle('d-none', !show); if (show) visible++; });
        if (noResults) noResults.classList.toggle('d-none', visible !== 0 || rows.length === 0);
    });

    const addForm = document.getElementById('typeAddForm');
    if (addForm) addForm.addEventListener('submit', async function(e) {
        e.preventDefault(); const submit = this.querySelector('button[type="submit"]'); submit.disabled = true;
        try {
            const data = await typeRequest({action:'add',type_name:document.getElementById('typeAddName').value,type_group:document.getElementById('typeAddGroup').value,type_description:document.getElementById('typeAddDescription').value});
            bootstrap.Modal.getInstance(document.getElementById('typeAddModal'))?.hide(); showFeedback(data.message,true); setTimeout(()=>location.reload(),500);
        } catch(err) { showFeedback(err.message,false); } finally { submit.disabled = false; }
    });

    const editModalEl = document.getElementById('typeEditModal');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    document.querySelectorAll('.type-edit-btn').forEach(btn => btn.addEventListener('click', function() {
        const locked = this.dataset.groupLocked === '1';
        document.getElementById('typeEditId').value = this.dataset.id || '';
        document.getElementById('typeEditName').value = this.dataset.name || '';
        const group = document.getElementById('typeEditGroup'); group.value = this.dataset.group || ''; group.disabled = locked;
        document.getElementById('typeEditDescription').value = this.dataset.description || '';
        document.getElementById('typeEditGroupLockNote')?.classList.toggle('d-none', !locked);
        editModal?.show();
    }));

    const editForm = document.getElementById('typeEditForm');
    if (editForm) editForm.addEventListener('submit', async function(e) {
        e.preventDefault(); const submit = this.querySelector('button[type="submit"]'); submit.disabled = true;
        try {
            const groupField = document.getElementById('typeEditGroup');
            const data = await typeRequest({action:'edit',id:document.getElementById('typeEditId').value,type_name:document.getElementById('typeEditName').value,type_group:groupField.value,type_description:document.getElementById('typeEditDescription').value});
            editModal?.hide(); showFeedback(data.message,true); setTimeout(()=>location.reload(),500);
        } catch(err) { showFeedback(err.message,false); } finally { submit.disabled = false; }
    });

    const deleteModalEl = document.getElementById('typeDeleteModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
    document.querySelectorAll('.type-delete-btn').forEach(btn => btn.addEventListener('click', function() {
        document.getElementById('typeDeleteId').value = this.dataset.id || '';
        document.getElementById('typeDeleteName').textContent = this.dataset.name || '';
        deleteModal?.show();
    }));
    const deleteConfirm = document.getElementById('typeDeleteConfirm');
    if (deleteConfirm) deleteConfirm.addEventListener('click', async function() {
        this.disabled = true;
        try { const data = await typeRequest({action:'delete',id:document.getElementById('typeDeleteId').value}); deleteModal?.hide(); showFeedback(data.message,true); setTimeout(()=>location.reload(),500); }
        catch(err) { deleteModal?.hide(); showFeedback(err.message,false); }
        finally { this.disabled = false; }
    });
});
</script>
