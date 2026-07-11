<?php
///template/admin/daily_report_details.php
?>

<div class="content-page">
    <div class="content">

        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">
                            <?php echo _lang['daily_report_details']; ?>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-body">

                            <form id="dailyReportEditForm" action="./daily_report?id=<?php echo $_GET['id']; ?>"
      method="post" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?php echo $report_detials['id']; ?>">
    <input type="hidden" name="last_end_date" value="<?php echo $report_detials['end_date']; ?>">

    <div class="row">

        <div class="col-xl-6">

            <div class="mb-3">
                <label for="subject" class="form-label">
                    <?php echo _lang['subject']; ?>
                </label>

                <input type="text"
                       name="subject"
                       id="subject"
                       class="form-control form-control-lg"
                       value="<?php echo htmlspecialchars($report_detials['subject']); ?>"
                       readonly>
            </div>

            <div class="mb-3">
                <label for="priority" class="form-label">
                    <?php echo _lang['priority']; ?>
                </label>

                <select name="priority" class="form-control form-select" id="priority" required>
                    <option value="low" <?php echo ($report_detials['priority'] == 'low') ? 'selected' : ''; ?>>
                        <?php echo _lang['low']; ?>
                    </option>

                    <option value="medium" <?php echo ($report_detials['priority'] == 'medium') ? 'selected' : ''; ?>>
                        <?php echo _lang['medium']; ?>
                    </option>

                    <option value="high" <?php echo ($report_detials['priority'] == 'high') ? 'selected' : ''; ?>>
                        <?php echo _lang['high']; ?>
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label for="example-range" class="form-label">
                    <?php echo _lang['progress_percentage']; ?>
                </label>

                <div class="d-flex align-items-center">
                    <span>0%</span>

                    <input class="form-range mx-2"
                           id="example-range"
                           type="range"
                           name="progress_percentage"
                           min="0"
                           max="100"
                           value="<?php echo (int) $report_detials['progress_percentage']; ?>">

                    <span id="current-value" class="fw-bold">
                        <?php echo (int) $report_detials['progress_percentage']; ?>%
                    </span>
                </div>

                <div class="invalid-feedback d-block d-none" id="progress-error">
                <?php echo _lang['progress_percentage_cannot_be_zero']; ?>
                </div>
            </div>

        </div>

        <div class="col-xl-6">

            <div class="mb-3">
                <label for="unit_id" class="form-label">
                    <?php echo _lang['request_unit']; ?>
                </label>

                <select name="unit_id"
                        id="unit_id"
                        class="form-control select2"
                        data-toggle="select2"
                        readonly>
                    <option selected>
                        <?php echo htmlspecialchars($report_detials['member_name']); ?>
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label for="attach_file" class="form-label">
                    <?php echo _lang['attach_file']; ?>
                </label>

                <input type="file"
                       name="attach_file"
                       id="attach_file"
                       class="form-control">
            </div>

            <div class="mb-3 position-relative" id="datepicker1">
                <label for="datetime-datepicker" class="form-label">
                    <?php echo _lang['start_date']; ?>
                </label>

                <input type="text"
                       id="datetime-datepicker"
                       class="form-control persianDatepicker"
                       name="start_date"
                       value="<?php
                       $dateConverter = new DateConverter($report_detials['start_date'], $config->getNowLanguage('a'));
                       echo $dateConverter->convertToShamsi();
                       ?>"
                       readonly
                       required>

                <div class="invalid-feedback">
                   <?php echo _lang['start_date_required']; ?>
                </div>
            </div>

            <div class="mb-3 position-relative" id="datepicker2">
                <label for="datetime-datepicker2" class="form-label">
                    <?php echo _lang['end_date']; ?>
                </label>

                <input type="text"
                       id="datetime-datepicker2"
                       data-date-container="#datepicker2"
                       class="form-control persianDatepicker"
                       name="end_date"
                       placeholder="<?php
                       $dateConverter = new DateConverter($report_detials['end_date'], $config->getNowLanguage('a'));
                       echo $dateConverter->convertToShamsi();
                       ?>">
            </div>

        </div>

    </div>

    <div class="row mt-3">
        <div class="col-12">

            <div class="card border-light bg-light">
                <div class="card-body">

                    <label for="description" class="form-label fw-bold">
                        <i class="mdi mdi-text-box-outline me-1"></i>
                        <?php echo _lang['description']; ?>
                    </label>

                    <textarea class="form-control"
                              name="description"
                              id="description"
                              rows="12"
                              required><?php echo htmlspecialchars($report_detials['description']); ?></textarea>

                </div>
            </div>

        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 text-center">

            <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="mdi mdi-content-save-outline me-1"></i>
                <?php echo _lang['edit']; ?>
            </button>

        </div>
    </div>

</form>

<script>
document.getElementById('example-range').addEventListener('input', function () {
    document.getElementById('current-value').innerText = this.value + '%';
});

document.getElementById('dailyReportEditForm').addEventListener('submit', function (e) {
    let hasError = false;

    const startDate = document.getElementById('datetime-datepicker');
    const progress = document.getElementById('example-range');
    const progressError = document.getElementById('progress-error');

    startDate.classList.remove('is-invalid');
    progressError.classList.add('d-none');

    if (startDate.value.trim() === '') {
        startDate.classList.add('is-invalid');
        hasError = true;
    }

    if (parseInt(progress.value, 10) === 0) {
        progressError.classList.remove('d-none');
        hasError = true;
    }

    if (hasError) {
        e.preventDefault();
        return false;
    }
});
</script>

                        </div>
                    </div>

                    <?php if (!empty($isManagerOfReportOwner)) { ?>

                        <div class="card mt-3 border-success shadow-sm">
                            <div class="card-body">

                                <div class="row align-items-center">

                                    <div class="col-lg-12">

                                        <h5 class="text-success mb-2">
                                            <i class="mdi mdi-check-decagram me-1"></i>
                                            <?php echo _lang['manager_approval']; ?>
                                        </h5>

                                        <?php if (!empty($approvalLog)) { ?>

                                            <div class="alert alert-success mb-0">
                                                <strong>
                                                    <?php echo _lang['report_approved']; ?>
                                                </strong>

                                                <br>

                                                <?php echo _lang['approved_by']; ?>:
                                                <strong>
                                                    <?php echo htmlspecialchars($approvalLog['manager_name']); ?>
                                                </strong>

                                                <br>

                                                <?php echo _lang['approved_date']; ?>:
                                                <?php
                                                $dateConverter = new DateConverter(
                                                    $approvalLog['created_at'],
                                                    $config->getNowLanguage('a')
                                                );
                                                echo $dateConverter->convertToShamsi();
                                                ?>
                                            </div>

                                        <?php } else { ?>

                                            <div class="alert alert-warning mb-0">
                                                <i class="mdi mdi-alert-circle-outline me-1"></i>
                                                <?php echo _lang['report_not_approved_yet']; ?>
                                            </div>

                                        <?php } ?>

                                    </div>

                                    <div class="col-lg-12 text-center  mt-10 mt-lg-0">

                                        <form action="./daily_report?id=<?php echo $_GET['id']; ?>" method="post"
                                            class="mb-0">

                                            <input type="hidden" name="id" value="<?php echo $report_detials['id']; ?>">
                                            <input type="hidden" name="action_type" value="approve_report">

                                            <button type="submit" class="btn btn-success btn-lg px-4">
                                                <i class="mdi mdi-check-circle-outline me-1"></i>
                                                <?php echo _lang['approve_report']; ?>
                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>
                        </div>

                    <?php } ?>

                    <div class="card mt-3">
                        <div class="card-body">

                            <h5 class="mb-3">
                                <i class="mdi mdi-paperclip me-1"></i>
                                <?php echo _lang['attached_files']; ?>
                            </h5>

                            <?php if (!empty($allFileInfo)) { ?>

                                <?php foreach ($allFileInfo as $fileInfo) { ?>

                                    <?php
                                    $fileDownloadUrl = "./daily_report?id=" . $_GET['id'] . "&file=." . $fileInfo['downloadLink'];

                                    $fileTitle = empty($fileInfo['fileTitle'])
                                        ? $fileInfo['fileName']
                                        : $fileInfo['fileTitle'];
                                    ?>

                                    <div class="alert alert-info mb-2 border-info border">
                                        <div class="p-2">
                                            <div class="row align-items-center">

                                                <div class="col-auto">
                                                    <div class="avatar-sm">
                                                        <span class="avatar-title rounded">
                                                            .<?php echo htmlspecialchars($fileInfo['fileType']); ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="col ps-0">
                                                    <a href="<?php echo $fileDownloadUrl; ?>" class="text-muted fw-bold">
                                                        <?php echo htmlspecialchars($fileTitle); ?>
                                                    </a>

                                                    <p class="mb-0">
                                                        <?php echo htmlspecialchars($fileInfo['fileSize']); ?>
                                                    </p>
                                                </div>

                                                <div class="col-auto">
                                                    <a href="<?php echo $fileDownloadUrl; ?>"
                                                        class="btn btn-link btn-lg text-muted">
                                                        <i class="ri-download-2-line"></i>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                <?php } ?>

                            <?php } else { ?>

                                <div class="alert alert-light border mb-0">
                                    <?php echo _lang['no_attached_file']; ?>
                                </div>

                            <?php } ?>

                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">

                            <h5 class="mb-3">
                                <?php echo _lang['report_progress_history']; ?>
                            </h5>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo _lang['type']; ?></th>
                                            <th><?php echo _lang['added_date']; ?></th>
                                            <th><?php echo _lang['progress_percentage']; ?></th>
                                            <th><?php echo _lang['user']; ?></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php if ($progressLogs && $progressLogs->num_rows > 0) { ?>
                                            <?php $i = 1; ?>

                                            <?php while ($log = $progressLogs->fetch_assoc()) { ?>

                                                <tr>
                                                    <td><?php echo $i++; ?></td>

                                                    <td>
                                                        <?php echo ($log['log_type'] == 'create')
                                                            ? _lang['report_created']
                                                            : _lang['report_updated']; ?>
                                                    </td>

                                                    <td>
                                                        <?php
                                                        $dateConverter = new DateConverter($log['created_at'], $config->getNowLanguage('a'));
                                                        echo $dateConverter->convertToShamsi();
                                                        ?>
                                                    </td>

                                                    <td>
                                                        <?php echo (int) $log['progress_percentage']; ?>%
                                                    </td>

                                                    <td>
                                                        <?php echo htmlspecialchars($log['admin_name'] ?? '-'); ?>
                                                    </td>
                                                </tr>

                                            <?php } ?>

                                        <?php } else { ?>

                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <?php echo _lang['no_progress_history']; ?>
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

        </div>

    </div>
</div>

<script>
    var rangeInput = document.getElementById('example-range');
    var currentValue = document.getElementById('current-value');

    if (rangeInput && currentValue) {
        currentValue.textContent = rangeInput.value + '%';

        rangeInput.addEventListener('input', function () {
            currentValue.textContent = rangeInput.value + '%';
        });
    }

    if (typeof jQuery !== 'undefined' && typeof $.fn.persianDatepicker === 'function') {
        var dpOptions = {
            format: 'YYYY/MM/DD',
            observer: true,
            initialValue: false,
            autoClose: true,
            persianDigit: true,
            initialValueType: 'persian',
            calendar: {
                persian: {
                    locale: 'fa'
                }
            },
            calendarType: 'persian'
        };

        $('#datetime-datepicker2').persianDatepicker(dpOptions);
    }
</script>