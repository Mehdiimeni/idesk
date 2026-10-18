<?php
require_once "../class/mysql.php";
require_once "../class/config.php";
require_once "../class/rbac.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=UTF-8');

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$rbacClass = new RBAC($db);

$allowedLanguages = $config->getConfig('allowedLanguage');
if (!is_array($allowedLanguages)) {
    $allowedLanguages = [];
}
$adminLanguage = $_COOKIE['admin_language'] ?? $config->getConfig('defaultLanguageAdmin');
if (!in_array($adminLanguage, $allowedLanguages, true)) {
    $adminLanguage = $config->getConfig('defaultLanguageAdmin');
}
require_once "../lang/{$adminLanguage}.php";
define('_lang', $config->getLang($adminLanguage));

function usersJson(bool $success, string $message, array $extra = [], int $httpCode = 200): void
{
    http_response_code($httpCode);
    echo json_encode(array_merge([
        'status' => $success ? 'success' : 'error',
        'message' => $message,
    ], $extra), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function usersTableExistsManage(mysqli $db, string $table): bool
{
    $stmt = $db->prepare("SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? LIMIT 1");
    $stmt->bind_param('s', $table);
    $stmt->execute();
    $exists = (bool) $stmt->get_result()->fetch_row();
    $stmt->close();
    return $exists;
}

function usersLoadCompany(mysqli $db, int $companyId): ?array
{
    $stmt = $db->prepare("SELECT id, company_name FROM company_profiles WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $companyId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

function usersLoadUnit(mysqli $db, int $unitId): ?array
{
    $stmt = $db->prepare("SELECT id, company_id, unit_name FROM units WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $unitId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

function usersLoadRbac(mysqli $db, int $rbacId): ?array
{
    $stmt = $db->prepare("SELECT id, company_id, unit_id, rbac_name FROM rbac WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $rbacId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

function usersLoadSection(mysqli $db, int $sectionId): ?array
{
    $stmt = $db->prepare("SELECT id, company_id, name, is_active FROM iweb_customer_portal_sections WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $sectionId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

function usersLoadTarget(mysqli $db, int $userId): ?array
{
    $stmt = $db->prepare("
        SELECT
            u.id, u.name, u.email, u.mobile, u.status, u.role AS legacy_role, u.unit_id, u.rbac_id,
            un.company_id,
            p.portal_role, p.section_id, p.company_id AS profile_company_id, p.is_active AS profile_is_active
        FROM users u
        INNER JOIN units un ON un.id = u.unit_id
        LEFT JOIN iweb_customer_portal_user_profiles p ON p.user_id = u.id
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

function usersCheckDuplicate(mysqli $db, string $email, string $mobile, int $excludeId = 0): bool
{
    $stmt = $db->prepare("SELECT id FROM users WHERE id <> ? AND (LOWER(email) = LOWER(?) OR mobile = ?) LIMIT 1");
    $stmt->bind_param('iss', $excludeId, $email, $mobile);
    $stmt->execute();
    $exists = (bool) $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $exists;
}

function usersCountOtherActiveManagers(mysqli $db, int $sectionId, int $excludeUserId): int
{
    $stmt = $db->prepare("
        SELECT COUNT(*) AS cnt
        FROM iweb_customer_portal_user_profiles p
        INNER JOIN users u ON u.id = p.user_id
        WHERE p.section_id = ?
          AND p.portal_role = 'MANAGER'
          AND p.is_active = 1
          AND u.status = 'Active'
          AND p.user_id <> ?
    ");
    $stmt->bind_param('ii', $sectionId, $excludeUserId);
    $stmt->execute();
    $count = (int) ($stmt->get_result()->fetch_assoc()['cnt'] ?? 0);
    $stmt->close();
    return $count;
}

function usersCountActiveExperts(mysqli $db, int $sectionId, int $excludeUserId = 0): int
{
    $stmt = $db->prepare("
        SELECT COUNT(*) AS cnt
        FROM iweb_customer_portal_user_profiles p
        INNER JOIN users u ON u.id = p.user_id
        WHERE p.section_id = ?
          AND p.portal_role = 'EXPERT'
          AND p.is_active = 1
          AND u.status = 'Active'
          AND p.user_id <> ?
    ");
    $stmt->bind_param('ii', $sectionId, $excludeUserId);
    $stmt->execute();
    $count = (int) ($stmt->get_result()->fetch_assoc()['cnt'] ?? 0);
    $stmt->close();
    return $count;
}

function usersCountActiveManagers(mysqli $db, int $sectionId): int
{
    return usersCountOtherActiveManagers($db, $sectionId, 0);
}

function usersAudit(mysqli $db, int $companyId, ?int $sectionId, int $targetUserId, string $action, ?array $oldData, ?array $newData): void
{
    $actorAdminId = (int) ($_SESSION['admin_id'] ?? 0);
    $actorRole = 'INTEK_ADMIN';
    $actorSource = 'ADMIN';
    $entityType = 'USER';
    $oldJson = $oldData === null ? null : json_encode($oldData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $newJson = $newData === null ? null : json_encode($newData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

    $stmt = $db->prepare("
        INSERT INTO iweb_customer_portal_ticket_audit_logs
        (company_id, section_id, actor_user_id, actor_source, actor_role, action_type, entity_type, entity_id, target_user_id, old_data, new_data, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('iiissssiissss', $companyId, $sectionId, $actorAdminId, $actorSource, $actorRole, $action, $entityType, $targetUserId, $targetUserId, $oldJson, $newJson, $ip, $ua);
    if (!$stmt->execute()) {
        throw new RuntimeException(_lang['customer_users_audit_failed']);
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    usersJson(false, _lang['invalid_request'], [], 405);
}

if (empty($_SESSION['admin_id']) || empty($_SESSION['rbac_id'])) {
    usersJson(false, _lang['customer_users_unauthorized'], [], 401);
}

if (!$rbacClass->checkPermissionSubpartByName('users', 'members', 'systems')) {
    usersJson(false, _lang['customer_users_forbidden'], [], 403);
}

$csrf = (string) ($_POST['csrf_token'] ?? '');
$sessionCsrf = (string) ($_SESSION['users_management_csrf'] ?? '');
if ($csrf === '' || $sessionCsrf === '' || !hash_equals($sessionCsrf, $csrf)) {
    usersJson(false, _lang['customer_users_invalid_csrf'], [], 419);
}

foreach (['iweb_customer_portal_user_profiles', 'iweb_customer_portal_sections', 'iweb_customer_portal_ticket_audit_logs'] as $requiredTable) {
    if (!usersTableExistsManage($db, $requiredTable)) {
        usersJson(false, _lang['customer_users_portal_tables_missing'], [], 409);
    }
}

$action = strtolower(trim((string) ($_POST['action'] ?? '')));
$operationByAction = [
    'add' => 'add_operation',
    'edit' => 'edit_operation',
    'activate' => 'active_operation',
    'inactivate' => 'inactive_operation',
    'assign_section' => 'edit_operation',
];

if (!isset($operationByAction[$action])) {
    usersJson(false, _lang['invalid_request'], [], 400);
}

if (!$rbacClass->checkPermissionOperationByName($operationByAction[$action])) {
    usersJson(false, _lang['customer_users_forbidden'], [], 403);
}

$companyId = (int) ($_POST['company_id'] ?? 0);
$company = usersLoadCompany($db, $companyId);
if (!$company) {
    usersJson(false, _lang['customer_users_company_invalid'], [], 422);
}

if ($action === 'add' || $action === 'edit') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $mobile = trim((string) ($_POST['mobile'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $unitId = (int) ($_POST['unit_id'] ?? 0);
    $rbacId = (int) ($_POST['rbac_id'] ?? 0);
    $portalRole = strtoupper(trim((string) ($_POST['portal_role'] ?? '')));
    $sectionId = (int) ($_POST['section_id'] ?? 0);

    if ($name === '' || mb_strlen($name) > 200) {
        usersJson(false, _lang['customer_users_name_invalid'], [], 422);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 200) {
        usersJson(false, _lang['customer_users_email_invalid'], [], 422);
    }
    if ($mobile === '' || mb_strlen($mobile) > 25) {
        usersJson(false, _lang['customer_users_mobile_invalid'], [], 422);
    }
    if (!in_array($portalRole, ['ADMIN', 'MANAGER', 'EXPERT'], true)) {
        usersJson(false, _lang['customer_users_role_invalid'], [], 422);
    }

    $unit = usersLoadUnit($db, $unitId);
    if (!$unit || (int) $unit['company_id'] !== $companyId) {
        usersJson(false, _lang['customer_users_unit_invalid'], [], 422);
    }

    $rbac = usersLoadRbac($db, $rbacId);
    if (!$rbac || (int) $rbac['company_id'] !== $companyId) {
        usersJson(false, _lang['customer_users_rbac_invalid'], [], 422);
    }

    $section = null;
    if ($portalRole === 'ADMIN') {
        $sectionId = 0;
    } elseif ($sectionId > 0) {
        $section = usersLoadSection($db, $sectionId);
        if (!$section || (int) $section['company_id'] !== $companyId || (int) $section['is_active'] !== 1) {
            usersJson(false, _lang['customer_users_section_invalid'], [], 422);
        }
    } elseif ($action === 'add') {
        // New Manager/Expert users follow the normal scenario and must start inside a real Section.
        usersJson(false, _lang['customer_users_section_required_for_new'], [], 422);
    }

    if ($portalRole === 'EXPERT' && $sectionId > 0 && usersCountActiveManagers($db, $sectionId) <= 0) {
        usersJson(false, _lang['customer_users_expert_requires_manager'], [], 409);
    }

    if ($action === 'add' && $portalRole === 'MANAGER' && $sectionId > 0 && usersCountActiveManagers($db, $sectionId) > 0) {
        usersJson(false, _lang['customer_users_section_manager_exists'], [], 409);
    }
}

if ($action === 'add') {
    if ($password === '') {
        usersJson(false, _lang['customer_users_password_required'], [], 422);
    }
    if (usersCheckDuplicate($db, $email, $mobile, 0)) {
        usersJson(false, _lang['customer_users_duplicate'], [], 409);
    }

    $db->begin_transaction();
    try {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $legacyRole = 'member';
        $status = 'Active';
        $stmt = $db->prepare("INSERT INTO users (email, mobile, password, name, role, status, unit_id, rbac_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssii', $email, $mobile, $passwordHash, $name, $legacyRole, $status, $unitId, $rbacId);
        $stmt->execute();
        $userId = (int) $stmt->insert_id;
        $stmt->close();

        $profileSectionId = ($portalRole === 'ADMIN' || $sectionId <= 0) ? null : $sectionId;
        $actorAdminId = (int) $_SESSION['admin_id'];
        $profileActive = 1;
        $stmt = $db->prepare("INSERT INTO iweb_customer_portal_user_profiles (user_id, company_id, portal_role, section_id, is_active, created_by, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iisiiii', $userId, $companyId, $portalRole, $profileSectionId, $profileActive, $actorAdminId, $actorAdminId);
        $stmt->execute();
        $stmt->close();

        $newData = [
            'name' => $name,
            'email' => $email,
            'mobile' => $mobile,
            'portal_role' => $portalRole,
            'section_id' => $profileSectionId,
            'unit_id' => $unitId,
            'rbac_id' => $rbacId,
            'status' => 'Active',
            'legacy_role' => 'member',
        ];
        usersAudit($db, $companyId, $profileSectionId, $userId, 'USER_CREATED', null, $newData);
        if ($portalRole === 'MANAGER') {
            usersAudit($db, $companyId, $profileSectionId, $userId, 'MANAGER_CREATED', null, $newData);
        } elseif ($portalRole === 'EXPERT') {
            usersAudit($db, $companyId, $profileSectionId, $userId, 'EXPERT_CREATED', null, $newData);
        }

        $db->commit();
        usersJson(true, _lang['customer_users_add_success'], ['user_id' => $userId]);
    } catch (Throwable $e) {
        $db->rollback();
        usersJson(false, _lang['customer_users_unexpected_error'] . ' ' . $e->getMessage(), [], 500);
    }
}

$userId = (int) ($_POST['user_id'] ?? 0);
$target = usersLoadTarget($db, $userId);
if (!$target || (int) $target['company_id'] !== $companyId) {
    usersJson(false, _lang['customer_users_not_found'], [], 404);
}

if ($action === 'assign_section') {
    $role = strtoupper((string) ($target['portal_role'] ?? ''));
    $currentSectionId = (int) ($target['section_id'] ?? 0);
    $sectionId = (int) ($_POST['section_id'] ?? 0);

    if (!in_array($role, ['MANAGER', 'EXPERT'], true)) {
        usersJson(false, _lang['customer_users_assign_role_invalid'], [], 422);
    }
    if (($target['status'] ?? '') !== 'Active' || (int) ($target['profile_is_active'] ?? 0) !== 1) {
        usersJson(false, _lang['customer_users_assign_inactive'], [], 409);
    }
    if ($currentSectionId > 0) {
        usersJson(false, _lang['customer_users_assign_already_has_section'], [], 409);
    }

    $section = usersLoadSection($db, $sectionId);
    if (!$section || (int) $section['company_id'] !== $companyId || (int) $section['is_active'] !== 1) {
        usersJson(false, _lang['customer_users_section_invalid'], [], 422);
    }

    if ($role === 'MANAGER' && usersCountActiveManagers($db, $sectionId) > 0) {
        usersJson(false, _lang['customer_users_section_manager_exists'], [], 409);
    }
    if ($role === 'EXPERT' && usersCountActiveManagers($db, $sectionId) <= 0) {
        usersJson(false, _lang['customer_users_expert_requires_manager'], [], 409);
    }

    $db->begin_transaction();
    try {
        $actorAdminId = (int) $_SESSION['admin_id'];
        $stmt = $db->prepare("UPDATE iweb_customer_portal_user_profiles SET section_id = ?, updated_by = ? WHERE user_id = ? AND company_id = ?");
        $stmt->bind_param('iiii', $sectionId, $actorAdminId, $userId, $companyId);
        $stmt->execute();
        if ($stmt->affected_rows <= 0) {
            $stmt->close();
            throw new RuntimeException(_lang['customer_users_assign_profile_missing']);
        }
        $stmt->close();

        $oldData = [
            'portal_role' => $role,
            'section_id' => null,
            'status' => $target['status'],
        ];
        $newData = [
            'portal_role' => $role,
            'section_id' => $sectionId,
            'status' => $target['status'],
        ];
        $eventName = $role === 'MANAGER' ? 'MANAGER_ASSIGNED_TO_SECTION' : 'EXPERT_ASSIGNED_TO_SECTION';
        usersAudit($db, $companyId, $sectionId, $userId, $eventName, $oldData, $newData);

        $db->commit();
        usersJson(true, _lang['customer_users_assign_success']);
    } catch (Throwable $e) {
        $db->rollback();
        usersJson(false, _lang['customer_users_unexpected_error'] . ' ' . $e->getMessage(), [], 500);
    }
}

if ($action === 'edit') {
    if (usersCheckDuplicate($db, $email, $mobile, $userId)) {
        usersJson(false, _lang['customer_users_duplicate'], [], 409);
    }

    $oldRole = strtoupper((string) ($target['portal_role'] ?? ''));
    $oldSectionId = (int) ($target['section_id'] ?? 0);
    $isCurrentlyActive = ($target['status'] === 'Active');

    if ($oldRole === 'MANAGER' && $oldSectionId > 0 && $isCurrentlyActive && ($portalRole !== 'MANAGER' || $sectionId !== $oldSectionId)) {
        $otherManagers = usersCountOtherActiveManagers($db, $oldSectionId, $userId);
        $activeExperts = usersCountActiveExperts($db, $oldSectionId);
        if ($activeExperts > 0 && $otherManagers <= 0) {
            usersJson(false, _lang['customer_users_last_manager_change_blocked'], [], 409);
        }
    }

    if ($portalRole === 'EXPERT' && $sectionId > 0 && $isCurrentlyActive && usersCountOtherActiveManagers($db, $sectionId, $userId) <= 0) {
        usersJson(false, _lang['customer_users_expert_requires_manager'], [], 409);
    }

    if ($portalRole === 'MANAGER' && $sectionId > 0 && $isCurrentlyActive && usersCountOtherActiveManagers($db, $sectionId, $userId) > 0) {
        usersJson(false, _lang['customer_users_section_manager_exists'], [], 409);
    }

    $db->begin_transaction();
    try {
        $oldData = [
            'name' => $target['name'],
            'email' => $target['email'],
            'mobile' => $target['mobile'],
            'portal_role' => $target['portal_role'],
            'section_id' => $target['section_id'],
            'unit_id' => $target['unit_id'],
            'rbac_id' => $target['rbac_id'],
            'status' => $target['status'],
        ];

        if ($password !== '') {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, mobile = ?, unit_id = ?, rbac_id = ?, password = ? WHERE id = ?");
            $stmt->bind_param('sssiisi', $name, $email, $mobile, $unitId, $rbacId, $passwordHash, $userId);
        } else {
            $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, mobile = ?, unit_id = ?, rbac_id = ? WHERE id = ?");
            $stmt->bind_param('sssiii', $name, $email, $mobile, $unitId, $rbacId, $userId);
        }
        $stmt->execute();
        $stmt->close();

        $profileSectionId = ($portalRole === 'ADMIN' || $sectionId <= 0) ? null : $sectionId;
        $profileActive = $target['status'] === 'Active' ? 1 : 0;
        $actorAdminId = (int) $_SESSION['admin_id'];

        if (!empty($target['portal_role'])) {
            $stmt = $db->prepare("UPDATE iweb_customer_portal_user_profiles SET company_id = ?, portal_role = ?, section_id = ?, is_active = ?, updated_by = ? WHERE user_id = ?");
            $stmt->bind_param('isiiii', $companyId, $portalRole, $profileSectionId, $profileActive, $actorAdminId, $userId);
        } else {
            $stmt = $db->prepare("INSERT INTO iweb_customer_portal_user_profiles (user_id, company_id, portal_role, section_id, is_active, created_by, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('iisiiii', $userId, $companyId, $portalRole, $profileSectionId, $profileActive, $actorAdminId, $actorAdminId);
        }
        $stmt->execute();
        $stmt->close();

        $newData = [
            'name' => $name,
            'email' => $email,
            'mobile' => $mobile,
            'portal_role' => $portalRole,
            'section_id' => $profileSectionId,
            'unit_id' => $unitId,
            'rbac_id' => $rbacId,
            'status' => $target['status'],
        ];
        usersAudit($db, $companyId, $profileSectionId, $userId, 'USER_UPDATED', $oldData, $newData);

        if ($oldRole === 'MANAGER' && ($portalRole !== 'MANAGER' || $oldSectionId !== (int) $profileSectionId)) {
            usersAudit($db, $companyId, $oldSectionId ?: null, $userId, 'MANAGER_CHANGED', $oldData, $newData);
        } elseif ($portalRole === 'MANAGER' && ($oldRole !== 'MANAGER' || $oldSectionId !== (int) $profileSectionId)) {
            usersAudit($db, $companyId, $profileSectionId, $userId, 'MANAGER_ASSIGNED', $oldData, $newData);
        }

        if ($oldRole === 'EXPERT' && ($portalRole !== 'EXPERT' || $oldSectionId !== (int) $profileSectionId)) {
            usersAudit($db, $companyId, $oldSectionId ?: null, $userId, 'EXPERT_REMOVED_FROM_SECTION', $oldData, $newData);
        } elseif ($portalRole === 'EXPERT' && ($oldRole !== 'EXPERT' || $oldSectionId !== (int) $profileSectionId)) {
            usersAudit($db, $companyId, $profileSectionId, $userId, 'EXPERT_ASSIGNED', $oldData, $newData);
        }

        $db->commit();
        usersJson(true, _lang['customer_users_edit_success']);
    } catch (Throwable $e) {
        $db->rollback();
        usersJson(false, _lang['customer_users_unexpected_error'] . ' ' . $e->getMessage(), [], 500);
    }
}

if ($action === 'inactivate') {
    if ($target['status'] === 'Inactive') {
        usersJson(true, _lang['customer_users_inactivate_success']);
    }

    $role = strtoupper((string) ($target['portal_role'] ?? ''));
    $sectionId = (int) ($target['section_id'] ?? 0);
    if ($role === 'MANAGER' && $sectionId > 0) {
        $otherManagers = usersCountOtherActiveManagers($db, $sectionId, $userId);
        $activeExperts = usersCountActiveExperts($db, $sectionId);
        if ($activeExperts > 0 && $otherManagers <= 0) {
            usersJson(false, _lang['customer_users_last_manager_deactivate_blocked'], [], 409);
        }
    }

    $db->begin_transaction();
    try {
        $stmt = $db->prepare("UPDATE users SET status = 'Inactive' WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->close();

        $stmt = $db->prepare("UPDATE iweb_customer_portal_user_profiles SET is_active = 0, updated_by = ? WHERE user_id = ?");
        $actorAdminId = (int) $_SESSION['admin_id'];
        $stmt->bind_param('ii', $actorAdminId, $userId);
        $stmt->execute();
        $stmt->close();

        usersAudit($db, $companyId, $sectionId ?: null, $userId, 'USER_DEACTIVATED', ['status' => 'Active'], ['status' => 'Inactive']);
        $db->commit();
        usersJson(true, _lang['customer_users_inactivate_success']);
    } catch (Throwable $e) {
        $db->rollback();
        usersJson(false, _lang['customer_users_unexpected_error'] . ' ' . $e->getMessage(), [], 500);
    }
}

if ($action === 'activate') {
    if ($target['status'] === 'Active') {
        usersJson(true, _lang['customer_users_activate_success']);
    }

    $role = strtoupper((string) ($target['portal_role'] ?? ''));
    $sectionId = (int) ($target['section_id'] ?? 0);
    if ($role === 'EXPERT' && $sectionId > 0 && usersCountActiveManagers($db, $sectionId) <= 0) {
        usersJson(false, _lang['customer_users_expert_requires_manager'], [], 409);
    }
    if ($role === 'MANAGER' && $sectionId > 0 && usersCountOtherActiveManagers($db, $sectionId, $userId) > 0) {
        usersJson(false, _lang['customer_users_section_manager_exists'], [], 409);
    }

    $db->begin_transaction();
    try {
        $stmt = $db->prepare("UPDATE users SET status = 'Active' WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->close();

        $stmt = $db->prepare("UPDATE iweb_customer_portal_user_profiles SET is_active = 1, updated_by = ? WHERE user_id = ?");
        $actorAdminId = (int) $_SESSION['admin_id'];
        $stmt->bind_param('ii', $actorAdminId, $userId);
        $stmt->execute();
        $stmt->close();

        usersAudit($db, $companyId, $sectionId ?: null, $userId, 'USER_ACTIVATED', ['status' => 'Inactive'], ['status' => 'Active']);
        $db->commit();
        usersJson(true, _lang['customer_users_activate_success']);
    } catch (Throwable $e) {
        $db->rollback();
        usersJson(false, _lang['customer_users_unexpected_error'] . ' ' . $e->getMessage(), [], 500);
    }
}

usersJson(false, _lang['invalid_request'], [], 400);
