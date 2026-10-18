<?php
require_once "../class/mysql.php";
require_once "../class/config.php";
require_once "../class/rbac.php";

header('Content-Type: application/json; charset=UTF-8');

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$rbacClass = new RBAC($db);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$allowedLanguages = $config->getConfig('allowedLanguage');
if (!is_array($allowedLanguages)) {
    $allowedLanguages = [];
}
$adminLanguage = $_COOKIE['admin_language'] ?? $config->getConfig('defaultLanguageAdmin');
if (!in_array($adminLanguage, $allowedLanguages, true)) {
    $adminLanguage = $config->getConfig('defaultLanguageAdmin');
}
$langFile = "../lang/{$adminLanguage}.php";
if (is_file($langFile)) {
    require $langFile;
}
define('_lang', $config->getLang($adminLanguage));

function typeLang(string $key, string $fallbackKey = 'invalid_request'): string
{
    if (isset(_lang[$key])) {
        return (string) _lang[$key];
    }
    if (isset(_lang[$fallbackKey])) {
        return (string) _lang[$fallbackKey];
    }
    return $key;
}

function typeRespond(string $status, string $message, int $httpCode = 200, array $data = []): void
{
    http_response_code($httpCode);
    echo json_encode(
        array_merge(['status' => $status, 'message' => $message], $data),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

function typeCleanText($value): string
{
    return trim(strip_tags((string) $value));
}

function typeTextLength(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function typeExistsByNameAndGroup(mysqli $db, string $name, string $group, int $excludeId = 0): bool
{
    if ($excludeId > 0) {
        $stmt = $db->prepare("SELECT id FROM types WHERE type_name = ? AND type_group = ? AND id <> ? LIMIT 1");
        $stmt->bind_param('ssi', $name, $group, $excludeId);
    } else {
        $stmt = $db->prepare("SELECT id FROM types WHERE type_name = ? AND type_group = ? LIMIT 1");
        $stmt->bind_param('ss', $name, $group);
    }
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $exists;
}

/**
 * Protect current/future BASE TABLEs that reference a Type by type_id.
 * This catches tickets today and additive modules later without hardcoding them.
 */
function typeDependencySummary(mysqli $db, int $typeId): array
{
    $dependencies = [];
    $total = 0;

    $sql = "
        SELECT c.TABLE_NAME
        FROM information_schema.COLUMNS c
        INNER JOIN information_schema.TABLES t
            ON t.TABLE_SCHEMA = c.TABLE_SCHEMA
           AND t.TABLE_NAME = c.TABLE_NAME
        WHERE c.TABLE_SCHEMA = DATABASE()
          AND c.COLUMN_NAME = 'type_id'
          AND t.TABLE_TYPE = 'BASE TABLE'
          AND c.TABLE_NAME <> 'types'
        ORDER BY c.TABLE_NAME
    ";

    $result = $db->query($sql);
    if (!$result) {
        return ['total' => 0, 'tables' => []];
    }

    while ($row = $result->fetch_assoc()) {
        $table = (string) $row['TABLE_NAME'];
        if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
            continue;
        }
        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM `{$table}` WHERE type_id = ?");
        if (!$stmt) {
            continue;
        }
        $stmt->bind_param('i', $typeId);
        $stmt->execute();
        $countRow = $stmt->get_result()->fetch_assoc();
        $count = (int) ($countRow['cnt'] ?? 0);
        $stmt->close();

        if ($count > 0) {
            $dependencies[$table] = $count;
            $total += $count;
        }
    }
    $result->free();

    return ['total' => $total, 'tables' => $dependencies];
}

function typeGroupContext(mysqli $db, string $group, int $excludeTypeId = 0): array
{
    $otherTypes = 0;
    $priorityLists = 0;

    if ($excludeTypeId > 0) {
        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM types WHERE type_group = ? AND id <> ?");
        $stmt->bind_param('si', $group, $excludeTypeId);
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM types WHERE type_group = ?");
        $stmt->bind_param('s', $group);
    }
    $stmt->execute();
    $otherTypes = (int) (($stmt->get_result()->fetch_assoc()['cnt'] ?? 0));
    $stmt->close();

    // priority_lists is part of the current baseline. Keep a defensive existence check.
    $tableCheck = $db->query("SHOW TABLES LIKE 'priority_lists'");
    $hasPriorityLists = $tableCheck && $tableCheck->num_rows > 0;
    if ($tableCheck) {
        $tableCheck->free();
    }

    if ($hasPriorityLists) {
        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM priority_lists WHERE type_group = ?");
        $stmt->bind_param('s', $group);
        $stmt->execute();
        $priorityLists = (int) (($stmt->get_result()->fetch_assoc()['cnt'] ?? 0));
        $stmt->close();
    }

    return ['other_types' => $otherTypes, 'priority_lists' => $priorityLists];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    typeRespond('error', typeLang('invalid_request'), 405);
}

$adminId = (int) ($_SESSION['admin_id'] ?? 0);
if ($adminId <= 0) {
    typeRespond('error', typeLang('type_unauthorized'), 401);
}

$stmt = $db->prepare("SELECT id, status, rbac_id FROM admins WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $adminId);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$admin || strcasecmp((string) $admin['status'], 'Active') !== 0) {
    typeRespond('error', typeLang('type_unauthorized'), 401);
}

if (!$rbacClass->checkPermissionSubpartByName('type', 'structure', 'systems')) {
    typeRespond('error', typeLang('type_forbidden'), 403);
}

$csrf = (string) ($_POST['csrf'] ?? '');
$sessionCsrf = (string) ($_SESSION['type_csrf'] ?? '');
if ($sessionCsrf === '' || $csrf === '' || !hash_equals($sessionCsrf, $csrf)) {
    typeRespond('error', typeLang('type_invalid_csrf'), 403);
}

$action = strtolower(trim((string) ($_POST['action'] ?? '')));
$operationMap = [
    'add' => 'add_operation',
    'edit' => 'edit_operation',
    'delete' => 'delete_operation',
];
if (!isset($operationMap[$action])) {
    typeRespond('error', typeLang('invalid_request'), 400);
}
if (!$rbacClass->checkPermissionOperationByName($operationMap[$action])) {
    typeRespond('error', typeLang('type_forbidden'), 403);
}

try {
    if ($action === 'add') {
        $name = typeCleanText($_POST['type_name'] ?? '');
        $group = typeCleanText($_POST['type_group'] ?? '');
        $description = typeCleanText($_POST['type_description'] ?? '');

        if ($name === '') {
            typeRespond('error', typeLang('type_name_required'), 422);
        }
        if ($group === '') {
            typeRespond('error', typeLang('type_group_required'), 422);
        }
        if (typeTextLength($name) > 255) {
            typeRespond('error', typeLang('type_name_too_long'), 422);
        }
        if (typeTextLength($group) > 255) {
            typeRespond('error', typeLang('type_group_too_long'), 422);
        }
        if (typeExistsByNameAndGroup($db, $name, $group)) {
            typeRespond('error', typeLang('type_duplicate_group'), 409);
        }

        $stmt = $db->prepare("INSERT INTO types (type_name, type_group, type_description) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $group, $description);
        $stmt->execute();
        $stmt->close();
        typeRespond('success', typeLang('insert_success'));
    }

    if ($action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = typeCleanText($_POST['type_name'] ?? '');
        $group = typeCleanText($_POST['type_group'] ?? '');
        $description = typeCleanText($_POST['type_description'] ?? '');

        if ($id <= 0) {
            typeRespond('error', typeLang('invalid_request'), 422);
        }
        if ($name === '') {
            typeRespond('error', typeLang('type_name_required'), 422);
        }
        if ($group === '') {
            typeRespond('error', typeLang('type_group_required'), 422);
        }
        if (typeTextLength($name) > 255) {
            typeRespond('error', typeLang('type_name_too_long'), 422);
        }
        if (typeTextLength($group) > 255) {
            typeRespond('error', typeLang('type_group_too_long'), 422);
        }

        $stmt = $db->prepare("SELECT id, type_group FROM types WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$current) {
            typeRespond('error', typeLang('type_not_found'), 404);
        }

        $currentGroup = (string) ($current['type_group'] ?? '');
        if ($group !== $currentGroup) {
            $dependency = typeDependencySummary($db, $id);
            if ((int) $dependency['total'] > 0) {
                typeRespond('error', typeLang('type_group_change_blocked'), 409, [
                    'dependency_count' => (int) $dependency['total'],
                    'dependencies' => $dependency['tables'],
                ]);
            }

            $groupContext = typeGroupContext($db, $currentGroup, $id);
            if ((int) $groupContext['other_types'] === 0 && (int) $groupContext['priority_lists'] > 0) {
                typeRespond('error', typeLang('type_group_priority_change_blocked'), 409, [
                    'priority_list_count' => (int) $groupContext['priority_lists'],
                ]);
            }
        }

        if (typeExistsByNameAndGroup($db, $name, $group, $id)) {
            typeRespond('error', typeLang('type_duplicate_group'), 409);
        }

        $stmt = $db->prepare("UPDATE types SET type_name = ?, type_group = ?, type_description = ? WHERE id = ?");
        $stmt->bind_param('sssi', $name, $group, $description, $id);
        $stmt->execute();
        $stmt->close();
        typeRespond('success', typeLang('update_success'));
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            typeRespond('error', typeLang('invalid_request'), 422);
        }

        $stmt = $db->prepare("SELECT id, type_group FROM types WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$current) {
            typeRespond('error', typeLang('type_not_found'), 404);
        }

        $dependency = typeDependencySummary($db, $id);
        if ((int) $dependency['total'] > 0) {
            $message = typeLang('type_delete_in_use');
            if (strpos($message, '%') !== false) {
                $message = sprintf($message, (int) $dependency['total']);
            }
            typeRespond('error', $message, 409, [
                'dependency_count' => (int) $dependency['total'],
                'dependencies' => $dependency['tables'],
            ]);
        }

        $group = (string) ($current['type_group'] ?? '');
        $groupContext = typeGroupContext($db, $group, $id);
        if ((int) $groupContext['other_types'] === 0 && (int) $groupContext['priority_lists'] > 0) {
            $message = typeLang('type_delete_group_in_use');
            if (strpos($message, '%') !== false) {
                $message = sprintf($message, (int) $groupContext['priority_lists']);
            }
            typeRespond('error', $message, 409, [
                'priority_list_count' => (int) $groupContext['priority_lists'],
            ]);
        }

        $stmt = $db->prepare("DELETE FROM types WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        typeRespond('success', typeLang('delete_success'));
    }
} catch (Throwable $e) {
    error_log('[type_manage] ' . $e->getMessage());
    typeRespond('error', typeLang('type_unexpected_error'), 500);
}

typeRespond('error', typeLang('invalid_request'), 400);
