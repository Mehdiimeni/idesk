<?php
declare(strict_types=1);
namespace iweb\model;


final class CustomerPriorityService
{

    private $conn;

    /**
     * وضعیت‌هایی که تیکت اگر در این‌ها باشد:
     * - نباید اجازه افزودن به لیست داشته باشد
     * - اگر بعداً به این‌ها تغییر کند باید از لیست حذف شود
     *
     * این لیست در کد است (طبق خواسته شما) و قابل تغییر.
     */
    private array $excludedConditions = [
        'condition_duplicate',
        'condition_pendency',
        'condition_final_done',
        'condition_clearing',
        'condition_done',
        'condition_archive',
        'condition_regect',
        'condition_acepted_test_auto',
    ];



    public function __construct($db, ?array $excludedConditions = null)
    {
        $this->conn = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // اگر PDO است، خطاها را Exception کن (اختیاری ولی توصیه‌شده)
        if ($this->conn instanceof \PDO) {
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        if (is_array($excludedConditions)) {
            $this->excludedConditions = $excludedConditions;
        }
    }

    public function getExcludedConditions(): array
    {
        return $this->excludedConditions;
    }

    /**
     * دریافت/ایجاد لیست برای مشتری + type_group
     */
    public function getOrCreateList(int $userId, string $typeGroup, ?int $companyId = null): array
    {
        $sql = "SELECT * FROM priority_lists WHERE user_id = ? AND type_group = ? LIMIT 1";
        $st = $this->conn->prepare($sql);
        $st->bind_param('is', $userId, $typeGroup);
        $st->execute();
        $result = $st->get_result();
        $row = $result->fetch_assoc();
        $result->free();
        $st->close();
        if ($row)
            return $row;

        $ins = "INSERT INTO priority_lists (user_id, company_id, type_group, status, revision)
                VALUES (?, ?, ?, 'needs_approval', 1)";
        $st = $this->conn->prepare($ins);
        $st->bind_param('iis', $userId, $companyId, $typeGroup);
        $st->execute();
        $id = (int) $this->conn->insert_id;
        $st->close();
        return $this->getListById($id);
    }

    public function getListById(int $listId): array
    {
        $st = $this->conn->prepare("SELECT * FROM priority_lists WHERE id = ? LIMIT 1");
        $st->bind_param('i', $listId);
        $st->execute();
        $result = $st->get_result();
        $row = $result->fetch_assoc();
        $result->free();
        $st->close();
        if (!$row)
            throw new \RuntimeException("Priority list not found: {$listId}");
        return $row;
    }

    public function getListItems(int $listId): array
    {
        $sql = "SELECT pli.*, t.ticket_number, t.ticket_status, t.type_id, t.ticket_title
                FROM priority_list_items pli
                JOIN grid_user_ticket_data t ON t.ticket_id = pli.ticket_id
                WHERE pli.priority_list_id = ?
                ORDER BY pli.priority ASC";
        $st = $this->conn->prepare($sql);
        $st->bind_param('i', $listId);
        $st->execute();
        $result = $st->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $result->free();
        $st->close();
        return $items;
    }

    /**
     * مشتری: افزودن تیکت به لیست با اولویت مشخص + کامنت اجباری
     */
    public function addTicketToList(
        int $userId,
        string $typeGroup,
        int $ticketId,
        int $priority,
        string $commentText,
        ?int $companyId = null
    ): void {
        $this->assertPriority($priority);
        $this->assertNonEmptyComment($commentText);

        $this->conn->begin_transaction();
        try {
            $list = $this->lockListRow($userId, $typeGroup, $companyId);
            $listId = (int) $list['id'];

            $ticket = $this->fetchTicketForUserAndGroupOrFail($companyId, $ticketId, $typeGroup);
            $this->assertTicketAllowedByStatus(status: $ticket['ticket_status']);

            $count = $this->countItemsForUpdate($listId);
            if ($count >= 5) {
                throw new \RuntimeException(_lang['list_is_full'] ?? "List is full (max 5).");
            }

            // تکراری نباشد
            $st = $this->conn->prepare("SELECT 1 FROM priority_list_items WHERE priority_list_id=? AND ticket_id=? LIMIT 1");
            $st->bind_param('ii', $listId, $ticketId);
            $st->execute();
            $result = $st->get_result();
            if ($result->fetch_assoc()) {
                $result->free();
                $st->close();
                throw new \RuntimeException(_lang['ticket_already_in_list'] ?? "Ticket already exists in the list.");
            }
            $result->free();
            $st->close();

            // اولویت آزاد باشد
            $st = $this->conn->prepare("SELECT 1 FROM priority_list_items WHERE priority_list_id=? AND priority=? LIMIT 1");
            $st->bind_param('ii', $listId, $priority);
            $st->execute();
            $result = $st->get_result();
            if ($result->fetch_assoc()) {
                $result->free();
                $st->close();
                throw new \RuntimeException(strtr(_lang['priority_already_taken'] ?? "Priority {priority} is already taken.", ['{priority}' => $priority]));
            }
            $result->free();
            $st->close();

            // افزودن آیتم
            $userCompany = $_SESSION["user_company"] ?? '';
            $companyIdSession = (int) ($_SESSION["company_id"] ?? 0);
            $st = $this->conn->prepare(
                "INSERT INTO priority_list_items (priority_list_id, ticket_id, priority, created_by_user_id,  company_id, user_company)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $st->bind_param('iiiiis', $listId, $ticketId, $priority, $userId, $companyIdSession, $userCompany);
            $st->execute();
            $st->close();

            $this->insertItemLog($listId, $ticketId, 'added', $userId, null, null, null, _lang['log_ticket_added'] ?? "added by customer");

            // کامنت سطح آیتم (برای افزودن)
            $itemId = $this->getListItemId($listId, $ticketId);
            $this->insertItemComment($itemId, $userId, null, $commentText);

            // تغییر وضعیت لیست -> needs_approval + revision++
            $this->bumpListRevisionNeedsApproval($listId, $userId, strtr(_lang['log_customer_added_ticket'] ?? "customer added ticket {ticketId} with priority {priority}", ['{ticketId}' => $ticketId, '{priority}' => $priority]));

            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /**
     * مشتری: تغییر اولویت یک تیکت + کامنت اجباری
     */
    public function changeTicketPriority(
        int $userId,
        string $typeGroup,
        int $ticketId,
        int $newPriority,
        string $commentText,
        ?int $companyId = null
    ): void {
        $this->assertPriority($newPriority);
        $this->assertNonEmptyComment($commentText);

        $this->conn->begin_transaction();
        try {
            $list = $this->lockListRow($userId, $typeGroup, $companyId);
            $listId = (int) $list['id'];

            // آیتم را قفل کن
            $item = $this->lockListItemOrFail($listId, $ticketId);

            $oldPriority = (int) $item['priority'];
            if ($oldPriority === $newPriority) {
                throw new \RuntimeException(_lang['priority_same_as_old'] ?? "New priority is the same as old priority.");
            }

            // بررسی اگر اولویت جدید قبلاً گرفته شده باشد
            $st = $this->conn->prepare("SELECT id FROM priority_list_items WHERE priority_list_id=? AND priority=? LIMIT 1 FOR UPDATE");
            $st->bind_param('ii', $listId, $newPriority);
            $st->execute();
            $result = $st->get_result();
            $existingItem = $result->fetch_assoc();
            $result->free();
            $st->close();

            if ($existingItem) {
                // Swap: از priority موقت استفاده کن تا conflict نشود
                // 1. تیکت دیگر را به priority موقت (0) منتقل کن
                $existingItemId = (int) $existingItem['id'];
                $tempPriority = 0;
                $st = $this->conn->prepare("UPDATE priority_list_items SET priority = ? WHERE id = ?");
                $st->bind_param('ii', $tempPriority, $existingItemId);
                $st->execute();
                $st->close();

                // 2. تیکت فعلی را به priority جدید منتقل کن
                $st = $this->conn->prepare("UPDATE priority_list_items SET priority = ? WHERE id = ?");
                $st->bind_param('ii', $newPriority, $item['id']);
                $st->execute();
                $st->close();

                // 3. تیکت دیگر را به priority قدیم منتقل کن
                $st = $this->conn->prepare("UPDATE priority_list_items SET priority = ? WHERE id = ?");
                $st->bind_param('ii', $oldPriority, $existingItemId);
                $st->execute();
                $st->close();
            } else {
                // تیکت فعلی را به اولویت جدید تغییر بده
                $st = $this->conn->prepare("UPDATE priority_list_items SET priority = ? WHERE id = ?");
                $st->bind_param('ii', $newPriority, $item['id']);
                $st->execute();
                $st->close();
            }

            $this->insertItemLog($listId, $ticketId, 'priority_changed', $userId, null, $oldPriority, $newPriority, _lang['log_priority_changed'] ?? "changed by customer");
            $this->insertItemComment((int) $item['id'], $userId, null, $commentText);

            $this->bumpListRevisionNeedsApproval($listId, $userId, strtr(_lang['log_customer_changed_priority'] ?? "customer changed priority ticket {ticketId} {oldPriority}->{newPriority}", ['{ticketId}' => $ticketId, '{oldPriority}' => $oldPriority, '{newPriority}' => $newPriority]));

            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /**
     * مشتری: حذف دستی تیکت از لیست + کامنت اجباری
     */
    public function removeTicketManual(
        int $userId,
        string $typeGroup,
        int $ticketId,
        string $commentText,
        ?int $companyId = null
    ): void {
        $this->assertNonEmptyComment($commentText);

        $this->conn->begin_transaction();
        try {
            $list = $this->lockListRow($userId, $typeGroup, $companyId);
            $listId = (int) $list['id'];

            $item = $this->lockListItemOrFail($listId, $ticketId);
            $removedPriority = (int) $item['priority'];

            // کامنت آیتم
            $this->insertItemComment((int) $item['id'], $userId, null, $commentText);

            // حذف
            $st = $this->conn->prepare("DELETE FROM priority_list_items WHERE id = ?");
            $st->bind_param('i', $item['id']);
            $st->execute();
            $st->close();

            $this->insertItemLog($listId, $ticketId, 'removed_manual', $userId, null, $removedPriority, null, _lang['log_removed_by_customer'] ?? "removed by customer");

            // شیفت اولویت‌ها
            $this->shiftDownAfterRemoval($listId, $removedPriority, $userId, null, _lang['log_manual_removal'] ?? "manual removal");

            $this->bumpListRevisionNeedsApproval($listId, $userId, strtr(_lang['log_customer_removed_ticket'] ?? "customer removed ticket {ticketId} from priority {removedPriority}", ['{ticketId}' => $ticketId, '{removedPriority}' => $removedPriority]));

            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /* ------------------------------------------------------------------ */
    /* Hooks (برای صدا زدن از جای دیگر سیستم شما)                           */
    /* ------------------------------------------------------------------ */

    /**
     * Hook: وقتی وضعیت تیکت تغییر کرد.
     * اگر وضعیت جدید جزو excluded بود -> حذف خودکار از هر لیستی که این تیکت داخلش هست.
     */
    public function onTicketStatusChanged(int $ticketId, string $newStatus, ?int $actorUserId = null, ?int $actorAdminId = null): void
    {
        if (!in_array(strtolower($newStatus), $this->excludedConditions, true)) {
            return;
        }

        $this->conn->begin_transaction();
        try {
            $lists = $this->findListsContainingTicketForUpdate($ticketId);

            foreach ($lists as $row) {
                $listId = (int) $row['priority_list_id'];
                $removedPriority = (int) $row['priority'];

                // حذف آیتم
                $st = $this->conn->prepare("DELETE FROM priority_list_items WHERE id = ?");
                $st->bind_param('i', $row['id']);
                $st->execute();
                $st->close();

                $reason = strtr(_lang['log_status_changed'] ?? "status changed to {status}", ['{status}' => $newStatus]);
                $this->insertItemLog($listId, $ticketId, 'removed_auto', $actorUserId, $actorAdminId, $removedPriority, null, $reason);

                // شیفت
                $this->shiftDownAfterRemoval($listId, $removedPriority, $actorUserId, $actorAdminId, $reason);

                // لیست needs_approval + revision++ (طبق قانون کلی شما)
                $this->bumpListRevisionNeedsApproval($listId, $actorUserId, $reason, $actorAdminId);
            }

            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /**
     * Hook: وقتی نوع تیکت تغییر کرد (type_id / type_group).
     * باید از هر لیستی که هست حذف شود و reason شامل "type changed by user/admin" باشد.
     */
    public function onTicketTypeChanged(int $ticketId, ?int $actorUserId = null, ?int $actorAdminId = null): void
    {
        $this->conn->begin_transaction();
        try {
            $lists = $this->findListsContainingTicketForUpdate($ticketId);

            foreach ($lists as $row) {
                $listId = (int) $row['priority_list_id'];
                $removedPriority = (int) $row['priority'];

                $st = $this->conn->prepare("DELETE FROM priority_list_items WHERE id = ?");
                $st->bind_param('i', $row['id']);
                $st->execute();
                $st->close();

                $reason = strtr(_lang['log_type_changed'] ?? "type changed by {by}", ['{by}' => ($actorAdminId ? "admin_id={$actorAdminId}" : "user_id={$actorUserId}")]);
                $this->insertItemLog($listId, $ticketId, 'removed_due_to_type_change', $actorUserId, $actorAdminId, $removedPriority, null, $reason);

                $this->shiftDownAfterRemoval($listId, $removedPriority, $actorUserId, $actorAdminId, $reason);

                $this->bumpListRevisionNeedsApproval($listId, $actorUserId, $reason, $actorAdminId);
            }

            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    private function assertPriority(int $p): void
    {
        if ($p < 1 || $p > 5)
            throw new \InvalidArgumentException(_lang['priority_invalid'] ?? "priority must be 1..5");
    }

    private function assertNonEmptyComment(string $text): void
    {
        if (trim($text) === '')
            throw new \InvalidArgumentException(_lang['comment_required_error'] ?? "comment is required");
    }

    private function assertTicketAllowedByStatus(string $status): void
    {
        if (in_array(strtolower($status), $this->excludedConditions, true)) {
            throw new \RuntimeException(strtr(_lang['ticket_status_not_allowed'] ?? 'Ticket status "{status}" is not allowed for prioritization.', ['{status}' => $status]));
        }
    }

    /**
     * لیست را با FOR UPDATE قفل می‌کند. اگر نبود ایجاد می‌کند و سپس همان را قفل می‌کند.
     */
    private function lockListRow(int $userId, string $typeGroup, ?int $companyId): array
    {
        // اول تلاش برای قفل کردن
        $st = $this->conn->prepare(
            "SELECT * FROM priority_lists WHERE user_id=? AND type_group=? LIMIT 1 FOR UPDATE"
        );
        $st->bind_param('is', $userId, $typeGroup);
        $st->execute();
        $result = $st->get_result();
        $row = $result->fetch_assoc();
        $result->free();
        $st->close();
        if ($row)
            return $row;

        // اگر نبود ایجاد کن
        $st = $this->conn->prepare(
            "INSERT INTO priority_lists (user_id, company_id, type_group, status, revision)
             VALUES (?, ?, ?, 'needs_approval', 1)"
        );
        $st->bind_param('iis', $userId, $companyId, $typeGroup);
        $st->execute();
        $st->close();

        // دوباره قفل و دریافت
        $st = $this->conn->prepare(
            "SELECT * FROM priority_lists WHERE user_id=? AND type_group=? LIMIT 1 FOR UPDATE"
        );
        $st->bind_param('is', $userId, $typeGroup);
        $st->execute();
        $result = $st->get_result();
        $row = $result->fetch_assoc();
        $result->free();
        $st->close();
        if (!$row)
            throw new \RuntimeException(_lang['failed_to_create_list'] ?? "Failed to create/list lock");
        return $row;
    }

    private function countItemsForUpdate(int $listId): int
    {
        $st = $this->conn->prepare("SELECT COUNT(*) as cnt FROM priority_list_items WHERE priority_list_id=? FOR UPDATE");
        $st->bind_param('i', $listId);
        $st->execute();
        $result = $st->get_result();
        $row = $result->fetch_assoc();
        $result->free();
        $st->close();
        return (int) ($row['cnt'] ?? 0);
    }

    /**
     * مطمئن می‌شود تیکت متعلق به همین مشتری است و type_group همخوان دارد.
     */
    private function fetchTicketForUserAndGroupOrFail(int $companyId, int $ticketId, string $typeGroup): array
    {
        $sql = "SELECT t.ticket_id, t.company_id, t.ticket_status, t.type_group
                FROM grid_user_ticket_data t
                WHERE t.ticket_id = ? AND t.company_id = ? AND t.type_group = ?
                LIMIT 1";
        $st = $this->conn->prepare($sql);
        $st->bind_param('iis', $ticketId, $companyId, $typeGroup);
        $st->execute();
        $result = $st->get_result();
        $row = $result->fetch_assoc();
        $result->free();
        $st->close();
        if (!$row)
            throw new \RuntimeException(_lang['ticket_not_found_for_user'] ?? "Ticket not found for this user/type_group.");
        return $row;
    }

    private function lockListItemOrFail(int $listId, int $ticketId): array
    {
        $st = $this->conn->prepare(
            "SELECT * FROM priority_list_items
             WHERE priority_list_id = ? AND ticket_id = ?
             LIMIT 1 FOR UPDATE"
        );
        $st->bind_param('ii', $listId, $ticketId);
        $st->execute();
        $result = $st->get_result();
        $item = $result->fetch_assoc();
        $result->free();
        $st->close();
        if (!$item)
            throw new \RuntimeException(_lang['ticket_not_in_list'] ?? "Ticket not found in list.");
        return $item;
    }

    private function getListItemId(int $listId, int $ticketId): int
    {
        $st = $this->conn->prepare("SELECT id FROM priority_list_items WHERE priority_list_id=? AND ticket_id=? LIMIT 1");
        $st->bind_param('ii', $listId, $ticketId);
        $st->execute();
        $result = $st->get_result();
        $row = $result->fetch_assoc();
        $result->free();
        $st->close();
        if (!$row)
            throw new \RuntimeException(_lang['list_item_not_found'] ?? "List item not found after insert.");
        return (int) $row['id'];
    }

    private function insertItemComment(int $itemId, ?int $userId, ?int $adminId, string $body): void
    {
        $st = $this->conn->prepare(
            "INSERT INTO priority_item_comments (priority_list_item_id, user_id, admin_id, comment_text)
             VALUES (?, ?, ?, ?)"
        );
        $st->bind_param('iiis', $itemId, $userId, $adminId, $body);
        $st->execute();
        $st->close();
    }

    private function insertItemLog(
        int $listId,
        int $ticketId,
        string $action,
        ?int $userId,
        ?int $adminId,
        ?int $fromPriority,
        ?int $toPriority,
        ?string $reason
    ): void {

        $st = $this->conn->prepare(
            "INSERT INTO priority_item_logs
             (priority_list_id, ticket_id, action, admin_id, user_id, from_priority, to_priority, reason)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $st->bind_param('iisiiiss', $listId, $ticketId, $action, $adminId, $userId, $fromPriority, $toPriority, $reason);
        $st->execute();
        $st->close();
    }

    /**
     * شیفت بعد از حذف: هر آیتمی که priority > removedPriority دارد، یک واحد کم می‌شود.
     * برای هر شیفت، لاگ ثبت می‌کنیم.
     */
    private function shiftDownAfterRemoval(int $listId, int $removedPriority, ?int $actorUserId, ?int $actorAdminId, string $reason): void
    {
        // آیتم‌های تحت تاثیر را قفل کن و بخوان (برای لاگ دقیق)
        $st = $this->conn->prepare(
            "SELECT id, ticket_id, priority
             FROM priority_list_items
             WHERE priority_list_id = ? AND priority > ?
             ORDER BY priority ASC
             FOR UPDATE"
        );
        $st->bind_param('ii', $listId, $removedPriority);
        $st->execute();
        $result = $st->get_result();
        $affected = [];
        while ($row = $result->fetch_assoc()) {
            $affected[] = $row;
        }
        $result->free();
        $st->close();

        foreach ($affected as $row) {
            $old = (int) $row['priority'];
            $new = $old - 1;

            $up = $this->conn->prepare("UPDATE priority_list_items SET priority = ? WHERE id = ?");
            $up->bind_param('ii', $new, $row['id']);
            $up->execute();
            $up->close();

            $this->insertItemLog(
                $listId,
                (int) $row['ticket_id'],
                'shifted_due_to_removal',
                $actorUserId,
                $actorAdminId,
                $old,
                $new,
                strtr(_lang['log_shifted_due_to_removal'] ?? "{reason} (priority {old}->{new})", ['{old}' => $old, '{new}' => $new, '{reason}' => $reason])
            );
        }
    }

    private function bumpListRevisionNeedsApproval(int $listId, ?int $actorUserId, string $note, ?int $actorAdminId = null): void
    {
        $st = $this->conn->prepare(
            "UPDATE priority_lists
             SET status='needs_approval', revision = revision + 1
             WHERE id = ?"
        );
        $st->bind_param('i', $listId);
        $st->execute();
        $st->close();

        $log = $this->conn->prepare(
            "INSERT INTO priority_list_logs (priority_list_id, action, admin_id, user_id, note)
             VALUES (?, ?, ?, ?, ?)"
        );
        $action = 'revision_bumped';
        $log->bind_param('isii' . 's', $listId, $action, $actorAdminId, $actorUserId, $note);
        $log->execute();
        $log->close();
    }

    /**
     * پیدا کردن همه آیتم‌هایی که ticket_id داخلشان هست (برای حذف خودکار).
     */
    private function findListsContainingTicketForUpdate(int $ticketId): array
    {
        $st = $this->conn->prepare(
            "SELECT pli.id, pli.priority_list_id, pli.ticket_id, pli.priority
             FROM priority_list_items pli
             WHERE pli.ticket_id = ?
             FOR UPDATE"
        );
        $st->bind_param('i', $ticketId);
        $st->execute();
        $result = $st->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $result->free();
        $st->close();
        return $items;
    }

    public function findListsTypeGroup(): array
    {
        $sql = "
        SELECT DISTINCT type_group
        FROM types
        WHERE type_group IS NOT NULL
        ORDER BY type_group
    ";

        $st = $this->conn->prepare($sql);
        $st->execute();
        $result = $st->get_result();

        $typeGroups = [];
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $typeGroups[] = $row['type_group'];
        }
        $st->close();

        return $typeGroups;
    }

    /**
     * پاکسازی تیکت‌های با وضعیت منع‌شده از لیست
     * وقتی صفحه باز می‌شود، تمام آیتم‌های لیست را چک می‌کند
     * اگر وضعیت تیکت تغییر کرده و به یکی از excludedConditions رسیده، حذف می‌کند
     * نوت: status جدول priority_lists تغییر نمی‌کند
     */
    public function cleanupExcludedTickets(int $listId, $structureModel, ?int $actorAdminId = null): void
    {
        $this->conn->begin_transaction();
        try {
            // تمام آیتم‌های لیست را بخوان
            $st = $this->conn->prepare(
                "SELECT pli.id, pli.ticket_id, pli.priority
                 FROM priority_list_items pli
                 WHERE pli.priority_list_id = ?"
            );
            $st->bind_param('i', $listId);
            $st->execute();
            $result = $st->get_result();
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            $result->free();
            $st->close();

            // آیتم‌های برای حذف
            $itemsToDelete = [];

            // برای هر آیتم، وضعیت تیکت را چک کن
            foreach ($items as $item) {
                $ticketId = (int) $item['ticket_id'];
                $itemId = (int) $item['id'];
                $removedPriority = (int) $item['priority'];

                // وضعیت جدید تیکت را بخوان
                $ticketSt = $this->conn->prepare(
                    "SELECT t.status FROM tickets t WHERE t.id = ? LIMIT 1"
                );
                $ticketSt->bind_param('i', $ticketId);
                $ticketSt->execute();
                $ticketResult = $ticketSt->get_result();
                $ticketRow = $ticketResult->fetch_assoc();
                $ticketResult->free();
                $ticketSt->close();

                // اگر تیکت وجود نداشت یا وضعیتش در excludedConditions است
                if (!$ticketRow || in_array(strtolower($ticketRow['status']), $this->excludedConditions, true)) {
                    $newStatus = $ticketRow['status'] ?? 'not_found';
                    $itemsToDelete[] = [
                        'itemId' => $itemId,
                        'ticketId' => $ticketId,
                        'priority' => $removedPriority,
                        'status' => $newStatus
                    ];
                }
            }

            // حذف آیتم‌ها و شیفت‌دهی
            foreach ($itemsToDelete as $deleteItem) {
                $itemId = (int) $deleteItem['itemId'];
                $ticketId = (int) $deleteItem['ticketId'];
                $removedPriority = (int) $deleteItem['priority'];
                $condition = $structureModel->getConditionsByName($deleteItem['status']);
                $newStatus = _lang[$condition['condition_name']];

                // حذف آیتم
                $delSt = $this->conn->prepare("DELETE FROM priority_list_items WHERE id = ?");
                $delSt->bind_param('i', $itemId);
                $delSt->execute();
                $delSt->close();

                // لاگ: خروج از لیست بخاطر تغییر وضعیت
                $reason = strtr(
                    $_lang['log_auto_removed_status_changed']
                    ?? 'خروج خودکار از لیست - تغییر وضعیت به {status} توسط مدیریت',
                    ['{status}' => $newStatus]
                );
                $this->insertItemLog($listId, $ticketId, 'removed_auto_status_changed', null, $actorAdminId, $removedPriority, null, $reason);

                // شیفت اولویت‌های بعد از این آیتم
                $this->shiftDownAfterRemovalNoLock($listId, $removedPriority, null, $actorAdminId, $reason);
            }

            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /**
     * شیفت بدون FOR UPDATE (برای استفاده در cleanupExcludedTickets)
     */
    private function shiftDownAfterRemovalNoLock(int $listId, int $removedPriority, ?int $actorUserId, ?int $actorAdminId, string $reason): void
    {
        // آیتم‌های تحت تاثیر را بخوان (بدون FOR UPDATE)
        $st = $this->conn->prepare(
            "SELECT id, ticket_id, priority
             FROM priority_list_items
             WHERE priority_list_id = ? AND priority > ?
             ORDER BY priority ASC"
        );
        $st->bind_param('ii', $listId, $removedPriority);
        $st->execute();
        $result = $st->get_result();
        $affected = [];
        while ($row = $result->fetch_assoc()) {
            $affected[] = $row;
        }
        $result->free();
        $st->close();

        foreach ($affected as $row) {
            $old = (int) $row['priority'];
            $new = $old - 1;

            $up = $this->conn->prepare("UPDATE priority_list_items SET priority = ? WHERE id = ?");
            $up->bind_param('ii', $new, $row['id']);
            $up->execute();
            $up->close();

            $this->insertItemLog(
                $listId,
                (int) $row['ticket_id'],
                'shifted_due_to_removal',
                $actorUserId,
                $actorAdminId,
                $old,
                $new,
                strtr(_lang['log_shifted_due_to_removal'] ?? "{reason} (priority {old}->{new})", ['{old}' => $old, '{new}' => $new, '{reason}' => $reason])
            );
        }
    }

    /**
     * دریافت تمام کامنت‌های مربوط به آیتم‌های یک لیست
     */
    public function getListItemsComments(int $listId): array
    {
        $sql = "
            SELECT 
                pic.id as comment_id, 
                pic.priority_list_item_id, 
                pic.user_id, 
                pic.admin_id, 
                pic.comment_text, 
                pic.creation_date,
                pli.priority,
                pli.ticket_id,
                t.ticket_number
            FROM priority_item_comments pic
            JOIN priority_list_items pli ON pic.priority_list_item_id = pli.id
            JOIN tickets t ON pli.ticket_id = t.id
            WHERE pli.priority_list_id = ?
            ORDER BY pic.creation_date DESC
        ";

        $st = $this->conn->prepare($sql);
        $st->bind_param('i', $listId);
        $st->execute();
        $result = $st->get_result();
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        $result->free();
        $st->close();
        return $comments;
    }


    public function getListComments(int $listId): array
    {
        $sql = "
            SELECT 
                plc.id as comment_id,
                plc.priority_list_id,
                plc.user_id,
                plc.admin_id,
                plc.comment_text,
                plc.creation_date,
                COALESCE(u.name, a.name, 'Unknown') as author_name,
                CASE WHEN plc.user_id IS NOT NULL AND plc.user_id > 0 THEN 'user' 
                     WHEN plc.admin_id IS NOT NULL AND plc.admin_id > 0 THEN 'admin' 
                     ELSE 'unknown' END as author_type
            FROM priority_list_comments plc
            LEFT JOIN users_details_view u ON plc.user_id = u.user_id AND plc.user_id > 0
            LEFT JOIN admins_details_view a ON plc.admin_id = a.admin_id AND plc.admin_id > 0
            WHERE plc.priority_list_id = ?
            ORDER BY plc.creation_date DESC
        ";

        $st = $this->conn->prepare($sql);
        $st->bind_param('i', $listId);
        $st->execute();
        $result = $st->get_result();
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        $result->free();
        $st->close();
        return $comments;
    }
}