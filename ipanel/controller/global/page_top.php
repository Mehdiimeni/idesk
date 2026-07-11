<?php
///ipanel/controller/global/page_top.php
use ipanel\model\NotificationModel;
use ipanel\model\StructureModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$allLanguages = $config->getConfig('allLanguage');

$textToolsClass = TextTools::getInstance();

$notificationModel = new NotificationModel($db);
$rbacClass = new RBAC($db);

$structureModel = new StructureModel($db);


$allNewTicketsNoteUser = $notificationModel->noteCommentTicket(0, 1, 0, 2);
$allNewForwarderTicket = $notificationModel->noteForwarderTicket(0, 2);
$allNewCalendar = $notificationModel->noteCalendar(0);
$allNewMessages = $notificationModel->noteNewMessages();
$allNewImportantMessages = $notificationModel->noteNewImportantMessages();
$allNewChatMessages = $notificationModel->noteNewChatMessages($_SESSION['admin_id'], 'a');
$totalNote = $allNewTicketsNoteUser->num_rows + $allNewForwarderTicket->num_rows + $allNewCalendar->num_rows + $allNewMessages->num_rows + $allNewImportantMessages->num_rows;
$totalChatNote = count($allNewChatMessages);


$encryptorClass = new Encryptor($config->getConfig('encryptPanelKey'));

$company_profilesResult = $structureModel->getCompanies();

$companyData = [];

if ($company_profilesResult && $company_profilesResult->num_rows > 0) {
    while ($company = $company_profilesResult->fetch_assoc()) {
        $companyName = $company['company_name'] ?? '';
        $companyDevelopment = (int) ($company['company_development'] ?? 1);
        $companySupport = (int) ($company['company_support'] ?? 1);
        $deliveryDevelopment = (int) ($company['delivery_development'] ?? 1);
        $deliverySupport = (int) ($company['delivery_support'] ?? 1);

        $devStatusMessage = '';
        $supportStatusMessage = '';
        $devBackgroundColor = '';
        $supportBackgroundColor = '';

        if ($companyDevelopment === 0 && $deliveryDevelopment === 0) {
            $devStatusMessage = "توسعه توقف کامل";
            $devBackgroundColor = 'bg-danger text-white';
        } elseif ($companyDevelopment === 1 && $deliveryDevelopment === 0) {
            $devStatusMessage = "توسعه انجام - تحویل توقف";
            $devBackgroundColor = 'bg-warning text-dark';
        }
   
        if ($companySupport === 0 && $deliverySupport === 0) {
            $supportStatusMessage = "پشتیبانی توقف کامل";
            $supportBackgroundColor = 'bg-danger text-white';
        } elseif ($companySupport === 1 && $deliverySupport === 0) {
            $supportStatusMessage = "پشتیبانی انجام - تحویل توقف";
            $supportBackgroundColor = 'bg-warning text-dark';
        }

        if (!empty($devStatusMessage) || !empty($supportStatusMessage)) {
            $companyData[] = [
                'name' => $companyName,
                'devMessage' => $devStatusMessage,
                'supportMessage' => $supportStatusMessage,
                'devBgColor' => $devBackgroundColor,
                'supportBgColor' => $supportBackgroundColor,
            ];
        }
    }
}

