<?php
//index.php 

require __DIR__ . "/vendor/autoload.php";
SessionTools::init();
$logFile = __DIR__ . "/logs/user_error.log";

if (!file_exists(__DIR__ . "/logs")) {
    mkdir(__DIR__ . "/logs", 0777, true);
}

if (!file_exists($logFile)) {
    touch($logFile);
    chmod($logFile, 0666);
}

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


require __DIR__ . "/iweb/core/route.php";
exit();





