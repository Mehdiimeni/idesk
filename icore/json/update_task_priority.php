<?php

require_once "../class/mysql.php";
require_once "../class/config.php";

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $taskId = $_POST['id'];
    $newPriority = $_POST['priority'];

    $stmt = $db->prepare("UPDATE kanban_board SET board_tag_id = ? WHERE id = ?");
    $stmt->bind_param("si", $newPriority, $taskId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $db->close();
}
?>