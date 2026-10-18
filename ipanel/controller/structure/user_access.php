<?php
///ipanel/controller/structure/user_access.php
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

if (empty($_SESSION['user_access_csrf'])) {
    $_SESSION['user_access_csrf'] = bin2hex(random_bytes(32));
}
$userAccessCsrf = $_SESSION['user_access_csrf'];

$canAddAccessRole = (bool) $rbacClass->checkPermissionOperationByName('add_operation');
$canEditAccessRole = (bool) $rbacClass->checkPermissionOperationByName('edit_operation');
$canDeleteAccessRole = (bool) $rbacClass->checkPermissionOperationByName('delete_operation');

$accessSystemFilter = isset($_GET['system']) ? strtolower(trim((string) $_GET['system'])) : 'all';
if (!in_array($accessSystemFilter, ['all', 'ipanel', 'iweb'], true)) {
    $accessSystemFilter = 'all';
}

function accessFetchAll($result)
{
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        if (method_exists($result, 'free')) {
            $result->free();
        }
    }
    return $rows;
}

function accessFetchRoleUsage($db, $table)
{
    $usage = [];
    if (!in_array($table, ['admins', 'users'], true)) {
        return $usage;
    }

    $result = $db->query("SELECT rbac_id, COUNT(*) AS member_count FROM {$table} WHERE rbac_id IS NOT NULL GROUP BY rbac_id");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $usage[(int) $row['rbac_id']] = (int) $row['member_count'];
        }
        $result->free();
    }
    return $usage;
}

/**
 * Read-only dependency map for safe-delete/structural-lock indicators.
 * Covers every BASE TABLE column ending in rbac_id, plus rbac.manager_id children.
 */
function accessFetchRoleDependencies($db)
{
    $counts = [];

    $columns = $db->query("\n        SELECT c.TABLE_NAME, c.COLUMN_NAME\n        FROM information_schema.COLUMNS c\n        INNER JOIN information_schema.TABLES t\n            ON t.TABLE_SCHEMA = c.TABLE_SCHEMA\n           AND t.TABLE_NAME = c.TABLE_NAME\n        WHERE c.TABLE_SCHEMA = DATABASE()\n          AND t.TABLE_TYPE = 'BASE TABLE'\n          AND c.TABLE_NAME <> 'rbac'\n          AND c.COLUMN_NAME LIKE '%rbac_id'\n        ORDER BY c.TABLE_NAME, c.COLUMN_NAME\n    ");

    if ($columns) {
        while ($col = $columns->fetch_assoc()) {
            $table = (string) $col['TABLE_NAME'];
            $column = (string) $col['COLUMN_NAME'];
            if (!preg_match('/^[A-Za-z0-9_]+$/', $table) || !preg_match('/^[A-Za-z0-9_]+$/', $column)) {
                continue;
            }

            $result = $db->query("SELECT `{$column}` AS role_id, COUNT(*) AS cnt FROM `{$table}` WHERE `{$column}` IS NOT NULL AND `{$column}` > 0 GROUP BY `{$column}`");
            if (!$result) {
                continue;
            }
            while ($row = $result->fetch_assoc()) {
                $roleId = (int) ($row['role_id'] ?? 0);
                if ($roleId > 0) {
                    $counts[$roleId] = ($counts[$roleId] ?? 0) + (int) ($row['cnt'] ?? 0);
                }
            }
            $result->free();
        }
        $columns->free();
    }

    $children = $db->query("SELECT manager_id AS role_id, COUNT(*) AS cnt FROM rbac WHERE manager_id > 0 GROUP BY manager_id");
    if ($children) {
        while ($row = $children->fetch_assoc()) {
            $roleId = (int) ($row['role_id'] ?? 0);
            if ($roleId > 0) {
                $counts[$roleId] = ($counts[$roleId] ?? 0) + (int) ($row['cnt'] ?? 0);
            }
        }
        $children->free();
    }

    return $counts;
}

$allRoles = accessFetchAll($structureModel->getRBAC());
$tags = accessFetchAll($structureModel->getTags());

$unitOptions = [];
$unitResult = $db->query("\n    SELECT\n        u.id,\n        u.unit_name,\n        u.company_id,\n        cp.company_name,\n        cp.activity_id,\n        a.activity_name\n    FROM units u\n    INNER JOIN company_profiles cp ON cp.id = u.company_id\n    LEFT JOIN activities a ON a.id = cp.activity_id\n    ORDER BY cp.company_name ASC, u.unit_name ASC, u.id ASC\n");
if ($unitResult) {
    while ($row = $unitResult->fetch_assoc()) {
        $unitOptions[] = $row;
    }
    $unitResult->free();
}

$roleById = [];
foreach ($allRoles as $role) {
    $roleById[(int) $role['id']] = $role;
}

$ipanelRoleUsage = accessFetchRoleUsage($db, 'admins');
$iwebRoleUsage = accessFetchRoleUsage($db, 'users');
$dependencyCounts = accessFetchRoleDependencies($db);

$childRoleCount = [];
foreach ($allRoles as $role) {
    $managerId = (int) ($role['manager_id'] ?? 0);
    if ($managerId > 0) {
        $childRoleCount[$managerId] = ($childRoleCount[$managerId] ?? 0) + 1;
    }
}

$accessStats = [
    'total_roles' => count($allRoles),
    'ipanel_roles' => 0,
    'iweb_roles' => 0,
    'unassigned_roles' => 0,
];

$accessRoles = [];
$accessRoleEditData = [];

foreach ($allRoles as $role) {
    $roleId = (int) $role['id'];
    $adminCount = $ipanelRoleUsage[$roleId] ?? 0;
    $userCount = $iwebRoleUsage[$roleId] ?? 0;

    $role['ipanel_member_count'] = $adminCount;
    $role['iweb_member_count'] = $userCount;
    $role['child_role_count'] = $childRoleCount[$roleId] ?? 0;
    $role['dependency_count'] = $dependencyCounts[$roleId] ?? 0;
    $role['is_protected'] = ((int) ($role['all_rbac'] ?? 0) === 1);
    $role['unit_change_locked'] = !$role['is_protected'] && ((int) ($role['unit_id'] ?? 0) > 0) && ((int) $role['dependency_count'] > 0);
    $role['manager_name'] = '-';

    $managerId = (int) ($role['manager_id'] ?? 0);
    if ($managerId > 0 && isset($roleById[$managerId])) {
        $role['manager_name'] = $roleById[$managerId]['rbac_name'];
    }

    if ($adminCount > 0 && $userCount > 0) {
        $role['system_scope'] = 'shared';
    } elseif ($adminCount > 0) {
        $role['system_scope'] = 'ipanel';
    } elseif ($userCount > 0) {
        $role['system_scope'] = 'iweb';
    } else {
        $role['system_scope'] = 'unassigned';
    }

    if ($adminCount > 0) {
        $accessStats['ipanel_roles']++;
    }
    if ($userCount > 0) {
        $accessStats['iweb_roles']++;
    }
    if ($adminCount === 0 && $userCount === 0) {
        $accessStats['unassigned_roles']++;
    }

    $accessRoleEditData[$roleId] = [
        'id' => $roleId,
        'rbac_name' => (string) ($role['rbac_name'] ?? ''),
        'rbac_description' => (string) ($role['rbac_description'] ?? ''),
        'manager_id' => (int) ($role['manager_id'] ?? 0),
        'unit_id' => (int) ($role['unit_id'] ?? 0),
        'company_id' => (int) ($role['company_id'] ?? 0),
        'company_name' => (string) ($role['company_name'] ?? ''),
        'activity_id' => (int) ($role['activity_id'] ?? 0),
        'activity_name' => (string) ($role['activity_name'] ?? ''),
        'tag_id' => (int) ($role['tag_id'] ?? 0),
        'all_rbac' => (int) ($role['all_rbac'] ?? 0),
        'dependency_count' => (int) ($role['dependency_count'] ?? 0),
        'unit_change_locked' => (bool) $role['unit_change_locked'],
    ];

    $visible = true;
    if ($accessSystemFilter === 'ipanel') {
        $visible = $adminCount > 0;
    } elseif ($accessSystemFilter === 'iweb') {
        $visible = $userCount > 0;
    }

    if ($visible) {
        $accessRoles[] = $role;
    }
}
