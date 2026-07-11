<?php

use ipanel\model\AdminModel;
<<<<<<< HEAD
use ICore\SessionTools;

// controller/admin/logout.php

SessionTools::init();

$config = Configuration::getInstance();

=======
///controller/admin/logout.php
$config = Configuration::getInstance();
>>>>>>> 5591029... some change
$database = Database::getInstance($config);
$db = $database->getConnection();

$adminModel = new AdminModel($db);
<<<<<<< HEAD

$rememberToken = $_COOKIE['remember_admin_token'] ?? '';

try {

    if (!empty($rememberToken)) {
        $adminModel->deleteRememberToken($rememberToken);
    }

    $adminModel->admin_log('logout');

} catch (Throwable $e) {
    // Logout نباید به دلیل خطا متوقف شود.
}

setcookie(
    'remember_admin_token',
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
$adminModel->admin_log('logout');

if (session_status() == PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}

if (!headers_sent()) {
    header("Location: ./login");
} else {
    echo "<script>window.location.href='./login';</script>";
}

exit;

>>>>>>> 5591029... some change
