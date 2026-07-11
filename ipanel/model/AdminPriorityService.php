<?php
declare(strict_types=1);
namespace ipanel\model;

class AdminPriorityService
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /* =========================================================
       List Page
       ========================================================= */

    public function getTypeGroups(): array
    {
        $st = $this->conn->prepare("SELECT DISTINCT type_group FROM priority_lists ORDER BY type_group");
        $st->execute();
        $res = $st->get_result();
        $out = [];
        while ($r = $res->fetch_assoc())
            $out[] = $r['type_group'];
        $res->free();
        $st->close();
        return $out;
    }

    /**
     * شرکت‌های موجود در priority_lists
     */
    public function getCompanies(): array
    {
        $sql = "SELECT DISTINCT pl.company_id, pli.user_company
                FROM priority_lists pl
                LEFT JOIN priority_list_items pli ON pli.priority_list_id = pl.id
                WHERE pl.company_id IS NOT NULL
                AND pl.company_id > 0
                AND pli.user_company IS NOT NULL
                AND pli.user_company != ''
                ORDER BY pli.user_company ASC";
        $st = $this->conn->prepare($sql);
        $st->execute();
        $res = $st->get_result();
        $out = [];
        while ($r = $res->fetch_assoc()) {
            $out[] = [
                'company_id' => (int) $r['company_id'],
                'company_name' => $r['user_company']
            ];
        }
        $res->free();
        $st->close();
        return $out;
    }

    /**
     * لیست همه لیست‌ها برای صفحه اصلی مدیریت
     * فیلترها اختیاری هستند.
     */
    public function getLists(?int $companyId = null, ?string $typeGroup = null, ?string $status = null, int $limit = 300): array
    {
        $where = "1=1";
        $params = [];
        $types = "";

        if ($companyId !== null) {
            $where .= " AND pl.company_id = ?";
            $params[] = $companyId;
            $types .= "i";
        }
        if ($typeGroup !== null && $typeGroup !== '') {
            $where .= " AND pl.type_group = ?";
            $params[] = $typeGroup;
            $types .= "s";
        }
        if ($status !== null && $status !== '') {
            $where .= " AND pl.status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $sql = "SELECT pl.*,
                       (SELECT COUNT(*) FROM priority_list_items pli WHERE pli.priority_list_id = pl.id) AS items_count
                FROM priority_lists pl
                WHERE {$where}
                AND (SELECT COUNT(*) FROM priority_list_items pli WHERE pli.priority_list_id = pl.id) > 0
                ORDER BY pl.last_updated_date DESC
                LIMIT {$limit}";

        $st = $this->conn->prepare($sql);
        if (!empty($params)) {
            $this->bindParams($st, $types, $params);
        }

        $st->execute();
        $res = $st->get_result();
        $lists = [];
        while ($r = $res->fetch_assoc()) {
            $r['company_name'] = $this->getCompanyNameFromList((int) $r['id']);
            $lists[] = $r;
        }
        $res->free();
        $st->close();

        return $lists;
    }

    /**
     * برای نمایش نام شرکت در لیست صفحه مدیریت
     * چون گفتی در آیتم‌ها user_company موجود است.
     */
    public function getCompanyNameFromList(int $listId): string
    {
        $sql = "SELECT user_company FROM priority_list_items WHERE priority_list_id = ? LIMIT 1";
        $st = $this->conn->prepare($sql);
        if (!$st)
            return '';
        $st->bind_param('i', $listId);
        $st->execute();
        $res = $st->get_result();
        $row = $res->fetch_assoc();
        $res->free();
        $st->close();
        return $row['user_company'] ?? '';
    }

    /* =========================================================
       Details Page
       ========================================================= */

    public function getListById(int $listId): array
    {
        $st = $this->conn->prepare("SELECT * FROM priority_lists WHERE id=? LIMIT 1");
        $st->bind_param('i', $listId);
        $st->execute();
        $res = $st->get_result();
        $list = $res->fetch_assoc();
        $res->free();
        $st->close();

        if (!$list)
            throw new RuntimeException("list not found");
        return $list;
    }

    public function getListItems(int $listId): array
    {
        // اگر ticket_title در دیتابیس شما اسم دیگری دارد، اینجا اصلاح کن
        $sql = "SELECT pli.*, t.ticket_number, t.ticket_title, t.status
                FROM priority_list_items pli
                JOIN tickets t ON t.id = pli.ticket_id
                WHERE pli.priority_list_id = ?
                ORDER BY pli.priority ASC";

        $st = $this->conn->prepare($sql);
        $st->bind_param('i', $listId);
        $st->execute();
        $res = $st->get_result();
        $items = [];
        while ($r = $res->fetch_assoc())
            $items[] = $r;
        $res->free();
        $st->close();

        return $items;
    }

    /* =========================================================
       Approve / Reject
       ========================================================= */

    public function approveList(int $adminId, int $listId, ?string $comment = null): void
    {
        $this->conn->begin_transaction();
        try {
            if ($comment !== null && trim($comment) !== '') {
                $this->insertListComment($listId, $adminId, $comment);
            }

            $sql = "UPDATE priority_lists
                    SET status='approved',
                        last_decision='approved',
                        last_decision_admin_id=?,
                        last_decision_date=NOW()
                    WHERE id=?";
            $st = $this->conn->prepare($sql);
            $st->bind_param('ii', $adminId, $listId);
            $st->execute();
            $st->close();

            $this->insertListLog($listId, 'approved', $adminId, "approved");

            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function rejectList(int $adminId, int $listId, string $comment): void
    {
        if (trim($comment) === '') {
            throw new RuntimeException(_lang['comment_required'] ?? "کامنت برای رد کردن اجباری است.");
        }

        $this->conn->begin_transaction();
        try {
            $commentId = $this->insertListComment($listId, $adminId, $comment);

            $sql = "UPDATE priority_lists
                    SET status='rejected',
                        last_decision='rejected',
                        last_decision_admin_id=?,
                        last_decision_date=NOW()
                    WHERE id=?";
            $st = $this->conn->prepare($sql);
            $st->bind_param('ii', $adminId, $listId);
            $st->execute();
            $st->close();

            $this->insertListLog($listId, 'rejected', $adminId, "rejected - comment_id={$commentId}");

            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /* =========================================================
       Admin can change priority / remove item (with comment)
       ========================================================= */

    public function changeTicketPriorityByAdmin(int $adminId, int $listId, int $ticketId, int $newPriority, string $comment): void
    {
        if ($newPriority < 1 || $newPriority > 5)
            throw new RuntimeException("priority must be 1..5");
        if (trim($comment) === '')
            throw new RuntimeException(_lang['comment_required'] ?? "کامنت اجباری است.");

        $this->conn->begin_transaction();
        try {
            // آیتم
            $st = $this->conn->prepare("SELECT id, priority FROM priority_list_items WHERE priority_list_id=? AND ticket_id=? LIMIT 1");
            $st->bind_param('ii', $listId, $ticketId);
            $st->execute();
            $res = $st->get_result();
            $item = $res->fetch_assoc();
            $res->free();
            $st->close();
            if (!$item)
                throw new RuntimeException("ticket not in list");

            $itemId = (int) $item['id'];
            $oldPriority = (int) $item['priority'];
            if ($oldPriority === $newPriority)
                throw new RuntimeException("same priority");

            // بررسی اگر اولویت جدید قبلاً گرفته شده باشد
            $st = $this->conn->prepare("SELECT id FROM priority_list_items WHERE priority_list_id=? AND priority=? LIMIT 1");
            $st->bind_param('ii', $listId, $newPriority);
            $st->execute();
            $res = $st->get_result();
            $existingItem = $res->fetch_assoc();
            $res->free();
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
                $st->bind_param('ii', $newPriority, $itemId);
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
                $st->bind_param('ii', $newPriority, $itemId);
                $st->execute();
                $st->close();
            }

            // comment + log
            $this->insertItemComment($itemId, $adminId, $comment);
            $this->insertItemLog($listId, $ticketId, 'priority_changed', $adminId, $oldPriority, $newPriority, _lang['log_priority_changed_by_admin'] ?? "priority changed by admin");

            $this->bumpListRevision($listId, $adminId, "admin changed priority ticket {$ticketId} {$oldPriority}->{$newPriority}");

            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function removeTicketByAdmin(int $adminId, int $listId, int $ticketId, string $comment): void
    {
        if (trim($comment) === '')
            throw new RuntimeException(_lang['comment_required'] ?? "کامنت اجباری است.");

        $this->conn->begin_transaction();
        try {
            $st = $this->conn->prepare("SELECT id, priority FROM priority_list_items WHERE priority_list_id=? AND ticket_id=? LIMIT 1");
            $st->bind_param('ii', $listId, $ticketId);
            $st->execute();
            $res = $st->get_result();
            $item = $res->fetch_assoc();
            $res->free();
            $st->close();
            if (!$item)
                throw new RuntimeException("ticket not in list");

            $itemId = (int) $item['id'];
            $removedPriority = (int) $item['priority'];

            $this->insertItemComment($itemId, $adminId, $comment);

            $st = $this->conn->prepare("DELETE FROM priority_list_items WHERE id=?");
            $st->bind_param('i', $itemId);
            $st->execute();
            $st->close();

            $this->insertItemLog($listId, $ticketId, 'removed_manual', $adminId, $removedPriority, null, _lang['log_removed_by_admin'] ?? "removed by admin");

            // shift
            $reason = _lang['log_shift_due_to_admin_remove'] ?? "shift due to admin removal";
            $this->shiftDownAfterRemoval($listId, $removedPriority, $adminId, $reason);

            $this->bumpListRevision($listId, $adminId, "admin removed ticket {$ticketId} from priority {$removedPriority}");

            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /* =========================================================
       Logs page data (separate page)
       ========================================================= */

    public function getLogsAndComments(int $listId): array
    {
        $list = $this->getListById($listId);
        $companyName = $this->getCompanyNameFromList($listId);

        // NOTE: اگر ستون نام‌ها در view های شما فرق دارد، اینجا تغییر بده:
        // admins_details_view.admin_name
        // users_details_view.name

        // list comments
        $sql = "SELECT plc.*, adv.name AS admin_name, udv.name AS user_name
                FROM priority_list_comments plc
                LEFT JOIN admins_details_view adv ON adv.admin_id = plc.admin_id
                LEFT JOIN users_details_view udv ON udv.user_id = plc.user_id
                WHERE plc.priority_list_id = ?
                ORDER BY plc.creation_date DESC";
        $listComments = $this->fetchAll($sql, 'i', [$listId]);

        // list logs
        $sql = "SELECT pll.*, adv.name AS admin_name, udv.name AS user_name
                FROM priority_list_logs pll
                LEFT JOIN admins_details_view adv ON adv.admin_id = pll.admin_id
                LEFT JOIN users_details_view udv ON udv.user_id = pll.user_id
                WHERE pll.priority_list_id = ?
                ORDER BY pll.creation_date DESC";
        $listLogs = $this->fetchAll($sql, 'i', [$listId]);

        // item logs
        $sql = "SELECT pil.*, t.ticket_number, adv.name AS admin_name, udv.name AS user_name
                FROM priority_item_logs pil
                LEFT JOIN tickets t ON t.id = pil.ticket_id
                LEFT JOIN admins_details_view adv ON adv.admin_id = pil.admin_id
                LEFT JOIN users_details_view udv ON udv.user_id = pil.user_id
                WHERE pil.priority_list_id = ?
                ORDER BY pil.creation_date DESC";
        $itemLogs = $this->fetchAll($sql, 'i', [$listId]);

        // item comments
        $sql = "SELECT pic.*, pli.ticket_id, t.ticket_number, adv.name AS admin_name, udv.name AS user_name
                FROM priority_item_comments pic
                JOIN priority_list_items pli ON pli.id = pic.priority_list_item_id
                LEFT JOIN tickets t ON t.id = pli.ticket_id
                LEFT JOIN admins_details_view adv ON adv.admin_id = pic.admin_id
                LEFT JOIN users_details_view udv ON udv.user_id = pic.user_id
                WHERE pli.priority_list_id = ?
                ORDER BY pic.creation_date DESC";
        $itemComments = $this->fetchAll($sql, 'i', [$listId]);

        return [
            'list' => $list,
            'companyName' => $companyName,
            'listComments' => $listComments,
            'listLogs' => $listLogs,
            'itemLogs' => $itemLogs,
            'itemComments' => $itemComments,
        ];
    }

    /* =========================================================
       Internal helpers (DB writes)
       ========================================================= */

    private function insertListComment(int $listId, int $adminId, string $comment): int
    {
        $sql = "INSERT INTO priority_list_comments (priority_list_id, admin_id, comment_text) VALUES (?, ?, ?)";
        $st = $this->conn->prepare($sql);
        $st->bind_param('iis', $listId, $adminId, $comment);
        $st->execute();
        $id = (int) $this->conn->insert_id;
        $st->close();
        return $id;
    }

    private function insertListLog(int $listId, string $action, int $adminId, ?string $note): void
    {
        $sql = "INSERT INTO priority_list_logs (priority_list_id, action, admin_id, note) VALUES (?, ?, ?, ?)";
        $st = $this->conn->prepare($sql);
        $st->bind_param('isis', $listId, $action, $adminId, $note);
        $st->execute();
        $st->close();
    }

    private function insertItemComment(int $listItemId, int $adminId, string $comment): void
    {
        $sql = "INSERT INTO priority_item_comments (priority_list_item_id, admin_id, comment_text) VALUES (?, ?, ?)";
        $st = $this->conn->prepare($sql);
        $st->bind_param('iis', $listItemId, $adminId, $comment);
        $st->execute();
        $st->close();
    }

    private function insertItemLog(int $listId, int $ticketId, string $action, int $adminId, ?int $fromP, ?int $toP, ?string $reason): void
    {
        $sql = "INSERT INTO priority_item_logs
                (priority_list_id, ticket_id, action, admin_id, from_priority, to_priority, reason)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $st = $this->conn->prepare($sql);
        $st->bind_param('iisiiss', $listId, $ticketId, $action, $adminId, $fromP, $toP, $reason);
        $st->execute();
        $st->close();
    }

    private function shiftDownAfterRemoval(int $listId, int $removedPriority, int $adminId, string $reason): void
    {
        $sql = "SELECT id, ticket_id, priority
                FROM priority_list_items
                WHERE priority_list_id = ? AND priority > ?
                ORDER BY priority ASC";
        $st = $this->conn->prepare($sql);
        $st->bind_param('ii', $listId, $removedPriority);
        $st->execute();
        $res = $st->get_result();

        $rows = [];
        while ($r = $res->fetch_assoc())
            $rows[] = $r;

        $res->free();
        $st->close();

        foreach ($rows as $r) {
            $old = (int) $r['priority'];
            $new = $old - 1;

            $up = $this->conn->prepare("UPDATE priority_list_items SET priority=? WHERE id=?");
            $up->bind_param('ii', $new, $r['id']);
            $up->execute();
            $up->close();

            $this->insertItemLog($listId, (int) $r['ticket_id'], 'shifted_due_to_removal', $adminId, $old, $new, $reason);
        }
    }

    private function bumpListRevision(int $listId, int $adminId, string $note): void
    {
        $st = $this->conn->prepare("UPDATE priority_lists SET revision = revision + 1 WHERE id=?");
        $st->bind_param('i', $listId);
        $st->execute();
        $st->close();

        $this->insertListLog($listId, 'revision_bumped', $adminId, $note);
    }

    /* =========================================================
       Generic helpers
       ========================================================= */

    private function fetchAll(string $sql, string $types, array $params): array
    {
        $st = $this->conn->prepare($sql);
        $this->bindParams($st, $types, $params);
        $st->execute();
        $res = $st->get_result();
        $out = [];
        while ($r = $res->fetch_assoc())
            $out[] = $r;
        $res->free();
        $st->close();
        return $out;
    }

    private function bindParams($stmt, string $types, array $params): void
    {
        $refs = [];
        foreach ($params as $k => &$v)
            $refs[$k] = &$v;
        array_unshift($refs, $types);
        call_user_func_array([$stmt, 'bind_param'], $refs);
    }
}
