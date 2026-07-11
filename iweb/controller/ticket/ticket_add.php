<?php
///iweb/controller/ticket/tickets_add.php
use iweb\model\StructureModel;
use iweb\model\TicketModel;


$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$dbHandler = new DatabaseHandler($db);
$structureModel = new StructureModel($db);
$rbacClass = new RBAC($db);
$ticketsModel = new TicketModel($db);

$types = $structureModel->getTypesGroupedByTypeGroup();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['ticket_title'];
    $description = $_POST['ticket_description'];
    $type_id = $_POST['type_id'];
    $priority = $_POST['priority'];
    $indicator_number = $_POST['indicator_number'];

    if (!isset($_POST['ticket_title']) || $_POST['ticket_title'] == '') {
        header("Refresh:0; url=./tickets");
        exit;
    }

    $part_name = 'tickets';

    $table_set = 'tickets';

    $arrData = [
        'ticket_title' => $_POST['ticket_title'],
        'ticket_description' => $_POST['ticket_description'],
        'type_id' => $type_id,
        'priority' => $priority,
        'user_id' => $_SESSION['user_id'],
        'company_id' => $_SESSION['company_id'],
        'indicator_number' => $indicator_number

    ];

    $unique_fields = [
        ''
    ];

    $insertResult = $dbHandler->insertData($table_set, $arrData);

    $message = $insertResult['message'];
    $insert_id = $insertResult['insert_id'];

    $ticketsModel->checkStatusTable($insert_id, $table_set);

    // add ticket number
    $currentDate = new DateTime();

    $currentMonth = $currentDate->format('m');
    $ticketNumber = $_SESSION['company_id'] . $currentMonth .( (round($insert_id*2.7)) % 10000). random_int(10, 99);

    $arrData = [
        'ticket_number' => $ticketNumber
    ];

    $whereCondition = 'id = ' . $insert_id;
    $dbHandler->updateData($table_set, $arrData, $whereCondition);

    // add file 

    if (isset($_FILES['attach_file']) && ($_FILES['attach_file']['size']) > 0) {

        $uploadDir = './irepository/tickets/';
        $fileManager = new FileManager($db, $uploadDir);
        $uploadedFile = $fileManager->uploadFile($_FILES['attach_file']);


        $table_set = 'file_manage';

        $arrData = [
            'file_name' => $uploadedFile,
            'file_path' => $uploadDir,
            'file_title' => $_POST['ticket_title'],
            'user_id' => $_SESSION['user_id'],
            'part_id' => $insert_id,
            'part_name' => $part_name

        ];

        $unique_fields = [
            ''
        ];

        $insertResult = $dbHandler->insertData($table_set, $arrData);
        $message = $insertResult['message'];
        $insert_id = $insertResult['insert_id'];
    }

    header("Refresh:0; url=./tickets");
    exit;
}
