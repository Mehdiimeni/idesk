<?php
///ipanel/controller/member/users.php

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

$canAddUser = (bool) $rbacClass->checkPermissionOperationByName('add_operation');
$canEditUser = (bool) $rbacClass->checkPermissionOperationByName('edit_operation');
$canActivateUser = (bool) $rbacClass->checkPermissionOperationByName('active_operation');
$canInactivateUser = (bool) $rbacClass->checkPermissionOperationByName('inactive_operation');

if (empty($_SESSION['users_management_csrf'])) {
    $_SESSION['users_management_csrf'] = bin2hex(random_bytes(32));
}
$usersManagementCsrf = $_SESSION['users_management_csrf'];

function usersTableExists(mysqli $db, string $table): bool
{
    $stmt = $db->prepare("SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? LIMIT 1");
    $stmt->bind_param('s', $table);
    $stmt->execute();
    $exists = (bool) $stmt->get_result()->fetch_row();
    $stmt->close();
    return $exists;
}

function usersFetchAll(mysqli_result $result): array
{
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$portalProfilesAvailable = usersTableExists($db, 'iweb_customer_portal_user_profiles');
$portalSectionsAvailable = usersTableExists($db, 'iweb_customer_portal_sections');
$portalAuditAvailable = usersTableExists($db, 'iweb_customer_portal_ticket_audit_logs');
$portalManagementReady = $portalProfilesAvailable && $portalSectionsAvailable && $portalAuditAvailable;

$selectedCompanyId = isset($_GET['company_id']) ? (int) $_GET['company_id'] : 0;
$selectedCompany = null;
$companyUnits = [];
$companyRbacs = [];
$companySections = [];
$companyUsers = [];
$companyChartSections = [];
$companyAdmins = [];
$companyUnassignedUsers = [];
$companyUnassignedManagers = [];
$companyUnassignedExperts = [];
$companyLegacyUnclassified = [];
$companyStats = [
    'total' => 0,
    'active' => 0,
    'inactive' => 0,
    'admins' => 0,
    'managers' => 0,
    'experts' => 0,
    'unassigned' => 0,
    'waiting_managers' => 0,
    'waiting_experts' => 0,
    'legacy_unclassified' => 0,
];

$companyListSql = "
    SELECT
        cp.id,
        cp.company_name,
        cp.company_phone,
        a.activity_name,
        COALESCE(us.total_users, 0) AS total_users,
        COALESCE(us.active_users, 0) AS active_users,
        COALESCE(us.inactive_users, 0) AS inactive_users,
        COALESCE(sec.section_count, 0) AS section_count
    FROM company_profiles cp
    LEFT JOIN activities a ON a.id = cp.activity_id
    LEFT JOIN (
        SELECT
            un.company_id,
            COUNT(u.id) AS total_users,
            SUM(CASE WHEN u.status = 'Active' THEN 1 ELSE 0 END) AS active_users,
            SUM(CASE WHEN u.status = 'Inactive' THEN 1 ELSE 0 END) AS inactive_users
        FROM units un
        LEFT JOIN users u ON u.unit_id = un.id
        GROUP BY un.company_id
    ) us ON us.company_id = cp.id
";

if ($portalSectionsAvailable) {
    $companyListSql .= "
        LEFT JOIN (
            SELECT company_id, COUNT(*) AS section_count
            FROM iweb_customer_portal_sections
            GROUP BY company_id
        ) sec ON sec.company_id = cp.id
    ";
} else {
    $companyListSql .= " LEFT JOIN (SELECT NULL AS company_id, 0 AS section_count) sec ON 1 = 0 ";
}
$companyListSql .= " ORDER BY cp.company_name ASC, cp.id ASC";

$companies = usersFetchAll($db->query($companyListSql));
$companyRoleStats = [];

if ($portalProfilesAvailable) {
    $roleResult = $db->query("SELECT company_id, portal_role, COUNT(*) AS cnt FROM iweb_customer_portal_user_profiles GROUP BY company_id, portal_role");
    if ($roleResult) {
        while ($row = $roleResult->fetch_assoc()) {
            $companyId = (int) $row['company_id'];
            if (!isset($companyRoleStats[$companyId])) {
                $companyRoleStats[$companyId] = ['ADMIN' => 0, 'MANAGER' => 0, 'EXPERT' => 0];
            }
            $role = strtoupper((string) $row['portal_role']);
            if (isset($companyRoleStats[$companyId][$role])) {
                $companyRoleStats[$companyId][$role] = (int) $row['cnt'];
            }
        }
        $roleResult->free();
    }
}

if ($selectedCompanyId > 0) {
    $stmt = $db->prepare("SELECT cp.*, a.activity_name FROM company_profiles cp LEFT JOIN activities a ON a.id = cp.activity_id WHERE cp.id = ? LIMIT 1");
    $stmt->bind_param('i', $selectedCompanyId);
    $stmt->execute();
    $selectedCompany = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$selectedCompany) {
        echo "<script>window.location.replace('./users');</script>";
        exit();
    }

    $stmt = $db->prepare("SELECT id, unit_name, unit_type FROM units WHERE company_id = ? ORDER BY unit_name ASC, id ASC");
    $stmt->bind_param('i', $selectedCompanyId);
    $stmt->execute();
    $companyUnits = usersFetchAll($stmt->get_result());
    $stmt->close();

    $stmt = $db->prepare("SELECT id, rbac_name, all_rbac, unit_id FROM rbac WHERE company_id = ? ORDER BY rbac_name ASC, id ASC");
    $stmt->bind_param('i', $selectedCompanyId);
    $stmt->execute();
    $companyRbacs = usersFetchAll($stmt->get_result());
    $stmt->close();

    if ($portalSectionsAvailable) {
        $stmt = $db->prepare("SELECT id, company_id, name, is_active FROM iweb_customer_portal_sections WHERE company_id = ? ORDER BY is_active DESC, name ASC, id ASC");
        $stmt->bind_param('i', $selectedCompanyId);
        $stmt->execute();
        $companySections = usersFetchAll($stmt->get_result());
        $stmt->close();
    }

    $profileJoin = $portalProfilesAvailable
        ? "LEFT JOIN iweb_customer_portal_user_profiles p ON p.user_id = u.id LEFT JOIN iweb_customer_portal_sections s ON s.id = p.section_id"
        : "LEFT JOIN (SELECT NULL AS user_id, NULL AS company_id, NULL AS portal_role, NULL AS section_id, NULL AS is_active) p ON 1=0 LEFT JOIN (SELECT NULL AS id, NULL AS name, NULL AS is_active) s ON 1=0";

    $sql = "
        SELECT
            u.id,
            u.name,
            u.email,
            u.mobile,
            u.status,
            u.role AS legacy_role,
            u.unit_id,
            u.rbac_id,
            u.creation_date,
            u.last_updated_date,
            un.unit_name,
            r.rbac_name,
            r.all_rbac,
            p.company_id AS profile_company_id,
            p.portal_role,
            p.section_id,
            p.is_active AS profile_is_active,
            s.name AS section_name,
            s.is_active AS section_is_active
        FROM users u
        INNER JOIN units un ON un.id = u.unit_id
        LEFT JOIN rbac r ON r.id = u.rbac_id
        {$profileJoin}
        WHERE un.company_id = ?
        ORDER BY
            CASE UPPER(COALESCE(p.portal_role, ''))
                WHEN 'ADMIN' THEN 1
                WHEN 'MANAGER' THEN 2
                WHEN 'EXPERT' THEN 3
                ELSE 4
            END,
            s.name ASC,
            u.name ASC,
            u.id ASC
    ";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $selectedCompanyId);
    $stmt->execute();
    $companyUsers = usersFetchAll($stmt->get_result());
    $stmt->close();

    $sendAllOperationId = 0;
    $opResult = $db->query("SELECT id FROM operations WHERE operation_name = 'ticket_send_all_operation' LIMIT 1");
    if ($opResult && ($opRow = $opResult->fetch_assoc())) {
        $sendAllOperationId = (int) $opRow['id'];
    }

    $sendAllByRbac = [];
    foreach ($companyRbacs as $rbacRow) {
        $rid = (int) $rbacRow['id'];
        $sendAllByRbac[$rid] = ((int) $rbacRow['all_rbac'] === 1);
    }

    if ($sendAllOperationId > 0 && !empty($companyRbacs)) {
        $rbacIds = array_map(static fn($row) => (int) $row['id'], $companyRbacs);
        $placeholders = implode(',', array_fill(0, count($rbacIds), '?'));
        $types = str_repeat('i', count($rbacIds));
        $stmt = $db->prepare("SELECT rbac_id, operation FROM permissions_operation WHERE status = 'Active' AND rbac_id IN ({$placeholders})");
        $stmt->bind_param($types, ...$rbacIds);
        $stmt->execute();
        $permissionResult = $stmt->get_result();
        while ($permissionRow = $permissionResult->fetch_assoc()) {
            $rid = (int) $permissionRow['rbac_id'];
            $ops = @unserialize($permissionRow['operation'] ?? '', ['allowed_classes' => false]);
            if (is_array($ops) && in_array((string) $sendAllOperationId, array_map('strval', $ops), true)) {
                $sendAllByRbac[$rid] = true;
            }
        }
        $stmt->close();
    }

    $sectionsById = [];
    foreach ($companySections as $section) {
        $sid = (int) $section['id'];
        $sectionsById[$sid] = $section;
        $companyChartSections[$sid] = [
            'section' => $section,
            'managers' => [],
            'experts' => [],
        ];
    }

    foreach ($companyUsers as &$user) {
        $user['send_all'] = !empty($sendAllByRbac[(int) ($user['rbac_id'] ?? 0)]);
        $role = strtoupper((string) ($user['portal_role'] ?? ''));
        $isProfileForCompany = !empty($user['portal_role']) && (int) ($user['profile_company_id'] ?? 0) === $selectedCompanyId;

        $companyStats['total']++;
        if ($user['status'] === 'Active') {
            $companyStats['active']++;
        } else {
            $companyStats['inactive']++;
        }

        if (!$isProfileForCompany || !in_array($role, ['ADMIN', 'MANAGER', 'EXPERT'], true)) {
            $rbacName = (string) ($user['rbac_name'] ?? '');
            $suggestedRole = '';
            if ($rbacName !== '' && mb_strpos($rbacName, 'کارشناس') !== false) {
                $suggestedRole = 'EXPERT';
            } elseif ($rbacName !== '' && mb_strpos($rbacName, 'مدیر') !== false) {
                $suggestedRole = 'MANAGER';
            }
            $user['migration_suggested_role'] = $suggestedRole;
            $companyStats['unassigned']++;
            $companyStats['legacy_unclassified']++;
            $companyLegacyUnclassified[] = $user;
            $companyUnassignedUsers[] = $user;
            continue;
        }

        if ($role === 'ADMIN') {
            $companyStats['admins']++;
            $companyAdmins[] = $user;
            continue;
        }

        $sid = (int) ($user['section_id'] ?? 0);

        if ($role === 'MANAGER') {
            $companyStats['managers']++;
            if ($sid <= 0 || !isset($companyChartSections[$sid])) {
                $companyStats['unassigned']++;
                $companyStats['waiting_managers']++;
                $companyUnassignedManagers[] = $user;
                $companyUnassignedUsers[] = $user;
                continue;
            }
            $companyChartSections[$sid]['managers'][] = $user;
            continue;
        }

        if ($role === 'EXPERT') {
            $companyStats['experts']++;
            if ($sid <= 0 || !isset($companyChartSections[$sid])) {
                $companyStats['unassigned']++;
                $companyStats['waiting_experts']++;
                $companyUnassignedExperts[] = $user;
                $companyUnassignedUsers[] = $user;
                continue;
            }
            $companyChartSections[$sid]['experts'][] = $user;
        }
    }
    unset($user);

    $companyManagerAssignableSections = [];
    $companyExpertAssignableSections = [];
    foreach ($companyChartSections as $sid => $sectionData) {
        if ((int) ($sectionData['section']['is_active'] ?? 0) !== 1) {
            continue;
        }

        $activeManagers = 0;
        foreach ($sectionData['managers'] as $managerRow) {
            if (($managerRow['status'] ?? '') === 'Active' && (int) ($managerRow['profile_is_active'] ?? 0) === 1) {
                $activeManagers++;
            }
        }

        if ($activeManagers === 0) {
            $companyManagerAssignableSections[$sid] = $sectionData['section'];
        }
        if ($activeManagers > 0) {
            $companyExpertAssignableSections[$sid] = $sectionData['section'];
        }
    }
}
