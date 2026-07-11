<?php
///template/global/page_css.php
$defaultUserDir = $_COOKIE['userLanguageDir'] ?? $userLanguageDir ?? 'ltr';
$arrayComponents = $_SESSION['arrayComponents'] ?? [];
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="./itheme/panel/css/css2.css" rel="stylesheet">

<!-- Preload Critical Resources -->
<link rel="preload" href="./itheme/panel/css/icons.min.css" as="style">
<link rel="preload" href="./itheme/panel/js/hyper-config.js" as="script">

<!-- CSS Components با ترتیب بهینه -->
<?php
$cssComponents = [
    'calendar' => [
        './itheme/panel/vendor/fullcalendar/main.min.css'
    ],

    'editor' => [
        './itheme/panel/vendor/simplemde/simplemde.min.css'
    ],

    'table' => [
        './itheme/panel/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        './itheme/panel/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css',
    ],

    'select2' => [
        './itheme/panel/vendor/select2/css/select2.min.css',
        './itheme/panel/css/multi-select.css'
    ],

    'date' => [
        './itheme/panel/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css',
        './itheme/panel/vendor/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
        './itheme/panel/vendor/flatpickr/flatpickr.min.css'
    ],

    'tree' => [
        './itheme/panel/vendor/jstree/themes/default/style.min.css'
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

<!-- Theme Config JS (در head برای جلوگیری از FOUC) -->
<script src="./itheme/panel/js/hyper-config.js"></script>

<!-- Main CSS Files -->
<link href="./itheme/panel/css/icons.min.css" rel="stylesheet" type="text/css" />

<?php if ($defaultUserDir === 'rtl'): ?>
    <link href="./itheme/panel/css/app-creative-rtl.min.css" rel="stylesheet" type="text/css" id="app-style" />
<?php else: ?>
    <link href="./itheme/panel/css/app-creative.min.css" rel="stylesheet" type="text/css" id="app-style" />
<?php endif; ?>

<!-- Chart JS (اگر نیاز باشد) -->
<?php if (in_array('chart', $arrayComponents)): ?>
    <script src="./itheme/panel/js/jquery-3.6.4.min.js" defer></script>
    <script src="./itheme/panel/js/chart.js" defer></script>
<?php endif; ?>

<!-- Use the Persian-capable font loaded from Google Fonts and fallbacks -->
<style>
    /* Use a Persian-optimized font family (Noto Sans Arabic) with fallbacks */
    body {
        font-family: 'Noto Sans Arabic', 'Vazirmatn', Tahoma, 'Helvetica Neue', Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
</style>
</head>