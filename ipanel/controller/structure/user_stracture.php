<?php
///ipanel/controller/structure/user_stracture.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_stracture_csrf'])) {
    $_SESSION['user_stracture_csrf'] = bin2hex(random_bytes(32));
}
$userStractureCsrf = $_SESSION['user_stracture_csrf'];

$canAddUserStracture = (bool) $rbacClass->checkPermissionOperationByName('add_operation');
$canEditUserStracture = (bool) $rbacClass->checkPermissionOperationByName('edit_operation');
$canDeleteUserStracture = (bool) $rbacClass->checkPermissionOperationByName('delete_operation');

$userStractureSystemFilter = isset($_GET['system']) ? strtolower(trim((string) $_GET['system'])) : 'all';
if (!in_array($userStractureSystemFilter, ['all', 'ipanel', 'iweb'], true)) {
    $userStractureSystemFilter = 'all';
}

function userStractureFetchRoleUsage(mysqli $db, string $table): array
{
    $usage = [];
    if (!in_array($table, ['admins', 'users'], true)) {
        return $usage;
    }

    $result = $db->query("SELECT rbac_id, COUNT(*) AS cnt FROM {$table} WHERE rbac_id IS NOT NULL AND rbac_id > 0 GROUP BY rbac_id");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $usage[(int) $row['rbac_id']] = (int) $row['cnt'];
        }
        $result->free();
    }
    return $usage;
}

function userStractureDecodeUnits(?string $serialized): array
{
    if ($serialized === null || $serialized === '') {
        return [];
    }

    $decoded = @unserialize($serialized, ['allowed_classes' => false]);
    if (!is_array($decoded)) {
        return [];
    }

    $ids = [];
    foreach ($decoded as $value) {
        $id = (int) $value;
        if ($id > 0) {
            $ids[$id] = true;
        }
    }
    $ids = array_keys($ids);
    sort($ids, SORT_NUMERIC);
    return $ids;
}

// Company of the currently logged-in IPanel admin. This is used only to
// identify internal/unassigned RBAC roles safely without hard-coding INTEK ID.
$currentAdminCompanyId = 0;
$currentAdminId = (int) ($_SESSION['admin_id'] ?? 0);
if ($currentAdminId > 0) {
    $stmt = $db->prepare("SELECT u.company_id FROM admins a INNER JOIN units u ON u.id = a.unit_id WHERE a.id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $currentAdminId);
        $stmt->execute();
        $adminCompanyRow = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $currentAdminCompanyId = (int) ($adminCompanyRow['company_id'] ?? 0);
    }
}

$ipanelRoleUsage = userStractureFetchRoleUsage($db, 'admins');
$iwebRoleUsage = userStractureFetchRoleUsage($db, 'users');

$unitOptions = [];
$unitById = [];
$unitResult = $db->query("\n    SELECT\n        u.id,\n        u.unit_name,\n        u.unit_type,\n        u.company_id,\n        cp.company_name,\n        cp.activity_id,\n        a.activity_name\n    FROM units u\n    INNER JOIN company_profiles cp ON cp.id = u.company_id\n    LEFT JOIN activities a ON a.id = cp.activity_id\n    ORDER BY cp.company_name ASC, u.unit_name ASC, u.id ASC\n");
if ($unitResult) {
    while ($row = $unitResult->fetch_assoc()) {
        $row['id'] = (int) $row['id'];
        $row['company_id'] = (int) $row['company_id'];
        $row['activity_id'] = (int) ($row['activity_id'] ?? 0);
        $unitOptions[] = $row;
        $unitById[$row['id']] = $row;
    }
    $unitResult->free();
}

// Merge all legacy rows per RBAC. permissions_stracture has no UNIQUE(rbac_id)
// and the current DB contains legacy duplicates. We never assume one row here.
$permissionMap = [];
$permissionResult = $db->query("SELECT id, rbac_id, stracture, last_updated_date FROM permissions_stracture ORDER BY rbac_id ASC, id ASC");
if ($permissionResult) {
    while ($row = $permissionResult->fetch_assoc()) {
        $roleId = (int) $row['rbac_id'];
        if (!isset($permissionMap[$roleId])) {
            $permissionMap[$roleId] = [
                'row_count' => 0,
                'row_ids' => [],
                'selected' => [],
                'last_updated_date' => null,
            ];
        }
        $permissionMap[$roleId]['row_count']++;
        $permissionMap[$roleId]['row_ids'][] = (int) $row['id'];
        foreach (userStractureDecodeUnits((string) $row['stracture']) as $unitId) {
            $permissionMap[$roleId]['selected'][$unitId] = true;
        }
        $updated = (string) ($row['last_updated_date'] ?? '');
        if ($updated !== '' && ($permissionMap[$roleId]['last_updated_date'] === null || $updated > $permissionMap[$roleId]['last_updated_date'])) {
            $permissionMap[$roleId]['last_updated_date'] = $updated;
        }
    }
    $permissionResult->free();
}

$roles = [];
$roleResult = $db->query("\n    SELECT\n        r.*,\n        cp.company_name,\n        u.unit_name,\n        u.unit_type,\n        a.activity_name\n    FROM rbac r\n    LEFT JOIN company_profiles cp ON cp.id = r.company_id\n    LEFT JOIN units u ON u.id = r.unit_id\n    LEFT JOIN activities a ON a.id = r.activity_id\n    ORDER BY cp.company_name ASC, u.unit_name ASC, r.rbac_name ASC, r.id ASC\n");
if ($roleResult) {
    while ($role = $roleResult->fetch_assoc()) {
        $roleId = (int) $role['id'];
        $companyId = (int) ($role['company_id'] ?? 0);
        $homeUnitId = (int) ($role['unit_id'] ?? 0);
        $adminCount = $ipanelRoleUsage[$roleId] ?? 0;
        $userCount = $iwebRoleUsage[$roleId] ?? 0;
        $protected = ((int) ($role['all_rbac'] ?? 0) === 1);

        if ($adminCount > 0 && $userCount > 0) {
            $systemScope = 'shared';
        } elseif ($adminCount > 0) {
            $systemScope = 'ipanel';
        } elseif ($userCount > 0) {
            $systemScope = 'iweb';
        } else {
            $systemScope = 'unassigned';
        }

        // If a RBAC is used by IWeb users, cross-company structure access is
        // never allowed here. Internal iPanel roles (or unassigned roles owned
        // by the current internal company) may receive multi-company scope.
        $canUseCrossCompany = false;
        if (!$protected && $userCount === 0) {
            if ($adminCount > 0 || ($currentAdminCompanyId > 0 && $companyId === $currentAdminCompanyId)) {
                $canUseCrossCompany = true;
            }
        }

        $allowedUnitIds = [];
        if (!$protected) {
            foreach ($unitOptions as $unit) {
                if ($canUseCrossCompany || ((int) $unit['company_id'] === $companyId && $companyId > 0)) {
                    $allowedUnitIds[] = (int) $unit['id'];
                }
            }
        }

        $permission = $permissionMap[$roleId] ?? [
            'row_count' => 0,
            'row_ids' => [],
            'selected' => [],
            'last_updated_date' => null,
        ];
        $selectedUnitIds = array_keys($permission['selected']);
        sort($selectedUnitIds, SORT_NUMERIC);

        $invalidSelected = [];
        if (!$protected) {
            $allowedSet = array_fill_keys($allowedUnitIds, true);
            foreach ($selectedUnitIds as $unitId) {
                if (!isset($allowedSet[$unitId]) || !isset($unitById[$unitId])) {
                    $invalidSelected[] = $unitId;
                }
            }
        }

        $selectedCompanies = [];
        foreach ($selectedUnitIds as $unitId) {
            if (isset($unitById[$unitId])) {
                $selectedCompanies[(int) $unitById[$unitId]['company_id']] = true;
            }
        }

        $lastUpdatedDisplay = '-';
        if (!empty($permission['last_updated_date'])) {
            try {
                $dateConverter = new DateConverter($permission['last_updated_date'], $config->getNowLanguage('a'));
                $lastUpdatedDisplay = $dateConverter->convertToShamsi();
            } catch (Throwable $e) {
                $lastUpdatedDisplay = (string) $permission['last_updated_date'];
            }
        }

        $role['id'] = $roleId;
        $role['company_id'] = $companyId;
        $role['unit_id'] = $homeUnitId;
        $role['ipanel_member_count'] = $adminCount;
        $role['iweb_member_count'] = $userCount;
        $role['system_scope'] = $systemScope;
        $role['is_protected'] = $protected;
        $role['can_cross_company'] = $canUseCrossCompany;
        $role['structure_row_count'] = (int) $permission['row_count'];
        $role['structure_configured'] = ((int) $permission['row_count'] > 0);
        $role['selected_unit_ids'] = $selectedUnitIds;
        $role['selected_unit_count'] = count($selectedUnitIds);
        $role['selected_company_count'] = count($selectedCompanies);
        $role['invalid_selected_unit_ids'] = $invalidSelected;
        $role['has_scope_issue'] = !empty($invalidSelected);
        $role['has_duplicate_rows'] = ((int) $permission['row_count'] > 1);
        $role['last_updated_display'] = $lastUpdatedDisplay;
        $role['last_updated_date'] = $permission['last_updated_date'];
        $role['allowed_unit_ids'] = $allowedUnitIds;

        $visible = true;
        if ($userStractureSystemFilter === 'ipanel') {
            $visible = ($adminCount > 0);
        } elseif ($userStractureSystemFilter === 'iweb') {
            $visible = ($userCount > 0);
        }

        if ($visible) {
            $roles[] = $role;
        }
    }
    $roleResult->free();
}

$userStractureStats = [
    'total_roles' => 0,
    'configured_roles' => 0,
    'multi_company_roles' => 0,
    'attention_roles' => 0,
];
foreach ($roles as $role) {
    if ($role['is_protected']) {
        continue;
    }
    $userStractureStats['total_roles']++;
    if ($role['structure_configured']) {
        $userStractureStats['configured_roles']++;
    }
    if ((int) $role['selected_company_count'] > 1) {
        $userStractureStats['multi_company_roles']++;
    }
    if ($role['has_scope_issue'] || $role['has_duplicate_rows']) {
        $userStractureStats['attention_roles']++;
    }
}

$userStractureRoleData = [];
foreach ($roles as $role) {
    $userStractureRoleData[(int) $role['id']] = [
        'id' => (int) $role['id'],
        'rbac_name' => (string) ($role['rbac_name'] ?? ''),
        'company_id' => (int) ($role['company_id'] ?? 0),
        'company_name' => (string) ($role['company_name'] ?? ''),
        'unit_id' => (int) ($role['unit_id'] ?? 0),
        'unit_name' => (string) ($role['unit_name'] ?? ''),
        'activity_name' => (string) ($role['activity_name'] ?? ''),
        'system_scope' => (string) $role['system_scope'],
        'is_protected' => (bool) $role['is_protected'],
        'can_cross_company' => (bool) $role['can_cross_company'],
        'configured' => (bool) $role['structure_configured'],
        'selected_unit_ids' => array_values($role['selected_unit_ids']),
        'allowed_unit_ids' => array_values($role['allowed_unit_ids']),
        'invalid_selected_unit_ids' => array_values($role['invalid_selected_unit_ids']),
        'row_count' => (int) $role['structure_row_count'],
    ];
}
