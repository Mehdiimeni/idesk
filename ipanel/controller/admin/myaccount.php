<?php

use ipanel\model\AdminModel;
///controller/admin/myaccount.php

$part_name = 'admin_profile';

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$admin = new AdminModel($db);
$dbHandler = new DatabaseHandler($db);
$uploadDir = '../irepository/profile/';
$uploadDirDb = './irepository/profile/';

$fileManager = new FileManager($db, $uploadDir);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_FILES['profile_image']) && !empty($_FILES['profile_image']) && ($_FILES['profile_image']['size']) > 0) {

        $uploadedFile = $fileManager->uploadFile($_FILES['profile_image']);


        $table_set = 'file_manage';

        $arrData = [
            'file_name' => $uploadedFile,
            'file_path' => $uploadDirDb,
            'file_title' => $_SESSION['name'],
            'admin_id' => $_SESSION['admin_id'],
            'part_id' => $_SESSION['admin_id'],
            'part_name' => 'admin_profile',

        ];


        if ($fileManager->getFileManageByPart($_SESSION['admin_id'], $part_name, '', $_SESSION['admin_id'])) {

            $whereCondition = "part_name = 'admin_profile' AND part_id = " . $_SESSION["admin_id"] . " AND  admin_id = " . $_SESSION["admin_id"];

            $dbHandler->updateData($table_set, $arrData, $whereCondition);
            @unlink($uploadDir . $_SESSION['profile_image']);



        } else {

            $dbHandler->insertData($table_set, $arrData);
        }

    }


    $table_set = 'admins';

    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];


    $arrData = [
        'name' => $name,
        'mobile' => $mobile,
        'email' => $email,
    ];



    if (isset($_POST['password']) && !empty($_POST['password'])) {

        $arrData['password'] = $_POST['password'];

    }

    $unique_fields = [
        'name',
        'email',
        'mobile'
    ];

    $whereCondition = 'id = ' . $_SESSION['admin_id'];
    $dbHandler->updateData($table_set, $arrData, $whereCondition);

    $message = _lang['need_login_to_change'];

    function redirectTo($location, $message)
    {
        echo "<script>alert('$message'); window.location.replace('$location');</script>";
    }

    $requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $config = Configuration::getInstance();
    $allowedHosts = $config->getConfig('allowedHosts');

    if (in_array($_SERVER['HTTP_HOST'], $allowedHosts) && strpos($requestUrl, '/voc/ipanel/') !== false) {
        redirectTo('./logout', $message);
    } else {
        redirectTo('./logout', $message);
    }


}