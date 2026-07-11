<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>جدول داده Bootstrap با قابلیت Export</title>

    <!-- Bootstrap CSS -->
    <!-- *** این لینک را با مسیر فایل CSS بوت استرپ خود جایگزین کنید *** -->
    <link rel="stylesheet" href="../itheme/panel/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css">


  
      

    <!-- DataTables CSS -->
    <!-- *** این لینک‌ها را با مسیر فایل‌های CSS دیتاتبل خود  کنید *** -->
        <link href="../itheme/panel/vendor/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

    <style>
        /* استایل‌های اضافی برای هماهنگی بهتر با بوت استرپ */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin: 10px 0;
        }

        .dt-buttons {
            margin-bottom: 10px;
        }

        /* برای راست به چپ کردن جداول */
        table.dataTable.display,
        table.dataTable.compact {
            direction: rtl;
        }

        table.dataTable.display thead th,
        table.dataTable.display tfoot th,
        table.dataTable.display td,
        table.dataTable.display tfoot td {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h3 class="mb-3">لیست کاربران</h3>

        <table id="usersTable" class="table table-striped table-bordered nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>شناسه</th>
                    <th>نام</th>
                    <th>ایمیل</th>
                    <th>تلفن</th>
                    <th>تاریخ ایجاد</th>
                </tr>
            </thead>
            <tbody>
                <!-- داده‌ها از طریق AJAX بارگذاری می‌شوند -->
            </tbody>
        </table>
    </div>

    <!-- jQuery -->
    <!-- *** این لینک را با مسیر فایل jQuery خود جایگزین کنید *** -->
    <script src="../itheme/panel/js/jquery-3.6.4.min.js"></script>

    <!-- Bootstrap JS -->
    <!-- *** این لینک را با مسیر فایل JS بوت استرپ خود جایگزین کنید *** -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <!-- *** این لینک‌ها را با مسیر فایل‌های JS دیتاتبل خود جایگزین کنید *** -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <!-- Buttons JS -->
    <!-- *** این لینک‌ها را با مسیر فایل‌های JS دکمه‌های دیتاتبل خود جایگزین کنید *** -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- Dependencies for Excel export -->
    <!-- *** این لینک را با مسیر فایل JSZip خود جایگزین کنید *** -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#usersTable').DataTable({
                "processing": true, // نمایش پیام "در حال پردازش..."
                "serverSide": true, // فعال کردن پردازش سمت سرور
                "ajax": {
                    "url": "fetch_users.php", // مسیر فایل PHP شما برای دریافت داده‌ها
                    "type": "POST" // یا "GET" بسته به تنظیمات سرور شما
                },
                "columns": [
                    { "data": "id" },
                    { "data": "name" },
                    { "data": "email" },
                    { "data": "phone" },
                    { "data": "created_at" }
                ],
                "pageLength": 10, // تعداد ردیف‌های پیش‌فرض در هر صفحه
                "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]], // گزینه‌های تغییر تعداد ردیف
                "dom": 'Bfrtip', // مشخص کردن محل نمایش المان‌ها (B=Buttons, f=filtering input, r=processing display, t=table, i=information, p=pagination)
                "buttons": [
                    'copyHtml5', // دکمه کپی کردن
                    'excelHtml5', // دکمه خروجی اکسل
                    'print' // دکمه چاپ
                ],
                "language": { // تنظیمات زبان فارسی برای DataTables
                    "processing": "در حال پردازش...",
                    "lengthMenu": "نمایش _MENU_ رکورد در صفحه",
                    "zeroRecords": "رکوردی یافت نشد.",
                    "info": "نمایش صفحه _PAGE_ از _PAGES_",
                    "infoEmpty": "هیچ رکوردی موجود نیست",
                    "infoFiltered": "(فیلتر شده از _MAX_ کل رکورد)",
                    "search": "جستجو:",
                    "paginate": {
                        "first": "اولین",
                        "last": "آخرین",
                        "next": "بعدی",
                        "previous": "قبلی"
                    }
                }
            });
        });
    </script>

</body>

</html>