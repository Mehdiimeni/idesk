<?php
require_once "../class/mysql.php";
require_once "../class/config.php";

try {
    $config = Configuration::getInstance();
    $database = Database::getInstance($config);
    $db = $database->getConnection();

    $admin_language = $_COOKIE['admin_language'] ?? $config->getConfig('defaultLanguage');
    require_once "../lang/{$admin_language}.php";
    define('_lang', $config->getLang($admin_language));

    $request_id = $_POST['id'] ?? null;
    if (empty($request_id) || !is_numeric($request_id)) {
        throw new Exception("Invalid request ID.");
    }

    $sql = "UPDATE requests SET  request_view = 1 WHERE id = ?";
    $stmt = $db->prepare($sql);

    if ($stmt === false) {
        throw new Exception("Prepare failed: " . htmlspecialchars($db->error));
    }

    $stmt->bind_param('i', $request_id);

    if (!$stmt->execute()) {
        throw new Exception("Execution failed: " . htmlspecialchars($stmt->error));
    }

    echo "confirmation successful!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {

    if (isset($db)) {
        $db->close();
    }
}
