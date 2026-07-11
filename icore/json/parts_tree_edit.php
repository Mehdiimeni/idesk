<?php
require_once "../class/mysql.php";
require_once "../class/config.php";

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);

$rbac_id = $data['rbac_id'];
$adminSelections = $data['admin_selections'];
$userSelections = $data['user_selections'];

$allSelections = [];

if (count($adminSelections) > 0 && count($userSelections) > 0) {
    $allSelections = array_merge($adminSelections, $userSelections);
} elseif (count($adminSelections) > 0) {
    $allSelections = $adminSelections;
} elseif (count($userSelections) > 0) {
    $allSelections = $userSelections;
}


$stmtCheck = $db->prepare("SELECT COUNT(*) AS count FROM permissions_operation WHERE rbac_id = ?");
$stmtCheck->bind_param("i", $rbac_id);
$stmtCheck->execute();
$result = $stmtCheck->get_result();
$count = $result->fetch_assoc()['count'];

if ($count == 1) {
    $sqlQuery = "UPDATE permissions_operation SET parts = ? WHERE rbac_id = ?";
    $stmt = $db->prepare($sqlQuery);

    if ($stmt === false) {
        $response = ['status' => 'error', 'message' => 'Prepare statement failed'];
    } else {
        $serializedData = serialize($allSelections);
        $stmt->bind_param("si",  $serializedData, $rbac_id);
        $stmt->execute();
        $stmt->close();

        $response = ['status' => 'success', 'message' => 'Data updated successfully'];
    }
} else {
    $response = ['status' => 'error', 'message' => 'id not exists'];

    
}

header('Content-Type: application/json');
echo json_encode($response);
?>