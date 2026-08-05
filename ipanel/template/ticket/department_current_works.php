<?php
///template/ticket/department_current_works.php
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">
                                    <?php echo _lang['department_current_works']; ?>
                                </li>
                            </ol>
                        </div>
                        <h4 class="page-title">
                            <?php echo _lang['department_current_works']; ?>
                        </h4>
                    </div>
                </div>
            </div>

            <?php if (!empty($flash['ok'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-check-line me-2"></i>
                    <?php echo htmlspecialchars($flash['ok'], ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <?php if (!empty($flash['err'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i>
                    <?php echo htmlspecialchars($flash['err'], ENT_QUOTES, 'UTF-8'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <?php if (!empty($visibleSections)) { ?>
                <ul class="nav nav-tabs nav-bordered mb-3" role="tablist">
                    <?php foreach ($visibleSections as $section) {
                        $sectionId = (int) $section['id'];
                        $isActiveSection = $sectionId === (int) $activeSectionId;
                        ?>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link <?php echo $isActiveSection ? 'active' : ''; ?>"
                                id="section-tab-<?php echo $sectionId; ?>"
                                data-bs-toggle="tab"
                                data-bs-target="#section-content-<?php echo $sectionId; ?>"
                                type="button"
                                role="tab">
                                <i class="ri-building-4-line me-1"></i>
                                <?php echo _lang[$section['section_caption']] ?? htmlspecialchars($section['section_name']); ?>
                                <span class="badge bg-primary ms-1">
                                    <?php echo (int) ($sectionData[$sectionId]['statistics']['total_current'] ?? 0); ?>
                                </span>
                            </button>
                        </li>
                    <?php } ?>
                </ul>

                <div class="tab-content">
                    <?php foreach ($visibleSections as $section) {
                        $sectionId = (int) $section['id'];
                        $isActiveSection = $sectionId === (int) $activeSectionId;
                        $data = $sectionData[$sectionId];
                        $currentWorks = $data['current_works'];
                        $archivedWorks = $data['archived_works'];
                        $statistics = $data['statistics'];
                        $canManageSection = !empty($section['can_manage']);
                        ?>

                        <div
                            class="tab-pane fade <?php echo $isActiveSection ? 'show active' : ''; ?>"
                            id="section-content-<?php echo $sectionId; ?>"
                            role="tabpanel">

                            <?php if ($canManageSection) { ?>
                                <div class="card border shadow-sm mb-3">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="ri-add-circle-line me-1 text-primary"></i>
                                            <?php echo _lang['add_new_work']; ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form method="post" action="./department_current_works?section_id=<?php echo $sectionId; ?>" class="row g-3">
                                            <input type="hidden" name="section_id" value="<?php echo $sectionId; ?>">

                                            <div class="col-lg-6">
                                                <label class="form-label"><?php echo _lang['work_subject']; ?> *</label>
                                                <textarea
                                                    name="subject"
                                                    class="form-control form-control-light"
                                                    rows="3"
                                                    required
                                                    placeholder="<?php echo _lang['work_subject_placeholder']; ?>"></textarea>
                                            </div>

                                            <div class="col-lg-3">
                                                <label class="form-label"><?php echo _lang['work_priority']; ?> *</label>
                                                <select name="priority" class="form-select form-control" required>
                                                    <option value="normal"><?php echo _lang['normal']; ?></option>
                                                    <option value="force"><?php echo _lang['force']; ?></option>
                                                </select>
                                            </div>

                                            <div class="col-lg-3">
                                                <label class="form-label"><?php echo _lang['requested_by']; ?> *</label>
                                                <input
                                                    type="text"
                                                    name="requested_by"
                                                    class="form-control form-control-light"
                                                    required
                                                    maxlength="255"
                                                    placeholder="<?php echo _lang['requested_by_placeholder']; ?>">
                                            </div>

                                            <div class="col-lg-3">
                                                <label class="form-label"><?php echo _lang['ticket_number_optional']; ?></label>
                                                <input
                                                    type="text"
                                                    name="ticket_number"
                                                    class="form-control form-control-light"
                                                    maxlength="120"
                                                    placeholder="<?php echo _lang['ticket_number']; ?>">
                                            </div>

                                            <div class="col-lg-3">
                                                <label class="form-label d-block"><?php echo _lang['phone_request']; ?></label>
                                                <div class="form-check form-switch mt-2">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input phone-request-switch"
                                                        name="is_phone_request"
                                                        value="1"
                                                        id="phone-request-<?php echo $sectionId; ?>"
                                                        data-section-id="<?php echo $sectionId; ?>">
                                                    <label class="form-check-label" for="phone-request-<?php echo $sectionId; ?>">
                                                        <?php echo _lang['is_phone_request']; ?>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 caller-name-wrapper" id="caller-name-wrapper-<?php echo $sectionId; ?>" style="display:none;">
                                                <label class="form-label"><?php echo _lang['caller_name']; ?> *</label>
                                                <input
                                                    type="text"
                                                    name="caller_name"
                                                    id="caller-name-<?php echo $sectionId; ?>"
                                                    class="form-control form-control-light"
                                                    maxlength="255"
                                                    placeholder="<?php echo _lang['caller_name_placeholder']; ?>">
                                            </div>

                                            <div class="col-lg-2 d-flex align-items-end">
                                                <button type="submit" name="add_department_work" class="btn btn-primary w-100">
                                                    <i class="ri-save-line me-1"></i>
                                                    <?php echo _lang['add_work']; ?>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="card border shadow-sm mb-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="ri-list-check-2 me-1 text-primary"></i>
                                        <?php echo _lang['current_work_list']; ?>
                                    </h5>
                                    <span class="badge bg-primary">
                                        <?php echo count($currentWorks); ?>
                                    </span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-centered mb-0 align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th><?php echo _lang['row_number']; ?></th>
                                                    <th><?php echo _lang['work_subject']; ?></th>
                                                    <th><?php echo _lang['priority']; ?></th>
                                                    <th><?php echo _lang['requested_by']; ?></th>
                                                    <th><?php echo _lang['ticket_number']; ?></th>
                                                    <th><?php echo _lang['request_source']; ?></th>
                                                    <th><?php echo _lang['created_by']; ?></th>
                                                    <th><?php echo _lang['creation_date']; ?></th>
                                                    <th><?php echo _lang['work_duration']; ?></th>
                                                    <?php if ($canManageSection) { ?>
                                                        <th><?php echo _lang['actions']; ?></th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($currentWorks)) { ?>
                                                    <tr>
                                                        <td colspan="<?php echo $canManageSection ? 10 : 9; ?>" class="text-center text-muted py-4">
                                                            <?php echo _lang['no_current_work']; ?>
                                                        </td>
                                                    </tr>
                                                <?php } else {
                                                    $rowNumber = 1;
                                                    foreach ($currentWorks as $work) { ?>
                                                        <tr class="<?php echo $work['priority'] === 'force' ? 'table-danger' : ''; ?>">
                                                            <td><?php echo $rowNumber++; ?></td>
                                                            <td style="min-width:260px; white-space:pre-wrap;">
                                                                <?php echo nl2br(htmlspecialchars($work['subject'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')); ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($work['priority'] === 'force') { ?>
                                                                    <span class="badge bg-danger"><?php echo _lang['force']; ?></span>
                                                                <?php } else { ?>
                                                                    <span class="badge bg-secondary"><?php echo _lang['normal']; ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($work['requested_by'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                            <td>
                                                                <?php if (!empty($work['ticket_number'])) { ?>
                                                                    <a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($work['ticket_id']); ?>" class="fw-semibold">
                                                                        <?php echo htmlspecialchars($work['ticket_number']); ?>
                                                                    </a>
                                                                <?php } else { ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if ((int) $work['is_phone_request'] === 1) { ?>
                                                                    <span class="badge bg-info text-dark"><?php echo _lang['phone']; ?></span>
                                                                    <div class="small mt-1"><?php echo htmlspecialchars($work['caller_name'] ?? ''); ?></div>
                                                                <?php } else { ?>
                                                                    <span class="badge bg-light text-dark border"><?php echo _lang['non_phone']; ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($work['created_by_admin_name'] ?? '-'); ?></td>
                                                            <td>
                                                                <?php
                                                                $dateConverter = new DateConverter($work['creation_date'], $config->getNowLanguage('a'));
                                                                echo $dateConverter->convertToShamsi();
                                                                ?>
                                                            </td>
                                                            <td><?php echo formatDepartmentWorkDuration((int) $work['duration_minutes']); ?></td>
                                                            <?php if ($canManageSection) { ?>
                                                                <td>
                                                                    <form
                                                                        method="post"
                                                                        action="./department_current_works?section_id=<?php echo $sectionId; ?>"
                                                                        onsubmit="return confirm('<?php echo _lang['finish_work_confirm']; ?>');">
                                                                        <input type="hidden" name="section_id" value="<?php echo $sectionId; ?>">
                                                                        <input type="hidden" name="work_item_id" value="<?php echo (int) $work['id']; ?>">
                                                                        <button type="submit" name="complete_department_work" class="btn btn-sm btn-success">
                                                                            <i class="ri-checkbox-circle-line me-1"></i>
                                                                            <?php echo _lang['finish_work']; ?>
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                    <?php }
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card border shadow-sm mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="ri-archive-line me-1 text-primary"></i>
                                        <?php echo _lang['monthly_archive']; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="get" action="./department_current_works" class="row g-2 mb-3">
                                        <input type="hidden" name="section_id" value="<?php echo $sectionId; ?>">
                                        <div class="col-md-4">
                                            <label class="form-label"><?php echo _lang['archive_month']; ?></label>
                                            <select name="archive_period" class="form-select" onchange="setArchivePeriod(this, <?php echo $sectionId; ?>)">
                                                <option value=""><?php echo _lang['all_months']; ?></option>
                                                <?php foreach ($data['archive_months'] as $archivePeriod) {
                                                    $periodValue = (int) $archivePeriod['archive_year'] . '-' . str_pad((string) $archivePeriod['archive_month'], 2, '0', STR_PAD_LEFT);
                                                    $selectedPeriod = $isActiveSection && $archiveYear === (int) $archivePeriod['archive_year'] && $archiveMonth === (int) $archivePeriod['archive_month'];
                                                    ?>
                                                    <option value="<?php echo $periodValue; ?>" <?php echo $selectedPeriod ? 'selected' : ''; ?>>
                                                        <?php echo (int) $archivePeriod['archive_year']; ?>/<?php echo str_pad((string) $archivePeriod['archive_month'], 2, '0', STR_PAD_LEFT); ?>
                                                        (<?php echo (int) $archivePeriod['total']; ?>)
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" name="archive_year" id="archive-year-<?php echo $sectionId; ?>">
                                            <input type="hidden" name="archive_month" id="archive-month-<?php echo $sectionId; ?>">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="submit" class="btn btn-outline-primary w-100">
                                                <?php echo _lang['filter']; ?>
                                            </button>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th><?php echo _lang['row_number']; ?></th>
                                                    <th><?php echo _lang['work_subject']; ?></th>
                                                    <th><?php echo _lang['priority']; ?></th>
                                                    <th><?php echo _lang['requested_by']; ?></th>
                                                    <th><?php echo _lang['ticket_number']; ?></th>
                                                    <th><?php echo _lang['created_by']; ?></th>
                                                    <th><?php echo _lang['completed_by']; ?></th>
                                                    <th><?php echo _lang['creation_date']; ?></th>
                                                    <th><?php echo _lang['completion_date']; ?></th>
                                                    <th><?php echo _lang['work_duration']; ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($archivedWorks)) { ?>
                                                    <tr>
                                                        <td colspan="10" class="text-center text-muted py-4">
                                                            <?php echo _lang['no_archived_work']; ?>
                                                        </td>
                                                    </tr>
                                                <?php } else {
                                                    $archiveRow = 1;
                                                    foreach ($archivedWorks as $work) { ?>
                                                        <tr>
                                                            <td><?php echo $archiveRow++; ?></td>
                                                            <td style="min-width:260px; white-space:pre-wrap;">
                                                                <?php echo nl2br(htmlspecialchars($work['subject'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')); ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge <?php echo $work['priority'] === 'force' ? 'bg-danger' : 'bg-secondary'; ?>">
                                                                    <?php echo _lang[$work['priority']]; ?>
                                                                </span>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($work['requested_by']); ?></td>
                                                            <td><?php echo !empty($work['ticket_number']) ? htmlspecialchars($work['ticket_number']) : '-'; ?></td>
                                                            <td><?php echo htmlspecialchars($work['created_by_admin_name'] ?? '-'); ?></td>
                                                            <td><?php echo htmlspecialchars($work['completed_by_admin_name'] ?? '-'); ?></td>
                                                            <td>
                                                                <?php
                                                                $creationDate = new DateConverter($work['creation_date'], $config->getNowLanguage('a'));
                                                                echo $creationDate->convertToShamsi();
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $completionDate = new DateConverter($work['completion_date'], $config->getNowLanguage('a'));
                                                                echo $completionDate->convertToShamsi();
                                                                ?>
                                                            </td>
                                                            <td><?php echo formatDepartmentWorkDuration((int) $work['duration_minutes']); ?></td>
                                                        </tr>
                                                    <?php }
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-xl col-md-4 col-6">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="text-muted mb-1"><?php echo _lang['total_current_works']; ?></div>
                                            <h3 class="mb-0 text-primary"><?php echo (int) $statistics['total_current']; ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl col-md-4 col-6">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="text-muted mb-1"><?php echo _lang['total_force_works']; ?></div>
                                            <h3 class="mb-0 text-danger"><?php echo (int) $statistics['total_force']; ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl col-md-4 col-6">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="text-muted mb-1"><?php echo _lang['total_normal_works']; ?></div>
                                            <h3 class="mb-0"><?php echo (int) $statistics['total_normal']; ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl col-md-4 col-6">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="text-muted mb-1"><?php echo _lang['completed_this_month']; ?></div>
                                            <h3 class="mb-0 text-success"><?php echo (int) $statistics['completed_this_month']; ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl col-md-4 col-6">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="text-muted mb-1"><?php echo _lang['average_completion_time']; ?></div>
                                            <h5 class="mb-0"><?php echo formatDepartmentWorkDuration((int) $statistics['average_completion_minutes']); ?></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl col-md-4 col-6">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="text-muted mb-1"><?php echo _lang['oldest_work_age']; ?></div>
                                            <h5 class="mb-0"><?php echo formatDepartmentWorkDuration((int) $statistics['oldest_current_minutes']); ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.phone-request-switch').forEach(function (switchElement) {
        switchElement.addEventListener('change', function () {
            const sectionId = this.dataset.sectionId;
            const wrapper = document.getElementById('caller-name-wrapper-' + sectionId);
            const input = document.getElementById('caller-name-' + sectionId);

            if (this.checked) {
                wrapper.style.display = '';
                input.required = true;
                input.focus();
            } else {
                wrapper.style.display = 'none';
                input.required = false;
                input.value = '';
            }
        });
    });
});

function setArchivePeriod(selectElement, sectionId) {
    const yearInput = document.getElementById('archive-year-' + sectionId);
    const monthInput = document.getElementById('archive-month-' + sectionId);

    if (!selectElement.value) {
        yearInput.value = '';
        monthInput.value = '';
        return;
    }

    const parts = selectElement.value.split('-');
    yearInput.value = parts[0] || '';
    monthInput.value = parts[1] || '';
}
</script>
