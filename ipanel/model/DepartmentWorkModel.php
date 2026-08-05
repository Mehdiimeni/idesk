<?php

namespace ipanel\model;

class DepartmentWorkModel
{
    private $conn;
    private $sectionsTable = 'department_work_sections';
    private $itemsTable = 'department_work_items';
    private $itemsView = 'department_work_items_view';

    public function __construct($db)
    {
        $this->conn = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Flow دریافت واحدهای فعال:
     * 1) فقط واحدهای فعال خوانده می‌شوند.
     * 2) ترتیب نمایش از display_order و سپس id پیروی می‌کند.
     * 3) خروجی برای ساخت تب‌ها و کنترل واحدهای قابل مشاهده استفاده می‌شود.
     */
    public function getActiveSections(): array
    {
        $sql = "SELECT *
                FROM {$this->sectionsTable}
                WHERE is_active = 1
                ORDER BY display_order ASC, id ASC";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new \RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $stmt->close();

        return $rows ?: [];
    }

    public function getSectionById(int $sectionId): ?array
    {
        $sql = "SELECT *
                FROM {$this->sectionsTable}
                WHERE id = ? AND is_active = 1
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new \RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param('i', $sectionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $result->free();
        $stmt->close();

        return $row ?: null;
    }

    public function getSectionByName(string $sectionName): ?array
    {
        $sql = "SELECT *
                FROM {$this->sectionsTable}
                WHERE section_name = ? AND is_active = 1
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new \RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param('s', $sectionName);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $result->free();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Flow ثبت کار:
     * 1) اطلاعات توسط Controller اعتبارسنجی می‌شود.
     * 2) شماره تیکت، در صورت وجود، ابتدا به ticket_id تبدیل می‌شود.
     * 3) تاریخ ثبت توسط دیتابیس و مدیر ثبت‌کننده توسط نشست تعیین می‌شود.
     * 4) وضعیت اولیه همیشه current است و از فرم دریافت نمی‌شود.
     */
    public function addWorkItem(
        int $sectionId,
        string $subject,
        string $priority,
        string $requestedBy,
        ?int $ticketId,
        bool $isPhoneRequest,
        ?string $callerName,
        int $createdByAdminId
    ): int {
        $sql = "INSERT INTO {$this->itemsTable}
                (
                    section_id,
                    subject,
                    priority,
                    requested_by,
                    ticket_id,
                    is_phone_request,
                    caller_name,
                    status,
                    created_by_admin_id
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, 'current', ?)";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new \RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $phoneRequest = $isPhoneRequest ? 1 : 0;
        $stmt->bind_param(
            'isssiisi',
            $sectionId,
            $subject,
            $priority,
            $requestedBy,
            $ticketId,
            $phoneRequest,
            $callerName,
            $createdByAdminId
        );

        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new \RuntimeException('Insert failed: ' . $error);
        }

        $insertId = (int) $stmt->insert_id;
        $stmt->close();

        return $insertId;
    }

    /**
     * پایان کار به‌صورت کنترل‌شده انجام می‌شود:
     * - رکورد باید متعلق به واحد مجاز باشد.
     * - وضعیت باید current باشد تا پایان دوباره ثبت نشود.
     * - رکورد حذف نمی‌شود و با ثبت مدیر و زمان پایان وارد آرشیو می‌شود.
     */
    public function completeWorkItem(int $itemId, int $sectionId, int $completedByAdminId): bool
    {
        $sql = "UPDATE {$this->itemsTable}
                SET status = 'completed',
                    completed_by_admin_id = ?,
                    completion_date = CURRENT_TIMESTAMP
                WHERE id = ?
                  AND section_id = ?
                  AND status = 'current'";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new \RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param('iii', $completedByAdminId, $itemId, $sectionId);
        $stmt->execute();
        $updated = $stmt->affected_rows === 1;
        $stmt->close();

        return $updated;
    }

    public function getCurrentWorks(int $sectionId): array
    {
        $sql = "SELECT *
                FROM {$this->itemsView}
                WHERE section_id = ?
                  AND status = 'current'
                ORDER BY creation_date ASC, id ASC";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new \RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param('i', $sectionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $stmt->close();

        return $rows ?: [];
    }

    public function getArchiveMonths(int $sectionId): array
    {
        $sql = "SELECT
                    YEAR(completion_date) AS archive_year,
                    MONTH(completion_date) AS archive_month,
                    COUNT(*) AS total
                FROM {$this->itemsTable}
                WHERE section_id = ?
                  AND status = 'completed'
                  AND completion_date IS NOT NULL
                GROUP BY YEAR(completion_date), MONTH(completion_date)
                ORDER BY archive_year DESC, archive_month DESC";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new \RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param('i', $sectionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $stmt->close();

        return $rows ?: [];
    }

    public function getArchivedWorks(int $sectionId, ?int $year = null, ?int $month = null): array
    {
        $where = "section_id = ? AND status = 'completed'";
        $types = 'i';
        $params = [$sectionId];

        if ($year !== null && $month !== null) {
            $where .= " AND YEAR(completion_date) = ? AND MONTH(completion_date) = ?";
            $types .= 'ii';
            $params[] = $year;
            $params[] = $month;
        }

        $sql = "SELECT *
                FROM {$this->itemsView}
                WHERE {$where}
                ORDER BY completion_date DESC, id DESC";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new \RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $stmt->close();

        return $rows ?: [];
    }

    /**
     * آمار هر واحد در یک Query محاسبه می‌شود تا صفحه برای هر کارت آماری
     * چندین Query مجزا اجرا نکند.
     */
    public function getSectionStatistics(int $sectionId): array
    {
        $sql = "SELECT
                    SUM(CASE WHEN status = 'current' THEN 1 ELSE 0 END) AS total_current,
                    SUM(CASE WHEN status = 'current' AND priority = 'force' THEN 1 ELSE 0 END) AS total_force,
                    SUM(CASE WHEN status = 'current' AND priority = 'normal' THEN 1 ELSE 0 END) AS total_normal,
                    SUM(CASE
                        WHEN status = 'completed'
                         AND YEAR(completion_date) = YEAR(CURRENT_DATE)
                         AND MONTH(completion_date) = MONTH(CURRENT_DATE)
                        THEN 1 ELSE 0 END) AS completed_this_month,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS total_completed,
                    AVG(CASE
                        WHEN status = 'completed' AND completion_date IS NOT NULL
                        THEN TIMESTAMPDIFF(MINUTE, creation_date, completion_date)
                        ELSE NULL END) AS average_completion_minutes,
                    MAX(CASE
                        WHEN status = 'current'
                        THEN TIMESTAMPDIFF(MINUTE, creation_date, CURRENT_TIMESTAMP)
                        ELSE NULL END) AS oldest_current_minutes
                FROM {$this->itemsTable}
                WHERE section_id = ?";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new \RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param('i', $sectionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc() ?: [];
        $result->free();
        $stmt->close();

        return [
            'total_current' => (int) ($row['total_current'] ?? 0),
            'total_force' => (int) ($row['total_force'] ?? 0),
            'total_normal' => (int) ($row['total_normal'] ?? 0),
            'completed_this_month' => (int) ($row['completed_this_month'] ?? 0),
            'total_completed' => (int) ($row['total_completed'] ?? 0),
            'average_completion_minutes' => isset($row['average_completion_minutes'])
                ? (int) round((float) $row['average_completion_minutes'])
                : 0,
            'oldest_current_minutes' => isset($row['oldest_current_minutes'])
                ? (int) $row['oldest_current_minutes']
                : 0,
        ];
    }

    public function getTicketByNumber(string $ticketNumber): ?array
    {
        $sql = "SELECT id, ticket_number, ticket_title
                FROM tickets
                WHERE ticket_number = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new \RuntimeException('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param('s', $ticketNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $result->free();
        $stmt->close();

        return $row ?: null;
    }
}
