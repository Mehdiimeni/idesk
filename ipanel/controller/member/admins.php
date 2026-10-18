<?php
///ipanel/controller/member/admins.php

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$rbacClass = new RBAC($db);
$textToolsClass = TextTools::getInstance();

$canAddAdmin = (bool) $rbacClass->checkPermissionOperationByName('add_operation');
$canEditAdmin = (bool) $rbacClass->checkPermissionOperationByName('edit_operation');
$canActivateAdmin = (bool) $rbacClass->checkPermissionOperationByName('active_operation');
$canInactivateAdmin = (bool) $rbacClass->checkPermissionOperationByName('inactive_operation');

if (empty($_SESSION['admins_management_csrf'])) {
    $_SESSION['admins_management_csrf'] = bin2hex(random_bytes(32));
}
$adminsManagementCsrf = $_SESSION['admins_management_csrf'];

$internalCompanyId = (int) ($_SESSION['company_id'] ?? 0);
$internalCompany = null;
$internalUnits = [];
$internalRbacs = [];
$internalAdmins = [];
$adminChartUnits = [];
$unassignedAdmins = [];

$adminStats = [
    'total' => 0,
    'active' => 0,
    'inactive' => 0,
    'managers' => 0,
    'experts' => 0,
    'units' => 0,
    'unassigned' => 0,
];

function internalAdminsFetchAll(mysqli_result $result): array
{
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function internalAdminsRoleKind(?string $rbacName): string
{
    $name = trim((string) $rbacName);
    if ($name === '') {
        return 'UNASSIGNED';
    }

    if (mb_stripos($name, 'کارشناس') !== false || stripos($name, 'expert') !== false) {
        return 'EXPERT';
    }

    // In the internal IPanel structure, every non-expert RBAC is treated as
    // manager/responsible level. The exact access name remains visible beside it.
    return 'MANAGER';
}

if ($internalCompanyId > 0) {
    $stmt = $db->prepare("SELECT id, company_name, company_phone, activity_id FROM company_profiles WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $internalCompanyId);
    $stmt->execute();
    $internalCompany = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
}

if (!$internalCompany) {
    $internalCompany = [
        'id' => $internalCompanyId,
        'company_name' => _lang['internal_admins_company_unknown'],
        'company_phone' => null,
        'activity_id' => null,
    ];
}

if ($internalCompanyId > 0) {
    $stmt = $db->prepare("SELECT id, unit_name, unit_type FROM units WHERE company_id = ? ORDER BY unit_name ASC, id ASC");
    $stmt->bind_param('i', $internalCompanyId);
    $stmt->execute();
    $internalUnits = internalAdminsFetchAll($stmt->get_result());
    $stmt->close();

    $stmt = $db->prepare("
        SELECT
            r.id,
            r.rbac_name,
            r.manager_id,
            r.all_rbac,
            r.unit_id,
            parent.rbac_name AS parent_rbac_name,
            parent.all_rbac AS parent_all_rbac
        FROM rbac r
        LEFT JOIN rbac parent ON parent.id = r.manager_id
        WHERE r.company_id = ?
        ORDER BY r.unit_id ASC, r.rbac_name ASC, r.id ASC
    ");
    $stmt->bind_param('i', $internalCompanyId);
    $stmt->execute();
    $internalRbacs = internalAdminsFetchAll($stmt->get_result());
    $stmt->close();

    $stmt = $db->prepare("
        SELECT
            a.id,
            a.name,
            a.email,
            a.mobile,
            a.status,
            a.role AS legacy_role,
            a.unit_id,
            a.rbac_id,
            a.creation_date,
            a.last_updated_date,
            un.unit_name,
            un.unit_type,
            r.rbac_name,
            r.manager_id AS rbac_manager_id,
            r.all_rbac,
            parent.rbac_name AS parent_rbac_name,
            parent.all_rbac AS parent_all_rbac
        FROM admins a
        INNER JOIN units un ON un.id = a.unit_id
        LEFT JOIN rbac r ON r.id = a.rbac_id
        LEFT JOIN rbac parent ON parent.id = r.manager_id
        WHERE un.company_id = ?
        ORDER BY
            un.unit_name ASC,
            CASE WHEN r.rbac_name LIKE '%کارشناس%' THEN 2 ELSE 1 END,
            CASE WHEN a.status = 'Active' THEN 0 ELSE 1 END,
            a.name ASC,
            a.id ASC
    ");
    $stmt->bind_param('i', $internalCompanyId);
    $stmt->execute();
    $internalAdmins = internalAdminsFetchAll($stmt->get_result());
    $stmt->close();
}

$rbacsByUnit = [];
foreach ($internalRbacs as &$rbacRow) {
    $rbacRow['org_role'] = internalAdminsRoleKind($rbacRow['rbac_name'] ?? null);
    $unitId = (int) ($rbacRow['unit_id'] ?? 0);
    if (!isset($rbacsByUnit[$unitId])) {
        $rbacsByUnit[$unitId] = [];
    }
    $rbacsByUnit[$unitId][] = $rbacRow;
}
unset($rbacRow);

foreach ($internalUnits as $unit) {
    $unitId = (int) $unit['id'];
    $adminChartUnits[$unitId] = [
        'unit' => $unit,
        'managers' => [],
        'experts' => [],
        'unassigned' => [],
        'active_count' => 0,
        'inactive_count' => 0,
    ];
}

foreach ($internalAdmins as &$adminRow) {
    $adminRow['org_role'] = internalAdminsRoleKind($adminRow['rbac_name'] ?? null);
    $adminStats['total']++;

    if (($adminRow['status'] ?? '') === 'Active') {
        $adminStats['active']++;
    } else {
        $adminStats['inactive']++;
    }

    if ($adminRow['org_role'] === 'MANAGER') {
        $adminStats['managers']++;
    } elseif ($adminRow['org_role'] === 'EXPERT') {
        $adminStats['experts']++;
    } else {
        $adminStats['unassigned']++;
    }

    $unitId = (int) ($adminRow['unit_id'] ?? 0);
    if (!isset($adminChartUnits[$unitId])) {
        $unassignedAdmins[] = $adminRow;
        continue;
    }

    if (($adminRow['status'] ?? '') === 'Active') {
        $adminChartUnits[$unitId]['active_count']++;
    } else {
        $adminChartUnits[$unitId]['inactive_count']++;
    }

    if ($adminRow['org_role'] === 'MANAGER') {
        $adminChartUnits[$unitId]['managers'][] = $adminRow;
    } elseif ($adminRow['org_role'] === 'EXPERT') {
        $adminChartUnits[$unitId]['experts'][] = $adminRow;
    } else {
        $adminChartUnits[$unitId]['unassigned'][] = $adminRow;
    }
}
unset($adminRow);

$adminStats['units'] = count($internalUnits);
