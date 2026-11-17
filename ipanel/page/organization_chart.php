<?php
use ipanel\model\AdminModel;
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$admin = new AdminModel($db);
if (!$admin->loggedIn()) { echo "<script>window.location.replace('./login');</script>"; exit; }
$rbacClass = new RBAC($db);
// Reuse the exact existing Admins subpart permission, without introducing a new permission.
if (!$rbacClass->checkPermissionSubpartByName('admins', 'members', 'systems')) { http_response_code(403); exit; }
$_SESSION['arrayComponents'] = ['table', 'main', 'lang'];
$_SESSION['pageTitle'] = ['admins'];
$objFileCaller = FileCaller::getInstance();
$objFileCaller->includeFileWithController('.', 'global/', 'page_icon');
$objFileCaller->includeFileWithController('.', 'global/', 'page_css');
$objFileCaller->includeFileWithController('.', 'global/', 'page_top');
$objFileCaller->includeFileWithController('.', 'global/', 'menu');
$objFileCaller->includeFileWithController('.', 'member/', 'organization_chart');
$objFileCaller->includeFileWithController('.', 'global/', 'page_footer');
$objFileCaller->includeFileWithController('.', 'global/', 'page_js');
