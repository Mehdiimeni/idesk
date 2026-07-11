<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once "../class/mysql.php";
    require_once "../class/config.php";

    if (!isset($_SESSION["user_id"])) {
        echo json_encode(['status' => 'error', 'message' => 'Session data not found']);
        exit;
    }

    $config = Configuration::getInstance();
    $database = Database::getInstance($config);
    $db = $database->getConnection();

    $admin_language = isset($_COOKIE['admin_language']) ? $_COOKIE['admin_language'] : $config->getConfig('defaultLanguage');
    require_once "../lang/{$admin_language}.php";
    define('_lang', $config->getLang($admin_language));

    try {
        if (isset($_POST['table_set'], $_POST['operation'], $_POST['id'])) {
            $table_set = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table_set']); 
            $operation = $_POST['operation'];
            $id = $_POST['id'];
            $description = isset($_POST['description']) ? $_POST['description'] : null;

            $stmt = $db->prepare("UPDATE `$table_set` SET status = ? WHERE id = ?");
            if ($stmt === false) {
                throw new Exception(_lang['query_preparation_failed']);
            }
            $stmt->bind_param("si", $operation,  $id);
            $stmt->execute();
            $stmt->close();

            $sqlQuery = "INSERT INTO status (`part_id`, `part_name`, `status_name`, `user_id`, `rbac_id`, `status_description`) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sqlQuery);
            if ($stmt === false) {
                throw new Exception(_lang['query_preparation_failed']);
            }

            $stmt->bind_param("isssis", $id, $table_set, $operation, $_SESSION["user_id"], $_SESSION["rbac_id"], $description);
            $executionResult = $stmt->execute();
            $stmt->close();

            if ($executionResult) {
                echo json_encode(['status' => 'success', 'message' => _lang['status_updated_success']]);
            } else {
                throw new Exception(_lang['query_execution_failed']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => _lang['invalid_parameters']]);
        }
    } catch (Exception $e) {
        if (isset($stmt) && $stmt !== false) {
            $stmt->close();
        }
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
