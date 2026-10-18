<?php
///ipanel/controller/structure/user_view.php
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

$canAddUserView = (bool)$rbacClass->checkPermissionOperationByName('add_operation');
$canEditUserView = (bool)$rbacClass->checkPermissionOperationByName('edit_operation');
$canDeleteUserView = (bool)$rbacClass->checkPermissionOperationByName('delete_operation');

$userViewSystemFilter = isset($_GET['system']) ? strtolower(trim((string)$_GET['system'])) : 'all';
if (!in_array($userViewSystemFilter, ['all','ipanel','iweb','shared','unassigned'], true)) {
    $userViewSystemFilter = 'all';
}

function userViewDecodeOperationIds($serialized): array
{
    if ($serialized === null || $serialized === '') return [];
    $decoded = @unserialize((string)$serialized, ['allowed_classes' => false]);
    if (!is_array($decoded)) return [];
    $ids = [];
    foreach ($decoded as $id) {
        $id = (int)$id;
        if ($id > 0) $ids[$id] = true;
    }
    $ids = array_keys($ids);
    sort($ids, SORT_NUMERIC);
    return $ids;
}

function userViewRoleUsage(mysqli $db, string $table): array
{
    if (!in_array($table, ['admins','users'], true)) return [];
    $out = [];
    $res = $db->query("SELECT rbac_id, COUNT(*) cnt FROM {$table} WHERE rbac_id IS NOT NULL GROUP BY rbac_id");
    if ($res) {
        while ($row = $res->fetch_assoc()) $out[(int)$row['rbac_id']] = (int)$row['cnt'];
        $res->free();
    }
    return $out;
}

function userViewCompanyUsage(mysqli $db, string $table): array
{
    if (!in_array($table, ['admins','users'], true)) return [];
    $out = [];
    $res = $db->query("SELECT un.company_id, COUNT(*) cnt FROM {$table} p INNER JOIN units un ON un.id=p.unit_id WHERE un.company_id IS NOT NULL GROUP BY un.company_id");
    if ($res) {
        while ($row = $res->fetch_assoc()) $out[(int)$row['company_id']] = (int)$row['cnt'];
        $res->free();
    }
    return $out;
}

$ticketSendAllOperationId = 0;
$stmt = $db->prepare("SELECT id FROM operations WHERE operation_name='ticket_send_all_operation' LIMIT 1");
if ($stmt) {
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $ticketSendAllOperationId = (int)($row['id'] ?? 0);
}

/*
 * permissions_operation is a shared Legacy row (parts + operation).
 * For duplicate Legacy rows, the lowest id is treated as the current canonical read.
 * The protected save endpoint synchronizes operation across every row for the role.
 */
$permissionByRole = [];
$res = $db->query("SELECT id,rbac_id,operation,status,last_updated_date FROM permissions_operation ORDER BY rbac_id,id");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $rid = (int)$row['rbac_id'];
        if ($rid <= 0) continue;
        $ops = userViewDecodeOperationIds($row['operation']);
        $signature = implode(',', $ops);
        if (!isset($permissionByRole[$rid])) {
            $permissionByRole[$rid] = [
                'row_count'=>0,
                'canonical_id'=>(int)$row['id'],
                'operation_ids'=>$ops,
                'canonical_signature'=>$signature,
                'duplicate_diff'=>false,
                'last_updated_date'=>(string)$row['last_updated_date'],
                'status'=>(string)$row['status'],
            ];
        } elseif ($signature !== $permissionByRole[$rid]['canonical_signature']) {
            $permissionByRole[$rid]['duplicate_diff'] = true;
        }
        $permissionByRole[$rid]['row_count']++;
        if (strcmp((string)$row['last_updated_date'], (string)$permissionByRole[$rid]['last_updated_date']) > 0) {
            $permissionByRole[$rid]['last_updated_date'] = (string)$row['last_updated_date'];
        }
    }
    $res->free();
}

$adminUsage = userViewRoleUsage($db, 'admins');
$userUsage = userViewRoleUsage($db, 'users');
$companyAdminUsage = userViewCompanyUsage($db, 'admins');
$companyUserUsage = userViewCompanyUsage($db, 'users');

$userViewRoles = [];
$userViewSummary = ['roles'=>0,'configured'=>0,'send_all'=>0,'full_access'=>0,'attention'=>0];

$sql = "SELECT r.id,r.rbac_name,r.all_rbac,r.company_id,r.unit_id,r.manager_id,
               cp.company_name,un.unit_name,act.activity_name
        FROM rbac r
        LEFT JOIN company_profiles cp ON cp.id=r.company_id
        LEFT JOIN units un ON un.id=r.unit_id
        LEFT JOIN activities act ON act.id=cp.activity_id
        ORDER BY r.all_rbac DESC,cp.company_name,un.unit_name,r.rbac_name,r.id";
$res = $db->query($sql);
if ($res) {
    while ($role = $res->fetch_assoc()) {
        $rid = (int)$role['id'];
        $cid = (int)($role['company_id'] ?? 0);
        $ac = $adminUsage[$rid] ?? 0;
        $uc = $userUsage[$rid] ?? 0;
        if ($ac > 0 && $uc > 0) $scope='shared';
        elseif ($ac > 0) $scope='ipanel';
        elseif ($uc > 0) $scope='iweb';
        else {
            $ca = $companyAdminUsage[$cid] ?? 0;
            $cu = $companyUserUsage[$cid] ?? 0;
            if ($ca > 0 && $cu === 0) $scope='ipanel';
            elseif ($cu > 0 && $ca === 0) $scope='iweb';
            elseif ($ca > 0 && $cu > 0) $scope='shared';
            else $scope='unassigned';
        }

        if ($userViewSystemFilter !== 'all' && $scope !== $userViewSystemFilter) continue;

        $permission = $permissionByRole[$rid] ?? [
            'row_count'=>0,'canonical_id'=>0,'operation_ids'=>[],'duplicate_diff'=>false,
            'last_updated_date'=>null,'status'=>'Active'
        ];
        $ops = $permission['operation_ids'];
        $opCount = count($ops);
        $sendAll = $ticketSendAllOperationId > 0 && in_array($ticketSendAllOperationId, $ops, true);
        $full = (int)$role['all_rbac'] === 1;

        $role['system_scope'] = $scope;
        $role['permission_row_count'] = (int)$permission['row_count'];
        $role['permission_id'] = (int)$permission['canonical_id'];
        $role['operation_count'] = $opCount;
        $role['send_all'] = $sendAll;
        $role['duplicate_diff'] = !empty($permission['duplicate_diff']);
        $role['last_updated_date'] = $permission['last_updated_date'];
        $role['is_full_access'] = $full;
        $role['is_configured'] = $opCount > 0;
        $role['can_mutate'] = !$full && (($permission['row_count'] > 0 && $canEditUserView) || ($permission['row_count'] === 0 && $canAddUserView));
        $role['needs_attention'] = !$full && ($opCount === 0 || !empty($role['duplicate_diff']));

        $userViewRoles[] = $role;
        $userViewSummary['roles']++;
        if ($opCount > 0) $userViewSummary['configured']++;
        if ($sendAll) $userViewSummary['send_all']++;
        if ($full) $userViewSummary['full_access']++;
        if ($role['needs_attention']) $userViewSummary['attention']++;
    }
    $res->free();
}
