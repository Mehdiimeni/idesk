<?php
///template/bi/sla_report.php
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
                            <?php echo (_lang['sla_report']); ?>
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
                                                <th width="70">#</th>
                                                <th width="100"><?php echo _lang['group']; ?></th>
                                                <th width="80"><?php echo _lang['priority']; ?></th>
                                                <th><?php echo _lang['title']; ?></th>
                                                <th width="100"><?php echo _lang['status']; ?></th>
                                                <th width="150"><?php echo _lang['company']; ?></th>
                                                <th width="120"><?php echo _lang['user']; ?></th>
                                                <th width="100"><?php echo _lang['inbox']; ?></th>
                                                <th width="100"><?php echo _lang['added_date']; ?>
                                                </th>
                                                <th width="100"><?php echo _lang['sla_defrence']; ?>
                                                </th>
                                                <th width="120"><?php echo _lang['action']; ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // ابتدا بررسی می‌کنیم که آیا فانکشن با موفقیت اجرا شده یا خیر
                                            if (isset($allHighPriorityViolatedTickets) && is_array($allHighPriorityViolatedTickets)) {

                                                // اگر فانکشن ساختار success دارد (نسخه جدید)
                                                if (isset($allHighPriorityViolatedTickets['success'])) {

                                                    if ($allHighPriorityViolatedTickets['success'] === true) {

                                                        // داده‌ها در کلید 'data' قرار دارند
                                                        $tickets = $allHighPriorityViolatedTickets['data'];
                                                        $counter = 1;

                                                        foreach ($tickets as $ticket) {
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $counter++; ?></td>
                                                                <td><?php echo isset($ticket["type_group"]) ? $ticket["type_group"] : ''; ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    $priority = isset($ticket["priority"]) ? $ticket["priority"] : '';
                                                                    if ($priority == 'low') {
                                                                        echo '<span class="badge bg-primary ">' . _lang['low'] . '</span>';
                                                                    } elseif ($priority == 'medium') {
                                                                        echo '<span class="badge bg-warning ">' . _lang['medium'] . '</span>';
                                                                    } elseif ($priority == 'high') {
                                                                        echo '<span class="badge bg-danger ">' . _lang['high'] . '</span>';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td><?php echo isset($ticket["ticket_title"]) ? $ticket["ticket_title"] : ''; ?>
                                                                </td>
                                                                <td>
                                                                    <?php $condition = $structureModel->getConditionsByName($ticket["status"]); ?>
                                                                    <span
                                                                        class="badge alert-<?php echo $condition['condition_color']; ?> rounded-pill">
                                                                        <?php echo _lang[$condition['condition_name']]; ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo isset($ticket["company_name"]) ? $ticket["company_name"] : ''; ?>
                                                                </td>
                                                                <td><?php echo isset($ticket["user_name"]) ? $ticket["user_name"] : ''; ?>
                                                                </td>
                                                                <td><?php echo isset($ticket["last_receiver_name"]) ? $ticket["last_receiver_name"] : ''; ?>
                                                                </td>
                                                                <td><?php echo isset($ticket["creation_date"]) ? $ticket["creation_date"] : ''; ?>
                                                                </td>
                                                                <td><?php echo isset($ticket["hours_difference"]) ? $ticket["hours_difference"] : ''; ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    $ticket_id = isset($ticket['id']) ? $ticket['id'] : (isset($ticket['ticket_id']) ? $ticket['ticket_id'] : '');
                                                                    if ($ticket_id) {
                                                                        $encrypted_ticket_id = $encryptorClass->encrypt($ticket_id);
                                                                        $ticketUrl = "./tickets?ticket_id=" . $encrypted_ticket_id;
                                                                        ?>
                                                                        <a href="<?php echo $ticketUrl; ?>"
                                                                            class="btn btn-xs btn-outline-primary rounded">
                                                                            <i class="ri-eye-line"></i>
                                                                        </a>
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }

                                                        // اگر داده‌ای وجود نداشت
                                                        if (empty($tickets)) {
                                                            ?>
                                                            <tr>
                                                                <td colspan="9" class="text-center text-muted py-3">
                                                                    <i class="ri-inbox-line fs-2"></i><br>
                                                                    هیچ تیکت نقض SLA در این بازه زمانی یافت نشد.
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }

                                                    } else {
                                                        // اگر خطایی رخ داده باشد
                                                        ?>
                                                        <tr>
                                                            <td colspan="9" class="text-center text-danger py-3">
                                                                <i class="ri-error-warning-line fs-2"></i><br>
                                                                خطا در دریافت اطلاعات:
                                                                <?php echo isset($allHighPriorityViolatedTickets['error']) ? $allHighPriorityViolatedTickets['error'] : 'خطای ناشناخته'; ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }

                                                } else {
                                                    // نسخه قدیمی - ساختار آرایه ساده
                                                    $counter = 1;
                                                    foreach ($allHighPriorityViolatedTickets as $ticket) {
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $counter++; ?></td>
                                                            <td><?php echo isset($ticket["type_group"]) ? $ticket["type_group"] : ''; ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-danger">
                                                                    <?php echo isset($ticket["priority"]) ? $ticket["priority"] : ''; ?>
                                                                </span>
                                                            </td>
                                                            <td><?php echo isset($ticket["ticket_title"]) ? $ticket["ticket_title"] : ''; ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-warning text-dark">
                                                                    <?php $condition = $structureModel->getConditionsByName($ticket["status"]); ?>
                                                                    <span
                                                                        class="badge alert-<?php echo $condition['condition_color']; ?> rounded-pill">
                                                                        <?php echo _lang[$condition['condition_name']]; ?>
                                                                    </span>
                                                                </span>
                                                            </td>
                                                            <td><?php echo isset($ticket["company_name"]) ? $ticket["company_name"] : ''; ?>
                                                            </td>
                                                            <td><?php echo isset($ticket["user_name"]) ? $ticket["user_name"] : ''; ?>
                                                            </td>
                                                            <td><?php echo isset($ticket["last_receiver_name"]) ? $ticket["last_receiver_name"] : ''; ?>
                                                            </td>
                                                            <td>
                                                                <?php if (isset($permissionView) && $permissionView) { ?>
                                                                    <?php
                                                                    $ticket_id = isset($ticket['id']) ? $ticket['id'] : (isset($ticket['ticket_id']) ? $ticket['ticket_id'] : '');
                                                                    if ($ticket_id) {
                                                                        $encrypted_ticket_id = $encryptorClass->encrypt($ticket_id);
                                                                        $ticketUrl = "./tickets?ticket_id=" . $encrypted_ticket_id;
                                                                        ?>
                                                                        <a href="<?php echo $ticketUrl; ?>"
                                                                            class="btn btn-xs btn-outline-primary rounded">
                                                                            <i class="ri-eye-line"></i>
                                                                        </a>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }

                                                    if (empty($allHighPriorityViolatedTickets)) {
                                                        ?>
                                                        <tr>
                                                            <td colspan="9" class="text-center text-muted py-3">
                                                                <i class="ri-inbox-line fs-2"></i><br>
                                                                هیچ تیکت نقض SLA در این بازه زمانی یافت نشد.
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }

                                            } else {
                                                // اگر متغیر تعریف نشده باشد
                                                ?>
                                                <tr>
                                                    <td colspan="9" class="text-center text-warning py-3">
                                                        <i class="ri-alert-line fs-2"></i><br>
                                                        اطلاعاتی برای نمایش وجود ندارد.
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
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