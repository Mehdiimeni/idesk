<?php
///template/global/page_css.php
$arrayComponents = $_SESSION['arrayComponents'] ?? [];
$adminLanguageDir = $_COOKIE['adminLanguageDir'] ?? 'ltr';
$basePath = '../itheme/panel/';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />

    <!-- Preload Critical Resources -->
    <link rel="preload" href="<?php echo $basePath; ?>css/icons.min.css" as="style">
    <link rel="preload" href="<?php echo $basePath; ?>js/hyper-config.js" as="script">

    <!-- CSS Components با ترتیب بهینه -->
    <?php
    $cssComponents = [
        'calendar' => [
            $basePath . 'vendor/fullcalendar/main.min.css'
        ],

        'editor' => [
            $basePath . 'vendor/simplemde/simplemde.min.css'
        ],

        'table' => [
            $basePath . 'vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
            $basePath . 'vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
            'https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css',
            'https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.bootstrap5.min.css'
        ],

        'select2' => [
            $basePath . 'vendor/select2/css/select2.min.css',
            $basePath . 'css/multi-select.css'
        ],

        'range' => [
            $basePath . 'vendor/daterangepicker/daterangepicker.css'
        ],

        'date' => [
            $basePath . 'vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css',
            $basePath . 'vendor/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
            $basePath . 'vendor/flatpickr/flatpickr.min.css'
        ],

        'tree' => [
            $basePath . 'vendor/jstree/themes/default/style.min.css'
        ]
    ];

    // لود کامپوننت‌های CSS
    foreach ($cssComponents as $component => $styles) {
        if (in_array($component, $arrayComponents)) {
            foreach ($styles as $style) {
                echo "<link href=\"$style\" rel=\"stylesheet\" type=\"text/css\" />" . PHP_EOL;
            }
        }
    }
    ?>

    <!-- Theme Config JS -->
    <script src="<?php echo $basePath; ?>js/hyper-config.js"></script>

    <!-- Main CSS Files -->
    <link href="<?php echo $basePath; ?>css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- App CSS بر اساس زبان -->
    <?php if ($adminLanguageDir === 'rtl'): ?>
        <link href="<?php echo $basePath; ?>css/app-creative-rtl.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <?php else: ?>
        <link href="<?php echo $basePath; ?>css/app-creative.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <?php endif; ?>

    <!-- Chart JS (اگر نیاز باشد) -->
    <?php if (in_array('chart', $arrayComponents)): ?>
        <script src="<?php echo $basePath; ?>js/jquery-3.6.4.min.js" defer></script>
        <script src="<?php echo $basePath; ?>js/chart.js" defer></script>
    <?php endif; ?>

    <!-- Inline CSS برای بهینه‌سازی -->
    <style>
        /* جلوگیری از FOIT (Flash of Invisible Text) */
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url('<?php echo $basePath; ?>fonts/Nunito-Regular.woff') format('woff');
        }

        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-weight: 600;
            font-display: swap;
            src: url('<?php echo $basePath; ?>fonts/Nunito-SemiBold.woff') format('woff');
        }

        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url('<?php echo $basePath; ?>fonts/Nunito-Bold.woff') format('woff');
        }

        /* Fallback font styling */
        body {
            font-family: 'Nunito', system-ui, -apple-system, sans-serif;
        }

        /* Loading state برای جداول */
        .dataTables_wrapper {
            position: relative;
        }

        .dataTables_processing {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>