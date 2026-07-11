<?php
///template/admin/daily_report_add.php
?>

<div class="content-page">
    <div class="content">

        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">
                            <?php echo _lang['daily_report_add']; ?>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-body">

                            <form id="dailyReportForm" action="./daily_report?add=r" method="post" enctype="multipart/form-data">

    <div class="row">

        <div class="col-xl-6">

            <div class="mb-3">
                <label for="subject" class="form-label">
                    <?php echo _lang['subject']; ?>
                                            </label>
                            
                                            <input type="text" name="subject" id="subject" class="form-control form-control-lg" maxlength="255"
                                                required>
                                        </div>
                            
                                        <div class="mb-3">
                                            <label for="priority" class="form-label">
                                                <?php echo _lang['priority']; ?>
                                            </label>
                            
                                            <select name="priority" class="form-control form-select" id="priority" required>
                                                <option selected value="low">
                                                    <?php echo _lang['low']; ?>
                                                </option>
                                                <option value="medium">
                                                    <?php echo _lang['medium']; ?>
                                                </option>
                                                <option value="high">
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
                            
                                                <input class="form-range mx-2" id="example-range" type="range" name="progress_percentage" min="0"
                                                    max="100" value="0">
                            
                                                <span id="current-value" class="fw-bold">0%</span>
                                            </div>
                            
                                            <div class="invalid-feedback d-block d-none" id="progress-error">
                                               <?php echo _lang['progress_percentage_cannot_be_zero']; ?>
                                            </div>
                                        </div>
                            
                                    </div>
                            
                                    <div class="col-xl-6">
                            
                                        <div class="mb-3">
                                            <label for="request" class="form-label">
                                                <?php echo _lang['request_unit']; ?>
                                            </label>
                            
                                            <select name="request" id="request" class="form-control select2" data-toggle="select2">
                                                <option>
                                                    <?php echo _lang['select']; ?>
                                                </option>
                            
                                                <?php while ($allCompaniesResult = $allCompanies->fetch_assoc()) { ?>
                            
                                                    <optgroup label="<?php echo htmlspecialchars($allCompaniesResult['company_name']); ?>">
                            
                                                        <?php
                                                        $allUserCompany = $structureModel->getAllUsersByCompanyId($allCompaniesResult['id']);

                                                        foreach ($allUserCompany as $allUserCompanyResult) {
                                                            ?>
                            
                                                            <option value="<?php echo (
                                                                $allUserCompanyResult['company_id'] . '&&' .
                                                                $allUserCompanyResult['member_id'] . '&&' .
                                                                $allUserCompanyResult['company_name'] . '&&' .
                                                                $allUserCompanyResult['member_name']
                                                            ); ?>">
                                                                <?php echo htmlspecialchars($allUserCompanyResult['member_name']); ?>
                                                            </option>
                            
                                                        <?php } ?>
                            
                                                    </optgroup>
                            
                                                <?php } ?>
                                            </select>
                                        </div>
                            
                                        <div class="mb-3">
                                            <label for="attach_file" class="form-label">
                                                <?php echo _lang['attach_file']; ?>
                                            </label>
                            
                                            <input type="file" name="attach_file" id="attach_file" class="form-control">
                                        </div>
                            
                                        <div class="mb-3 position-relative" id="datepicker1">
                                            <label for="datetime-datepicker" class="form-label">
                                                <?php echo _lang['start_date']; ?>
                                            </label>
                            
                                            <input type="text" id="datetime-datepicker" data-date-container="#datepicker1"
                                                class="form-control persianDatepicker" name="start_date" required>
                            
                                            <div class="invalid-feedback">
                                            <?php echo _lang['start_date_required']; ?>
                                            </div>
                                        </div>
                            
                                        <div class="mb-3 position-relative" id="datepicker2">
                                            <label for="datetime-datepicker2" class="form-label">
                                                <?php echo _lang['end_date']; ?>
                                            </label>
                            
                                            <input type="text" id="datetime-datepicker2" data-date-container="#datepicker2"
                                                class="form-control persianDatepicker" name="end_date">
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
                            
                                                <textarea class="form-control" name="description" id="description" rows="12" required></textarea>
                            
                                            </div>
                                        </div>
                            
                                    </div>
                                </div>
                            
                                <div class="row mt-3">
                                    <div class="col-12 text-center">
                            
                                        <?php if ($rbacClass->checkPermissionOperationByName('add_operation')) { ?>
                            
                                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                                <i class="mdi mdi-content-save me-1"></i>
                                                <?php echo _lang['submit']; ?>
                                            </button>
                            
                                        <?php } ?>
                            
                                    </div>
                                </div>
                            
                            </form>
                            
                            <script>
                                document.getElementById('example-range').addEventListener('input', function () {
                                    document.getElementById('current-value').innerText = this.value + '%';
                                });

                                document.getElementById('dailyReportForm').addEventListener('submit', function (e) {
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

        $('#datetime-datepicker').persianDatepicker(dpOptions);
        $('#datetime-datepicker2').persianDatepicker(dpOptions);
    }
</script>