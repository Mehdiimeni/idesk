<?php

use iweb\model\UserModel;
///controller/user/myaccount.php
$part_name = 'user_profile';

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);
$dbHandler = new DatabaseHandler($db);
$uploadDir = './irepository/profile/';
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
            'user_id' => $_SESSION['user_id'],
            'part_id' => $_SESSION['user_id'],
            'part_name' => 'user_profile',

        ];


        if ($fileManager->getFileManageByPart($_SESSION['user_id'], $part_name, $_SESSION['user_id'])) {

            $whereCondition = "part_name = 'user_profile' AND part_id = " . $_SESSION["user_id"] . " AND  user_id = " . $_SESSION["user_id"];

            $dbHandler->updateData($table_set, $arrData, $whereCondition);
            @unlink($uploadDir . $_SESSION['profile_image']);
        } else {

            $dbHandler->insertData($table_set, $arrData);
        }
    }


    $table_set = 'users';

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



    $whereCondition = 'id = ' . $_SESSION['user_id'];
    $dbHandler->updateData($table_set, $arrData, $whereCondition);

    $message = _lang['need_login_to_change'];
    header("Refresh:0; url=./logout");
    exit;
}
