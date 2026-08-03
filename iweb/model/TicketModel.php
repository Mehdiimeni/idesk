<?php

namespace iweb\model;
class TicketModel
{
    private $ticketsTable = 'tickets';
    private $scheduleTable = 'schedule';
    private $fileManageTable = 'file_manage';
    private $statusTable = 'status';

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getUserIdFromSession()
    {
        return $_SESSION["user_id"] ?? 0;
    }

    public function getTotalTicket($conditions_name = '', $company_id = null)
    {


        $sqlWhere = '';
        $sqlWhere = $this->generateSqlWhereFromSession($company_id);

        if ($conditions_name != '')
            $sqlWhere .= " AND ticket_status = '" . $conditions_name . "'";

        $stmt = $this->conn->prepare("SELECT count(*) AS total
            FROM grid_user_ticket_data 
            WHERE  $sqlWhere");


        $stmt->execute();
        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();
        $stmt->close();
        return $reply['total'];

    }

    function generateSqlWhereFromSession($company_id = null)
    {
        if ($company_id != null) {
            return "company_id = " . $company_id;
        } else {
            return "user_id = " . $this->getUserIdFromSession();
        }
    }



    public function getTicket($condition_name = '', $company_id = null, $searchWhere = null, $limit = null, $orderBy = null, $mark_id = null)
    {
        try {
            $userId = $this->getUserIdFromSession();

            $whereClauses = [];
            $params = [];
            $types = "";

            if (!empty($company_id)) {
                $whereClauses[] = "tuv.company_id = ?";
                $params[] = (int) $company_id;
                $types .= "i";
            } else {
                $whereClauses[] = "tuv.user_id = ?";
                $params[] = (int) $userId;
                $types .= "i";
            }

            if ($condition_name != '') {
                $whereClauses[] = "tuv.ticket_status = ?";
                $params[] = $condition_name;
                $types .= "s";
            }

            $markConditions = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $markConditions = array_map('intval', $markConditions);
                $markConditions = array_filter($markConditions);

                if (!empty($markConditions)) {
                    $whereClauses[] = "tuv.ticket_id IN (" . implode(',', $markConditions) . ")";
                }
            }

            $typeConditions = $this->getCookieConditions('type', 'type_id');

            if (!empty($typeConditions)) {
                $typeConditions = array_map('intval', $typeConditions);
                $typeConditions = array_filter($typeConditions);

                if (!empty($typeConditions)) {
                    $whereClauses[] = "tuv.type_id IN (" . implode(',', $typeConditions) . ")";
                }
            }

            $statusConditions = $this->getCookieConditions('condition', 'status');
            $statusConditions = $this->getConditionNamesByIds($statusConditions);

            if (!empty($statusConditions)) {
                $placeholders = implode(',', array_fill(0, count($statusConditions), '?'));
                $whereClauses[] = "tuv.ticket_status IN ($placeholders)";

                foreach ($statusConditions as $statusCondition) {
                    $params[] = $statusCondition;
                    $types .= "s";
                }
            }

            if (!empty($searchWhere)) {
                $whereClauses[] = "(" . $searchWhere . ")";
            }

            $sql = "
            SELECT *
            FROM grid_user_ticket_data AS tuv
            WHERE " . implode(" AND ", $whereClauses);

            if (!empty($orderBy)) {
                $sql .= " " . $orderBy;
            }

            if (!empty($limit)) {
                $sql .= " " . $limit;
            }

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();

            return $stmt->get_result();
        } catch (\Exception $e) {
            throw new \Exception("Failed to get tickets: " . $e->getMessage());
        }
    }

    public function getTicketCount($condition_name = '', $company_id = null, $mark_id = null)
    {
        try {
            $userId = $this->getUserIdFromSession();

            $whereClauses = [];
            $params = [];
            $types = "";

            if (!empty($company_id)) {
                $whereClauses[] = "tuv.company_id = ?";
                $params[] = (int) $company_id;
                $types .= "i";
            } else {
                $whereClauses[] = "tuv.user_id = ?";
                $params[] = (int) $userId;
                $types .= "i";
            }

            if ($condition_name != '') {
                $whereClauses[] = "tuv.ticket_status = ?";
                $params[] = $condition_name;
                $types .= "s";
            }

            $markConditions = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $markConditions = array_map('intval', $markConditions);
                $markConditions = array_filter($markConditions);

                if (!empty($markConditions)) {
                    $whereClauses[] = "tuv.ticket_id IN (" . implode(',', $markConditions) . ")";
                }
            }

            $typeConditions = $this->getCookieConditions('type', 'type_id');

            if (!empty($typeConditions)) {
                $typeConditions = array_map('intval', $typeConditions);
                $typeConditions = array_filter($typeConditions);

                if (!empty($typeConditions)) {
                    $whereClauses[] = "tuv.type_id IN (" . implode(',', $typeConditions) . ")";
                }
            }

            $statusConditions = $this->getCookieConditions('condition', 'status');
            $statusConditions = $this->getConditionNamesByIds($statusConditions);

            if (!empty($statusConditions)) {
                $placeholders = implode(',', array_fill(0, count($statusConditions), '?'));

                $whereClauses[] = "tuv.ticket_status IN ($placeholders)";

                foreach ($statusConditions as $statusCondition) {
                    $params[] = $statusCondition;
                    $types .= "s";
                }
            }

            $sql = "
            SELECT COUNT(*) AS cnt
            FROM grid_user_ticket_data AS tuv
            WHERE " . implode(" AND ", $whereClauses);

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $count = isset($row['cnt']) ? (int) $row['cnt'] : 0;

            if ($result) {
                $result->free();
            }

            $stmt->close();

            return $count;
        } catch (\Exception $e) {
            throw new \Exception("Failed to get ticket count: " . $e->getMessage());
        }
    }

    public function getTicketSearchCount($condition_name = '', $company_id = null, $searchWhere = null, $mark_id = null)
    {
        try {
            $userId = $this->getUserIdFromSession();

            $whereClauses = [];
            $params = [];
            $types = "";

            if (!empty($company_id)) {
                $whereClauses[] = "tuv.company_id = ?";
                $params[] = (int) $company_id;
                $types .= "i";
            } else {
                $whereClauses[] = "tuv.user_id = ?";
                $params[] = (int) $userId;
                $types .= "i";
            }

            if ($condition_name != '') {
                $whereClauses[] = "tuv.ticket_status = ?";
                $params[] = $condition_name;
                $types .= "s";
            }

            $markConditions = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $markConditions = array_map('intval', $markConditions);
                $markConditions = array_filter($markConditions);

                if (!empty($markConditions)) {
                    $whereClauses[] = "tuv.ticket_id IN (" . implode(',', $markConditions) . ")";
                }
            }

            $typeConditions = $this->getCookieConditions('type', 'type_id');

            if (!empty($typeConditions)) {
                $typeConditions = array_map('intval', $typeConditions);
                $typeConditions = array_filter($typeConditions);

                if (!empty($typeConditions)) {
                    $whereClauses[] = "tuv.type_id IN (" . implode(',', $typeConditions) . ")";
                }
            }

            $statusConditions = $this->getCookieConditions('condition', 'status');
            $statusConditions = $this->getConditionNamesByIds($statusConditions);

            if (!empty($statusConditions)) {
                $placeholders = implode(',', array_fill(0, count($statusConditions), '?'));

                $whereClauses[] = "tuv.ticket_status IN ($placeholders)";

                foreach ($statusConditions as $statusCondition) {
                    $params[] = $statusCondition;
                    $types .= "s";
                }
            }

            if (!empty($searchWhere)) {
                $whereClauses[] = "(" . $searchWhere . ")";
            }

            $sql = "
            SELECT COUNT(*) AS cnt
            FROM grid_user_ticket_data AS tuv
            WHERE " . implode(" AND ", $whereClauses);

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $count = isset($row['cnt']) ? (int) $row['cnt'] : 0;

            if ($result) {
                $result->free();
            }

            $stmt->close();

            return $count;
        } catch (\Exception $e) {
            throw new \Exception("Failed to get ticket search count: " . $e->getMessage());
        }
    }



    public function getTicketIfTimeOverSetAutoCondition($condition_name, $condition_name_change, $timeOver = 19)
    {
        $userId = $this->getUserIdFromSession();

        $tickets = [];
        $expiredTicketIds = [];

        try {
            $sqlWhere = "tuv.user_id = ?";
            $params = [$userId];
            $types = "i";

            if ($condition_name != '') {
                $sqlWhere .= " AND tuv.ticket_status = ?";
                $params[] = $condition_name;
                $types .= "s";
            }

            $whereClauses = [];

            $typeConditions = $this->getCookieConditions('type', 'type_id');
            if (!empty($typeConditions)) {
                $typeConditions = array_map('intval', $typeConditions);
                $typeConditions = array_filter($typeConditions, function ($id) {
                    return $id > 0;
                });

                if (!empty($typeConditions)) {
                    $whereClauses[] = "tuv.type_id IN (" . implode(',', $typeConditions) . ")";
                }
            }

            $statusConditions = $this->getCookieConditions('condition', 'status');
            $statusConditions = $this->getConditionNamesByIds($statusConditions);

            if (!empty($statusConditions)) {
                $safeStatusConditions = [];

                foreach ($statusConditions as $statusCondition) {
                    $safeStatusConditions[] = "'" . $this->conn->real_escape_string($statusCondition) . "'";
                }

                if (!empty($safeStatusConditions)) {
                    $whereClauses[] = "tuv.ticket_status IN (" . implode(',', $safeStatusConditions) . ")";
                }
            }

            if (!empty($whereClauses)) {
                $sqlWhere .= " AND " . implode(" AND ", $whereClauses);
            }

            $sql = "
            SELECT *
            FROM grid_user_ticket_data AS tuv
            WHERE {$sqlWhere}
        ";

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            $result = $stmt->get_result();

            $timeLimit = strtotime("-{$timeOver} days");

            while ($row = $result->fetch_assoc()) {
                if (strtotime($row['last_updated_date']) < $timeLimit) {
                    $expiredTicketIds[] = (int) $row['ticket_id'];
                } else {
                    $tickets[] = $row;
                }
            }

            $result->free();
            $stmt->close();

            $expiredTicketIds = array_values(array_unique(array_filter($expiredTicketIds)));

            if (!empty($expiredTicketIds)) {
                $idsList = implode(',', $expiredTicketIds);

                $updateSql = "
                UPDATE tickets
                SET status = ?
                WHERE id IN ({$idsList})
            ";

                $updateStmt = $this->conn->prepare($updateSql);

                if ($updateStmt === false) {
                    throw new \Exception('Update prepare failed: ' . $this->conn->error);
                }

                $updateStmt->bind_param("s", $condition_name_change);
                $updateStmt->execute();
                $updateStmt->close();
            }

            return $tickets;
        } catch (\Exception $e) {
            throw new \Exception("Failed to get tickets and update overdue conditions: " . $e->getMessage());
        }
    }



    public function checkLastStatusMatch($part_id, $part_name, $person_id, $company_id)
    {
        try {
            $sql = "
            SELECT person_id, company_id
            FROM status_history_view
            WHERE section_element_id = ?
              AND section_part_name = ?
            ORDER BY creation_date DESC
            LIMIT 1
        ";

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            $stmt->bind_param("is", $part_id, $part_name);
            $stmt->execute();

            $result = $stmt->get_result();
            $lastRecord = $result->fetch_assoc();

            if ($result) {
                $result->free();
            }

            $stmt->close();

            if (!$lastRecord) {
                return true;
            }

            if (
                (int) $lastRecord['person_id'] === (int) $person_id &&
                (int) $lastRecord['company_id'] === (int) $company_id
            ) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception("Failed to check last status match: " . $e->getMessage());
        }
    }

    public function getTicketRejectDescription($condition_name = '', $company_id = null)
    {
        try {
            $userId = $this->getUserIdFromSession();

            $sql = "
            SELECT *
            FROM grid_user_ticket_data AS tuv
            WHERE
        ";

            if ($company_id !== null) {
                $sql .= " tuv.company_id = ?";

                $stmt = $this->conn->prepare($sql);

                if ($stmt === false) {
                    throw new \Exception('Prepare failed: ' . $this->conn->error);
                }

                $stmt->bind_param("i", $company_id);
            } else {
                $sql .= " tuv.user_id = ?";

                if ($condition_name != '') {
                    $sql .= " AND tuv.ticket_status = ?";
                }

                $stmt = $this->conn->prepare($sql);

                if ($stmt === false) {
                    throw new \Exception('Prepare failed: ' . $this->conn->error);
                }

                if ($condition_name != '') {
                    $stmt->bind_param("is", $userId, $condition_name);
                } else {
                    $stmt->bind_param("i", $userId);
                }
            }

            $stmt->execute();

            return $stmt->get_result();
        } catch (\Exception $e) {
            throw new \Exception("Failed to get ticket reject description: " . $e->getMessage());
        }
    }


    public function getTicketTopLevel($condition_name = '')
    {
        $sqlWhere = "tuv.company_id = ?";

        if ($condition_name != '') {
            $sqlWhere .= " AND tuv.ticket_status = '" . $condition_name . "'";
        }

        $stmt = $this->conn->prepare("SELECT *
                FROM grid_user_ticket_data AS tuv           
                WHERE " . $sqlWhere);

        $stmt->bind_param("i", $_SESSION["company_id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
    public function getConditionNamesByIds($conditionIds)
    {
        // Prepare an array to hold condition names
        $conditionNames = [];

        // Make sure $conditionIds is not empty
        if (!empty($conditionIds)) {
            // Prepare a placeholder for each condition id
            $placeholders = implode(',', array_fill(0, count($conditionIds), '?'));

            // Query to select condition_name for given condition_ids
            $sqlQuery = "SELECT id, condition_name FROM conditions WHERE id IN ($placeholders)";

            $stmt = $this->conn->prepare($sqlQuery);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }

            // Bind parameters dynamically
            $types = str_repeat('i', count($conditionIds)); // 'i' for integer
            $stmt->bind_param($types, ...$conditionIds);

            $stmt->execute();
            $result = $stmt->get_result();
            if ($result === false) {
                die('Execute failed: ' . htmlspecialchars($stmt->error));
            }

            // Fetch condition names into an associative array
            while ($row = $result->fetch_assoc()) {
                $conditionNames[$row['id']] = $row['condition_name'];
            }

            $stmt->close();
        }

        return $conditionNames;
    }

    private function getCookieConditions($cookiePrefix, $idField)
    {
        $conditions = [];
        foreach ($_COOKIE as $cookieName => $cookieValue) {
            if (strpos($cookieName, $cookiePrefix . '_') === 0 && $cookieValue === 'true') {
                $id = substr($cookieName, strlen($cookiePrefix) + 1);
                if (is_numeric($id)) {
                    $conditions[] = (int) $id;
                }
            }
        }
        return $conditions;
    }

    public function getSchedule($section_part_name, $section_element_id)
    {
        $sqlQuery = "SELECT * FROM $this->scheduleTable WHERE section_part_name =? AND section_element_id =? ORDER BY date_time ASC";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("si", $section_part_name, $section_element_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    public function checkStatusTable($part_id, $part_name)
    {
        $userId = $this->getUserIdFromSession();
        // بررسی موجود بودن رکورد در جدول status
        $sql = "SELECT id FROM $this->statusTable WHERE part_id = ? AND part_name = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $part_id, $part_name);
        $stmt->execute();
        $result = $stmt->get_result();

        // اگر رکوردی وجود نداشت، یک رکورد جدید درج می‌شود
        if ($result->num_rows < 1) {
            $sqlQuery = "INSERT INTO " . $this->statusTable . " (`part_id`, `part_name`, `user_id`, `rbac_id`) VALUES (?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sqlQuery);

            if ($stmt === false) {
                return false;
            }

            $stmt->bind_param("isii", $part_id, $part_name, $userId, $_SESSION["rbac_id"]);
            $executionResult = $stmt->execute();
            $stmt->close();
            return $executionResult;
        }

        // در صورت موجود بودن رکورد، false برگردانده می‌شود
        $stmt->close();
        return false;
    }


    public function checkStatusTableStatusArray($part_id, $part_name, array $array_status)
    {
        if (empty($array_status)) {
            return false;
        }

        try {
            $array_status = array_map('strtolower', $array_status);

            $placeholders = implode(',', array_fill(0, count($array_status), '?'));

            $sql = "
            SELECT 1
            FROM {$this->statusTable}
            WHERE part_id = ?
              AND part_name = ?
              AND LOWER(status_name) IN ($placeholders)
            LIMIT 1
        ";

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            $types = "is" . str_repeat('s', count($array_status));

            $params = array_merge(
                [(int) $part_id, $part_name],
                $array_status
            );

            $stmt->bind_param($types, ...$params);

            $stmt->execute();

            $result = $stmt->get_result();

            $exists = $result && $result->num_rows > 0;

            if ($result) {
                $result->free();
            }

            $stmt->close();

            return $exists;
        } catch (\Exception $e) {
            throw new \Exception(
                "Failed to check status array: " . $e->getMessage()
            );
        }
    }
    public function getTicketById($id)
    {
        try {
            $id = (int) $id;

            $sql = "
            SELECT 
    tickets.*, 
    ty.type_name, 
    ty.type_group,
    COALESCE(ud.user_id, ad.admin_id) AS user_id, 
    COALESCE(ud.company_id, ad.company_id) AS company_id,
    COALESCE(ud.company_name, ad.company_name) AS company_name,
    last_status.status_name AS last_status_name
FROM idesk.tickets AS tickets
LEFT JOIN idesk.types ty 
    ON tickets.type_id = ty.id
LEFT JOIN idesk.users_details_view ud 
    ON ud.user_id = tickets.user_id
LEFT JOIN idesk.admins_details_view ad 
    ON ad.admin_id = tickets.admin_id
LEFT JOIN (
    SELECT s1.part_id, s1.status_name
    FROM idesk.status AS s1
    INNER JOIN (
        SELECT part_id, MAX(id) AS max_status_id
        FROM idesk.status
        WHERE status_name != 'Condition_referral_to_manager'
        GROUP BY part_id
    ) s2
        ON s2.part_id = s1.part_id
        AND s2.max_status_id = s1.id
) last_status
    ON last_status.part_id = tickets.id
WHERE tickets.id = ?
LIMIT 1
        ";

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $this->checkStatusTable($id, $this->ticketsTable);
                $stmt->close();

                return $result;
            }

            if ($result) {
                $result->free();
            }

            $stmt->close();

            return false;
        } catch (\Exception $e) {
            throw new \Exception("Failed to get ticket by id: " . $e->getMessage());
        }
    }

    public function getTicketNumberById($id)
    {
        $sql = "SELECT ticket_number
            FROM 
                 tickets
            WHERE 
                id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();

        $stmt->close();
        return $reply['ticket_number'];
    }
    public function getCountFile($id, $part)
    {
        $query = " SELECT Count(id) as total
        FROM " . $this->fileManageTable . " m
        WHERE m.part_id = ? AND m.part_name = ?";

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("is", $id, $part);
        $stmt->execute();
        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();
        $stmt->close();
        return $reply['total'];
    }
    public function getTimeDifferenceArray($start_time, $end_time)
    {
        $start = new \DateTime($start_time);
        $end = new \DateTime($end_time);

        $interval = $start->diff($end);

        $days = $interval->d;
        $hours = $interval->h;
        $minutes = $interval->i;

        return [
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes
        ];
    }



    public function getAllKabanTag()
    {


        $stmt = $this->conn->prepare("SELECT board_tag,id
                FROM kanban_board_tags AS kbt            
                WHERE user_id = ? ORDER BY id DESC ");
        $stmt->bind_param("i", $_SESSION["user_id"]);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function getCountKabanTag()
    {


        $stmt = $this->conn->prepare("SELECT count(id) as count_id
                FROM kanban_board_tags            
                WHERE user_id = ?  ");
        $stmt->bind_param("i", $_SESSION["user_id"]);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllKabanByTagId($tag_id)
    {
        $stmt = $this->conn->prepare("SELECT kb.*,t.ticket_title ,t.ticket_number , t.priority,t.status,t.id as ticket_id ,ty.type_group 
                FROM kanban_board AS kb   
                LEFT JOIN tickets AS t ON t.id = kb.part_id   
                LEFT JOIN types   AS ty ON t.type_id = ty.id      
                WHERE kb.board_tag_id = ?  ");

        $stmt->bind_param("i", $tag_id);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }


    public function getAllMarking($marking_tag_id): array
    {


        $sqlQuery = "
        SELECT DISTINCT part_id
        FROM marking 
        WHERE
            marking_tag_id = ? ";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $marking_tag_id);
        $stmt->execute();

        $result = $stmt->get_result();

        $partIds = [];

        while ($row = $result->fetch_assoc()) {
            $partIds[] = (int) $row['part_id'];
        }

        return $partIds;
    }

    public function getMarkingTagsByTicketIds(array $ticketIds): array
    {
        if (empty($ticketIds)) {
            return [];
        }

        $ticketIds = array_map('intval', $ticketIds);
        $userId = (int) $_SESSION['user_id'];

        $placeholders = implode(',', array_fill(0, count($ticketIds), '?'));

        $sql = "
        SELECT
            m.part_id,
            mt.id,
            mt.marking_tag
        FROM marking m
        INNER JOIN marking_tags mt
            ON mt.id = m.marking_tag_id
        WHERE m.part_id IN ($placeholders)
          AND mt.user_id = ?
    ";

        $stmt = $this->conn->prepare($sql);

        $types = str_repeat('i', count($ticketIds)) . 'i';
        $params = [...$ticketIds, $userId];

        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result();

        $tags = [];

        while ($row = $result->fetch_assoc()) {
            $tags[$row['part_id']] = $row;
        }

        return $tags;
    }

    public function getMarkingTagByTicketId(int $ticketId): ?array
    {
        $userId = (int) $_SESSION['user_id'];

        $sql = "
        SELECT
            mt.id,
            mt.marking_tag
        FROM marking_tags mt
        INNER JOIN marking m
            ON m.marking_tag_id = mt.id
        WHERE m.part_id = ?
          AND mt.user_id = ?
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $ticketId, $userId);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc() ?: null;
    }


    /**
     * غیرفعال‌سازی نرم یک کامنت توسط سازنده آن در سمت کاربر.
     *
     * ترتیب کنترل:
     * 1) شناسه‌ها به عدد صحیح تبدیل می‌شوند.
     * 2) فقط رکورد متعلق به همان تیکت و همان کاربر انتخاب می‌شود.
     * 3) تنها رکورد فعال به is_active = 0 تغییر می‌کند.
     *
     * این محدودیت مانع غیرفعال‌سازی کامنت سایر کاربران یا کامنت تیکت دیگر می‌شود.
     */
    public function deactivateOwnComment(int $commentId, int $ticketId, int $userId): bool
    {
        if ($commentId <= 0 || $ticketId <= 0 || $userId <= 0) {
            return false;
        }

        $sql = "
            UPDATE comments
            SET is_active = 0
            WHERE id = ?
              AND part_id = ?
              AND part_name = 'tickets'
              AND user_id = ?
              AND is_active = 1
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            throw new \Exception('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param('iii', $commentId, $ticketId, $userId);
        $stmt->execute();

        $updated = $stmt->affected_rows === 1;
        $stmt->close();

        return $updated;
    }

}
