<?php
//page / login

$_SESSION['arrayComponents'] = array('');

$objFileCaller = FileCaller::getInstance();
$_SESSION['pageTitle'] = [];
$objFileCaller->includeFileWithController('.', 'admin/', 'logout');