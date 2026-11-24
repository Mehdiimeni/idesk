<?php

namespace iweb\model;

use RuntimeException;
use Throwable;

/**
 * Customer Portal business layer.
 *
 * Security principles:
 * - Company/Section scope is always derived server-side.
 * - Missing permissions are DENY.
 * - Feature flag is company-scoped and missing settings means disabled.
 * - Legacy ticket tables are not changed until transactional publish.
 */
class CustomerPortalService
{
    public const ROLE_ADMIN = 'ADMIN';
    public const ROLE_MANAGER = 'MANAGER';
    public const ROLE_EXPERT = 'EXPERT';

    public const STATUS_EXPERT_DRAFT = 'EXPERT_DRAFT';
    public const STATUS_MANAGER_REVIEW = 'MANAGER_REVIEW';
    public const STATUS_EXPERT_REVISION = 'EXPERT_REVISION';
    public const STATUS_SENT = 'SENT_TO_INTEK';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const PERMISSIONS = [
        'customer_admin_create_manager',
        'customer_admin_create_expert',
        'manager_create_expert',
        'manager_create_ticket_with_experts',
        'manager_return_ticket_to_expert',
        'customer_admin_assign_manager_to_section',
        'manager_assign_unassigned_expert',
        'customer_admin_reassign_expert',
        'manager_finance_access',
    ];

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getSessionContext(): array
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $sessionCompanyId = (int) ($_SESSION['company_id'] ?? 0);

        if ($userId <= 0 || $sessionCompanyId <= 0) {
            return $this->emptyContext($sessionCompanyId);
        }

        return $this->getContext($userId, $sessionCompanyId);
    }

    public function getContext(int $userId, int $sessionCompanyId): array
    {
        $context = $this->emptyContext($sessionCompanyId);
        $context['user_id'] = $userId;

        $settings = $this->getCompanySettings($sessionCompanyId);
        $context['settings'] = $settings;
        $context['company_workflow_enabled'] = !empty($settings['customer_portal_workflow_enabled']);

        $sql = "
            SELECT p.id AS profile_id,
                   p.user_id,
                   p.company_id,
                   p.portal_role,
                   p.section_id,
                   p.is_active AS profile_active,
                   u.status AS user_status,
                   s.name AS section_name,
                   s.is_active AS section_active
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id = p.user_id
            LEFT JOIN iweb_customer_portal_sections s ON s.id = p.section_id
            WHERE p.user_id = ?
            LIMIT 1
        ";
        try {
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) return $context;
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $profile = $result ? $result->fetch_assoc() : null;
            if ($result) $result->free();
            $stmt->close();
        } catch (Throwable $e) {
            return $context;
        }
        if (!$profile) return $context;

        $context = array_merge($context, $profile);
        $context['portal_role'] = strtoupper((string) $context['portal_role']);
        $context['profile_active'] = (int) $context['profile_active'];
        $context['section_active'] = $context['section_id'] === null ? null : (int) $context['section_active'];
        $context['profile_company_matches_session'] = ((int) $context['company_id'] === $sessionCompanyId);
        $context['profile_valid'] = $context['profile_company_matches_session']
            && $context['profile_active'] === 1
            && strcasecmp((string) ($context['user_status'] ?? ''), 'Active') === 0;
        $context['operational'] = $context['profile_valid'] && $context['company_workflow_enabled'];

        if (in_array($context['portal_role'], [self::ROLE_MANAGER, self::ROLE_EXPERT], true)) {
            $context['operational'] = $context['operational']
                && !empty($context['section_id'])
                && (int) $context['section_active'] === 1;
        }

        $context['permissions'] = $this->getUserPermissions($userId, $sessionCompanyId);
        $context['can_create_draft'] = $this->calculateCanCreateDraft($context);

        return $context;
    }

    private function emptyContext(int $companyId = 0): array
    {
        return [
            'user_id' => 0,
            'company_id' => $companyId,
            'portal_role' => null,
            'section_id' => null,
            'section_name' => null,
            'profile_active' => 0,
            'user_status' => null,
            'section_active' => null,
            'profile_valid' => false,
            'profile_company_matches_session' => false,
            'company_workflow_enabled' => false,
            'operational' => false,
            'permissions' => [],
            'settings' => $this->defaultSettings($companyId),
            'can_create_draft' => false,
        ];
    }

    private function defaultSettings(int $companyId): array
    {
        return [
            'company_id' => $companyId,
            'customer_portal_workflow_enabled' => 0,
            'portal_unit_id' => null,
            'admin_rbac_id' => null,
            'manager_rbac_id' => null,
            'expert_rbac_id' => null,
            'draft_aging_days' => 14,
        ];
    }

    public function getCompanySettings(int $companyId): array
    {
        if ($companyId <= 0) {
            return $this->defaultSettings($companyId);
        }
        try {
            $stmt = $this->conn->prepare("SELECT * FROM iweb_customer_portal_company_settings WHERE company_id = ? LIMIT 1");
            if ($stmt === false) {
                return $this->defaultSettings($companyId);
            }
            $stmt->bind_param('i', $companyId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result ? $result->fetch_assoc() : null;
            if ($result) $result->free();
            $stmt->close();
            if (!$row) return $this->defaultSettings($companyId);
            $row['customer_portal_workflow_enabled'] = (int)$row['customer_portal_workflow_enabled'];
            $row['draft_aging_days'] = max(1, (int)$row['draft_aging_days']);
            return $row;
        } catch (Throwable $e) {
            // Safe staged deployment: missing new tables means the feature is OFF.
            return $this->defaultSettings($companyId);
        }
    }

    public function requireContext(array $roles = []): array
    {
        $context = $this->getSessionContext();
        if (!$context['company_workflow_enabled']) {
            throw new RuntimeException('CUSTOMER_PORTAL_DISABLED');
        }
        if (!$context['profile_valid']) {
            throw new RuntimeException('CUSTOMER_PORTAL_PROFILE_REQUIRED');
        }
        if (!$context['operational']) {
            throw new RuntimeException('CUSTOMER_PORTAL_PROFILE_NOT_OPERATIONAL');
        }
        if ($roles && !in_array($context['portal_role'], $roles, true)) {
            throw new RuntimeException('CUSTOMER_PORTAL_ACCESS_DENIED');
        }
        return $context;
    }

    public function getUserPermissions(int $userId, int $companyId): array
    {
        $permissions=[];
        try {
            $stmt=$this->conn->prepare("SELECT permission_name,is_allowed FROM iweb_customer_portal_permissions WHERE user_id=? AND company_id=?");
            if ($stmt===false) return $permissions;
            $stmt->bind_param('ii',$userId,$companyId);
            $stmt->execute();
            $result=$stmt->get_result();
            while ($result && ($row=$result->fetch_assoc())) {
                $permissions[$row['permission_name']] = ((int)$row['is_allowed']===1);
            }
            if ($result) $result->free();
            $stmt->close();
        } catch (Throwable $e) {
            return [];
        }
        return $permissions;
    }

    public function hasPermission(array $context, string $permission): bool
    {
        if (!in_array($permission, self::PERMISSIONS, true)) {
            return false;
        }
        return !empty($context['permissions'][$permission]);
    }

    private function calculateCanCreateDraft(array $context): bool
    {
        if (empty($context['operational'])) {
            return false;
        }
        if ($context['portal_role'] === self::ROLE_EXPERT) {
            return $this->getActiveManagerForSection((int) $context['section_id']) !== null;
        }
        if ($context['portal_role'] !== self::ROLE_MANAGER) {
            return false;
        }

        $sectionId = (int) $context['section_id'];
        $activeManager = $this->getActiveManagerForSection($sectionId);
        if (!$activeManager || (int) $activeManager['user_id'] !== (int) $context['user_id']) {
            return false;
        }
        $activeExperts = $this->countActiveExperts($sectionId);
        return $activeExperts === 0 || $this->hasPermission($context, 'manager_create_ticket_with_experts');
    }

    public function listSections(int $companyId): array
    {
        $stmt = $this->conn->prepare("
            SELECT s.*,
                   m.user_id AS manager_user_id,
                   mu.name AS manager_name,
                   COALESCE(ec.expert_count, 0) AS expert_count
            FROM iweb_customer_portal_sections s
            LEFT JOIN (
                SELECT p.company_id, p.section_id, MIN(p.user_id) AS user_id
                FROM iweb_customer_portal_user_profiles p
                INNER JOIN users active_manager_user
                    ON active_manager_user.id = p.user_id
                   AND active_manager_user.status = 'Active'
                WHERE p.portal_role = 'MANAGER'
                  AND p.is_active = 1
                  AND p.section_id IS NOT NULL
                GROUP BY p.company_id, p.section_id
            ) m ON m.section_id = s.id AND m.company_id = s.company_id
            LEFT JOIN users mu ON mu.id = m.user_id
            LEFT JOIN (
                SELECT section_id, COUNT(*) AS expert_count
                FROM iweb_customer_portal_user_profiles p
                INNER JOIN users u ON u.id = p.user_id AND u.status = 'Active'
                WHERE p.portal_role = 'EXPERT' AND p.is_active = 1
                GROUP BY section_id
            ) ec ON ec.section_id = s.id
            WHERE s.company_id = ?
            ORDER BY s.is_active DESC, s.name ASC, s.id ASC
        ");
        if ($stmt === false) {
            return [];
        }
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $row['id'] = (int) $row['id'];
            $row['is_active'] = (int) $row['is_active'];
            $row['expert_count'] = (int) $row['expert_count'];
            $rows[] = $row;
        }
        if ($result) {
            $result->free();
        }
        $stmt->close();
        return $rows;
    }

    public function createSection(array $context, string $name): int
    {
        $this->assertRole($context, self::ROLE_ADMIN);
        $name = trim($name);
        if ($name === '' || mb_strlen($name, 'UTF-8') > 150) {
            throw new RuntimeException('INVALID_SECTION_NAME');
        }

        $stmt = $this->conn->prepare("
            INSERT INTO iweb_customer_portal_sections
            (company_id, name, is_active, created_by, updated_by)
            VALUES (?, ?, 1, ?, ?)
        ");
        if ($stmt === false) {
            throw new RuntimeException('SECTION_CREATE_PREPARE_FAILED');
        }
        $companyId = (int) $context['company_id'];
        $userId = (int) $context['user_id'];
        $stmt->bind_param('isii', $companyId, $name, $userId, $userId);
        if (!$stmt->execute()) {
            $error = $stmt->errno === 1062 ? 'SECTION_NAME_DUPLICATE' : 'SECTION_CREATE_FAILED';
            $stmt->close();
            throw new RuntimeException($error);
        }
        $id = (int) $stmt->insert_id;
        $stmt->close();
        $this->auditUser($context, 'SECTION_CREATED', 'SECTION', $id, null, null, ['name' => $name, 'is_active' => 1], $id);
        return $id;
    }

    public function updateSection(array $context, int $sectionId, string $name): void
    {
        $this->assertRole($context, self::ROLE_ADMIN);
        $section = $this->getSectionInCompany($sectionId, (int) $context['company_id']);
        if (!$section) {
            throw new RuntimeException('SECTION_NOT_FOUND');
        }
        $name = trim($name);
        if ($name === '' || mb_strlen($name, 'UTF-8') > 150) {
            throw new RuntimeException('INVALID_SECTION_NAME');
        }
        $stmt = $this->conn->prepare("UPDATE iweb_customer_portal_sections SET name = ?, updated_by = ? WHERE id = ? AND company_id = ?");
        $userId = (int) $context['user_id'];
        $companyId = (int) $context['company_id'];
        $stmt->bind_param('siii', $name, $userId, $sectionId, $companyId);
        if (!$stmt->execute()) {
            $error = $stmt->errno === 1062 ? 'SECTION_NAME_DUPLICATE' : 'SECTION_UPDATE_FAILED';
            $stmt->close();
            throw new RuntimeException($error);
        }
        $stmt->close();
        $this->auditUser($context, 'SECTION_UPDATED', 'SECTION', $sectionId, null, ['name' => $section['name']], ['name' => $name], $sectionId);
    }

    public function setSectionActive(array $context, int $sectionId, bool $active): void
    {
        $this->assertRole($context, self::ROLE_ADMIN);
        $section = $this->getSectionInCompany($sectionId, (int) $context['company_id']);
        if (!$section) {
            throw new RuntimeException('SECTION_NOT_FOUND');
        }

        if (!$active) {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) AS cnt
                FROM iweb_customer_portal_ticket_drafts
                WHERE section_id = ? AND workflow_status NOT IN ('SENT_TO_INTEK','CANCELLED')
            ");
            $stmt->bind_param('i', $sectionId);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ((int) ($row['cnt'] ?? 0) > 0) {
                throw new RuntimeException('SECTION_HAS_OPEN_DRAFTS');
            }
        }

        $value = $active ? 1 : 0;
        $userId = (int) $context['user_id'];
        $companyId = (int) $context['company_id'];
        $stmt = $this->conn->prepare("UPDATE iweb_customer_portal_sections SET is_active = ?, updated_by = ? WHERE id = ? AND company_id = ?");
        $stmt->bind_param('iiii', $value, $userId, $sectionId, $companyId);
        $stmt->execute();
        $stmt->close();
        $this->auditUser($context, $active ? 'SECTION_ACTIVATED' : 'SECTION_DEACTIVATED', 'SECTION', $sectionId, null, ['is_active' => (int) $section['is_active']], ['is_active' => $value], $sectionId);
    }

    public function listCompanyUsers(int $companyId): array
    {
        $stmt = $this->conn->prepare("
            SELECT p.id AS profile_id, p.user_id, p.company_id, p.portal_role, p.section_id,
                   p.is_active AS profile_active,
                   u.name, u.email, u.mobile, u.status, u.unit_id, u.rbac_id,
                   s.name AS section_name,
                   COALESCE(perm.permission_count,0) AS permission_count
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id = p.user_id
            LEFT JOIN iweb_customer_portal_sections s ON s.id = p.section_id
            LEFT JOIN (
                SELECT user_id, COUNT(*) AS permission_count
                FROM iweb_customer_portal_permissions
                WHERE is_allowed = 1
                GROUP BY user_id
            ) perm ON perm.user_id = p.user_id
            WHERE p.company_id = ?
            ORDER BY FIELD(p.portal_role,'ADMIN','MANAGER','EXPERT'), s.name, u.name
        ");
        if ($stmt === false) {
            return [];
        }
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $row['user_id'] = (int) $row['user_id'];
            $row['profile_active'] = (int) $row['profile_active'];
            $row['permission_count'] = (int) $row['permission_count'];
            $rows[] = $row;
        }
        if ($result) {
            $result->free();
        }
        $stmt->close();
        return $rows;
    }

    public function listUsersForContext(array $context): array
    {
        $companyId = (int) ($context['company_id'] ?? 0);
        if ($companyId <= 0) {
            return [];
        }
        if (($context['portal_role'] ?? null) === self::ROLE_ADMIN) {
            return $this->listCompanyUsers($companyId);
        }
        if (($context['portal_role'] ?? null) !== self::ROLE_MANAGER || empty($context['section_id'])) {
            return [];
        }

        $sectionId = (int) $context['section_id'];
        $includeUnassigned = $this->hasPermission($context, 'manager_assign_unassigned_expert');
        $sql = "
            SELECT p.id AS profile_id, p.user_id, p.company_id, p.portal_role, p.section_id,
                   p.is_active AS profile_active,
                   u.name, u.email, u.mobile, u.status, u.unit_id, u.rbac_id,
                   s.name AS section_name,
                   COALESCE(perm.permission_count,0) AS permission_count
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id=p.user_id
            LEFT JOIN iweb_customer_portal_sections s ON s.id=p.section_id
            LEFT JOIN (
                SELECT user_id, COUNT(*) AS permission_count
                FROM iweb_customer_portal_permissions
                WHERE is_allowed=1
                GROUP BY user_id
            ) perm ON perm.user_id=p.user_id
            WHERE p.company_id=? AND p.portal_role='EXPERT'
              AND (p.section_id=?" . ($includeUnassigned ? " OR p.section_id IS NULL" : "") . ")
            ORDER BY CASE WHEN p.section_id IS NULL THEN 1 ELSE 0 END, u.name
        ";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return [];
        }
        $stmt->bind_param('ii', $companyId, $sectionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows=[];
        while ($result && ($row=$result->fetch_assoc())) {
            $row['user_id']=(int)$row['user_id'];
            $row['profile_active']=(int)$row['profile_active'];
            $row['permission_count']=(int)$row['permission_count'];
            $rows[]=$row;
        }
        if ($result) $result->free();
        $stmt->close();
        return $rows;
    }

    public function createPortalUser(array $context, array $data): int
    {
        $role = strtoupper(trim((string) ($data['portal_role'] ?? '')));
        if (!in_array($role, [self::ROLE_MANAGER, self::ROLE_EXPERT], true)) {
            throw new RuntimeException('INVALID_PORTAL_ROLE');
        }

        if ($context['portal_role'] === self::ROLE_ADMIN) {
            $required = $role === self::ROLE_MANAGER ? 'customer_admin_create_manager' : 'customer_admin_create_expert';
            if (!$this->hasPermission($context, $required)) {
                throw new RuntimeException('PORTAL_PERMISSION_DENIED');
            }
        } elseif ($context['portal_role'] === self::ROLE_MANAGER) {
            if ($role !== self::ROLE_EXPERT || !$this->hasPermission($context, 'manager_create_expert')) {
                throw new RuntimeException('PORTAL_PERMISSION_DENIED');
            }
        } else {
            throw new RuntimeException('PORTAL_PERMISSION_DENIED');
        }

        $settings = $context['settings'];
        $unitId = (int) ($settings['portal_unit_id'] ?? 0);
        $rbacId = (int) ($role === self::ROLE_MANAGER ? ($settings['manager_rbac_id'] ?? 0) : ($settings['expert_rbac_id'] ?? 0));
        if ($unitId <= 0 || $rbacId <= 0 || !$this->validateLegacyMapping((int) $context['company_id'], $unitId, $rbacId)) {
            throw new RuntimeException('PORTAL_LEGACY_MAPPING_NOT_CONFIGURED');
        }

        $sectionId = isset($data['section_id']) && $data['section_id'] !== '' ? (int) $data['section_id'] : 0;
        if ($context['portal_role'] === self::ROLE_MANAGER && $role === self::ROLE_EXPERT) {
            $sectionId = (int) $context['section_id'];
        }
        if ($sectionId <= 0) {
            throw new RuntimeException('SECTION_REQUIRED');
        }
        $section = $this->getSectionInCompany($sectionId, (int) $context['company_id']);
        if (!$section || (int) $section['is_active'] !== 1) {
            throw new RuntimeException('SECTION_NOT_ACTIVE');
        }
        if ($role === self::ROLE_MANAGER && $this->getActiveManagerForSection($sectionId) !== null) {
            throw new RuntimeException('SECTION_ALREADY_HAS_MANAGER');
        }
        if ($role === self::ROLE_EXPERT && $this->getActiveManagerForSection($sectionId) === null) {
            throw new RuntimeException('SECTION_MANAGER_REQUIRED');
        }

        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $mobile = trim((string) ($data['mobile'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        if ($name === ''
            || mb_strlen($name, 'UTF-8') > 200
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || strlen($email) > 200
            || mb_strlen($mobile, 'UTF-8') > 25
            || strlen($password) < 8
            || strlen($password) > 128) {
            throw new RuntimeException('INVALID_USER_DATA');
        }

        $this->conn->begin_transaction();
        try {
            $check = $this->conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $check->bind_param('s', $email);
            $check->execute();
            $exists = $check->get_result()->num_rows > 0;
            $check->close();
            if ($exists) {
                throw new RuntimeException('USER_EMAIL_EXISTS');
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $legacyRole = 'member';
            $status = 'Active';
            $stmt = $this->conn->prepare("
                INSERT INTO users (email,mobile,password,name,role,status,unit_id,rbac_id)
                VALUES (?,?,?,?,?,?,?,?)
            ");
            $stmt->bind_param('ssssssii', $email, $mobile, $passwordHash, $name, $legacyRole, $status, $unitId, $rbacId);
            if (!$stmt->execute()) {
                throw new RuntimeException('USER_CREATE_FAILED');
            }
            $newUserId = (int) $stmt->insert_id;
            $stmt->close();

            $companyId = (int) $context['company_id'];
            $createdBy = (int) $context['user_id'];
            $stmt = $this->conn->prepare("
                INSERT INTO iweb_customer_portal_user_profiles
                (user_id,company_id,portal_role,section_id,is_active,created_by,updated_by)
                VALUES (?,?,?,?,1,?,?)
            ");
            $stmt->bind_param('iisiii', $newUserId, $companyId, $role, $sectionId, $createdBy, $createdBy);
            if (!$stmt->execute()) {
                throw new RuntimeException('PORTAL_PROFILE_CREATE_FAILED');
            }
            $stmt->close();

            $this->auditUser($context, 'USER_CREATED', 'USER', $newUserId, $newUserId, null, [
                'name' => $name,
                'email' => $email,
                'portal_role' => $role,
                'section_id' => $sectionId,
            ], $sectionId);
            $this->auditUser($context, $role === self::ROLE_MANAGER ? 'MANAGER_CREATED' : 'EXPERT_CREATED', 'USER', $newUserId, $newUserId, null, [
                'portal_role' => $role,
                'section_id' => $sectionId,
            ], $sectionId);

            $this->conn->commit();
            return $newUserId;
        } catch (Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function updatePortalUser(array $context, int $targetUserId, array $data): void
    {
        $this->assertRole($context, self::ROLE_ADMIN);
        $companyId = (int) $context['company_id'];
        $this->conn->begin_transaction();
        try {
            // Lock the target first; a company/role change racing with this edit must not bypass scope.
            $stmt = $this->conn->prepare("
                SELECT p.section_id, p.portal_role, p.is_active AS profile_active, u.name, u.email, u.mobile, u.status, u.unit_id, u.rbac_id
                FROM iweb_customer_portal_user_profiles p
                INNER JOIN users u ON u.id=p.user_id
                WHERE p.user_id=? AND p.company_id=?
                LIMIT 1 FOR UPDATE
            ");
            $stmt->bind_param('ii', $targetUserId, $companyId);
            $stmt->execute();
            $target = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$target) {
                throw new RuntimeException('USER_NOT_FOUND');
            }
            $name = trim((string) ($data['name'] ?? $target['name']));
            $email = trim((string) ($data['email'] ?? $target['email']));
            $mobile = trim((string) ($data['mobile'] ?? $target['mobile']));
            if ($name === ''
                || mb_strlen($name, 'UTF-8') > 200
                || !filter_var($email, FILTER_VALIDATE_EMAIL)
                || strlen($email) > 200
                || mb_strlen($mobile, 'UTF-8') > 25) {
                throw new RuntimeException('INVALID_USER_DATA');
            }

            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email=? AND id<>? LIMIT 1");
            $stmt->bind_param('si', $email, $targetUserId);
            $stmt->execute();
            $exists = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            if ($exists) {
                throw new RuntimeException('USER_EMAIL_EXISTS');
            }

            // Admin may change MANAGER <-> EXPERT within their own company only.
            // Portal ADMIN is intentionally immutable in this customer-facing form.
            $oldRole = strtoupper((string) $target['portal_role']);
            $requestedRole = strtoupper(trim((string) ($data['portal_role'] ?? $oldRole)));
            if ($oldRole === self::ROLE_ADMIN) {
                if ($requestedRole !== self::ROLE_ADMIN) {
                    throw new RuntimeException('PORTAL_ADMIN_ROLE_CHANGE_DENIED');
                }
                $requestedSectionId = null;
            } else {
                if (!in_array($requestedRole, [self::ROLE_MANAGER, self::ROLE_EXPERT], true)) {
                    throw new RuntimeException('INVALID_PORTAL_ROLE');
                }
                $requestedSectionId = array_key_exists('section_id', $data)
                    ? (int) $data['section_id'] : (int) $target['section_id'];
                if ($requestedSectionId <= 0) {
                    throw new RuntimeException('SECTION_REQUIRED');
                }
                $section = $this->getSectionInCompany($requestedSectionId, $companyId);
                if (!$section || (int) $section['is_active'] !== 1) {
                    throw new RuntimeException('SECTION_NOT_ACTIVE');
                }
                $oldSectionId = (int) $target['section_id'];
                $changedAssignment = $requestedRole !== $oldRole || $requestedSectionId !== $oldSectionId;
                if ($changedAssignment) {
                    if ($this->countOpenDraftsForOwner($targetUserId) > 0) {
                        throw new RuntimeException('USER_HAS_OPEN_DRAFTS');
                    }
                    if ($oldRole === self::ROLE_MANAGER && $oldSectionId > 0
                        && (int)$target['profile_active'] === 1 && $target['status'] === 'Active'
                        && $this->countActiveExperts($oldSectionId) > 0
                        && $this->countOtherActiveManagers($oldSectionId, $targetUserId) === 0) {
                        throw new RuntimeException('LAST_MANAGER_WITH_ACTIVE_EXPERTS');
                    }
                    if ($requestedRole === self::ROLE_MANAGER
                        && (int)$target['profile_active'] === 1 && $target['status'] === 'Active') {
                        $assignedManager = $this->getActiveManagerForSection($requestedSectionId);
                        if ($assignedManager && (int)$assignedManager['user_id'] !== $targetUserId) {
                            throw new RuntimeException('SECTION_ALREADY_HAS_MANAGER');
                        }
                    }
                    if ($requestedRole === self::ROLE_EXPERT
                        && (int)$target['profile_active'] === 1 && $target['status'] === 'Active') {
                        $assignedManager = $this->getActiveManagerForSection($requestedSectionId);
                        if (!$assignedManager || (int)$assignedManager['user_id'] === $targetUserId) {
                            throw new RuntimeException('SECTION_MANAGER_REQUIRED');
                        }
                    }
                }
            }

            $stmt = $this->conn->prepare("UPDATE users SET name=?, email=?, mobile=? WHERE id=?");
            $stmt->bind_param('sssi', $name, $email, $mobile, $targetUserId);
            if (!$stmt->execute()) {
                $stmt->close();
                throw new RuntimeException('USER_UPDATE_FAILED');
            }
            $stmt->close();

            if ($oldRole !== self::ROLE_ADMIN
                && ($requestedRole !== $oldRole || $requestedSectionId !== (int)$target['section_id'])) {
                $actorId = (int) $context['user_id'];
                $stmt = $this->conn->prepare("UPDATE iweb_customer_portal_user_profiles SET portal_role=?, section_id=?, updated_by=? WHERE user_id=? AND company_id=?");
                $stmt->bind_param('siiii', $requestedRole, $requestedSectionId, $actorId, $targetUserId, $companyId);
                if (!$stmt->execute()) {
                    throw new RuntimeException('PORTAL_PROFILE_UPDATE_FAILED');
                }
                $stmt->close();

                // Keep the legacy RBAC in sync for accounts created in the portal unit.
                // Do not alter old unit assignments/RBAC of pre-existing customer accounts.
                $portalUnitId = (int) ($context['settings']['portal_unit_id'] ?? 0);
                $newRbacId = (int) ($requestedRole === self::ROLE_MANAGER
                    ? ($context['settings']['manager_rbac_id'] ?? 0)
                    : ($context['settings']['expert_rbac_id'] ?? 0));
                if ($portalUnitId > 0 && (int)$target['unit_id'] === $portalUnitId) {
                    if ($newRbacId <= 0 || !$this->validateLegacyMapping($companyId, $portalUnitId, $newRbacId)) {
                        throw new RuntimeException('PORTAL_LEGACY_MAPPING_NOT_CONFIGURED');
                    }
                    $stmt = $this->conn->prepare("UPDATE users SET rbac_id=? WHERE id=? AND unit_id=?");
                    $stmt->bind_param('iii', $newRbacId, $targetUserId, $portalUnitId);
                    if (!$stmt->execute()) {
                        throw new RuntimeException('USER_RBAC_UPDATE_FAILED');
                    }
                    $stmt->close();
                }
                // A newly promoted manager needs the manager workflow permissions.
                // This applies only to a role promotion; existing manager overrides
                // are not modified during normal profile edits.
                if ($requestedRole === self::ROLE_MANAGER && $oldRole !== self::ROLE_MANAGER) {
                    foreach (['manager_create_expert','manager_create_ticket_with_experts',
                              'manager_return_ticket_to_expert','manager_assign_unassigned_expert'] as $permissionName) {
                        $allowed = 1;
                        $stmt = $this->conn->prepare("INSERT INTO iweb_customer_portal_permissions (company_id,user_id,permission_name,is_allowed) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE is_allowed=1");
                        $stmt->bind_param('iisi', $companyId, $targetUserId, $permissionName, $allowed);
                        if (!$stmt->execute()) {
                            throw new RuntimeException('PORTAL_PERMISSION_UPDATE_FAILED');
                        }
                        $stmt->close();
                    }
                }
                // Drop stale manager capabilities when demoting to expert.
                if ($requestedRole === self::ROLE_EXPERT) {
                    $stmt = $this->conn->prepare("UPDATE iweb_customer_portal_permissions SET is_allowed=0 WHERE company_id=? AND user_id=? AND permission_name LIKE 'manager_%'");
                    $stmt->bind_param('ii', $companyId, $targetUserId);
                    if (!$stmt->execute()) {
                        throw new RuntimeException('PORTAL_PERMISSION_UPDATE_FAILED');
                    }
                    $stmt->close();
                }
                $this->auditUser($context, 'PORTAL_USER_ROLE_UPDATED', 'USER', $targetUserId, $targetUserId,
                    ['portal_role' => $oldRole, 'section_id' => $target['section_id']],
                    ['portal_role' => $requestedRole, 'section_id' => $requestedSectionId],
                    $requestedSectionId);
            }

            $this->auditUser($context, 'USER_UPDATED', 'USER', $targetUserId, $targetUserId,
                ['name' => $target['name'], 'email' => $target['email'], 'mobile' => $target['mobile']],
                ['name' => $name, 'email' => $email, 'mobile' => $mobile],
                $target['section_id'] !== null ? (int) $target['section_id'] : null
            );
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function setPortalUserActive(array $context, int $targetUserId, bool $active): void
    {
        $this->assertRole($context, self::ROLE_ADMIN);
        $target = $this->getProfileUser($targetUserId, (int) $context['company_id']);
        if (!$target) {
            throw new RuntimeException('USER_NOT_FOUND');
        }
        if (!$active && $targetUserId === (int)$context['user_id']) {
            throw new RuntimeException('SELF_INACTIVATE_DENIED');
        }
        if (!$active && $target['portal_role'] === self::ROLE_ADMIN && $this->isProfileUserActive($target)
            && !empty($context['company_workflow_enabled'])
            && $this->countOtherActiveAdmins((int) $context['company_id'], $targetUserId) === 0) {
            throw new RuntimeException('LAST_PORTAL_ADMIN_REQUIRED');
        }

        if (!$active && $this->countOpenDraftsForOwner($targetUserId) > 0) {
            throw new RuntimeException('USER_HAS_OPEN_DRAFTS');
        }

        if ($active && !empty($target['section_id'])) {
            $targetSectionId = (int) $target['section_id'];
            $section = $this->getSectionInCompany($targetSectionId, (int) $context['company_id']);
            if (!$section || (int) $section['is_active'] !== 1) {
                throw new RuntimeException('SECTION_NOT_ACTIVE');
            }
            if ($target['portal_role'] === self::ROLE_EXPERT && $this->getActiveManagerForSection($targetSectionId) === null) {
                throw new RuntimeException('SECTION_MANAGER_REQUIRED');
            }
            if ($target['portal_role'] === self::ROLE_MANAGER) {
                $existingManager = $this->getActiveManagerForSection($targetSectionId);
                if ($existingManager && (int) $existingManager['user_id'] !== $targetUserId) {
                    throw new RuntimeException('SECTION_ALREADY_HAS_MANAGER');
                }
            }
        }

        if (!$active && $target['portal_role'] === self::ROLE_MANAGER && !empty($target['section_id'])) {
            if ($this->countActiveExperts((int) $target['section_id']) > 0) {
                $otherManagers = $this->countOtherActiveManagers((int) $target['section_id'], $targetUserId);
                if ($otherManagers === 0) {
                    throw new RuntimeException('LAST_MANAGER_WITH_ACTIVE_EXPERTS');
                }
            }
        }

        $status = $active ? 'Active' : 'Inactive';
        $profileActive = $active ? 1 : 0;
        $actorId = (int) $context['user_id'];
        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->bind_param('si', $status, $targetUserId);
            $stmt->execute();
            $stmt->close();
            $stmt = $this->conn->prepare("UPDATE iweb_customer_portal_user_profiles SET is_active = ?, updated_by = ? WHERE user_id = ?");
            $stmt->bind_param('iii', $profileActive, $actorId, $targetUserId);
            $stmt->execute();
            $stmt->close();
            $this->auditUser($context, $active ? 'USER_ACTIVATED' : 'USER_DEACTIVATED', 'USER', $targetUserId, $targetUserId,
                ['status' => $target['status'], 'profile_active' => (int) $target['profile_active']],
                ['status' => $status, 'profile_active' => $profileActive],
                $target['section_id'] !== null ? (int) $target['section_id'] : null
            );
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function assignManager(array $context, int $sectionId, int $managerUserId): void
    {
        $this->assertRole($context, self::ROLE_ADMIN);
        if (!$this->hasPermission($context, 'customer_admin_assign_manager_to_section')) {
            throw new RuntimeException('PORTAL_PERMISSION_DENIED');
        }

        $companyId = (int) $context['company_id'];
        $section = $this->getSectionInCompany($sectionId, $companyId);
        $target = $this->getProfileUser($managerUserId, $companyId);
        if (!$section || (int) $section['is_active'] !== 1 || !$target || $target['portal_role'] !== self::ROLE_MANAGER || !$this->isProfileUserActive($target)) {
            throw new RuntimeException('INVALID_MANAGER_ASSIGNMENT');
        }

        // Customer-side assignment is intentionally only for an unassigned Manager.
        // Replacing/moving an existing operational Manager is an INTEK-governed action.
        $targetSectionId = $target['section_id'] === null ? null : (int) $target['section_id'];
        if ($targetSectionId !== null && $targetSectionId !== $sectionId) {
            throw new RuntimeException('INVALID_MANAGER_ASSIGNMENT');
        }

        $currentManager = $this->getActiveManagerForSection($sectionId);
        if ($currentManager && (int) $currentManager['user_id'] !== $managerUserId) {
            throw new RuntimeException('SECTION_ALREADY_HAS_MANAGER');
        }
        if ($currentManager && (int) $currentManager['user_id'] === $managerUserId) {
            return;
        }

        $actor = (int) $context['user_id'];
        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("UPDATE iweb_customer_portal_user_profiles SET section_id = ?, updated_by = ? WHERE user_id = ? AND company_id = ? AND portal_role = 'MANAGER'");
            $stmt->bind_param('iiii', $sectionId, $actor, $managerUserId, $companyId);
            if (!$stmt->execute() || $stmt->affected_rows < 1) {
                $stmt->close();
                throw new RuntimeException('INVALID_MANAGER_ASSIGNMENT');
            }
            $stmt->close();

            $this->auditUser($context, 'MANAGER_ASSIGNED', 'SECTION', $sectionId, $managerUserId,
                ['section_id' => null],
                ['section_id' => $sectionId, 'manager_user_id' => $managerUserId],
                $sectionId
            );
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function assignExpert(array $context, int $expertUserId, int $sectionId): void
    {
        if (!in_array($context['portal_role'] ?? null, [self::ROLE_ADMIN, self::ROLE_MANAGER], true)) {
            throw new RuntimeException('PORTAL_PERMISSION_DENIED');
        }
        $companyId = (int) $context['company_id'];
        if ($context['portal_role'] === self::ROLE_MANAGER) {
            if (!$this->hasPermission($context, 'manager_assign_unassigned_expert')
                || $sectionId !== (int) $context['section_id']) {
                throw new RuntimeException('PORTAL_PERMISSION_DENIED');
            }
        } elseif (!$this->hasPermission($context, 'customer_admin_reassign_expert')) {
            throw new RuntimeException('PORTAL_PERMISSION_DENIED');
        }

        $actor = (int) $context['user_id'];
        $this->conn->begin_transaction();
        try {
            // Lock the expert so concurrent assignments cannot both pass the unassigned check.
            $stmt = $this->conn->prepare("
                SELECT p.section_id,p.is_active,u.status
                FROM iweb_customer_portal_user_profiles p
                INNER JOIN users u ON u.id=p.user_id
                WHERE p.user_id=? AND p.company_id=? AND p.portal_role='EXPERT'
                LIMIT 1 FOR UPDATE
            ");
            $stmt->bind_param('ii', $expertUserId, $companyId);
            $stmt->execute();
            $target = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$target || !$this->isProfileUserActive($target)) {
                throw new RuntimeException('INVALID_EXPERT_ASSIGNMENT');
            }

            $oldSectionId = $target['section_id'] === null ? null : (int) $target['section_id'];
            if ($context['portal_role'] === self::ROLE_MANAGER && $oldSectionId !== null) {
                throw new RuntimeException('PORTAL_PERMISSION_DENIED');
            }
            $section = $this->getSectionInCompany($sectionId, $companyId);
            if (!$section || (int) $section['is_active'] !== 1 || $this->getActiveManagerForSection($sectionId) === null) {
                throw new RuntimeException('SECTION_MANAGER_REQUIRED');
            }
            if ($oldSectionId !== null && $oldSectionId !== $sectionId && $this->countOpenDraftsForOwner($expertUserId) > 0) {
                throw new RuntimeException('USER_HAS_OPEN_DRAFTS');
            }
            if ($oldSectionId === $sectionId) {
                $this->conn->commit();
                return;
            }

            $stmt = $this->conn->prepare("
                UPDATE iweb_customer_portal_user_profiles
                SET section_id=?,updated_by=?
                WHERE user_id=? AND company_id=? AND portal_role='EXPERT'
            ");
            $stmt->bind_param('iiii', $sectionId, $actor, $expertUserId, $companyId);
            if (!$stmt->execute() || $stmt->affected_rows !== 1) {
                $stmt->close();
                throw new RuntimeException('INVALID_EXPERT_ASSIGNMENT');
            }
            $stmt->close();
            $this->auditUser($context, $oldSectionId === null ? 'EXPERT_ASSIGNED' : 'EXPERT_REASSIGNED', 'USER',
                $expertUserId, $expertUserId, ['section_id' => $oldSectionId],
                ['section_id' => $sectionId], $sectionId
            );
            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function createDraft(array $context, array $data): int
    {
        if (!$context['can_create_draft']) {
            throw new RuntimeException('DRAFT_CREATE_DENIED');
        }
        $title = trim((string) ($data['title'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));
        $typeId = (int) ($data['type_id'] ?? 0);
        $indicator = trim((string) ($data['indicator_number'] ?? ''));
        if ($title === '' || $description === '' || mb_strlen($indicator, 'UTF-8') > 255 || $typeId <= 0 || !$this->typeExists($typeId)) {
            throw new RuntimeException('INVALID_DRAFT_DATA');
        }

        $sectionId = (int) $context['section_id'];
        $manager = $this->getActiveManagerForSection($sectionId);
        if (!$manager) {
            throw new RuntimeException('SECTION_MANAGER_REQUIRED');
        }

        $creator = (int) $context['user_id'];
        $managerUserId = (int) $manager['user_id'];
        $priority = 'low';
        if ($context['portal_role'] === self::ROLE_MANAGER) {
            $priority = $this->normalizePriority((string) ($data['priority'] ?? 'low'));
            $status = self::STATUS_MANAGER_REVIEW;
            $owner = $creator;
            $managerUserId = $creator;
        } else {
            $status = self::STATUS_EXPERT_DRAFT;
            $owner = $creator;
        }

        $companyId = (int) $context['company_id'];
        $stmt = $this->conn->prepare("
            INSERT INTO iweb_customer_portal_ticket_drafts
            (company_id,section_id,creator_user_id,current_owner_user_id,manager_user_id,title,description,type_id,indicator_number,priority,workflow_status)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)
        ");
        $stmt->bind_param('iiiiississs', $companyId, $sectionId, $creator, $owner, $managerUserId, $title, $description, $typeId, $indicator, $priority, $status);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('DRAFT_CREATE_FAILED');
        }
        $draftId = (int) $stmt->insert_id;
        $stmt->close();
        $this->auditUser($context, 'TICKET_DRAFT_CREATED', 'DRAFT', $draftId, null, null, [
            'workflow_status' => $status,
            'title' => $title,
            'type_id' => $typeId,
            'priority' => $priority,
            'owner_user_id' => $owner,
        ]);
        return $draftId;
    }

    public function listDrafts(array $context, string $status = ''): array
    {
        $where = [];
        $params = [];
        $types = '';
        if ($context['portal_role'] === self::ROLE_ADMIN) {
            $where[] = 'd.company_id = ?';
            $types .= 'i';
            $params[] = (int) $context['company_id'];
        } elseif ($context['portal_role'] === self::ROLE_MANAGER) {
            $where[] = 'd.company_id = ? AND d.section_id = ?';
            $types .= 'ii';
            $params[] = (int) $context['company_id'];
            $params[] = (int) $context['section_id'];
        } else {
            $where[] = 'd.company_id = ? AND d.section_id = ? AND (d.creator_user_id = ? OR d.current_owner_user_id = ?)';
            $types .= 'iiii';
            $params[] = (int) $context['company_id'];
            $params[] = (int) $context['section_id'];
            $params[] = (int) $context['user_id'];
            $params[] = (int) $context['user_id'];
        }
        if ($status !== '') {
            $allowed = [self::STATUS_EXPERT_DRAFT, self::STATUS_MANAGER_REVIEW, self::STATUS_EXPERT_REVISION, self::STATUS_SENT, self::STATUS_CANCELLED];
            if (in_array($status, $allowed, true)) {
                $where[] = 'd.workflow_status = ?';
                $types .= 's';
                $params[] = $status;
            }
        }
        $whereSql = implode(' AND ', $where);
        $sql = "
            SELECT d.*, published.ticket_number AS published_ticket_number, s.name AS section_name, t.type_name, t.type_group,
                   creator.name AS creator_name, owner.name AS owner_name, manager.name AS manager_name
            FROM iweb_customer_portal_ticket_drafts d
            INNER JOIN iweb_customer_portal_sections s ON s.id = d.section_id
            LEFT JOIN types t ON t.id = d.type_id
            LEFT JOIN users creator ON creator.id = d.creator_user_id
            LEFT JOIN users owner ON owner.id = d.current_owner_user_id
            LEFT JOIN users manager ON manager.id = d.manager_user_id
            LEFT JOIN tickets published ON published.id = d.published_ticket_id
            WHERE $whereSql
            ORDER BY FIELD(d.workflow_status,'MANAGER_REVIEW','EXPERT_REVISION','EXPERT_DRAFT','SENT_TO_INTEK','CANCELLED'), d.updated_at DESC, d.id DESC
        ";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return [];
        }
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $rows[] = $row;
        }
        if ($result) {
            $result->free();
        }
        $stmt->close();
        return $rows;
    }

    public function getDraft(array $context, int $draftId, bool $recordView = true): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT d.*, published.ticket_number AS published_ticket_number, s.name AS section_name, t.type_name, t.type_group,
                   creator.name AS creator_name, owner.name AS owner_name, manager.name AS manager_name
            FROM iweb_customer_portal_ticket_drafts d
            INNER JOIN iweb_customer_portal_sections s ON s.id = d.section_id
            LEFT JOIN types t ON t.id = d.type_id
            LEFT JOIN users creator ON creator.id = d.creator_user_id
            LEFT JOIN users owner ON owner.id = d.current_owner_user_id
            LEFT JOIN users manager ON manager.id = d.manager_user_id
            LEFT JOIN tickets published ON published.id = d.published_ticket_id
            WHERE d.id = ?
            LIMIT 1
        ");
        $stmt->bind_param('i', $draftId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        if ($result) {
            $result->free();
        }
        $stmt->close();
        if (!$row || !$this->canViewDraft($context, $row)) {
            return null;
        }
        if ($recordView) {
            $this->recordDraftView($context, $draftId);
        }
        return $row;
    }

    public function updateDraft(array $context, int $draftId, array $data): void
    {
        $draft = $this->getDraft($context, $draftId, false);
        if (!$draft) {
            throw new RuntimeException('DRAFT_NOT_FOUND');
        }
        $this->assertCurrentOwner($context, $draft);
        if (!in_array($draft['workflow_status'], [self::STATUS_EXPERT_DRAFT, self::STATUS_EXPERT_REVISION, self::STATUS_MANAGER_REVIEW], true)) {
            throw new RuntimeException('DRAFT_LOCKED');
        }
        if ($context['portal_role'] === self::ROLE_ADMIN) {
            throw new RuntimeException('DRAFT_EDIT_DENIED');
        }

        $title = trim((string) ($data['title'] ?? $draft['title']));
        $description = trim((string) ($data['description'] ?? $draft['description']));
        $typeId = (int) ($data['type_id'] ?? $draft['type_id']);
        $indicator = trim((string) ($data['indicator_number'] ?? $draft['indicator_number']));
        if ($title === '' || $description === '' || mb_strlen($indicator, 'UTF-8') > 255 || $typeId <= 0 || !$this->typeExists($typeId)) {
            throw new RuntimeException('INVALID_DRAFT_DATA');
        }
        $priority = $draft['priority'];
        if ($context['portal_role'] === self::ROLE_MANAGER) {
            $priority = $this->normalizePriority((string) ($data['priority'] ?? $draft['priority']));
        }

        $stmt = $this->conn->prepare("UPDATE iweb_customer_portal_ticket_drafts SET title=?, description=?, type_id=?, indicator_number=?, priority=? WHERE id=?");
        $stmt->bind_param('ssissi', $title, $description, $typeId, $indicator, $priority, $draftId);
        $stmt->execute();
        $stmt->close();
        $this->auditUser($context, 'TICKET_EDITED', 'DRAFT', $draftId, null,
            ['title' => $draft['title'], 'description' => $draft['description'], 'type_id' => (int) $draft['type_id'], 'indicator_number' => $draft['indicator_number'], 'priority' => $draft['priority']],
            ['title' => $title, 'description' => $description, 'type_id' => $typeId, 'indicator_number' => $indicator, 'priority' => $priority]
        );
        if ($priority !== $draft['priority']) {
            $this->auditUser($context, 'TICKET_PRIORITY_CHANGED', 'DRAFT', $draftId, null, ['priority' => $draft['priority']], ['priority' => $priority]);
        }
    }

    public function sendDraftToManager(array $context, int $draftId): void
    {
        $draft = $this->getDraft($context, $draftId, false);
        if (!$draft || $context['portal_role'] !== self::ROLE_EXPERT) {
            throw new RuntimeException('DRAFT_ACTION_DENIED');
        }
        $this->assertCurrentOwner($context, $draft);
        if (!in_array($draft['workflow_status'], [self::STATUS_EXPERT_DRAFT, self::STATUS_EXPERT_REVISION], true)) {
            throw new RuntimeException('INVALID_DRAFT_STATE');
        }
        $manager = $this->getActiveManagerForSection((int) $draft['section_id']);
        if (!$manager || (int) $manager['user_id'] !== (int) $draft['manager_user_id']) {
            throw new RuntimeException('SECTION_MANAGER_REQUIRED');
        }
        $managerId = (int) $manager['user_id'];
        $stmt = $this->conn->prepare("UPDATE iweb_customer_portal_ticket_drafts SET workflow_status='MANAGER_REVIEW', current_owner_user_id=? WHERE id=?");
        $stmt->bind_param('ii', $managerId, $draftId);
        $stmt->execute();
        $stmt->close();
        $action = $draft['workflow_status'] === self::STATUS_EXPERT_REVISION ? 'TICKET_RETURNED_TO_MANAGER' : 'TICKET_SENT_TO_MANAGER';
        $this->auditUser($context, $action, 'DRAFT', $draftId, $managerId,
            ['workflow_status' => $draft['workflow_status'], 'owner_user_id' => (int) $draft['current_owner_user_id']],
            ['workflow_status' => self::STATUS_MANAGER_REVIEW, 'owner_user_id' => $managerId]
        );
    }

    public function assignDraftToExpert(array $context, int $draftId, int $expertUserId): void
    {
        if ($context['portal_role'] !== self::ROLE_MANAGER || !$this->hasPermission($context, 'manager_return_ticket_to_expert')) {
            throw new RuntimeException('PORTAL_PERMISSION_DENIED');
        }
        $draft = $this->getDraft($context, $draftId, false);
        if (!$draft) {
            throw new RuntimeException('DRAFT_NOT_FOUND');
        }
        $this->assertCurrentOwner($context, $draft);
        if ($draft['workflow_status'] !== self::STATUS_MANAGER_REVIEW) {
            throw new RuntimeException('INVALID_DRAFT_STATE');
        }
        $expert = $this->getProfileUser($expertUserId, (int) $context['company_id']);
        if (!$expert || $expert['portal_role'] !== self::ROLE_EXPERT || (int) $expert['section_id'] !== (int) $context['section_id'] || !$this->isProfileUserActive($expert)) {
            throw new RuntimeException('INVALID_EXPERT_ASSIGNMENT');
        }
        $stmt = $this->conn->prepare("UPDATE iweb_customer_portal_ticket_drafts SET workflow_status='EXPERT_REVISION', current_owner_user_id=?, revision_count=revision_count+1 WHERE id=?");
        $stmt->bind_param('ii', $expertUserId, $draftId);
        $stmt->execute();
        $stmt->close();
        $this->auditUser($context, 'TICKET_ASSIGNED_TO_EXPERT', 'DRAFT', $draftId, $expertUserId,
            ['workflow_status' => self::STATUS_MANAGER_REVIEW, 'owner_user_id' => (int) $draft['current_owner_user_id']],
            ['workflow_status' => self::STATUS_EXPERT_REVISION, 'owner_user_id' => $expertUserId]
        );
    }

    public function cancelDraft(array $context, int $draftId): void
    {
        $draft = $this->getDraft($context, $draftId, false);
        if (!$draft) {
            throw new RuntimeException('DRAFT_NOT_FOUND');
        }
        $this->assertCurrentOwner($context, $draft);
        if (in_array($draft['workflow_status'], [self::STATUS_SENT, self::STATUS_CANCELLED], true)) {
            throw new RuntimeException('DRAFT_LOCKED');
        }
        $stmt = $this->conn->prepare("UPDATE iweb_customer_portal_ticket_drafts SET workflow_status='CANCELLED', cancelled_at=NOW() WHERE id=?");
        $stmt->bind_param('i', $draftId);
        $stmt->execute();
        $stmt->close();
        $this->auditUser($context, 'TICKET_CANCELLED', 'DRAFT', $draftId, null,
            ['workflow_status' => $draft['workflow_status']], ['workflow_status' => self::STATUS_CANCELLED]
        );
    }

    public function publishDraft(array $context, int $draftId): array
    {
        if ($context['portal_role'] !== self::ROLE_MANAGER) {
            throw new RuntimeException('PUBLISH_DENIED');
        }

        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("SELECT * FROM iweb_customer_portal_ticket_drafts WHERE id=? FOR UPDATE");
            $stmt->bind_param('i', $draftId);
            $stmt->execute();
            $draft = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$draft || !$this->canViewDraft($context, $draft)) {
                throw new RuntimeException('DRAFT_NOT_FOUND');
            }
            $this->assertCurrentOwner($context, $draft);
            if ($draft['workflow_status'] !== self::STATUS_MANAGER_REVIEW || (int) $draft['manager_user_id'] !== (int) $context['user_id']) {
                throw new RuntimeException('PUBLISH_DENIED');
            }
            if (!empty($draft['published_ticket_id'])) {
                throw new RuntimeException('DRAFT_ALREADY_PUBLISHED');
            }

            $section = $this->getSectionInCompany((int) $draft['section_id'], (int) $context['company_id']);
            if (!$section || (int) $section['is_active'] !== 1) {
                throw new RuntimeException('SECTION_NOT_ACTIVE');
            }
            $manager = $this->getActiveManagerForSection((int) $draft['section_id']);
            if (!$manager || (int) $manager['user_id'] !== (int) $context['user_id']) {
                throw new RuntimeException('PUBLISH_DENIED');
            }

            $creatorUserId = (int) $draft['creator_user_id'];
            $companyId = (int) $draft['company_id'];
            $title = $draft['title'];
            $description = $draft['description'];
            $typeId = (int) $draft['type_id'];
            $priority = $this->normalizePriority($draft['priority']);
            $indicator = (string) $draft['indicator_number'];
            $stmt = $this->conn->prepare("
                INSERT INTO tickets (ticket_title,ticket_description,type_id,priority,user_id,company_id,indicator_number)
                VALUES (?,?,?,?,?,?,?)
            ");
            $stmt->bind_param('ssisiis', $title, $description, $typeId, $priority, $creatorUserId, $companyId, $indicator);
            if (!$stmt->execute()) {
                throw new RuntimeException('PUBLISH_TICKET_INSERT_FAILED');
            }
            $ticketId = (int) $stmt->insert_id;
            $stmt->close();

            $statusStmt = $this->conn->prepare("INSERT INTO status (part_id,part_name,user_id,rbac_id) VALUES (?,'tickets',?,?)");
            $publisherId = (int) $context['user_id'];
            $rbacId = (int) ($_SESSION['rbac_id'] ?? 0);
            $statusStmt->bind_param('iii', $ticketId, $publisherId, $rbacId);
            if (!$statusStmt->execute()) {
                throw new RuntimeException('PUBLISH_STATUS_FAILED');
            }
            $statusStmt->close();

            $currentMonth = (new \DateTime())->format('m');
            $ticketNumber = $companyId . $currentMonth . (((int) round($ticketId * 2.7)) % 10000) . random_int(10, 99);
            $stmt = $this->conn->prepare("UPDATE tickets SET ticket_number=? WHERE id=?");
            $stmt->bind_param('si', $ticketNumber, $ticketId);
            $stmt->execute();
            $stmt->close();

            $draftPartName = 'customer_portal_draft';
            $ticketPartName = 'tickets';
            $stmt = $this->conn->prepare("UPDATE file_manage SET part_id=?, part_name=? WHERE part_id=? AND part_name=? AND company_id=?");
            $stmt->bind_param('isisi', $ticketId, $ticketPartName, $draftId, $draftPartName, $companyId);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->conn->prepare("UPDATE comments SET part_id=?, part_name=? WHERE part_id=? AND part_name=? AND company_id=?");
            $stmt->bind_param('isisi', $ticketId, $ticketPartName, $draftId, $draftPartName, $companyId);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->conn->prepare("INSERT INTO iweb_customer_portal_ticket_publish_map (draft_id,ticket_id,section_id,published_by) VALUES (?,?,?,?)");
            $sectionId = (int) $draft['section_id'];
            $stmt->bind_param('iiii', $draftId, $ticketId, $sectionId, $publisherId);
            if (!$stmt->execute()) {
                throw new RuntimeException('PUBLISH_MAP_FAILED');
            }
            $stmt->close();

            $stmt = $this->conn->prepare("UPDATE iweb_customer_portal_ticket_drafts SET workflow_status='SENT_TO_INTEK', published_ticket_id=?, sent_to_intek_at=NOW() WHERE id=?");
            $stmt->bind_param('ii', $ticketId, $draftId);
            $stmt->execute();
            $stmt->close();

            $this->auditUser($context, 'TICKET_SENT_TO_INTEK', 'DRAFT', $draftId, null,
                ['workflow_status' => self::STATUS_MANAGER_REVIEW],
                ['workflow_status' => self::STATUS_SENT, 'ticket_id' => $ticketId, 'ticket_number' => $ticketNumber]
            );
            $this->conn->commit();
            return ['ticket_id' => $ticketId, 'ticket_number' => $ticketNumber];
        } catch (Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function addDraftComment(array $context, int $draftId, string $commentText): int
    {
        if (!in_array($context['portal_role'] ?? null, [self::ROLE_MANAGER, self::ROLE_EXPERT], true)) {
            throw new RuntimeException('DRAFT_ACTION_DENIED');
        }
        $draft = $this->getDraft($context, $draftId, false);
        if (!$draft) {
            throw new RuntimeException('DRAFT_NOT_FOUND');
        }
        $commentText = trim($commentText);
        if ($commentText === '') {
            throw new RuntimeException('COMMENT_EMPTY');
        }
        $partName = 'customer_portal_draft';
        $userId = (int) $context['user_id'];
        $companyId = (int) $context['company_id'];
        $stmt = $this->conn->prepare("INSERT INTO comments (user_id,part_id,company_id,part_name,comment_text,local,is_active) VALUES (?,?,?,?,?,0,1)");
        $stmt->bind_param('iiiss', $userId, $draftId, $companyId, $partName, $commentText);
        $stmt->execute();
        $commentId = (int) $stmt->insert_id;
        $stmt->close();
        $this->auditUser($context, 'COMMENT_CREATED', 'DRAFT', $draftId, null, null, ['comment_id' => $commentId]);
        return $commentId;
    }

    public function listDraftComments(array $context, int $draftId): array
    {
        if (!$this->getDraft($context, $draftId, false)) {
            return [];
        }
        $partName = 'customer_portal_draft';
        $stmt = $this->conn->prepare("
            SELECT c.*, u.name AS user_name
            FROM comments c
            LEFT JOIN users u ON u.id = c.user_id
            WHERE c.part_id=? AND c.part_name=? AND c.is_active=1
            ORDER BY c.creation_date ASC, c.id ASC
        ");
        $stmt->bind_param('is', $draftId, $partName);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $rows[] = $row;
        }
        if ($result) {
            $result->free();
        }
        $stmt->close();
        return $rows;
    }

    public function listDraftFiles(array $context, int $draftId): array
    {
        if (!$this->getDraft($context, $draftId, false)) {
            return [];
        }
        $partName = 'customer_portal_draft';
        $stmt = $this->conn->prepare("SELECT * FROM file_manage WHERE part_id=? AND part_name=? ORDER BY creation_date ASC, id ASC");
        $stmt->bind_param('is', $draftId, $partName);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $rows[] = $row;
        }
        if ($result) {
            $result->free();
        }
        $stmt->close();
        return $rows;
    }

    public function registerDraftFile(array $context, int $draftId, string $fileName, string $filePath, string $fileTitle): int
    {
        $draft = $this->getDraft($context, $draftId, false);
        if (!$draft) {
            throw new RuntimeException('DRAFT_NOT_FOUND');
        }
        $this->assertCurrentOwner($context, $draft);
        if (in_array($draft['workflow_status'], [self::STATUS_SENT, self::STATUS_CANCELLED], true)) {
            throw new RuntimeException('DRAFT_LOCKED');
        }
        $userId = (int) $context['user_id'];
        $companyId = (int) $context['company_id'];
        $partName = 'customer_portal_draft';
        $stmt = $this->conn->prepare("INSERT INTO file_manage (file_name,file_path,file_title,user_id,part_id,company_id,part_name,local) VALUES (?,?,?,?,?,?,?,0)");
        $stmt->bind_param('sssiiis', $fileName, $filePath, $fileTitle, $userId, $draftId, $companyId, $partName);
        $stmt->execute();
        $fileId = (int) $stmt->insert_id;
        $stmt->close();
        $this->auditUser($context, 'FILE_UPLOADED', 'DRAFT', $draftId, null, null, ['file_id' => $fileId, 'file_title' => $fileTitle]);
        return $fileId;
    }

    public function removeDraftFile(array $context, int $draftId, int $fileId): ?array
    {
        $draft = $this->getDraft($context, $draftId, false);
        if (!$draft) {
            throw new RuntimeException('DRAFT_NOT_FOUND');
        }
        $this->assertCurrentOwner($context, $draft);
        $partName = 'customer_portal_draft';
        $stmt = $this->conn->prepare("SELECT * FROM file_manage WHERE id=? AND part_id=? AND part_name=? AND company_id=? LIMIT 1");
        $companyId = (int) $context['company_id'];
        $stmt->bind_param('iisi', $fileId, $draftId, $partName, $companyId);
        $stmt->execute();
        $file = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$file) {
            throw new RuntimeException('FILE_NOT_FOUND');
        }
        $stmt = $this->conn->prepare("DELETE FROM file_manage WHERE id=? AND part_id=? AND part_name=? AND company_id=?");
        $stmt->bind_param('iisi', $fileId, $draftId, $partName, $companyId);
        $stmt->execute();
        $stmt->close();
        $this->auditUser($context, 'FILE_REMOVED', 'DRAFT', $draftId, null, ['file_id' => $fileId, 'file_title' => $file['file_title']], null);
        return $file;
    }

    public function listActiveExpertsForSection(int $companyId, int $sectionId): array
    {
        $stmt = $this->conn->prepare("
            SELECT p.user_id, u.name, u.email
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id=p.user_id AND u.status='Active'
            WHERE p.company_id=? AND p.section_id=? AND p.portal_role='EXPERT' AND p.is_active=1
            ORDER BY u.name
        ");
        $stmt->bind_param('ii', $companyId, $sectionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $rows[] = $row;
        }
        if ($result) {
            $result->free();
        }
        $stmt->close();
        return $rows;
    }

    public function getDraftHistory(array $context, int $draftId): array
    {
        if (($context['portal_role'] ?? '') !== self::ROLE_ADMIN) {
            throw new RuntimeException('AUDIT_MANAGEMENT_DENIED');
        }
        if (!$this->getDraft($context, $draftId, false)) {
            return [];
        }
        $stmt = $this->conn->prepare("
            SELECT a.*, COALESCE(u.name, ad.name, CONCAT('#',a.actor_user_id)) AS actor_name
            FROM iweb_customer_portal_ticket_audit_logs a
            LEFT JOIN users u ON a.actor_source='USER' AND u.id=a.actor_user_id
            LEFT JOIN admins ad ON a.actor_source='ADMIN' AND ad.id=a.actor_user_id
            WHERE a.entity_type='DRAFT' AND a.entity_id=?
            ORDER BY a.created_at ASC, a.id ASC
        ");
        $stmt->bind_param('i', $draftId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $rows[] = $row;
        }
        if ($result) {
            $result->free();
        }
        $stmt->close();
        return $rows;
    }

    public function listAudit(array $context, int $limit = 250): array
    {
        if (($context['portal_role'] ?? '') !== self::ROLE_ADMIN) {
            throw new RuntimeException('AUDIT_MANAGEMENT_DENIED');
        }
        $limit = max(1, min(1000, $limit));
        $companyId = (int) $context['company_id'];
        if ($context['portal_role'] === self::ROLE_MANAGER) {
            $sectionId = (int) $context['section_id'];
            $sql = "SELECT a.*, COALESCE(u.name, ad.name, CONCAT('#',a.actor_user_id)) actor_name FROM iweb_customer_portal_ticket_audit_logs a LEFT JOIN users u ON a.actor_source='USER' AND u.id=a.actor_user_id LEFT JOIN admins ad ON a.actor_source='ADMIN' AND ad.id=a.actor_user_id WHERE a.company_id=? AND a.section_id=? ORDER BY a.created_at DESC,a.id DESC LIMIT $limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('ii', $companyId, $sectionId);
        } else {
            $sql = "SELECT a.*, COALESCE(u.name, ad.name, CONCAT('#',a.actor_user_id)) actor_name FROM iweb_customer_portal_ticket_audit_logs a LEFT JOIN users u ON a.actor_source='USER' AND u.id=a.actor_user_id LEFT JOIN admins ad ON a.actor_source='ADMIN' AND ad.id=a.actor_user_id WHERE a.company_id=? ORDER BY a.created_at DESC,a.id DESC LIMIT $limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $companyId);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $rows[] = $row;
        }
        if ($result) {
            $result->free();
        }
        $stmt->close();
        return $rows;
    }

    /** A company-/section-scoped audit page with prepared filters and real pagination.
     *  The company is always derived from the server-side session profile.
     *  Managers cannot expand their section by sending a different section_id.
     */
    public function listAuditPage(array $context, array $filters = [], int $page = 1, int $pageSize = 30): array
    {
        $role = (string) ($context['portal_role'] ?? '');
        if ($role !== self::ROLE_ADMIN) {
            throw new RuntimeException('AUDIT_MANAGEMENT_DENIED');
        }
        $this->assertRole($context, $role);
        $companyId = (int) ($context['company_id'] ?? 0);
        if ($companyId <= 0) {
            throw new RuntimeException('INVALID_COMPANY');
        }

        $page = max(1, min(100000, $page));
        $pageSize = max(1, min(50, $pageSize));
        $sectionId = $role === self::ROLE_ADMIN ? max(0, (int) ($filters['section_id'] ?? 0)) : (int) ($context['section_id'] ?? 0);
        if ($role === self::ROLE_MANAGER && $sectionId <= 0) {
            throw new RuntimeException('AUDIT_MANAGEMENT_DENIED');
        }
        $event = substr(strtoupper(trim((string) ($filters['event'] ?? ''))), 0, 64);
        $from = trim((string) ($filters['date_from'] ?? ''));
        $to = trim((string) ($filters['date_to'] ?? ''));
        foreach (['from', 'to'] as $part) {
            $dateValue = $$part;
            if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $dateValue)
                || !checkdate((int) substr($dateValue, 5, 2), (int) substr($dateValue, 8, 2), (int) substr($dateValue, 0, 4))) {
                $$part = '';
            }
        }
        $where = ['a.company_id=?'];
        $types = 'i';
        $values = [$companyId];
        if ($sectionId > 0) {
            $where[] = 'a.section_id=?';
            $types .= 'i';
            $values[] = $sectionId;
        }
        if ($event !== '') {
            $where[] = 'a.action_type=?';
            $types .= 's';
            $values[] = $event;
        }
        if ($from !== '') {
            $where[] = 'a.created_at >= ?';
            $types .= 's';
            $values[] = $from . ' 00:00:00';
        }
        if ($to !== '') {
            $where[] = 'a.created_at < DATE_ADD(?, INTERVAL 1 DAY)';
            $types .= 's';
            $values[] = $to;
        }
        $whereSql = implode(' AND ', $where);

        $stmt = $this->conn->prepare('SELECT COUNT(*) AS total FROM iweb_customer_portal_ticket_audit_logs a WHERE ' . $whereSql);
        if ($stmt === false) {
            throw new RuntimeException('AUDIT_READ_FAILED');
        }
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $countResult = $stmt->get_result();
        $countRow = $countResult ? $countResult->fetch_assoc() : null;
        if ($countResult) $countResult->free();
        $stmt->close();
        $total = (int) ($countRow['total'] ?? 0);
        $pages = max(1, (int) ceil($total / $pageSize));
        $page = min($page, $pages);
        $offset = ($page - 1) * $pageSize;

        $sql = "SELECT a.id, a.created_at, a.actor_user_id, a.actor_source, a.actor_role,
                       a.action_type, a.entity_type, a.entity_id, a.section_id,
                       a.old_data, a.new_data,
                       COALESCE(u.name, ad.name, CONCAT('#', a.actor_user_id)) AS actor_name,
                       s.name AS section_name
                FROM iweb_customer_portal_ticket_audit_logs a
                LEFT JOIN users u ON a.actor_source='USER' AND u.id=a.actor_user_id
                LEFT JOIN admins ad ON a.actor_source='ADMIN' AND ad.id=a.actor_user_id
                LEFT JOIN iweb_customer_portal_sections s ON s.id=a.section_id AND s.company_id=a.company_id
                WHERE $whereSql
                ORDER BY a.created_at DESC, a.id DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new RuntimeException('AUDIT_READ_FAILED');
        }
        $readValues = array_merge($values, [$pageSize, $offset]);
        $stmt->bind_param($types . 'ii', ...$readValues);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $rows[] = $row;
        }
        if ($result) $result->free();
        $stmt->close();
        return [
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'page_size' => $pageSize,
            'filters' => [
                'section_id' => $role === self::ROLE_ADMIN ? $sectionId : 0,
                'event' => $event,
                'date_from' => $from,
                'date_to' => $to,
            ],
        ];
    }

    public function getReport(array $context): array
    {
        $companyId = (int) $context['company_id'];
        $sectionClause = '';
        $types = 'i';
        $params = [$companyId];
        if ($context['portal_role'] === self::ROLE_MANAGER) {
            $sectionClause = ' AND d.section_id=?';
            $types .= 'i';
            $params[] = (int) $context['section_id'];
        } elseif ($context['portal_role'] === self::ROLE_EXPERT) {
            throw new RuntimeException('REPORT_DENIED');
        }

        $agingDays = max(1, (int) ($context['settings']['draft_aging_days'] ?? 14));
        $sql = "
            SELECT COUNT(*) total,
                   SUM(d.workflow_status='SENT_TO_INTEK') sent,
                   SUM(d.workflow_status='CANCELLED') cancelled,
                   SUM(d.workflow_status NOT IN ('SENT_TO_INTEK','CANCELLED')) open_count,
                   SUM(d.workflow_status NOT IN ('SENT_TO_INTEK','CANCELLED') AND TIMESTAMPDIFF(DAY,d.created_at,NOW()) >= $agingDays) aging_count,
                   COALESCE(SUM(d.revision_count),0) revision_count,
                   AVG(CASE WHEN d.sent_to_intek_at IS NOT NULL THEN TIMESTAMPDIFF(MINUTE,d.created_at,d.sent_to_intek_at) END) avg_minutes_to_intek
            FROM iweb_customer_portal_ticket_drafts d
            WHERE d.company_id=? $sectionClause
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $summary = $stmt->get_result()->fetch_assoc() ?: [];
        $stmt->close();

        $sql = "
            SELECT s.id section_id,s.name section_name,
                   COUNT(d.id) total,
                   SUM(d.workflow_status='SENT_TO_INTEK') sent,
                   SUM(d.workflow_status='CANCELLED') cancelled,
                   SUM(d.workflow_status NOT IN ('SENT_TO_INTEK','CANCELLED')) open_count,
                   COALESCE(SUM(d.revision_count),0) revisions
            FROM iweb_customer_portal_sections s
            LEFT JOIN iweb_customer_portal_ticket_drafts d ON d.section_id=s.id
            WHERE s.company_id=?" . ($context['portal_role'] === self::ROLE_MANAGER ? ' AND s.id=?' : '') . "
            GROUP BY s.id,s.name
            ORDER BY total DESC,s.name
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $sections = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $sections[] = $row;
        }
        if ($result) {
            $result->free();
        }
        $stmt->close();

        $userSql = "
            SELECT p.portal_role,u.id user_id,u.name,u.status,p.is_active,p.section_id,s.name section_name,
                   COUNT(d.id) created_drafts,
                   SUM(d.workflow_status='SENT_TO_INTEK') sent_drafts
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id=p.user_id
            LEFT JOIN iweb_customer_portal_sections s ON s.id=p.section_id
            LEFT JOIN iweb_customer_portal_ticket_drafts d ON d.creator_user_id=p.user_id
            WHERE p.company_id=?" . ($context['portal_role'] === self::ROLE_MANAGER ? ' AND p.section_id=?' : '') . "
            GROUP BY p.user_id,p.portal_role,u.name,u.status,p.is_active,p.section_id,s.name
            ORDER BY FIELD(p.portal_role,'MANAGER','EXPERT','ADMIN'),u.name
        ";
        $stmt = $this->conn->prepare($userSql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $users[] = $row;
        }
        if ($result) {
            $result->free();
        }
        $stmt->close();

        return ['summary' => $summary, 'sections' => $sections, 'users' => $users, 'aging_days' => $agingDays];
    }

    /** Company-wide read-only overview for active company administrators only. */
    public function getCompanyTicketOverview(array $context): array
    {
        $this->assertRole($context, self::ROLE_ADMIN);
        $companyId = (int) $context['company_id'];
        if ($companyId <= 0) {
            throw new RuntimeException('INVALID_COMPANY');
        }

        // Tickets is the existing source of truth, including historical tickets
        // and drafts already published to INTEK. Do not add this total to drafts.
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total_tickets,
                   COALESCE(SUM(CASE WHEN creation_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END), 0) AS tickets_30d
            FROM tickets WHERE company_id = ?
        ");
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $tickets = $stmt->get_result()->fetch_assoc() ?: [];
        $stmt->close();

        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total_sections,
                   COALESCE(SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END), 0) AS active_sections
            FROM iweb_customer_portal_sections WHERE company_id = ?
        ");
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $sections = $stmt->get_result()->fetch_assoc() ?: [];
        $stmt->close();

        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total_profiles,
                   COALESCE(SUM(CASE WHEN p.is_active = 1 AND u.status = 'Active' THEN 1 ELSE 0 END), 0) AS active_users,
                   COALESCE(SUM(CASE WHEN p.portal_role = 'MANAGER' AND p.is_active = 1 AND u.status = 'Active' THEN 1 ELSE 0 END), 0) AS active_managers,
                   COALESCE(SUM(CASE WHEN p.portal_role = 'EXPERT' AND p.is_active = 1 AND u.status = 'Active' THEN 1 ELSE 0 END), 0) AS active_experts
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id = p.user_id
            WHERE p.company_id = ?
        ");
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_assoc() ?: [];
        $stmt->close();

        return [
            'total_tickets' => (int) ($tickets['total_tickets'] ?? 0),
            'tickets_30d' => (int) ($tickets['tickets_30d'] ?? 0),
            'total_sections' => (int) ($sections['total_sections'] ?? 0),
            'active_sections' => (int) ($sections['active_sections'] ?? 0),
            'total_profiles' => (int) ($users['total_profiles'] ?? 0),
            'active_users' => (int) ($users['active_users'] ?? 0),
            'active_managers' => (int) ($users['active_managers'] ?? 0),
            'active_experts' => (int) ($users['active_experts'] ?? 0),
        ];
    }

    /** Filtered, company-scoped ticket listing. Company is always derived from server session. */
    public function listCompanyTicketsPage(array $context, int $page = 1, int $pageSize = 25, array $filters = []): array
    {
        $this->assertRole($context, self::ROLE_ADMIN);
        $companyId = (int) ($context['company_id'] ?? 0);
        if ($companyId <= 0) throw new RuntimeException('INVALID_COMPANY');
        $pageSize = max(1, min(100, $pageSize));
        $search = trim((string) ($filters['q'] ?? ''));
        $search = mb_substr($search, 0, 120, 'UTF-8');
        $priority = strtolower(trim((string) ($filters['priority'] ?? '')));
        if (!in_array($priority, ['low','medium','high'], true)) $priority = '';
        $where = ' WHERE t.company_id = ?';
        $types = 'i';
        $params = [$companyId];
        if ($search !== '') {
            $where .= ' AND (t.ticket_number LIKE ? OR t.ticket_title LIKE ?)';
            $types .= 'ss';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if ($priority !== '') {
            $where .= ' AND t.priority = ?';
            $types .= 's';
            $params[] = $priority;
        }
        $stmt = $this->conn->prepare('SELECT COUNT(*) AS cnt FROM tickets t' . $where);
        if (!$stmt) throw new RuntimeException('TICKET_QUERY_FAILED');
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $total = (int) ($stmt->get_result()->fetch_assoc()['cnt'] ?? 0);
        $stmt->close();
        $pages = max(1, (int) ceil($total / $pageSize));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $pageSize;
        $stmt = $this->conn->prepare('SELECT t.id AS ticket_id, t.ticket_number, t.ticket_title, t.creation_date AS ticket_creation_date, t.priority FROM tickets t' . $where . ' ORDER BY t.id DESC LIMIT ? OFFSET ?');
        if (!$stmt) throw new RuntimeException('TICKET_QUERY_FAILED');
        $queryParams = array_merge($params, [$pageSize,$offset]);
        $stmt->bind_param($types.'ii', ...$queryParams);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($result && ($row = $result->fetch_assoc())) $rows[] = $row;
        if ($result) $result->free();
        $stmt->close();
        return ['rows'=>$rows,'total'=>$total,'page'=>$page,'pages'=>$pages,'q'=>$search,'priority'=>$priority,'page_size'=>$pageSize];
    }

    /** Export scoped tickets in chunks to avoid allocating a huge result array. */
    public function exportCompanyTicketsCsv(array $context, array $filters, $stream): void
    {
        $this->assertRole($context, self::ROLE_ADMIN);
        $page = 1;
        do {
            $data = $this->listCompanyTicketsPage($context, $page, 100, $filters);
            foreach ($data['rows'] as $ticket) {
                $cells = [(string)($ticket['ticket_number'] ?? ''),(string)($ticket['ticket_title'] ?? ''),(string)($ticket['ticket_creation_date'] ?? ''),(string)($ticket['priority'] ?? '')];
                foreach ($cells as &$cell) {
                    // Neutralize formula interpretation in spreadsheet applications.
                    if (preg_match('/^[\s]*[=+@\-]/u', $cell)) $cell = "'" . $cell;
                }
                unset($cell);
                fputcsv($stream, $cells);
            }
            ++$page;
        } while ($page <= (int)$data['pages']);
    }

    /** Read-only company ticket, including legacy and portal-published records.
     *  All access is guarded by a server-derived company ID and ADMIN role.
     *  No operation in this method mutates a legacy ticket.
     */
    public function getCompanyTicketDetail(array $context, int $ticketId): ?array
    {
        $this->assertRole($context, self::ROLE_ADMIN);
        $companyId = (int) ($context['company_id'] ?? 0);
        if ($companyId <= 0 || $ticketId <= 0) {
            return null;
        }

        $stmt = $this->conn->prepare("
            SELECT t.id AS ticket_id, t.ticket_number, t.ticket_title,
                   t.ticket_description, t.creation_date AS ticket_creation_date, t.priority,
                   t.status, t.indicator_number,
                   ty.type_name, ty.type_group
            FROM tickets AS t
            LEFT JOIN types AS ty ON ty.id = t.type_id
            WHERE t.id = ? AND t.company_id = ?
            LIMIT 1
        ");
        if ($stmt === false) {
            throw new RuntimeException('TICKET_DETAIL_QUERY_FAILED');
        }
        $stmt->bind_param('ii', $ticketId, $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        $ticket = $result ? $result->fetch_assoc() : null;
        if ($result) $result->free();
        $stmt->close();
        if (!$ticket) {
            return null;
        }

        // Match iWeb ticket detail visibility: non-local comments are public;
        // company-local comments are only visible to that same company.
        // Inactive comments remain hidden. Do not fetch internal-only company data.
        $stmt = $this->conn->prepare("
            SELECT c.id, c.parent_id, c.comment_text, c.creation_date,
                   COALESCE(u.name, a.name, '') AS author_name
            FROM comments AS c
            LEFT JOIN users AS u ON u.id = c.user_id
            LEFT JOIN admins AS a ON a.id = c.admin_id
            WHERE c.part_name = 'tickets'
              AND c.part_id = ?
              AND COALESCE(c.is_active, 1) = 1
              AND (COALESCE(c.local, 0) = 0 OR c.company_id = ?)
            ORDER BY c.id DESC
            LIMIT 100
        ");
        if ($stmt === false) {
            throw new RuntimeException('TICKET_COMMENTS_QUERY_FAILED');
        }
        $stmt->bind_param('ii', $ticketId, $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $comments[] = $row;
        }
        if ($result) $result->free();
        $stmt->close();
        $ticket['comments'] = $comments;
        return $ticket;
    }

    public function getPortalDashboard(array $context): array
    {
        $drafts = $this->listDrafts($context);
        if ($context['portal_role'] === self::ROLE_EXPERT) {
            $summary = ['total'=>0,'sent'=>0,'cancelled'=>0,'open_count'=>0,'aging_count'=>0,'revision_count'=>0,'avg_minutes_to_intek'=>0];
            $userId=(int)$context['user_id'];
            foreach ($drafts as $draft) {
                $summary['total']++;
                if ($draft['workflow_status'] === self::STATUS_SENT) $summary['sent']++;
                if ($draft['workflow_status'] === self::STATUS_CANCELLED) $summary['cancelled']++;
                if (!in_array($draft['workflow_status'], [self::STATUS_SENT,self::STATUS_CANCELLED], true)) $summary['open_count']++;
                if ((int)$draft['current_owner_user_id'] === $userId && !in_array($draft['workflow_status'], [self::STATUS_SENT,self::STATUS_CANCELLED], true)) $summary['aging_count']++;
                $summary['revision_count'] += (int)($draft['revision_count'] ?? 0);
            }
            $report=['summary'=>$summary,'sections'=>[],'users'=>[],'aging_days'=>(int)($context['settings']['draft_aging_days'] ?? 14)];
        } else {
            $report=$this->getReport($context);
        }
        $companyOverview = $context['portal_role'] === self::ROLE_ADMIN
            ? $this->getCompanyTicketOverview($context) : null;
        return ['report'=>$report,'recent_drafts'=>array_slice($drafts,0,8),'company_overview'=>$companyOverview];
    }

    public function saveCompanySettingsByAdmin(int $adminId, int $companyId, array $data): void
    {
        if ($adminId <= 0 || $companyId <= 0 || !$this->companyExists($companyId)) {
            throw new RuntimeException('INVALID_COMPANY');
        }
        $enabled = !empty($data['customer_portal_workflow_enabled']) ? 1 : 0;
        $agingDays = max(1, min(365, (int) ($data['draft_aging_days'] ?? 14)));
        $unitId = $this->nullablePositiveInt($data['portal_unit_id'] ?? null);
        $adminRbacId = $this->nullablePositiveInt($data['admin_rbac_id'] ?? null);
        $managerRbacId = $this->nullablePositiveInt($data['manager_rbac_id'] ?? null);
        $expertRbacId = $this->nullablePositiveInt($data['expert_rbac_id'] ?? null);

        if ($unitId !== null && !$this->unitBelongsToCompany($unitId, $companyId)) {
            throw new RuntimeException('INVALID_PORTAL_UNIT');
        }
        foreach ([$adminRbacId, $managerRbacId, $expertRbacId] as $rbacId) {
            if ($rbacId !== null && !$this->rbacBelongsToCompany($rbacId, $companyId)) {
                throw new RuntimeException('INVALID_PORTAL_RBAC');
            }
        }
        if ($enabled && ($unitId === null || $managerRbacId === null || $expertRbacId === null)) {
            throw new RuntimeException('PORTAL_MAPPING_REQUIRED_BEFORE_ENABLE');
        }
        if ($enabled && !$this->hasActiveCompanyAdmin($companyId)) {
            throw new RuntimeException('PORTAL_ADMIN_REQUIRED_BEFORE_ENABLE');
        }

        $old = $this->getCompanySettings($companyId);
        $stmt = $this->conn->prepare("
            INSERT INTO iweb_customer_portal_company_settings
            (company_id,customer_portal_workflow_enabled,portal_unit_id,admin_rbac_id,manager_rbac_id,expert_rbac_id,draft_aging_days,created_by,updated_by)
            VALUES (?,?,?,?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE
              customer_portal_workflow_enabled=VALUES(customer_portal_workflow_enabled),
              portal_unit_id=VALUES(portal_unit_id),
              admin_rbac_id=VALUES(admin_rbac_id),
              manager_rbac_id=VALUES(manager_rbac_id),
              expert_rbac_id=VALUES(expert_rbac_id),
              draft_aging_days=VALUES(draft_aging_days),
              updated_by=VALUES(updated_by)
        ");
        $stmt->bind_param('iiiiiiiii', $companyId, $enabled, $unitId, $adminRbacId, $managerRbacId, $expertRbacId, $agingDays, $adminId, $adminId);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('PORTAL_SETTINGS_SAVE_FAILED');
        }
        $stmt->close();
        $new = $this->getCompanySettings($companyId);
        $this->auditAdmin($adminId, $companyId, null, 'PORTAL_SETTINGS_UPDATED', 'COMPANY', $companyId, null, $old, $new);
    }

    public function listCompaniesForAdmin(): array
    {
        $result = $this->conn->query("
            SELECT c.id,c.company_name,
                   COALESCE(s.customer_portal_workflow_enabled,0) workflow_enabled,
                   s.portal_unit_id,s.admin_rbac_id,s.manager_rbac_id,s.expert_rbac_id,
                   COALESCE(s.draft_aging_days,14) draft_aging_days,
                   COALESCE(p.portal_users,0) portal_users
            FROM company_profiles c
            LEFT JOIN iweb_customer_portal_company_settings s ON s.company_id=c.id
            LEFT JOIN (
                SELECT company_id,COUNT(*) portal_users
                FROM iweb_customer_portal_user_profiles
                GROUP BY company_id
            ) p ON p.company_id=c.id
            ORDER BY c.company_name
        ");
        $rows = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $rows[] = $row;
        }
        if ($result) {
            $result->free();
        }
        return $rows;
    }

    /**
     * INTEK global audit browser. The IPanel controller owns the page/operation
     * authorization; this method only builds server-side filters.
     */
    public function listAuditForAdmin(array $filters = [], int $limit = 500): array
    {
        $limit = max(1, min(2000, $limit));
        $where = ['1=1'];
        $types = '';
        $params = [];

        $companyId = (int) ($filters['company_id'] ?? 0);
        if ($companyId > 0) {
            $where[] = 'a.company_id = ?';
            $types .= 'i';
            $params[] = $companyId;
        }
        $sectionId = (int) ($filters['section_id'] ?? 0);
        if ($sectionId > 0) {
            $where[] = 'a.section_id = ?';
            $types .= 'i';
            $params[] = $sectionId;
        }
        $userId = (int) ($filters['user_id'] ?? 0);
        if ($userId > 0) {
            $where[] = '(a.actor_user_id = ? OR a.target_user_id = ?)';
            $types .= 'ii';
            $params[] = $userId;
            $params[] = $userId;
        }
        $role = strtoupper(trim((string) ($filters['role'] ?? '')));
        if (in_array($role, [self::ROLE_ADMIN, self::ROLE_MANAGER, self::ROLE_EXPERT, 'INTEK_ADMIN'], true)) {
            $where[] = 'a.actor_role = ?';
            $types .= 's';
            $params[] = $role;
        }
        $action = trim((string) ($filters['action'] ?? ''));
        if ($action !== '' && preg_match('/^[A-Z0-9_]{2,100}$/', $action)) {
            $where[] = 'a.action_type = ?';
            $types .= 's';
            $params[] = $action;
        }

        $sql = "
            SELECT a.*,
                   c.company_name,
                   s.name AS section_name,
                   COALESCE(u.name, ad.name, CONCAT('#',a.actor_user_id)) AS actor_name,
                   tu.name AS target_user_name
            FROM iweb_customer_portal_ticket_audit_logs a
            LEFT JOIN company_profiles c ON c.id=a.company_id
            LEFT JOIN iweb_customer_portal_sections s ON s.id=a.section_id
            LEFT JOIN users u ON a.actor_source='USER' AND u.id=a.actor_user_id
            LEFT JOIN admins ad ON a.actor_source='ADMIN' AND ad.id=a.actor_user_id
            LEFT JOIN users tu ON tu.id=a.target_user_id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY a.created_at DESC, a.id DESC
            LIMIT {$limit}
        ";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return [];
        }
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $rows[] = $row;
        }
        if ($result) $result->free();
        $stmt->close();
        return $rows;
    }

    public function listPortalUsersForAdminAudit(int $companyId = 0): array
    {
        $sql = "
            SELECT p.user_id,p.company_id,p.portal_role,p.section_id,u.name,c.company_name
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id=p.user_id
            LEFT JOIN company_profiles c ON c.id=p.company_id
        ";
        if ($companyId > 0) {
            $sql .= ' WHERE p.company_id=?';
        }
        $sql .= ' ORDER BY c.company_name,u.name';
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) return [];
        if ($companyId > 0) $stmt->bind_param('i',$companyId);
        $stmt->execute();
        $result=$stmt->get_result();
        $rows=[];
        while ($result && ($row=$result->fetch_assoc())) $rows[]=$row;
        if ($result) $result->free();
        $stmt->close();
        return $rows;
    }

    public function listSectionsForAdminAudit(int $companyId = 0): array
    {
        $sql = "SELECT s.id,s.company_id,s.name,c.company_name FROM iweb_customer_portal_sections s LEFT JOIN company_profiles c ON c.id=s.company_id";
        if ($companyId > 0) $sql .= ' WHERE s.company_id=?';
        $sql .= ' ORDER BY c.company_name,s.name';
        $stmt=$this->conn->prepare($sql);
        if ($stmt===false) return [];
        if ($companyId>0) $stmt->bind_param('i',$companyId);
        $stmt->execute();
        $result=$stmt->get_result();
        $rows=[];
        while ($result && ($row=$result->fetch_assoc())) $rows[]=$row;
        if ($result) $result->free();
        $stmt->close();
        return $rows;
    }

    public function getAuditActionNames(int $companyId = 0): array
    {
        $sql='SELECT DISTINCT action_type FROM iweb_customer_portal_ticket_audit_logs';
        if ($companyId>0) $sql.=' WHERE company_id=?';
        $sql.=' ORDER BY action_type';
        $stmt=$this->conn->prepare($sql);
        if ($stmt===false) return [];
        if ($companyId>0) $stmt->bind_param('i',$companyId);
        $stmt->execute();
        $result=$stmt->get_result();
        $rows=[];
        while ($result && ($row=$result->fetch_assoc())) $rows[]=(string)$row['action_type'];
        if ($result) $result->free();
        $stmt->close();
        return $rows;
    }

    public function getCompanyLegacyOptions(int $companyId): array
    {
        $units = [];
        $stmt = $this->conn->prepare("SELECT id,unit_name FROM units WHERE company_id=? ORDER BY unit_name");
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($result && ($row = $result->fetch_assoc())) {
            $units[] = $row;
        }
        if ($result) $result->free();
        $stmt->close();

        $rbacs = [];
        $stmt = $this->conn->prepare("SELECT id,rbac_name,unit_id,all_rbac FROM rbac WHERE company_id=? ORDER BY rbac_name");
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($result && ($row = $result->fetch_assoc())) {
            $rbacs[] = $row;
        }
        if ($result) $result->free();
        $stmt->close();
        return ['units' => $units, 'rbacs' => $rbacs];
    }

    public function setPermissionByAdmin(int $adminId, int $targetUserId, string $permission, bool $allowed): void
    {
        if (!in_array($permission, self::PERMISSIONS, true)) {
            throw new RuntimeException('INVALID_PORTAL_PERMISSION');
        }
        $target = $this->getProfileUserAnyCompany($targetUserId);
        if (!$target) {
            throw new RuntimeException('USER_NOT_FOUND');
        }
        $role = strtoupper($target['portal_role']);
        if (strpos($permission, 'customer_admin_') === 0 && $role !== self::ROLE_ADMIN) {
            throw new RuntimeException('PERMISSION_ROLE_MISMATCH');
        }
        if (strpos($permission, 'manager_') === 0 && $role !== self::ROLE_MANAGER) {
            throw new RuntimeException('PERMISSION_ROLE_MISMATCH');
        }
        $companyId = (int) $target['company_id'];
        // A customer admin cannot grant financial rights outside their own company.
        $actor = $this->getContext($adminId, $companyId);
        if (empty($actor['profile_valid']) || (int)($_SESSION['company_id'] ?? 0) !== $companyId || $actor['portal_role'] !== self::ROLE_ADMIN
            || (int)$actor['company_id'] !== $companyId) {
            throw new RuntimeException('PORTAL_PERMISSION_DENIED');
        }
        $currentPermissions = $this->getUserPermissions($targetUserId, $companyId);
        $oldValue = !empty($currentPermissions[$permission]) ? 1 : 0;
        $value = $allowed ? 1 : 0;
        $stmt = $this->conn->prepare("
            INSERT INTO iweb_customer_portal_permissions (company_id,user_id,permission_name,is_allowed,granted_by_admin_id)
            VALUES (?,?,?,?,?)
            ON DUPLICATE KEY UPDATE is_allowed=VALUES(is_allowed), granted_by_admin_id=VALUES(granted_by_admin_id)
        ");
        $stmt->bind_param('iisii', $companyId, $targetUserId, $permission, $value, $adminId);
        $stmt->execute();
        $stmt->close();
        $this->auditAdmin($adminId, $companyId, $target['section_id'] ? (int) $target['section_id'] : null,
            $allowed ? 'USER_MANAGEMENT_PERMISSION_GRANTED' : 'USER_MANAGEMENT_PERMISSION_REVOKED',
            'USER', $targetUserId, $targetUserId,
            ['permission' => $permission, 'is_allowed' => $oldValue],
            ['permission' => $permission, 'is_allowed' => $value]
        );
    }

    public function savePortalProfileByAdmin(int $adminId, int $userId, int $companyId, string $role, ?int $sectionId, bool $active = true): void
    {
        $role = strtoupper($role);
        if (!in_array($role, [self::ROLE_ADMIN,self::ROLE_MANAGER,self::ROLE_EXPERT], true)) {
            throw new RuntimeException('INVALID_PORTAL_ROLE');
        }
        if (!$this->userBelongsToCompany($userId, $companyId)) {
            throw new RuntimeException('USER_COMPANY_MISMATCH');
        }

        $currentProfile = $this->getProfileUserAnyCompany($userId);
        if ($currentProfile && (int) $currentProfile['company_id'] !== $companyId) {
            throw new RuntimeException('USER_COMPANY_MISMATCH');
        }
        $oldSectionId = $currentProfile && $currentProfile['section_id'] !== null ? (int) $currentProfile['section_id'] : null;
        $oldRole = $currentProfile ? strtoupper((string) $currentProfile['portal_role']) : null;
        $oldActive = $currentProfile ? $this->isProfileUserActive($currentProfile) : false;

        if ($currentProfile && $oldActive) {
            $newSectionComparable = $role === self::ROLE_ADMIN ? null : $sectionId;
            $removesCurrentOwnership = !$active || $role !== $oldRole || $newSectionComparable !== $oldSectionId;
            if ($oldRole === self::ROLE_ADMIN && ($role !== self::ROLE_ADMIN || !$active)) {
                $settings = $this->getCompanySettings($companyId);
                if (!empty($settings['customer_portal_workflow_enabled']) && $this->countOtherActiveAdmins($companyId, $userId) === 0) {
                    throw new RuntimeException('LAST_PORTAL_ADMIN_REQUIRED');
                }
            }
            if ($removesCurrentOwnership && $this->countOpenDraftsForOwner($userId) > 0) {
                throw new RuntimeException('USER_HAS_OPEN_DRAFTS');
            }
            if ($oldRole === self::ROLE_MANAGER && $oldSectionId !== null && $removesCurrentOwnership
                && $this->countActiveExperts($oldSectionId) > 0
                && $this->countOtherActiveManagers($oldSectionId, $userId) === 0) {
                throw new RuntimeException('LAST_MANAGER_WITH_ACTIVE_EXPERTS');
            }
        }

        if ($role === self::ROLE_ADMIN) {
            $sectionId = null;
        } else {
            if ($sectionId === null) {
                // Allowed for migration/pending assignment only.
            } else {
                $section = $this->getSectionInCompany($sectionId, $companyId);
                if (!$section || (int) $section['is_active'] !== 1) {
                    throw new RuntimeException('SECTION_NOT_ACTIVE');
                }
                if ($role === self::ROLE_MANAGER) {
                    $existing = $this->getActiveManagerForSection($sectionId);
                    if ($existing && (int) $existing['user_id'] !== $userId) {
                        throw new RuntimeException('SECTION_ALREADY_HAS_MANAGER');
                    }
                }
                if ($role === self::ROLE_EXPERT && $this->getActiveManagerForSection($sectionId) === null) {
                    throw new RuntimeException('SECTION_MANAGER_REQUIRED');
                }
            }
        }
        $activeValue = $active ? 1 : 0;
        $stmt = $this->conn->prepare("
            INSERT INTO iweb_customer_portal_user_profiles (user_id,company_id,portal_role,section_id,is_active,created_by,updated_by)
            VALUES (?,?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE company_id=VALUES(company_id),portal_role=VALUES(portal_role),section_id=VALUES(section_id),is_active=VALUES(is_active),updated_by=VALUES(updated_by)
        ");
        $stmt->bind_param('iisiiii', $userId, $companyId, $role, $sectionId, $activeValue, $adminId, $adminId);
        $stmt->execute();
        $stmt->close();
        $this->auditAdmin($adminId, $companyId, $sectionId, 'PORTAL_PROFILE_SAVED', 'USER', $userId, $userId,
            $currentProfile ? [
                'portal_role' => $currentProfile['portal_role'],
                'section_id' => $currentProfile['section_id'],
                'is_active' => (int) $currentProfile['is_active']
            ] : null,
            ['portal_role' => $role, 'section_id' => $sectionId, 'is_active' => $activeValue]
        );
    }

    public function listLegacyUsersForCompany(int $companyId): array
    {
        $stmt = $this->conn->prepare("
            SELECT u.id,u.name,u.email,u.mobile,u.status,u.unit_id,u.rbac_id,
                   p.portal_role,p.section_id,p.is_active AS profile_active,s.name section_name
            FROM users u
            INNER JOIN units un ON un.id=u.unit_id AND un.company_id=?
            LEFT JOIN iweb_customer_portal_user_profiles p ON p.user_id=u.id
            LEFT JOIN iweb_customer_portal_sections s ON s.id=p.section_id
            ORDER BY u.name
        ");
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows=[];
        while ($result && ($row=$result->fetch_assoc())) $rows[]=$row;
        if ($result) $result->free();
        $stmt->close();
        return $rows;
    }

    private function recordDraftView(array $context, int $draftId): void
    {
        $userId = (int) $context['user_id'];
        // Atomic upsert prevents two simultaneous first views from racing into the
        // UNIQUE(draft_id,user_id) key. MySQL/MariaDB reports 1 affected row for
        // a new insert and 2 for an updated existing row.
        $stmt = $this->conn->prepare("
            INSERT INTO iweb_customer_portal_ticket_views (draft_id,user_id)
            VALUES (?,?)
            ON DUPLICATE KEY UPDATE
                last_viewed_at=NOW(),
                view_count=view_count+1
        ");
        if ($stmt === false) {
            return;
        }
        $stmt->bind_param('ii', $draftId, $userId);
        $stmt->execute();
        $isFirstView = ($stmt->affected_rows === 1);
        $stmt->close();

        if ($isFirstView) {
            $this->auditUser($context, 'TICKET_FIRST_VIEWED', 'DRAFT', $draftId, null, null, null);
        }
    }

    private function canViewDraft(array $context, array $draft): bool
    {
        if ((int) $draft['company_id'] !== (int) $context['company_id']) {
            return false;
        }
        if ($context['portal_role'] === self::ROLE_ADMIN) {
            return true;
        }
        if ((int) $draft['section_id'] !== (int) $context['section_id']) {
            return false;
        }
        if ($context['portal_role'] === self::ROLE_MANAGER) {
            return true;
        }
        $userId = (int) $context['user_id'];
        return (int) $draft['creator_user_id'] === $userId || (int) $draft['current_owner_user_id'] === $userId;
    }

    private function assertCurrentOwner(array $context, array $draft): void
    {
        if ((int) $draft['current_owner_user_id'] !== (int) $context['user_id']) {
            throw new RuntimeException('DRAFT_NOT_CURRENT_OWNER');
        }
    }

    private function assertRole(array $context, string $role): void
    {
        if (($context['portal_role'] ?? null) !== $role || empty($context['operational'])) {
            throw new RuntimeException('CUSTOMER_PORTAL_ACCESS_DENIED');
        }
    }

    private function getSectionInCompany(int $sectionId, int $companyId): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM iweb_customer_portal_sections WHERE id=? AND company_id=? LIMIT 1");
        if ($stmt === false) return null;
        $stmt->bind_param('ii', $sectionId, $companyId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    private function getActiveManagerForSection(int $sectionId): ?array
    {
        if ($sectionId <= 0) return null;
        $stmt = $this->conn->prepare("
            SELECT p.*,u.name,u.status
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id=p.user_id AND u.status='Active'
            WHERE p.section_id=? AND p.portal_role='MANAGER' AND p.is_active=1
            ORDER BY p.id ASC LIMIT 1
        ");
        if ($stmt === false) return null;
        $stmt->bind_param('i', $sectionId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    private function countOpenDraftsForOwner(int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM iweb_customer_portal_ticket_drafts WHERE current_owner_user_id=? AND workflow_status NOT IN ('SENT_TO_INTEK','CANCELLED')");
        if ($stmt === false) {
            return 0;
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int) ($row['cnt'] ?? 0);
    }

    private function countActiveExperts(int $sectionId): int
    {
        if ($sectionId <= 0) return 0;
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) cnt
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id=p.user_id AND u.status='Active'
            WHERE p.section_id=? AND p.portal_role='EXPERT' AND p.is_active=1
        ");
        if ($stmt === false) return 0;
        $stmt->bind_param('i', $sectionId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int) ($row['cnt'] ?? 0);
    }

    private function countOtherActiveAdmins(int $companyId, int $excludeUserId): int
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) cnt
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id=p.user_id AND u.status='Active'
            WHERE p.company_id=? AND p.portal_role='ADMIN' AND p.is_active=1 AND p.user_id<>?
        ");
        if ($stmt === false) return 0;
        $stmt->bind_param('ii', $companyId, $excludeUserId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int) ($row['cnt'] ?? 0);
    }

    private function countOtherActiveManagers(int $sectionId, int $excludeUserId): int
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) cnt
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id=p.user_id AND u.status='Active'
            WHERE p.section_id=? AND p.portal_role='MANAGER' AND p.is_active=1 AND p.user_id<>?
        ");
        $stmt->bind_param('ii', $sectionId, $excludeUserId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int) ($row['cnt'] ?? 0);
    }

    private function getProfileUser(int $userId, int $companyId): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT p.*,u.name,u.email,u.mobile,u.status,u.unit_id,u.rbac_id,s.name section_name,s.is_active section_active
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id=p.user_id
            LEFT JOIN iweb_customer_portal_sections s ON s.id=p.section_id
            WHERE p.user_id=? AND p.company_id=? LIMIT 1
        ");
        $stmt->bind_param('ii', $userId, $companyId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    private function getProfileUserAnyCompany(int $userId): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT p.*,u.name,u.email,u.status
            FROM iweb_customer_portal_user_profiles p
            INNER JOIN users u ON u.id=p.user_id
            WHERE p.user_id=? LIMIT 1
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    private function isProfileUserActive(array $profile): bool
    {
        return (int) ($profile['is_active'] ?? 0) === 1 && ($profile['status'] ?? '') === 'Active';
    }

    private function validateLegacyMapping(int $companyId, int $unitId, int $rbacId): bool
    {
        return $this->unitBelongsToCompany($unitId, $companyId) && $this->rbacBelongsToCompany($rbacId, $companyId);
    }

    private function unitBelongsToCompany(int $unitId, int $companyId): bool
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM units WHERE id=? AND company_id=? LIMIT 1");
        $stmt->bind_param('ii', $unitId, $companyId);
        $stmt->execute();
        $ok = $stmt->get_result()->num_rows === 1;
        $stmt->close();
        return $ok;
    }

    private function rbacBelongsToCompany(int $rbacId, int $companyId): bool
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM rbac WHERE id=? AND company_id=? LIMIT 1");
        $stmt->bind_param('ii', $rbacId, $companyId);
        $stmt->execute();
        $ok = $stmt->get_result()->num_rows === 1;
        $stmt->close();
        return $ok;
    }

    private function userBelongsToCompany(int $userId, int $companyId): bool
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM users u INNER JOIN units un ON un.id=u.unit_id WHERE u.id=? AND un.company_id=? LIMIT 1");
        $stmt->bind_param('ii', $userId, $companyId);
        $stmt->execute();
        $ok = $stmt->get_result()->num_rows === 1;
        $stmt->close();
        return $ok;
    }

    private function hasActiveCompanyAdmin(int $companyId): bool
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM iweb_customer_portal_user_profiles p INNER JOIN users u ON u.id=p.user_id AND u.status='Active' WHERE p.company_id=? AND p.portal_role='ADMIN' AND p.is_active=1 LIMIT 1");
        if ($stmt === false) {
            return false;
        }
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $ok = $stmt->get_result()->num_rows === 1;
        $stmt->close();
        return $ok;
    }

    private function companyExists(int $companyId): bool
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM company_profiles WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $companyId);
        $stmt->execute();
        $ok = $stmt->get_result()->num_rows === 1;
        $stmt->close();
        return $ok;
    }

    private function typeExists(int $typeId): bool
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM types WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $typeId);
        $stmt->execute();
        $ok = $stmt->get_result()->num_rows === 1;
        $stmt->close();
        return $ok;
    }

    private function normalizePriority(string $priority): string
    {
        $priority = strtolower($priority);
        return in_array($priority, ['low','medium','high'], true) ? $priority : 'low';
    }

    private function nullablePositiveInt($value): ?int
    {
        $int = (int) $value;
        return $int > 0 ? $int : null;
    }

    private function auditUser(array $context, string $action, string $entityType, ?int $entityId, ?int $targetUserId, $oldData, $newData, ?int $sectionIdOverride = null): void
    {
        $auditSectionId = $sectionIdOverride !== null
            ? $sectionIdOverride
            : ($context['section_id'] !== null ? (int) $context['section_id'] : null);
        $this->insertAudit(
            (int) $context['company_id'],
            $auditSectionId,
            (int) $context['user_id'],
            'USER',
            (string) $context['portal_role'],
            $action,
            $entityType,
            $entityId,
            $targetUserId,
            $oldData,
            $newData
        );
    }

    private function auditAdmin(int $adminId, int $companyId, ?int $sectionId, string $action, string $entityType, ?int $entityId, ?int $targetUserId, $oldData, $newData): void
    {
        $this->insertAudit($companyId, $sectionId, $adminId, 'ADMIN', 'INTEK_ADMIN', $action, $entityType, $entityId, $targetUserId, $oldData, $newData);
    }

    private function insertAudit(int $companyId, ?int $sectionId, int $actorId, string $source, string $role, string $action, string $entityType, ?int $entityId, ?int $targetUserId, $oldData, $newData): void
    {
        $oldJson = $oldData === null ? null : json_encode($oldData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $newJson = $newData === null ? null : json_encode($newData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? mb_substr($_SERVER['HTTP_USER_AGENT'], 0, 500, 'UTF-8') : null;
        $stmt = $this->conn->prepare("
            INSERT INTO iweb_customer_portal_ticket_audit_logs
            (company_id,section_id,actor_user_id,actor_source,actor_role,action_type,entity_type,entity_id,target_user_id,old_data,new_data,ip_address,user_agent)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");
        if ($stmt === false) {
            throw new RuntimeException('AUDIT_WRITE_FAILED');
        }
        $stmt->bind_param('iiissssiissss', $companyId, $sectionId, $actorId, $source, $role, $action, $entityType, $entityId, $targetUserId, $oldJson, $newJson, $ip, $ua);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new RuntimeException('AUDIT_WRITE_FAILED');
        }
        $stmt->close();
    }
}
