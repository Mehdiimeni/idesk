<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once "../class/mysql.php";
    require_once "../class/config.php";

    $config = Configuration::getInstance();
    $database = Database::getInstance($config);
    $db = $database->getConnection();
    
    $admin_language = isset($_COOKIE['admin_language']) ? $_COOKIE['admin_language'] : $config->getConfig('defaultLanguage');
    require_once "../lang/{$admin_language}.php";
    define('_lang', $config->getLang($admin_language));

    if (isset($_POST['tableId']) && isset($_POST['tableName'])) {
        $id = $_POST['tableId'];
        $tableName = $_POST['tableName'];

        try {
            $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $activityData = $result->fetch_assoc();
                $activityData['table_set'] = $tableName;
                echo json_encode($activityData);
            } else {
                echo json_encode(['error' => _lang['failed_execute']]);
            }

            $stmt->close();
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }

    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => _lang['invalid_request']]);
    }
}


?>