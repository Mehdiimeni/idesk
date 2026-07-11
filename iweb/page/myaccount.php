<?php

use iweb\model\UserModel;
//page / myaccount

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);


if (!$userModel->loggedIn()) {
    echo "<script>window.location.replace('./login');</script>";
    exit();
}

$_SESSION['arrayComponents'] = array('lang_user');

$objFileCaller = FileCaller::getInstance();
$_SESSION['pageUserTitle'] = [];
function includeFiles($objFileCaller, $specificFile)
{
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_icon');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_css');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_top');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'menu');
    $objFileCaller->includeFileWithController('./iweb', 'user/', $specificFile);
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_footer');
    $objFileCaller->includeFileWithController('./iweb', 'global/', 'page_js');
}
<<<<<<< HEAD
$_SESSION['pageUserTitle'][] = 'my_account';
=======
$_SESSION['pageUserTitle'][] = 'profile';
>>>>>>> 5591029... some change
includeFiles($objFileCaller, 'myaccount');


