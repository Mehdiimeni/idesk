<?php
<<<<<<< HEAD

use iweb\model\UserModel;
use ICore\SessionTools;

// controller/user/logout.php

SessionTools::init();
=======
>>>>>>> 5591029... some change

use iweb\model\UserModel;
///controller/user/logout.php
$config = Configuration::getInstance();
<<<<<<< HEAD

=======
>>>>>>> 5591029... some change
$database = Database::getInstance($config);
$db = $database->getConnection();

$userModel = new UserModel($db);
<<<<<<< HEAD

$rememberToken = $_COOKIE['remember_user_token'] ?? '';

try {
    if (!empty($rememberToken)) {
        $userModel->deleteRememberToken($rememberToken);
    }

    $userModel->user_log('logout');
} catch (Throwable $e) {
    // Logout نباید به خاطر خطای لاگ یا توکن متوقف شود
}

setcookie(
    'remember_user_token',
    '',
    time() - 3600,
    '/',
    '',
    SessionTools::isHttps(),
    true
);

SessionTools::destroy();

header('Location: ./login');
exit;
=======
$userModel->user_log('logout');

session_unset();
session_destroy();

if (!headers_sent()) {
    header("Location: ./login");
    exit;
} else {
    echo "<script>window.location.href='./login';</script>";
    exit;
}

>>>>>>> 5591029... some change
