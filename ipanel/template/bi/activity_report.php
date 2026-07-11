<?php
///template/bi/activity_report.php
?>
<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">
                            <?php echo (_lang['activity_report']); ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

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

            <?php if ($permissionStatistics) { ?>

                <div class="row">

                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="header-title">
                                    <?php echo _lang['inbox']; ?>
                                </h4>

                            </div>

                            <div class="card-body pt-2">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-striped mb-0 w-100"
                                        id="alternative-page-datatable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>
                                                    <?php echo (_lang['name']); ?>
                                                </th>
                                                <th>
                                                    <?php echo (_lang['unit']); ?>
                                                </th>
                                                <th>
                                                    <?php echo (_lang['inbox']); ?>
                                                </th>
                                                <th>
                                                    <?php echo (_lang['assign']); ?>
                                                </th>
                                                <th>
                                                    <?php echo (_lang['condition_under_review']); ?>
                                                </th>
                                                <th>
                                                    <?php echo (_lang['condition_in_progress']); ?>
                                                </th>
                                                <th>
                                                    <?php echo (_lang['condition_need_action']); ?>
                                                </th>

                                                <th>
                                                    <?php echo (_lang['condition_done']); ?>
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            foreach ($allAdminAssignedTickets as $AdminAssignedTickets) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $AdminAssignedTickets["name"]; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $AdminAssignedTickets["unit_name"]; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $count = $AdminAssignedTickets["active_tickets_count"];
                                                        $style = ($count > 0) ? 'style="background-color: #ffeb3b; font-weight: bold; padding: 3px 8px; border-radius: 4px;"' : '';
                                                        echo "<span {$style}>{$count}</span>";
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $count = $AdminAssignedTickets["assigned_tickets_count"];
                                                        $style = ($count > 0) ? 'style="background-color: #ffeb3b; font-weight: bold; padding: 3px 8px; border-radius: 4px;"' : '';
                                                        echo "<span {$style}>{$count}</span>";
                                                        ?>
                                                    </td>

                                                    <td>
                                                        <?php
                                                        $count = $AdminAssignedTickets["condition_under_review"];
                                                        $style = ($count > 0) ? 'style="background-color: #ffeb3b; font-weight: bold; padding: 3px 8px; border-radius: 4px;"' : '';
                                                        echo "<span {$style}>{$count}</span>";
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $count = $AdminAssignedTickets["condition_in_progress"];
                                                        $style = ($count > 0) ? 'style="background-color: #ffeb3b; font-weight: bold; padding: 3px 8px; border-radius: 4px;"' : '';
                                                        echo "<span {$style}>{$count}</span>";
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $count = $AdminAssignedTickets["condition_need_action"];
                                                        $style = ($count > 0) ? 'style="background-color: #ffeb3b; font-weight: bold; padding: 3px 8px; border-radius: 4px;"' : '';
                                                        echo "<span {$style}>{$count}</span>";
                                                        ?>
                                                    </td>

                                                    <td>
                                                        <?php
                                                        $count = $AdminAssignedTickets["condition_done"];
                                                        $style = ($count > 0) ? 'style="background-color: #ffeb3b; font-weight: bold; padding: 3px 8px; border-radius: 4px;"' : '';
                                                        echo "<span {$style}>{$count}</span>";
                                                        ?>
                                                    </td>
                                                </tr>

                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div> <!-- end table-responsive-->

                            </div>
                            <!-- end card-body -->
                        </div>
                        <!-- end card-->
                    </div>
                    <!-- end col -->

                    <!-- end col -->
                </div>
                <!-- end row-->
            <?php } ?>

        </div> <!-- container -->

    </div> <!-- content -->

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