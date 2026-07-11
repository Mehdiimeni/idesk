<?php
///template/global/page_js.php
$arrayComponents = $_SESSION['arrayComponents'] ?? [];
?>

<!-- Vendor js -->
<script src="./itheme/panel/js/vendor.min.js"></script>

<?php
// گروه‌بندی کامپوننت‌ها برای لود بهینه
$components = [
    'select2' => function () {
        echo '<script src="./itheme/panel/vendor/select2/js/select2.min.js"></script>' . PHP_EOL;
        echo '<script src="./itheme/panel/js/jquery.multi-select.js"></script>' . PHP_EOL;
    },

    'flat' => function () {
        echo '<script src="./itheme/panel/vendor/flatpickr/flatpickr.min.js"></script>' . PHP_EOL;
    },

    'table' => function () {
        // DataTable libraries با ترتیب صحیح (FixedHeader حذف شد)
        echo '<!-- DataTable Components -->' . PHP_EOL;
        $tableLibs = [
            './itheme/panel/vendor/datatables.net/js/jquery.dataTables.min.js',
            './itheme/panel/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
            './itheme/panel/vendor/datatables.net-responsive/js/dataTables.responsive.min.js',
            './itheme/panel/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js',
            './itheme/panel/vendor/datatables.net-buttons/js/dataTables.buttons.min.js',
            './itheme/panel/vendor/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js',
            './itheme/panel/vendor/datatables.net-buttons/js/buttons.html5.min.js',
            './itheme/panel/vendor/datatables.net-buttons/js/buttons.print.min.js',
            './itheme/panel/vendor/datatables.net-buttons/js/jszip.min.js'
            // FixedHeader حذف شد تا خطا رفع شود
        ];

        foreach ($tableLibs as $lib) {
            echo "<script src=\"$lib\"></script>" . PHP_EOL;
        }
        echo '<script src="./itheme/panel/js/iw/datatable_user.js"></script>' . PHP_EOL;
    },

    'table_limit' => function () {
        echo '<script src="./itheme/panel/js/iw/datatable_limit.js"></script>' . PHP_EOL;
    },

    'editor' => function () {
        echo '<!-- Editor Components -->' . PHP_EOL;
        echo '<script src="./itheme/panel/vendor/simplemde/simplemde.min.js"></script>' . PHP_EOL;
        echo '<script src="./itheme/panel/js/pages/demo.inbox.js"></script>' . PHP_EOL;
    },

    'calendar' => function () {
        echo '<script src="./itheme/panel/vendor/fullcalendar/main.min.js"></script>' . PHP_EOL;
        echo '<script src="./itheme/panel/js/pages/demo.calendar.js"></script>' . PHP_EOL;
    },

    'date' => function () {
        echo '<script src="./itheme/panel/vendor/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>' . PHP_EOL;
        echo '<script src="./itheme/panel/js/pages/demo.timepicker.js"></script>' . PHP_EOL;
    },

    'kanban_board' => function () {
        echo '<script src="./itheme/panel/vendor/dragula/dragula.min.js"></script>' . PHP_EOL;
        echo '<script src="./itheme/panel/js/ui/component.dragula.js"></script>' . PHP_EOL;
        echo '<script src="./itheme/panel/js/iw/drag_user.js"></script>' . PHP_EOL;
    },

    'tree' => function () {
        echo '<script src="./itheme/panel/vendor/jstree/jstree.min.js"></script>' . PHP_EOL;
        echo '<script src="./itheme/panel/js/iw/tree.js"></script>' . PHP_EOL;
    }
];

// لود کامپوننت‌های اصلی
foreach ($components as $component => $callback) {
    if (in_array($component, $arrayComponents)) {
        $callback();
    }
}
?>

<!-- App js -->
<script src="./itheme/panel/js/app.min.js"></script>

<?php
// کامپوننت‌های سفارشی
$customComponents = [
    'cookie_list_user' => './itheme/panel/js/iw/cookie_list_user.js',
    'operation' => './itheme/panel/js/iw/operation.js',
    'main' => './itheme/panel/js/iw/main_user.js',
    'lang' => './itheme/panel/js/iw/lang.js',
    'lang_user' => './itheme/panel/js/iw/lang_user.js',
    'status_user' => './itheme/panel/js/iw/status_user.js',
    'multi' => './itheme/panel/js/iw/multi.js',
    'todo' => './itheme/panel/js/iw/todo.js',
    'chat' => './itheme/panel/js/iw/chat_user.js'
];

foreach ($customComponents as $component => $path) {
    if (in_array($component, $arrayComponents)) {
        echo "<script src=\"$path\"></script>" . PHP_EOL;
    }
}
?>

<script>
    // مدیریت خطاهای DataTable - نسخه بهبود یافته
    document.addEventListener('DOMContentLoaded', function () {
        // Fallback برای FixedHeader
        if (typeof $.fn.dataTable !== 'undefined' && typeof $.fn.dataTable.FixedHeader === 'undefined') {
            console.warn('FixedHeader not available, using basic DataTable configuration');
            $.fn.dataTable.ext.errMode = 'none'; // غیرفعال کردن نمایش خطاها
        }

        // استفاده از فونت‌های سیستم (هیچ منبع خارجی نیست)
        document.documentElement.style.fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'Vazir', 'B Yekan'";
        document.body.style.fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'Vazir', 'B Yekan'";
    });
</script>

</body>

</html>