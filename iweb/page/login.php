<?php
//page / login

$objFileCaller = FileCaller::getInstance();
$_SESSION['pageTitle'] = [];
$objFileCaller->includeFileWithController('./iweb', 'user/', 'login');