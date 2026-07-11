<?php
// template/bi/bi_tickets.php
?>


<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title"><?= _lang['ticket_admin_status'] ?></h4>
                    </div>
                </div>
            </div>
            <!-- فیلترهای تاریخ -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="dateFilterForm" method="GET" action="" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">امکان انتخاب سریع:</label>
                                    <div class="btn-group w-100" role="group">
                                        <button type="submit" name="days" value="0"
                                            class="btn <?php echo (isset($_GET['days']) && $_GET['days'] == 0) ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm">
                                            امروز
                                        </button>
                                        <button type="submit" name="days" value="1"
                                            class="btn <?php echo (isset($_GET['days']) && $_GET['days'] == 1) ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm">
                                            دیروز
                                        </button>
                                        <button type="submit" name="days" value="7"
                                            class="btn <?php echo (isset($_GET['days']) && $_GET['days'] == 7) ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm">
                                            ۷ روز گذشته
                                        </button>
                                        <button type="submit" name="days" value="30"
                                            class="btn <?php echo (isset($_GET['days']) && $_GET['days'] == 30) ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm">
                                            ۳۰ روز گذشته
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">بازه دلخواه:</label>
                                    <div class="row g-2">
                                        <div class="col">
                                            <input type="text" class="form-control form-control-sm" id="startDate"
                                                name="start_date_jalali" placeholder="از تاریخ" autocomplete="off"
                                                value="<?php echo isset($_GET['start_date_jalali']) ? htmlspecialchars($_GET['start_date_jalali']) : ''; ?>">
                                            <input type="hidden" id="startDateGregorian" name="start_date">
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control form-control-sm" id="endDate"
                                                name="end_date_jalali" placeholder="تا تاریخ" autocomplete="off"
                                                value="<?php echo isset($_GET['end_date_jalali']) ? htmlspecialchars($_GET['end_date_jalali']) : ''; ?>">
                                            <input type="hidden" id="endDateGregorian" name="end_date">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="d-grid gap-2 d-md-flex w-100">
                                        <button type="submit" name="custom_date" value="1"
                                            class="btn btn-primary btn-sm w-100" id="submitCustomDate">
                                            <i class="mdi mdi-filter-variant"></i> اجرای فیلتر
                                        </button>
                                        <a href="?" class="btn btn-outline-secondary btn-sm">
                                            <i class="mdi mdi-refresh"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <!-- نمایش بازه انتخاب شده -->
                            <?php
                            $selectedRange = '';
                            if (isset($_GET['start_date_jalali']) && isset($_GET['end_date_jalali']) && isset($_GET['custom_date'])) {
                                $selectedRange = "بازه انتخاب شده: " . $_GET['start_date_jalali'] . " تا " . $_GET['end_date_jalali'];
                            } elseif (isset($_GET['days'])) {
                                $days = (int) $_GET['days'];
                                if ($days == 0)
                                    $selectedRange = "امروز";
                                elseif ($days == 1)
                                    $selectedRange = "دیروز";
                                elseif ($days == 7)
                                    $selectedRange = "۷ روز گذشته";
                                elseif ($days == 30)
                                    $selectedRange = "۳۰ روز گذشته";
                            }

                            if (!empty($selectedRange)) {
                                echo '<div class="mt-2 text-center text-muted small">' . $selectedRange . '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- پایان فیلترهای تاریخ -->
            <?php if ($permissionStatistics): ?>
                <div class="row">

                    <!-- Total Tickets -->
                    <div class="col-md-6 col-xl-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="text-muted"><?= _lang['count_all'] ?></h5>
                                <h3 class="my-2">
                                    <?= number_format($intTotalTicket) ?>
                                </h3>
                            </div>
                        </div>

                        <!-- Priority Statistics -->
                        <?php if (!empty($priorityResult) && isset($priorityResult['high'])): ?>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><?= _lang['priority'] ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-danger">
                                            <i class="mdi mdi-alert-circle-outline"></i> <?= _lang['high'] ?>
                                        </span>
                                        <span class="badge bg-danger">
                                            <?= number_format($priorityResult['high']) ?>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-warning">
                                            <i class="mdi mdi-alert-outline"></i> <?= _lang['medium'] ?>
                                        </span>
                                        <span class="badge bg-warning">
                                            <?= number_format($priorityResult['medium']) ?>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-success">
                                            <i class="mdi mdi-check-circle-outline"></i> <?= _lang['low'] ?>
                                        </span>
                                        <span class="badge bg-success">
                                            <?= number_format($priorityResult['low']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Status List -->
                        <?php if (count($statusResult) > 0): ?>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><?= _lang['ticket_admin_status'] ?></h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($statusResult as $status): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <a href="./tickets?condition_name=<?= urlencode($status['condition_name']) ?>&start_date=<?= $startDate ?? date('Y-m-d') ?>&end_date=<?= $endDate ?? date('Y-m-d') ?>"
                                                class="text-decoration-none text-dark">
                                                <?= _lang[strtolower($status['condition_name'])] ?? $status['condition_name'] ?>
                                            </a>
                                            <span class="badge bg-secondary">
                                                <?= number_format($status['num_tickets']) ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- View Statistics -->
                    <div class="col-md-6 col-xl-6">
                        <div class="card">

                            <div class="card-body">
                                <?php if (!empty($allAdminsViewCount)): ?>
                                    <?php foreach ($allAdminsViewCount as $adminView): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <?= htmlspecialchars($adminView['person_name']) ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($adminView['unit_name']) ?>
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary rounded-pill fs-6">
                                                    <?= number_format($adminView['total_views']) ?>
                                                </span>

                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="mdi mdi-account-off-outline fs-1 text-muted"></i>
                                        <p class="text-muted mt-2"><?= _lang['no_viewers'] ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>

                </div>



            <?php endif; ?>

        </div>
    </div>
</div>


<script>
    // راه‌حل نهایی - مطمئن‌ترین روش
    function initializeAll() {
        // بررسی jQuery
        if (typeof window.jQuery === 'undefined') {
            loadScript('../itheme/panel/js/vendor.min.js', initAfterjQuery);
            return;
        }

        initWithjQuery();
    }

    function loadScript(src, callback) {
        var script = document.createElement('script');
        script.src = src;
        script.onload = callback;
        document.head.appendChild(script);
    }

    function initAfterjQuery() {
        // کمی تاخیر برای اطمینان
        setTimeout(initWithjQuery, 100);
    }

    function initWithjQuery() {
        $(document).ready(function () {
            // راه‌حل 1: استفاده از flatpickr اگر موجود است
            if (typeof flatpickr !== 'undefined') {
                initFlatpickr();
            }
            // راه‌حل 2: استفاده از datepicker ساده
            else {
                initSimpleDateInputs();
            }

            // مدیریت فرم
            initFormHandling();
        });
    }

    function initFlatpickr() {
        var config = {
            locale: 'fa',
            dateFormat: "Y/m/d",
            disableMobile: true,
            clickOpens: true
        };

        // راه‌حل قطعی با تاخیر
        setTimeout(function () {
            try {
                flatpickr('#startDate', config);
                flatpickr('#endDate', config);
            } catch (e) {
                initSimpleDateInputs();
            }
        }, 300);
    }

    function initSimpleDateInputs() {
        // ایجاد یک datepicker ساده
        $('#startDate, #endDate').each(function () {
            var input = $(this);

            input.on('focus', function () {
                this.type = 'date';
            });

            input.on('blur', function () {
                this.type = 'text';
            });

            // اضافه کردن آیکن تقویم
            input.wrap('<div class="input-group"></div>');
            input.after('<span class="input-group-text"><i class="mdi mdi-calendar"></i></span>');
        });
    }

    function initFormHandling() {
        $('#dateFilterForm').on('submit', function (e) {
            // برای دکمه‌های سریع، حذف فیلدهای تاریخ
            if ($('button[name="days"]:focus').length > 0) {
                $('input[name="start_date_jalali"], input[name="end_date_jalali"], input[name="start_date"], input[name="end_date"]').removeAttr('name');
            }

            // برای فیلتر دلخواه، بررسی پر بودن فیلدها
            if ($('button[name="custom_date"]:focus').length > 0) {
                var startVal = $('#startDate').val();
                var endVal = $('#endDate').val();

                if (!startVal || !endVal) {
                    e.preventDefault();
                    alert('لطفاً هر دو تاریخ را انتخاب کنید.');
                    return false;
                }
            }

            // تبدیل تاریخ‌ها
            convertDatesForSubmit();
        });
    }

    function convertDatesForSubmit() {
        var startJalali = $('#startDate').val();
        var endJalali = $('#endDate').val();

        if (startJalali && endJalali) {
            // تبدیل ساده
            var startGregorian = startJalali.replace(/\//g, '-');
            var endGregorian = endJalali.replace(/\//g, '-');

            $('#startDateGregorian').val(startGregorian);
            $('#endDateGregorian').val(endGregorian);
        }
    }

    // شروع initialization
    document.addEventListener('DOMContentLoaded', initializeAll);
</script>

<style>
    .btn-group .btn {
        border-radius: 0.25rem !important;
        margin: 0 2px;
    }
</style>