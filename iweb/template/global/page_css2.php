<?php
///template/global/page_css.php
$defaultUserDir = isset($_COOKIE['userLanguageDir']) && !empty($_COOKIE['userLanguageDir']) 
    ? $_COOKIE['userLanguageDir'] 
    : $userLanguageDir;
$arrayComponents = $_SESSION['arrayComponents'];
?>

<?php if (in_array('calendar', $arrayComponents)) { ?>
    <!-- Fullcalendar css -->
    <link href="./itheme/panel/vendor/fullcalendar/main.min.css" rel="stylesheet" type="text/css" />
<?php } ?>
<?php if (in_array('editor', $arrayComponents)) { ?>
    <!-- SimpleMDE Editor css -->
    <link href="./itheme/panel/vendor/simplemde/simplemde.min.css" rel="stylesheet" type="text/css" />
<?php } ?>
<?php if (in_array('table', $arrayComponents)) { ?>
    <!-- table -->
    <link href="./itheme/panel/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="./itheme/panel/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<?php } ?>
<?php if (in_array('select2', $arrayComponents)) { ?>
    <link href="./itheme/panel/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="./itheme/panel/css/multi-select.css">
<?php } ?>

<?php if (in_array('date', $arrayComponents)) { ?>
    <link href="./itheme/panel/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="./itheme/panel/vendor/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="./itheme/panel/vendor/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />

<?php } ?>
<!-- Theme Config Js -->
<script src="./itheme/panel/js/hyper-config.js"></script>

<!-- App css -->
<?php if ($defaultUserDir == 'rtl') { ?>
    <link href="./itheme/panel/css/app-creative-rtl.min.css" rel="stylesheet" type="text/css" id="app-style" />
<?php } else { ?>
    <link href="./itheme/panel/css/app-creative.min.css" rel="stylesheet" type="text/css" id="app-style" />
<?php } ?>

<!-- Icons css -->
<link href="./itheme/panel/css/icons.min.css" rel="stylesheet" type="text/css" />

<?php if (in_array('tree', $arrayComponents)) { ?>
    <!-- tree -->
    <link href="./itheme/panel/vendor/jstree/themes/default/style.min.css" rel="stylesheet" type="text/css">
<?php } ?>

<?php if (in_array('chart', $arrayComponents)) { ?>
    <!-- chart -->
    <script src="./itheme/panel/js/jquery-3.6.4.min.js"></script>
    <script src="./itheme/panel/js/chart.js"></script>
<?php } ?>

<?php if (in_array('editor', $arrayComponents)) { ?>
<link href="./itheme/panel/vendor/simplemde/simplemde.min.css" rel="stylesheet" type="text/css" />  
<?php } ?>

</head>