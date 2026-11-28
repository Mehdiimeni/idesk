<?php

use iweb\model\CustomerPortalService;
use iweb\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$customerPortalService = new CustomerPortalService($db);
$structureModel = new StructureModel($db);
$encryptorClass = new Encryptor($config->getConfig('encryptWebKey'));

if (!function_exists('customerPortalCsrfToken')) {
    function customerPortalCsrfToken(): string
    {
        if (empty($_SESSION['customer_portal_csrf'])) {
            $_SESSION['customer_portal_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['customer_portal_csrf'];
    }
}

if (!function_exists('customerPortalValidateCsrf')) {
    function customerPortalValidateCsrf(string $token): bool
    {
        return !empty($_SESSION['customer_portal_csrf']) && hash_equals($_SESSION['customer_portal_csrf'], $token);
    }
}

if (!function_exists('customerPortalRedirect')) {
    function customerPortalRedirect(string $url, string $type = 'success', string $message = ''): void
    {
        $_SESSION['customer_portal_flash'] = ['type' => $type, 'message' => $message];
        header('Location: ' . $url);
        exit;
    }
}

$customerPortalCsrf = customerPortalCsrfToken();
$customerPortalContext = $customerPortalService->getSessionContext();
$customerPortalFlash = $_SESSION['customer_portal_flash'] ?? null;
unset($_SESSION['customer_portal_flash']);

$customerPortalView = strtolower(trim((string) ($_GET['view'] ?? 'dashboard')));
$allowedPortalViews = ['dashboard', 'sections', 'users', 'drafts', 'new', 'draft', 'audit', 'reports', 'company_tickets', 'company_ticket'];
if (!in_array($customerPortalView, $allowedPortalViews, true)) {
    $customerPortalView = 'dashboard';
}

$customerPortalBlockedReason = null;
if (empty($customerPortalContext['company_workflow_enabled'])) {
    $customerPortalBlockedReason = 'CUSTOMER_PORTAL_DISABLED';
} elseif (empty($customerPortalContext['profile_valid'])) {
    $customerPortalBlockedReason = 'CUSTOMER_PORTAL_PROFILE_REQUIRED';
} elseif (empty($customerPortalContext['operational']) && in_array($customerPortalContext['portal_role'], [CustomerPortalService::ROLE_MANAGER, CustomerPortalService::ROLE_EXPERT], true)) {
    $customerPortalBlockedReason = 'CUSTOMER_PORTAL_SECTION_REQUIRED';
}

// Normalize read-only routes by role so direct GET tampering never exposes an
// irrelevant management view (backend authorization is still enforced separately).
if ($customerPortalBlockedReason === null) {
    $portalRole = (string) ($customerPortalContext['portal_role'] ?? '');
    if (in_array($customerPortalView, ['sections','company_tickets','company_ticket'], true) && $portalRole !== CustomerPortalService::ROLE_ADMIN) {
        $customerPortalView = 'dashboard';
    }
    if (in_array($customerPortalView, ['users','reports'], true)
        && !in_array($portalRole, [CustomerPortalService::ROLE_ADMIN, CustomerPortalService::ROLE_MANAGER], true)) {
        $customerPortalView = 'dashboard';
    }
    if ($customerPortalView === 'audit' && $portalRole !== CustomerPortalService::ROLE_ADMIN) {
        http_response_code(403);
        $customerPortalView = 'dashboard';
    }
    if ($customerPortalView === 'new' && empty($customerPortalContext['can_create_draft'])) {
        $customerPortalView = 'dashboard';
    }
}

// Secure file download. Scope is validated by service before the physical path is used.
if ($customerPortalBlockedReason === null && isset($_GET['download_file'], $_GET['draft_id'])) {
    $draftId = (int) $_GET['draft_id'];
    $fileId = (int) $_GET['download_file'];
    foreach ($customerPortalService->listDraftFiles($customerPortalContext, $draftId) as $portalFile) {
        if ((int) $portalFile['id'] === $fileId) {
            $realPath = $portalFile['file_path'] . $portalFile['file_name'];
            if (is_file($realPath)) {
                $fileManager = new FileManager($db, '');
                $fileManager->fileDownload($realPath);
            }
        }
    }
    http_response_code(404);
    exit;
}

// CSV export is available only for authenticated company admins and uses server-side company scope.
if ($customerPortalBlockedReason === null && $customerPortalView === 'company_tickets'
    && ($_GET['export'] ?? '') === 'csv'
    && ($customerPortalContext['portal_role'] ?? '') === CustomerPortalService::ROLE_ADMIN) {
    $filters = ['q' => (string)($_GET['q'] ?? ''), 'priority' => (string)($_GET['priority'] ?? '')];
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="company_tickets_' . date('Ymd_His') . '.csv"');
    header('Cache-Control: private, no-store');
    $output = fopen('php://output', 'w');
    fwrite($output, "\xEF\xBB\xBF");
    fputcsv($output, ['شماره تیکت', 'عنوان', 'تاریخ ایجاد', 'اولویت']);
    $customerPortalService->exportCompanyTicketsCsv($customerPortalContext, $filters, $output);
    fclose($output);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($customerPortalBlockedReason !== null) {
        customerPortalRedirect('./customer_portal', 'danger', $customerPortalBlockedReason);
    }
    if (!customerPortalValidateCsrf((string) ($_POST['csrf_token'] ?? ''))) {
        customerPortalRedirect('./customer_portal', 'danger', 'CSRF_INVALID');
    }

    $action = (string) ($_POST['cp_action'] ?? '');
    $returnView = (string) ($_POST['return_view'] ?? 'dashboard');
    $returnUrl = './customer_portal?view=' . urlencode($returnView);
    if (!empty($_POST['return_draft_id'])) {
        $returnUrl = './customer_portal?view=draft&draft_id=' . (int) $_POST['return_draft_id'];
    }

    try {
        switch ($action) {
            case 'section_create':
                $customerPortalService->createSection($customerPortalContext, (string) ($_POST['section_name'] ?? ''));
                customerPortalRedirect('./customer_portal?view=sections', 'success', 'SECTION_CREATED');
                break;

            case 'section_update':
                $customerPortalService->updateSection($customerPortalContext, (int) ($_POST['section_id'] ?? 0), (string) ($_POST['section_name'] ?? ''));
                customerPortalRedirect('./customer_portal?view=sections', 'success', 'SECTION_UPDATED');
                break;

            case 'section_toggle':
                $customerPortalService->setSectionActive($customerPortalContext, (int) ($_POST['section_id'] ?? 0), !empty($_POST['set_active']));
                customerPortalRedirect('./customer_portal?view=sections', 'success', 'SECTION_STATUS_UPDATED');
                break;

            case 'user_create':
                $customerPortalService->createPortalUser($customerPortalContext, [
                    'portal_role' => $_POST['portal_role'] ?? '',
                    'section_id' => $_POST['section_id'] ?? '',
                    'name' => $_POST['name'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'mobile' => $_POST['mobile'] ?? '',
                    'password' => $_POST['password'] ?? '',
                ]);
                customerPortalRedirect('./customer_portal?view=users', 'success', 'USER_CREATED');
                break;

            case 'user_update':
                $customerPortalService->updatePortalUser($customerPortalContext, (int) ($_POST['user_id'] ?? 0), [
                    'name' => $_POST['name'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'mobile' => $_POST['mobile'] ?? '',
                    'portal_role' => $_POST['portal_role'] ?? '',
                    'section_id' => $_POST['section_id'] ?? '',
                ]);
                customerPortalRedirect('./customer_portal?view=users', 'success', 'USER_UPDATED');
                break;

            case 'manager_finance_permission':
                if (($customerPortalContext['portal_role'] ?? '') !== CustomerPortalService::ROLE_ADMIN
                    || empty($customerPortalContext['profile_valid'])) {
                    throw new RuntimeException('PORTAL_PERMISSION_DENIED');
                }
                $managerId = (int) ($_POST['user_id'] ?? 0);
                $companyId = (int) $customerPortalContext['company_id'];
                // Validate target company and role before touching permissions.
                $stmt = $db->prepare("SELECT 1 FROM iweb_customer_portal_user_profiles WHERE user_id=? AND company_id=? AND portal_role='MANAGER' AND is_active=1 LIMIT 1");
                if (!$stmt) throw new RuntimeException('PORTAL_PERMISSION_DENIED');
                $stmt->bind_param('ii', $managerId, $companyId);
                $stmt->execute();
                $ok = (bool)$stmt->get_result()->fetch_assoc();
                $stmt->close();
                if (!$ok) throw new RuntimeException('PERMISSION_ROLE_MISMATCH');
                $customerPortalService->setPermissionByAdmin(
                    (int)$customerPortalContext['user_id'], $managerId,
                    'manager_finance_access', !empty($_POST['finance_allowed']), true
                );
                customerPortalRedirect('./customer_portal?view=users', 'success', 'USER_MANAGEMENT_PERMISSION_UPDATED');
                break;

            case 'user_toggle':
                $customerPortalService->setPortalUserActive($customerPortalContext, (int) ($_POST['user_id'] ?? 0), !empty($_POST['set_active']));
                customerPortalRedirect('./customer_portal?view=users', 'success', 'USER_STATUS_UPDATED');
                break;

            case 'assign_manager':
                $customerPortalService->assignManager($customerPortalContext, (int) ($_POST['section_id'] ?? 0), (int) ($_POST['manager_user_id'] ?? 0));
                customerPortalRedirect('./customer_portal?view=sections', 'success', 'MANAGER_ASSIGNED');
                break;

            case 'assign_expert':
                $customerPortalService->assignExpert($customerPortalContext, (int) ($_POST['expert_user_id'] ?? 0), (int) ($_POST['section_id'] ?? 0));
                customerPortalRedirect('./customer_portal?view=users', 'success', 'EXPERT_ASSIGNED');
                break;

            case 'draft_create':
                $draftId = $customerPortalService->createDraft($customerPortalContext, [
                    'title' => $_POST['title'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'type_id' => $_POST['type_id'] ?? 0,
                    'indicator_number' => $_POST['indicator_number'] ?? '',
                    'priority' => $_POST['priority'] ?? 'low',
                ]);
                if (isset($_FILES['attach_file']) && (int) ($_FILES['attach_file']['size'] ?? 0) > 0) {
                    $uploadDir = './irepository/tickets/';
                    $fileManager = new FileManager($db, $uploadDir);
                    $uploaded = $fileManager->uploadFile($_FILES['attach_file']);
                    if ($uploaded) {
                        $customerPortalService->registerDraftFile($customerPortalContext, $draftId, $uploaded, $uploadDir, (string) ($_POST['title'] ?? 'Draft attachment'));
                    }
                }
                customerPortalRedirect('./customer_portal?view=draft&draft_id=' . $draftId, 'success', 'DRAFT_CREATED');
                break;

            case 'draft_update':
                $draftId = (int) ($_POST['draft_id'] ?? 0);
                $customerPortalService->updateDraft($customerPortalContext, $draftId, [
                    'title' => $_POST['title'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'type_id' => $_POST['type_id'] ?? 0,
                    'indicator_number' => $_POST['indicator_number'] ?? '',
                    'priority' => $_POST['priority'] ?? 'low',
                ]);
                customerPortalRedirect('./customer_portal?view=draft&draft_id=' . $draftId, 'success', 'DRAFT_UPDATED');
                break;

            case 'draft_send_manager':
                $draftId = (int) ($_POST['draft_id'] ?? 0);
                $customerPortalService->sendDraftToManager($customerPortalContext, $draftId);
                customerPortalRedirect('./customer_portal?view=draft&draft_id=' . $draftId, 'success', 'DRAFT_SENT_TO_MANAGER');
                break;

            case 'draft_assign_expert':
                $draftId = (int) ($_POST['draft_id'] ?? 0);
                $customerPortalService->assignDraftToExpert($customerPortalContext, $draftId, (int) ($_POST['expert_user_id'] ?? 0));
                customerPortalRedirect('./customer_portal?view=draft&draft_id=' . $draftId, 'success', 'DRAFT_SENT_TO_EXPERT');
                break;

            case 'draft_cancel':
                $draftId = (int) ($_POST['draft_id'] ?? 0);
                $customerPortalService->cancelDraft($customerPortalContext, $draftId);
                customerPortalRedirect('./customer_portal?view=draft&draft_id=' . $draftId, 'success', 'DRAFT_CANCELLED');
                break;

            case 'draft_publish':
                $draftId = (int) ($_POST['draft_id'] ?? 0);
                $published = $customerPortalService->publishDraft($customerPortalContext, $draftId);
                $ticketEncrypted = $encryptorClass->encrypt((int) $published['ticket_id']);
                customerPortalRedirect('./tickets?ticket_id=' . urlencode($ticketEncrypted), 'success', 'DRAFT_PUBLISHED');
                break;

            case 'draft_comment':
                $draftId = (int) ($_POST['draft_id'] ?? 0);
                $customerPortalService->addDraftComment($customerPortalContext, $draftId, (string) ($_POST['comment_text'] ?? ''));
                customerPortalRedirect('./customer_portal?view=draft&draft_id=' . $draftId, 'success', 'COMMENT_CREATED');
                break;

            case 'draft_file_upload':
                $draftId = (int) ($_POST['draft_id'] ?? 0);
                if (!isset($_FILES['attach_file']) || (int) ($_FILES['attach_file']['size'] ?? 0) <= 0) {
                    throw new RuntimeException('FILE_REQUIRED');
                }
                $uploadDir = './irepository/tickets/';
                $fileManager = new FileManager($db, $uploadDir);
                $uploaded = $fileManager->uploadFile($_FILES['attach_file']);
                if (!$uploaded) {
                    throw new RuntimeException('FILE_UPLOAD_FAILED');
                }
                $title = trim((string) ($_POST['file_title'] ?? '')) ?: basename((string) ($_FILES['attach_file']['name'] ?? 'Attachment'));
                $customerPortalService->registerDraftFile($customerPortalContext, $draftId, $uploaded, $uploadDir, $title);
                customerPortalRedirect('./customer_portal?view=draft&draft_id=' . $draftId, 'success', 'FILE_UPLOADED');
                break;

            case 'draft_file_remove':
                $draftId = (int) ($_POST['draft_id'] ?? 0);
                $file = $customerPortalService->removeDraftFile($customerPortalContext, $draftId, (int) ($_POST['file_id'] ?? 0));
                if ($file) {
                    $path = $file['file_path'] . $file['file_name'];
                    if (is_file($path)) {
                        @unlink($path);
                    }
                }
                customerPortalRedirect('./customer_portal?view=draft&draft_id=' . $draftId, 'success', 'FILE_REMOVED');
                break;

            default:
                throw new RuntimeException('INVALID_ACTION');
        }
    } catch (Throwable $e) {
        customerPortalRedirect($returnUrl, 'danger', $e->getMessage());
    }
}

$customerPortalSections = [];
$customerPortalUsers = [];
$customerPortalDrafts = [];
$customerPortalDraft = null;
$customerPortalDraftComments = [];
$customerPortalDraftFiles = [];
$customerPortalDraftHistory = [];
$customerPortalExperts = [];
$customerPortalAudit = [];
$customerPortalAuditPage = null;
$customerPortalAuditSections = [];
$customerPortalCompanyReportOverview = null;
$customerPortalReport = null;
$customerPortalDashboard = null;
$customerPortalTypes = [];
$customerPortalCompanyTickets = null;
$customerPortalCompanyTicketDetail = null;

if ($customerPortalBlockedReason === null) {
    $companyId = (int) $customerPortalContext['company_id'];

    if (in_array($customerPortalView, ['dashboard'], true)) {
        $customerPortalDashboard = $customerPortalService->getPortalDashboard($customerPortalContext);
    }

    if ($customerPortalView === 'company_tickets' && $customerPortalContext['portal_role'] === CustomerPortalService::ROLE_ADMIN) {
        $customerPortalCompanyTickets = $customerPortalService->listCompanyTicketsPage(
            $customerPortalContext, (int) ($_GET['page_num'] ?? 1), 25,
            ['q' => (string)($_GET['q'] ?? ''), 'priority' => (string)($_GET['priority'] ?? '')]
        );
    }

    if ($customerPortalView === 'company_ticket' && $customerPortalContext['portal_role'] === CustomerPortalService::ROLE_ADMIN) {
        $ticketId = filter_input(INPUT_GET, 'ticket_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($ticketId !== false && $ticketId !== null) {
            $customerPortalCompanyTicketDetail = $customerPortalService->getCompanyTicketDetail(
                $customerPortalContext, (int) $ticketId
            );
        }
        if ($customerPortalCompanyTicketDetail === null) {
            http_response_code(404);
        }
    }

    if ($customerPortalView === 'sections' && $customerPortalContext['portal_role'] === CustomerPortalService::ROLE_ADMIN) {
        $customerPortalSections = $customerPortalService->listSections($companyId);
        $customerPortalUsers = $customerPortalService->listCompanyUsers($companyId);
    }

    if ($customerPortalView === 'users' && in_array($customerPortalContext['portal_role'], [CustomerPortalService::ROLE_ADMIN, CustomerPortalService::ROLE_MANAGER], true)) {
        $customerPortalUsers = $customerPortalService->listUsersForContext($customerPortalContext);
        $customerPortalSections = $customerPortalService->listSections($companyId);
        $customerPortalFinancePermissions = [];
        if ($customerPortalContext['portal_role'] === CustomerPortalService::ROLE_ADMIN) {
            $stmt = $db->prepare("SELECT p.user_id, p.is_allowed FROM iweb_customer_portal_permissions p JOIN iweb_customer_portal_user_profiles up ON up.user_id=p.user_id AND up.company_id=p.company_id AND up.portal_role='MANAGER' WHERE p.company_id=? AND p.permission_name='manager_finance_access'");
            if ($stmt) {
                $stmt->bind_param('i', $companyId);
                $stmt->execute();
                $rs = $stmt->get_result();
                while ($rs && ($pr = $rs->fetch_assoc())) $customerPortalFinancePermissions[(int)$pr['user_id']] = ((int)$pr['is_allowed']===1);
                $stmt->close();
            }
        }
    }

    if ($customerPortalView === 'drafts') {
        $statusFilter = strtoupper((string) ($_GET['status'] ?? ''));
        $customerPortalDrafts = $customerPortalService->listDrafts($customerPortalContext, $statusFilter);
    }

    if ($customerPortalView === 'new') {
        $customerPortalTypes = $structureModel->getTypesGroupedByTypeGroup();
    }

    if ($customerPortalView === 'draft') {
        $draftId = (int) ($_GET['draft_id'] ?? 0);
        $customerPortalDraft = $customerPortalService->getDraft($customerPortalContext, $draftId, true);
        if ($customerPortalDraft) {
            $customerPortalTypes = $structureModel->getTypesGroupedByTypeGroup();
            $customerPortalDraftComments = $customerPortalService->listDraftComments($customerPortalContext, $draftId);
            $customerPortalDraftFiles = $customerPortalService->listDraftFiles($customerPortalContext, $draftId);
            if ($customerPortalContext['portal_role'] === CustomerPortalService::ROLE_ADMIN) {
                $customerPortalDraftHistory = $customerPortalService->getDraftHistory($customerPortalContext, $draftId);
            }
            if ($customerPortalContext['portal_role'] === CustomerPortalService::ROLE_MANAGER) {
                $customerPortalExperts = $customerPortalService->listActiveExpertsForSection($companyId, (int) $customerPortalContext['section_id']);
            }
        }
    }

    if ($customerPortalView === 'audit' && $customerPortalContext['portal_role'] === CustomerPortalService::ROLE_ADMIN) {
        $customerPortalAuditPage = $customerPortalService->listAuditPage($customerPortalContext, [
            'section_id' => (int) ($_GET['section_id'] ?? 0),
            'event' => (string) ($_GET['event'] ?? ''),
            'date_from' => (string) ($_GET['date_from'] ?? ''),
            'date_to' => (string) ($_GET['date_to'] ?? ''),
        ], (int) ($_GET['audit_page'] ?? 1));
        $customerPortalAudit = $customerPortalAuditPage['rows'];
        if ($customerPortalContext['portal_role'] === CustomerPortalService::ROLE_ADMIN) {
            $customerPortalAuditSections = $customerPortalService->listSections($companyId);
        }
    }

    if ($customerPortalView === 'reports' && in_array($customerPortalContext['portal_role'], [CustomerPortalService::ROLE_ADMIN, CustomerPortalService::ROLE_MANAGER], true)) {
        $customerPortalReport = $customerPortalService->getReport($customerPortalContext);
        if ($customerPortalContext['portal_role'] === CustomerPortalService::ROLE_ADMIN) {
            $customerPortalCompanyReportOverview = $customerPortalService->getCompanyTicketOverview($customerPortalContext);
        }
    }
}
