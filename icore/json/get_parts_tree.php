<?php
require_once "../class/mysql.php";
require_once "../class/config.php";

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin_language = isset($_COOKIE['admin_language']) ? $_COOKIE['admin_language'] : $config->getConfig('defaultLanguage');
require_once "../lang/{$admin_language}.php";
define('_lang', $config->getLang($admin_language));

try {
    $type = isset($_GET['type']) ? $_GET['type'] : 'user';
    $id = isset($_GET['id']) && $_GET['id'] != '' ? $_GET['id'] : '0';

    if ($type === 'admin') {
        $groupTable = 'admins_groups';
        $partTable = 'admins_parts';
        $subpartTable = 'admins_subparts';
        $groupNameField = 'admins_groups_name';
        $partNameField = 'admins_parts_name';
        $groupIdField = 'admins_groups_id';
        $subpartNameField = 'admins_subparts_name';
        $partIdField = 'admins_parts_id';
        $prefix = 'a';
    } else {
        $groupTable = 'users_groups';
        $partTable = 'users_parts';
        $subpartTable = 'users_subparts';
        $groupNameField = 'users_groups_name';
        $partNameField = 'users_parts_name';
        $groupIdField = 'users_groups_id';
        $subpartNameField = 'users_subparts_name';
        $partIdField = 'users_parts_id';
        $prefix = 'u';
    }

    // اگر id وجود داشته باشد، مقادیر ذخیره‌شده را بارگذاری کنید
    $pers = [];
    if ($id > 0) {
        $stmtPers = $db->prepare("SELECT parts FROM permissions_operation WHERE id = ?");
        $stmtPers->bind_param("i", $id);
        $stmtPers->execute();
        $resultPers = $stmtPers->get_result();
        $pers = unserialize($resultPers->fetch_assoc()['parts']);
    }

    // تابع برای بررسی اینکه آیا گره باید تیک بخورد
    function isChecked($nodeId, $category, $pers)
    {
        foreach ($pers as $p) {
            if ($p['id'] === $nodeId && $p['category'] === $category) {
                return true;
            }
        }
        return false;
    }

    // دریافت داده‌های جدول groups
    $stmtGroups = $db->prepare("SELECT id, $groupNameField AS name FROM $groupTable ORDER BY id");
    $stmtGroups->execute();
    $resultGroups = $stmtGroups->get_result();
    $groups = $resultGroups->fetch_all(MYSQLI_ASSOC);

    // دریافت داده‌های جدول parts
    $stmtParts = $db->prepare("SELECT id, $partNameField AS name, $groupIdField AS group_id FROM $partTable ORDER BY group_id, id");
    $stmtParts->execute();
    $resultParts = $stmtParts->get_result();
    $parts = $resultParts->fetch_all(MYSQLI_ASSOC);

    // دریافت داده‌های جدول subparts
    $stmtSubparts = $db->prepare("SELECT id, $subpartNameField AS name, $partIdField AS part_id FROM $subpartTable ORDER BY part_id, id");
    $stmtSubparts->execute();
    $resultSubparts = $stmtSubparts->get_result();
    $subparts = $resultSubparts->fetch_all(MYSQLI_ASSOC);

    // تبدیل داده‌ها به فرمت سلسله‌مراتبی
    $tree = [];

    foreach ($groups as $group) {
        $groupNode = [
            'id' => $prefix . 'g' . $group['id'],
            'text' => _lang[$group['name']],
            'category' => 'group',
            'state' => ['selected' => isChecked($prefix . 'g' . $group['id'], 'group', $pers)],
            'children' => []

        ];

        foreach ($parts as $part) {
            if ($part['group_id'] == $group['id']) {
                $partNode = [
                    'id' => $prefix . 'p' . $part['id'],
                    'text' => isset(_lang[$part['name']]) ? _lang[$part['name']] : 'not define !', 
                    'category' => 'part',
                    'state' => ['selected' => isChecked($prefix . 'p' . $part['id'], 'part', $pers)],
                    'children' => []
                ];
        
                foreach ($subparts as $subpart) {
                    if ($subpart['part_id'] == $part['id']) {
                        $subpartNode = [
                            'id' => $prefix . 's' . $subpart['id'],
                            'text' => isset(_lang[$subpart['name']]) ? _lang[$subpart['name']] : 'not define !',
                            'category' => 'subpart',
                            'state' => ['selected' => isChecked($prefix . 's' . $subpart['id'], 'subpart', $pers)]
                        ];
                        $partNode['children'][] = $subpartNode;
                    }
                }
                $groupNode['children'][] = $partNode;
            }
        }
        
        $tree[] = $groupNode;
    }

    header('Content-Type: application/json');
    echo json_encode($tree);
} catch (mysqli_sql_exception $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
