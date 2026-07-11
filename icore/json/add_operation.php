<?php

require_once "../class/mysql.php";
require_once "../class/config.php";

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $rbacId = $data['rbac_id'];
    $operations = $data['operations'];

 

    $serializedOperations = serialize($operations);

   

    $sql = "UPDATE permissions_operation SET operation = ? WHERE rbac_id = ?";
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false]);
        exit;
    }

    $stmt->bind_param('si', $serializedOperations, $rbacId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $db->close();
}
