<?php

if (isset($_POST['id'], $_POST['table'])) {
    require_once "../class/mysql.php";
    require_once "../class/config.php";

    $config = Configuration::getInstance();
    $database = Database::getInstance($config);
    $db = $database->getConnection();

    $admin_language = isset($_COOKIE['admin_language']) ? $_COOKIE['admin_language'] : $config->getConfig('defaultLanguage');
    require_once "../lang/{$admin_language}.php";
    define('_lang', $config->getLang($admin_language));

    $itemId = htmlspecialchars(strip_tags($_POST['id']));
    $tableName = htmlspecialchars(strip_tags($_POST['table']));

    try {
        $stmt = $db->prepare("UPDATE " . $tableName . " SET status = 'Inactive' WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => _lang['inactivate_success']]);
    } catch (Exception $e) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['status' => 'error', 'message' => _lang['error_inactivate'] . $e->getMessage()]);
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['status' => 'error', 'message' => _lang['invalid_request_parameters']]);
}




?>