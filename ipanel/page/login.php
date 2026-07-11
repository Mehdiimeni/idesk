<?php
//page / login

$objFileCaller = FileCaller::getInstance();
$_SESSION['pageTitle'] = [];
$objFileCaller->includeFileWithController('.', 'admin/', 'login');