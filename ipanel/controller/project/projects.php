<?php

use ipanel\model\AdminModel;
use ipanel\model\CommentModel;
use ipanel\model\ProjectManagerModel;
///controller/project/projects.php

// general class
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$rbacClass = new RBAC($db);
$textToolsClass = TextTools::getInstance();
$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));

// admin model
$projectsModel = new ProjectManagerModel($db);
$adminModel = new AdminModel($db);
$commentModel = new CommentModel($db);

// all projects
$allProjects = $projectsModel->getAllProjects();
