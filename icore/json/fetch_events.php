<?php 

require_once "../class/mysql.php";
require_once "../class/config.php";

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$sql = "SELECT * FROM calendar";
$result = $db->query($sql);

$events = array();

while ($row = $result->fetch_assoc()) {
    $events[] = array(
        'id' => $row['id'],
        'title' => $row['title'],
        'start' => $row['start_date'],
        'end' => $row['end_date'],
        'className' => $row['category']
    );
}

echo json_encode($events);