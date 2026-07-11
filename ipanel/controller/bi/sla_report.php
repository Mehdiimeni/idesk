<?php
///controller/bi/sla_report.php
use ipanel\model\KPIModel;
use ipanel\model\StructureModel;


$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$rbacClass = new RBAC($db);

if ($rbacClass->checkPermissionOperationByName('statistics_operation')) {
    $permissionStatistics = true;
    $kpiModel = new KPIModel($db);
    $structureModel = new StructureModel($db);
    $encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));


    // دریافت پارامترهای GET
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    $days = isset($_GET['days']) ? (int) $_GET['days'] : null;
    $custom_date = isset($_GET['custom_date']) ? (bool) $_GET['custom_date'] : false;

    // دریافت تاریخ‌های شمسی برای نمایش (اختیاری)
    $start_date_jalali = isset($_GET['start_date_jalali']) ? $_GET['start_date_jalali'] : null;
    $end_date_jalali = isset($_GET['end_date_jalali']) ? $_GET['end_date_jalali'] : null;

    // اگر از دکمه‌های سریع استفاده شده
    if ($days !== null && !$custom_date) {
        if ($days === 0) {
            // امروز
            $start_date = date('Y-m-d') . ' 00:00:00';
            $end_date = date('Y-m-d') . ' 23:59:59';
        } elseif ($days === 1) {
            // دیروز
            $start_date = date('Y-m-d', strtotime('-1 day')) . ' 00:00:00';
            $end_date = date('Y-m-d', strtotime('-1 day')) . ' 23:59:59';
        } elseif ($days === 7) {
            // ۷ روز گذشته
            $start_date = date('Y-m-d', strtotime('-7 days')) . ' 00:00:00';
            $end_date = date('Y-m-d') . ' 23:59:59';
        } elseif ($days === 30) {
            // ۳۰ روز گذشته
            $start_date = date('Y-m-d', strtotime('-30 days')) . ' 00:00:00';
            $end_date = date('Y-m-d') . ' 23:59:59';
        }
    }
    // اگر از بازه دلخواه استفاده شده
    elseif ($start_date && $end_date && $custom_date) {
        // تاریخ‌ها در اینجا قبلاً در JavaScript به میلادی تبدیل شده‌اند
        // فقط نیاز به اضافه کردن زمان است
        $start_date = $start_date . ' 00:00:00';
        $end_date = $end_date . ' 23:59:59';
    } else {
        // حالت پیش‌فرض: امروز
        $start_date = date('Y-m-d') . ' 00:00:00';
        $end_date = date('Y-m-d') . ' 23:59:59';
    }




    $allHighPriorityViolatedTickets = $kpiModel->getHighPriorityViolatedTickets($start_date, $end_date);
} else {
    $permissionStatistics = false;
}