<?php
///template/global/page_js.php
$arrayComponents = $_SESSION['arrayComponents'] ?? [];
$basePath = '../itheme/panel/';
?>

<!-- Vendor js - Critical for core functionality -->
<script src="<?php echo $basePath; ?>js/vendor.min.js"></script>

<?php
// گروه‌بندی کامپوننت‌ها برای لود بهینه
$components = [
    'select2' => function () use ($basePath) {
        echo '<script src="' . $basePath . 'vendor/select2/js/select2.min.js"></script>' . PHP_EOL;
        echo '<script src="' . $basePath . 'js/jquery.multi-select.js"></script>' . PHP_EOL;
    },

    'range' => function () use ($basePath) {
        echo '<script src="' . $basePath . 'vendor/daterangepicker/moment.min.js"></script>' . PHP_EOL;
        echo '<script src="' . $basePath . 'vendor/daterangepicker/daterangepicker.js"></script>' . PHP_EOL;
    },

    'flat' => function () use ($basePath) {
        echo '<script src="' . $basePath . 'vendor/flatpickr/flatpickr.min.js"></script>' . PHP_EOL;
        echo '<script src="' . $basePath . 'js/fa.js"></script>' . PHP_EOL;
    },

    'table' => function () use ($basePath) {
        echo '<!-- DataTable Components -->' . PHP_EOL;
        $tableLibs = [
            $basePath . 'vendor/datatables.net/js/jquery.dataTables.min.js',
            $basePath . 'vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js',
            $basePath . 'vendor/datatables.net-responsive/js/dataTables.responsive.min.js',
            $basePath . 'vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js',
            $basePath . 'vendor/datatables.net-buttons/js/dataTables.buttons.min.js',
            $basePath . 'vendor/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js',
            $basePath . 'vendor/datatables.net-buttons/js/buttons.html5.min.js',
            $basePath . 'vendor/datatables.net-buttons/js/buttons.print.min.js',
            $basePath . 'vendor/datatables.net-buttons/js/jszip.min.js'
        ];

        foreach ($tableLibs as $lib) {
            echo '<script src="' . $lib . '"></script>' . PHP_EOL;
        }
        echo '<script src="' . $basePath . 'js/iw/datatable.js"></script>' . PHP_EOL;
    },

    'table_limit' => function () use ($basePath) {
        echo '<script src="' . $basePath . 'js/iw/datatable_limit.js"></script>' . PHP_EOL;
    },

    'editor' => function () use ($basePath) {
        echo '<script src="' . $basePath . 'vendor/simplemde/simplemde.min.js"></script>' . PHP_EOL;
        echo '<script src="' . $basePath . 'js/pages/demo.inbox.js"></script>' . PHP_EOL;
    },

    'calendar' => function () use ($basePath) {
        echo '<script src="' . $basePath . 'vendor/fullcalendar/main.min.js"></script>' . PHP_EOL;
        echo '<script src="' . $basePath . 'js/iw/event.js"></script>' . PHP_EOL;
    },

    'date' => function () use ($basePath) {
        echo '<script src="' . $basePath . 'vendor/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>' . PHP_EOL;
        echo '<script src="' . $basePath . 'js/iw/date.js"></script>' . PHP_EOL;
    },

    'kanban_board' => function () use ($basePath) {
        echo '<script src="' . $basePath . 'vendor/dragula/dragula.min.js"></script>' . PHP_EOL;
        echo '<script src="' . $basePath . 'js/ui/component.dragula.js"></script>' . PHP_EOL;
        echo '<script src="' . $basePath . 'js/iw/drag.js"></script>' . PHP_EOL;
    },

    'mask' => function () use ($basePath) {
        echo '<script src="' . $basePath . 'vendor/jquery-mask-plugin/jquery.mask.min.js"></script>' . PHP_EOL;
        echo '<script src="' . $basePath . 'js/iw/request_update.js"></script>' . PHP_EOL;
    },

    'confirm' => function () use ($basePath) {
        echo '<script src="' . $basePath . 'js/iw/request_confirmation.js"></script>' . PHP_EOL;
    },

    'tree' => function () use ($basePath) {
        echo '<script src="' . $basePath . 'vendor/jstree/jstree.min.js"></script>' . PHP_EOL;
        echo '<script src="' . $basePath . 'js/iw/tree.js"></script>' . PHP_EOL;
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
<script src="<?php echo $basePath; ?>js/app.min.js" defer></script>

<?php
// کامپوننت‌های سفارشی
$customComponents = [
    'cookie_list' => $basePath . 'js/iw/cookie_list.js',
    'operation' => $basePath . 'js/iw/operation.js',
    'main' => $basePath . 'js/iw/main.js',
    'lang' => $basePath . 'js/iw/lang.js',
    'status' => $basePath . 'js/iw/status.js',
    'multi' => $basePath . 'js/iw/multi.js',
    'todo' => $basePath . 'js/iw/todo.js',
    'chat' => $basePath . 'js/iw/chat.js'
];

foreach ($customComponents as $component => $path) {
    if (in_array($component, $arrayComponents)) {
        // Use defer for non-critical scripts to improve page load time
        echo '<script src="' . $path . '" defer></script>' . PHP_EOL;
    }
}
?>

<!-- بارگذاری پویا: اگر صفحه‌ای عنصر با کلاس persianDatepicker داشت، CSS/JS مرتبط را بارگذاری کن -->
<script>
    (function () {
        try {
            if (document.querySelector('.persianDatepicker')) {
                var base = '<?php echo $basePath; ?>';

                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = base + 'css/persian-datepicker.css';
                document.head.appendChild(link);

                var script = document.createElement('script');
                script.src = base + 'js/persian-datepicker.js';
                script.defer = false;
                document.body.appendChild(script);
            }
        } catch (e) {
            console.error('Error loading persian datepicker assets', e);
        }
    })();
</script>

<!-- اسکریپت مدیریت خطاها و بهینه‌سازی 
<script>
    // مدیریت خطاهای全局
    window.addEventListener('error', function (e) {
        // مدیریت خطاهای DataTable FixedHeader
        if (e.message && (e.message.includes('FixedHeader') || e.message.includes('dataTable'))) {
            if (typeof $.fn.dataTable !== 'undefined') {
                $.fn.dataTable.ext.errMode = 'none';
            }
        }
    });

    // استفاده از فونت‌های سیستم (هیچ منبع خارجی نیست)
    document.addEventListener('DOMContentLoaded', function () {
        // فونت‌های سیستم استفاده می‌شوند - بدون نیاز به باری خارجی
        document.documentElement.style.fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'Vazir', 'B Yekan'";
        document.body.style.fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'Vazir', 'B Yekan'";

        // مدیریت خطای FixedHeader با تاخیر
        setTimeout(function () {
            if (typeof $.fn.dataTable.FixedHeader === 'undefined' && typeof $.fn.dataTable !== 'undefined') {
                console.warn('FixedHeader not available, creating fallback...');
                $.fn.dataTable.FixedHeader = function (table) {
                    console.log('Using FixedHeader fallback');
                    return {
                        destroy: function () { }
                    };
                };
            }
        }, 1000);
    });

    // Fallback برای jQuery در صورت نیاز
    if (typeof jQuery === 'undefined') {
        console.error('jQuery not loaded!');
    }
</script>
-->




</body>

</html>