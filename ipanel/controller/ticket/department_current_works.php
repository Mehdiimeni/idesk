<?php
///controller/ticket/department_current_works.php

use ipanel\model\DepartmentWorkModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$departmentWorkModel = new DepartmentWorkModel($db);
$rbacClass = new RBAC($db);
$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));

$part_name = 'department_current_works';
$adminId = (int) ($_SESSION['admin_id'] ?? 0);

if ($adminId <= 0) {
    throw new \RuntimeException('Unauthorized');
}


$canManageDevelopment = (bool) $rbacClass->checkPermissionOperationByName('current_development_work_operation');
$canManageSupport = (bool) $rbacClass->checkPermissionOperationByName('current_support_work_operation');
$canViewAllDepartments = (bool) $rbacClass->checkPermissionOperationByName('current_work_management_view_operation');

$flash = [
    'ok' => $_SESSION['department_work_flash_ok'] ?? null,
    'err' => $_SESSION['department_work_flash_err'] ?? null,
];
unset($_SESSION['department_work_flash_ok'], $_SESSION['department_work_flash_err']);


function formatDepartmentWorkDuration(int $minutes): string
{
    if ($minutes <= 0) {
        return '0';
    }

    $days = intdiv($minutes, 1440);
    $hours = intdiv($minutes % 1440, 60);
    $remainingMinutes = $minutes % 60;

    $parts = [];

    if ($days > 0) {
        $parts[] = $days . ' ' . (_lang['day'] ?? 'روز');
    }

    if ($hours > 0) {
        $parts[] = $hours . ' ' . (_lang['hour'] ?? 'ساعت');
    }

    if ($days === 0 && $remainingMinutes > 0) {
        $parts[] = $remainingMinutes . ' ' . (_lang['minute'] ?? 'دقیقه');
    }

    return implode(' ', $parts);
}

try {
    $allSections = $departmentWorkModel->getActiveSections();

    $visibleSections = [];
    $manageableSectionIds = [];

    foreach ($allSections as $section) {
        $sectionId = (int) $section['id'];
        $sectionName = strtolower((string) $section['section_name']);

        $canManageSection =
            ($sectionName === 'development' && $canManageDevelopment) ||
            ($sectionName === 'support' && $canManageSupport);

        if ($canViewAllDepartments || $canManageSection) {
            $section['can_manage'] = $canManageSection;
            $visibleSections[] = $section;
        }

        if ($canManageSection) {
            $manageableSectionIds[] = $sectionId;
        }
    }

    if (empty($visibleSections)) {
        throw new \RuntimeException(_lang['operation_not_allowed'] ?? 'شما اجازه مشاهده این بخش را ندارید.');
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_department_work'])) {
            $sectionId = (int) ($_POST['section_id'] ?? 0);
            $subject = trim((string) ($_POST['subject'] ?? ''));
            $priority = trim((string) ($_POST['priority'] ?? 'normal'));
            $requestedBy = trim((string) ($_POST['requested_by'] ?? ''));
            $ticketNumber = trim((string) ($_POST['ticket_number'] ?? ''));
            $isPhoneRequest = isset($_POST['is_phone_request']) && $_POST['is_phone_request'] === '1';
            $callerName = trim((string) ($_POST['caller_name'] ?? ''));

            if (!in_array($sectionId, $manageableSectionIds, true)) {
                throw new \RuntimeException(_lang['operation_not_allowed'] ?? 'شما اجازه ثبت برای این واحد را ندارید.');
            }

            if ($subject === '') {
                throw new \RuntimeException(_lang['work_subject_required'] ?? 'وارد کردن موضوع کار الزامی است.');
            }

            if (!in_array($priority, ['normal', 'force'], true)) {
                throw new \RuntimeException(_lang['invalid_priority'] ?? 'درجه اهمیت معتبر نیست.');
            }

            if ($requestedBy === '') {
                throw new \RuntimeException(_lang['requested_by_required'] ?? 'نام درخواست‌کننده الزامی است.');
            }

            if ($isPhoneRequest && $callerName === '') {
                throw new \RuntimeException(_lang['caller_name_required'] ?? 'نام تماس‌گیرنده الزامی است.');
            }

            $ticketId = null;
            if ($ticketNumber !== '') {
                $ticket = $departmentWorkModel->getTicketByNumber($ticketNumber);
                if (!$ticket) {
                    throw new \RuntimeException(_lang['invalid_ticket_number'] ?? 'شماره تیکت معتبر نیست.');
                }
                $ticketId = (int) $ticket['id'];
            }

            $departmentWorkModel->addWorkItem(
                $sectionId,
                $subject,
                $priority,
                $requestedBy,
                $ticketId,
                $isPhoneRequest,
                $isPhoneRequest ? $callerName : null,
                $adminId
            );

            $_SESSION['department_work_flash_ok'] = _lang['work_added_successfully'] ?? 'کار با موفقیت ثبت شد.';
            header('Location: ./department_current_works?section_id=' . $sectionId);
            exit;
        }

        if (isset($_POST['complete_department_work'])) {
            $itemId = (int) ($_POST['work_item_id'] ?? 0);
            $sectionId = (int) ($_POST['section_id'] ?? 0);

            if ($itemId <= 0 || !in_array($sectionId, $manageableSectionIds, true)) {
                throw new \RuntimeException(_lang['operation_not_allowed'] ?? 'شما اجازه پایان این کار را ندارید.');
            }

            $completed = $departmentWorkModel->completeWorkItem($itemId, $sectionId, $adminId);

            if (!$completed) {
                throw new \RuntimeException(_lang['work_already_completed'] ?? 'این کار قبلاً پایان یافته یا معتبر نیست.');
            }

            $_SESSION['department_work_flash_ok'] = _lang['work_completed_successfully'] ?? 'کار با موفقیت پایان یافت.';
            header('Location: ./department_current_works?section_id=' . $sectionId);
            exit;
        }
    }

    $requestedSectionId = isset($_GET['section_id']) ? (int) $_GET['section_id'] : 0;
    $visibleSectionIds = array_map(static function ($section) {
        return (int) $section['id'];
    }, $visibleSections);

    $activeSectionId = in_array($requestedSectionId, $visibleSectionIds, true)
        ? $requestedSectionId
        : (int) $visibleSections[0]['id'];

    $archiveYear = isset($_GET['archive_year']) && $_GET['archive_year'] !== ''
        ? (int) $_GET['archive_year']
        : null;
    $archiveMonth = isset($_GET['archive_month']) && $_GET['archive_month'] !== ''
        ? (int) $_GET['archive_month']
        : null;

    if ($archiveYear !== null && ($archiveYear < 2000 || $archiveYear > 2200)) {
        $archiveYear = null;
    }

    if ($archiveMonth !== null && ($archiveMonth < 1 || $archiveMonth > 12)) {
        $archiveMonth = null;
    }

    $sectionData = [];

    foreach ($visibleSections as $section) {
        $sectionId = (int) $section['id'];

        $sectionData[$sectionId] = [
            'section' => $section,
            'current_works' => $departmentWorkModel->getCurrentWorks($sectionId),
            'archive_months' => $departmentWorkModel->getArchiveMonths($sectionId),
            'archived_works' => $departmentWorkModel->getArchivedWorks(
                $sectionId,
                $sectionId === $activeSectionId ? $archiveYear : null,
                $sectionId === $activeSectionId ? $archiveMonth : null
            ),
            'statistics' => $departmentWorkModel->getSectionStatistics($sectionId),
        ];
    }

} catch (\Throwable $e) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION['department_work_flash_err'] = $e->getMessage();
        header('Location: ./department_current_works');
        exit;
    }

    $flash['err'] = $e->getMessage();
    $visibleSections = $visibleSections ?? [];
    $sectionData = [];
    $activeSectionId = 0;
    $archiveYear = null;
    $archiveMonth = null;
}
