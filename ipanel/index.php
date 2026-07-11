<?php

<<<<<<< HEAD
// index.php

require_once __DIR__ . "/../vendor/autoload.php";

use ICore\SessionTools;

SessionTools::init();

$logDir = __DIR__ . "/../logs";
$logFile = $logDir . "/admin_error.log";

if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

=======
//index.php 

require "../vendor/autoload.php";
SessionTools::init();

$logFile ="../logs/admin_error.log";

if (!file_exists("../logs")) {
    mkdir( "../logs", 0777, true);

}
>>>>>>> 5591029... some change
if (!file_exists($logFile)) {
    touch($logFile);
    chmod($logFile, 0666);
}

<<<<<<< HEAD
$isLocalhost = !empty($_SERVER["HTTP_HOST"])
    && strpos($_SERVER["HTTP_HOST"], "localhost") !== false;

error_reporting(E_ALL);
ini_set("display_errors", $isLocalhost ? "1" : "0");
ini_set("log_errors", "1");
ini_set("error_log", $logFile);

require __DIR__ . "/core/route.php";
exit;
=======
if (strpos($_SERVER["HTTP_HOST"], "localhost") !== false) {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    ini_set("log_errors", 1);
    ini_set("error_log", $logFile);
} else {
    error_reporting(E_ALL);
    ini_set("display_errors", 0);
    ini_set("log_errors", 1);
    ini_set("error_log", $logFile);
}

// اجرای روتر
require __DIR__ . "/core/route.php";
exit();
>>>>>>> 5591029... some change
