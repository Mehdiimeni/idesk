<?php
///ipanel/controller/structure/user_operation.php
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

if (empty($_SESSION['user_operation_csrf'])) {
    $_SESSION['user_operation_csrf'] = bin2hex(random_bytes(32));
}
$userOperationCsrf = $_SESSION['user_operation_csrf'];

$canAddUserOperation = (bool) $rbacClass->checkPermissionOperationByName('add_operation');
$canEditUserOperation = (bool) $rbacClass->checkPermissionOperationByName('edit_operation');
$canDeleteUserOperation = (bool) $rbacClass->checkPermissionOperationByName('delete_operation');

$userOperationSystemFilter = isset($_GET['system']) ? strtolower(trim((string) $_GET['system'])) : 'all';
if (!in_array($userOperationSystemFilter, ['all', 'ipanel', 'iweb', 'shared', 'unassigned'], true)) {
    $userOperationSystemFilter = 'all';
}

function userOperationDecodeParts($serialized): array
{
    if ($serialized === null || $serialized === '') return [];
    $decoded = @unserialize((string)$serialized, ['allowed_classes' => false]);
    if (!is_array($decoded)) return [];

    $items = [];
    foreach ($decoded as $item) {
        if (is_array($item) && isset($item['id'])) {
            $id = trim((string)$item['id']);
            if ($id === '') continue;
            $category = trim((string)($item['category'] ?? ''));
            if ($category === '') {
                if (preg_match('/^[au]g\d+$/', $id)) $category = 'group';
                elseif (preg_match('/^[au]p\d+$/', $id)) $category = 'part';
                elseif (preg_match('/^[au]s\d+$/', $id)) $category = 'subpart';
            }
            $items[$id] = ['id'=>$id, 'category'=>$category];
            continue;
        }
        if (is_scalar($item)) {
            $id = trim((string)$item);
            if ($id === '') continue;
            $category = '';
            if (preg_match('/^[au]g\d+$/', $id)) $category = 'group';
            elseif (preg_match('/^[au]p\d+$/', $id)) $category = 'part';
            elseif (preg_match('/^[au]s\d+$/', $id)) $category = 'subpart';
            $items[$id] = ['id'=>$id, 'category'=>$category];
        }
    }
    return array_values($items);
}

function userOperationDecodeOperations($serialized): array
{
    if ($serialized === null || $serialized === '') return [];
    $decoded = @unserialize((string)$serialized, ['allowed_classes' => false]);
    if (!is_array($decoded)) return [];
    $ids = [];
    foreach ($decoded as $id) {
        $id = (int)$id;
        if ($id > 0) $ids[$id] = true;
    }
    return array_keys($ids);
}

function userOperationCompanyUsage(mysqli $db, string $table): array
{
    if (!in_array($table, ['admins','users'], true)) return [];
    $usage = [];
    $sql = "SELECT un.company_id, COUNT(*) AS cnt
            FROM {$table} p
            INNER JOIN units un ON un.id = p.unit_id
            WHERE un.company_id IS NOT NULL
            GROUP BY un.company_id";
    $res = $db->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $usage[(int)$row['company_id']] = (int)$row['cnt'];
        }
        $res->free();
    }
    return $usage;
}

function userOperationBuildCatalog(mysqli $db, string $type): array
{
    $isAdmin = $type === 'admin';
    $prefix = $isAdmin ? 'a' : 'u';
    $groupsTable = $isAdmin ? 'admins_groups' : 'users_groups';
    $partsTable = $isAdmin ? 'admins_parts' : 'users_parts';
    $subpartsTable = $isAdmin ? 'admins_subparts' : 'users_subparts';
    $groupName = $isAdmin ? 'admins_groups_name' : 'users_groups_name';
    $partName = $isAdmin ? 'admins_parts_name' : 'users_parts_name';
    $subpartName = $isAdmin ? 'admins_subparts_name' : 'users_subparts_name';
    $partGroup = $isAdmin ? 'admins_groups_id' : 'users_groups_id';
    $subpartPart = $isAdmin ? 'admins_parts_id' : 'users_parts_id';

    $groups = [];
    $result = $db->query("SELECT id, {$groupName} AS name FROM {$groupsTable} ORDER BY id");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $gid = (int)$row['id'];
            $groups[$gid] = [
                'id'=>$prefix.'g'.$gid,
                'category'=>'group',
                'name'=>(string)$row['name'],
                'parts'=>[]
            ];
        }
        $result->free();
    }

    $partIndex = [];
    $result = $db->query("SELECT id, {$partName} AS name, {$partGroup} AS group_id FROM {$partsTable} ORDER BY {$partGroup}, id");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $pid = (int)$row['id'];
            $gid = (int)$row['group_id'];
            if (!isset($groups[$gid])) continue;
            $groups[$gid]['parts'][$pid] = [
                'id'=>$prefix.'p'.$pid,
                'category'=>'part',
                'name'=>(string)$row['name'],
                'subparts'=>[]
            ];
            $partIndex[$pid] = $gid;
        }
        $result->free();
    }

    $result = $db->query("SELECT id, {$subpartName} AS name, {$subpartPart} AS part_id FROM {$subpartsTable} ORDER BY {$subpartPart}, id");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $sid = (int)$row['id'];
            $pid = (int)$row['part_id'];
            if (!isset($partIndex[$pid])) continue;
            $gid = $partIndex[$pid];
            $groups[$gid]['parts'][$pid]['subparts'][] = [
                'id'=>$prefix.'s'.$sid,
                'category'=>'subpart',
                'name'=>(string)$row['name']
            ];
        }
        $result->free();
    }

    foreach ($groups as &$group) {
        $group['parts'] = array_values($group['parts']);
    }
    unset($group);

    return array_values($groups);
}

$adminCatalog = userOperationBuildCatalog($db, 'admin');
$userCatalog = userOperationBuildCatalog($db, 'user');

$permissionByRole = [];
$permissionResult = $db->query("SELECT id, rbac_id, parts, operation, status, last_updated_date
                                FROM permissions_operation
                                ORDER BY id");
if ($permissionResult) {
    while ($row = $permissionResult->fetch_assoc()) {
        $roleId = (int)$row['rbac_id'];
        if ($roleId <= 0) continue;
        if (!isset($permissionByRole[$roleId])) {
            $permissionByRole[$roleId] = [
                'row_count'=>0,
                'row_ids'=>[],
                'parts'=>[],
                'operation_ids'=>[],
                'last_updated_date'=>null,
                'status'=>(string)$row['status'],
            ];
        }
        $permissionByRole[$roleId]['row_count']++;
        $permissionByRole[$roleId]['row_ids'][] = (int)$row['id'];

        foreach (userOperationDecodeParts($row['parts']) as $part) {
            $permissionByRole[$roleId]['parts'][$part['id']] = $part;
        }
        foreach (userOperationDecodeOperations($row['operation']) as $operationId) {
            $permissionByRole[$roleId]['operation_ids'][$operationId] = true;
        }

        if ($permissionByRole[$roleId]['last_updated_date'] === null ||
            strcmp((string)$row['last_updated_date'], (string)$permissionByRole[$roleId]['last_updated_date']) > 0) {
            $permissionByRole[$roleId]['last_updated_date'] = (string)$row['last_updated_date'];
        }
    }
    $permissionResult->free();
}

$companyAdminUsage = userOperationCompanyUsage($db, 'admins');
$companyUserUsage = userOperationCompanyUsage($db, 'users');

$roles = [];
$summary = ['roles'=>0, 'configured'=>0, 'ipanel'=>0, 'iweb'=>0, 'attention'=>0];

$sql = "SELECT
            r.id, r.rbac_name, r.all_rbac, r.company_id, r.unit_id, r.manager_id,
            cp.company_name,
            un.unit_name,
            act.activity_name,
            COALESCE(ad.cnt, 0) AS admin_count,
            COALESCE(us.cnt, 0) AS user_count
        FROM rbac r
        LEFT JOIN company_profiles cp ON cp.id = r.company_id
        LEFT JOIN units un ON un.id = r.unit_id
        LEFT JOIN activities act ON act.id = cp.activity_id
        LEFT JOIN (SELECT rbac_id, COUNT(*) AS cnt FROM admins WHERE rbac_id IS NOT NULL GROUP BY rbac_id) ad ON ad.rbac_id = r.id
        LEFT JOIN (SELECT rbac_id, COUNT(*) AS cnt FROM users WHERE rbac_id IS NOT NULL GROUP BY rbac_id) us ON us.rbac_id = r.id
        WHERE r.all_rbac = 0
        ORDER BY cp.company_name, un.unit_name, r.rbac_name, r.id";
$result = $db->query($sql);

if ($result) {
    while ($role = $result->fetch_assoc()) {
        $roleId = (int)$role['id'];
        $companyId = (int)($role['company_id'] ?? 0);
        $adminCount = (int)$role['admin_count'];
        $userCount = (int)$role['user_count'];

        if ($adminCount > 0 && $userCount > 0) $scope = 'shared';
        elseif ($adminCount > 0) $scope = 'ipanel';
        elseif ($userCount > 0) $scope = 'iweb';
        else {
            $companyAdmins = $companyAdminUsage[$companyId] ?? 0;
            $companyUsers = $companyUserUsage[$companyId] ?? 0;
            if ($companyAdmins > 0 && $companyUsers === 0) $scope = 'ipanel';
            elseif ($companyUsers > 0 && $companyAdmins === 0) $scope = 'iweb';
            elseif ($companyAdmins > 0 && $companyUsers > 0) $scope = 'shared';
            else $scope = 'unassigned';
        }

        $permission = $permissionByRole[$roleId] ?? [
            'row_count'=>0, 'row_ids'=>[], 'parts'=>[], 'operation_ids'=>[], 'last_updated_date'=>null, 'status'=>'Active'
        ];

        $partItems = array_values($permission['parts']);
        $adminPartCount = 0;
        $userPartCount = 0;
        foreach ($partItems as $part) {
            $id = (string)$part['id'];
            if (strpos($id, 'a') === 0) $adminPartCount++;
            elseif (strpos($id, 'u') === 0) $userPartCount++;
        }

        $configured = count($partItems) > 0;
        $role['system_scope'] = $scope;
        $role['permission_row_count'] = (int)$permission['row_count'];
        $role['permission_configured'] = $configured;
        $role['selected_parts'] = $partItems;
        $role['selected_part_ids'] = array_values(array_map(static fn($x)=>(string)$x['id'], $partItems));
        $role['admin_part_count'] = $adminPartCount;
        $role['user_part_count'] = $userPartCount;
        $role['operation_count'] = count($permission['operation_ids']);
        $role['permission_last_updated'] = $permission['last_updated_date'];
        $role['has_duplicate_rows'] = (int)$permission['row_count'] > 1;
        $role['can_admin_parts'] = in_array($scope, ['ipanel','shared','unassigned'], true);
        $role['can_user_parts'] = in_array($scope, ['iweb','shared','unassigned'], true);

        $summary['roles']++;
        if ($configured) $summary['configured']++;
        if (in_array($scope, ['ipanel','shared'], true)) $summary['ipanel']++;
        if (in_array($scope, ['iweb','shared'], true)) $summary['iweb']++;
        if ($scope === 'unassigned' || $role['has_duplicate_rows']) $summary['attention']++;

        $visible = $userOperationSystemFilter === 'all' || $userOperationSystemFilter === $scope;
        if ($userOperationSystemFilter === 'ipanel' && $scope === 'shared') $visible = true;
        if ($userOperationSystemFilter === 'iweb' && $scope === 'shared') $visible = true;
        if ($visible) $roles[] = $role;
    }
    $result->free();
}

$userOperationRoleData = [];
foreach ($roles as $role) {
    $userOperationRoleData[(int)$role['id']] = [
        'id'=>(int)$role['id'],
        'name'=>(string)$role['rbac_name'],
        'company'=>(string)($role['company_name'] ?? ''),
        'unit'=>(string)($role['unit_name'] ?? ''),
        'activity'=>(string)($role['activity_name'] ?? ''),
        'scope'=>(string)$role['system_scope'],
        'configured'=>(bool)$role['permission_configured'],
        'permission_row_count'=>(int)$role['permission_row_count'],
        'selected_part_ids'=>$role['selected_part_ids'],
        'can_admin_parts'=>(bool)$role['can_admin_parts'],
        'can_user_parts'=>(bool)$role['can_user_parts'],
        'operation_count'=>(int)$role['operation_count'],
    ];
}
