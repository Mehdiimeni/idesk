<?php
// model/ticket_model.php

namespace ipanel\model;
class TicketModel
{
    private $ticketsTable = 'tickets';
    private $scheduleTable = 'schedule';
    private $manHourTable = 'man_hour';
    private $forwardsTable = 'forwards';
    private $fileManageTable = 'file_manage';
    private $adminTable = 'admins';
    private $viewTable = 'views';
    private $statusTable = 'status';
    private $markingTable = 'marking';

    private $ticketGridView = 'grid_ticket_data';

    private $ticketGridViewLoad;
    private $conn;
    private $start_date;
    private $end_date;

    public function __construct($db)
    {
        $this->conn = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function setTicketGridView($gridView = '')
    {
        if ($gridView == '' or $gridView == 100000) {
            $this->ticketGridViewLoad = $this->ticketGridView;
        } else {
            $this->ticketGridViewLoad = $this->ticketGridView . "_" . $gridView;
        }
    }

    public function getTicketGridView()
    {

        return $this->ticketGridViewLoad;

    }

    public function setTimeReport($start = 30, $end = 0)
    {
        $this->start_date = date('Y-m-d', strtotime("-$start days"));
        $this->end_date = date('Y-m-d', strtotime("-$end days"));
    }

    public function getAllTickets($condition_name = '', $mark_id = null, $searchWhere = null, $limit = null, $orderBy = null)
    {
        $tickets = [];

        try {
            $sql = "SELECT * FROM " . $this->ticketGridView;

            $whereClauses = [];
            $params = [];

            $companyConditions = $this->getCookieConditions('company', 'company_id');
            $unitConditions = $this->getCookieConditions('unit', 'unit_id');
            $typeConditions = $this->getCookieConditions('type', 'type_id');
            $statusConditions = $this->getConditionNamesByIds($this->getCookieConditions('condition', 'status'));

            $markConditions = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $whereClauses[] = "ticket_id IN (" . implode(',', array_map('intval', $markConditions)) . ")";
            }

            if (!empty($companyConditions)) {
                $whereClauses[] = "company_id IN (" . implode(',', array_map('intval', $companyConditions)) . ")";
            }

            if (!empty($unitConditions)) {
                $whereClauses[] = "unit_id IN (" . implode(',', array_map('intval', $unitConditions)) . ")";
            }

            if (!empty($typeConditions)) {
                $whereClauses[] = "type_id IN (" . implode(',', array_map('intval', $typeConditions)) . ")";
            }

            if (!empty($statusConditions)) {
                $whereClauses[] = "ticket_status IN ('" . implode("','", array_map('addslashes', $statusConditions)) . "')";
            }

            if (!empty($whereClauses)) {
                $sql .= " WHERE " . implode(' AND ', $whereClauses);

                if (!empty($searchWhere)) {
                    $sql .= " AND (" . $searchWhere . ")";
                }
            } else {
                if (!empty($searchWhere)) {
                    $sql .= " WHERE (" . $searchWhere . ")";
                }
            }

            if (!empty($condition_name)) {
                $sql .= (stripos($sql, ' WHERE ') === false) ? " WHERE " : " AND ";
                $sql .= "ticket_status = ?";
                $params[] = $condition_name;
            }

            if (!empty($orderBy)) {
                $sql .= $orderBy;
            }

            if (!empty($limit)) {
                $sql .= $limit;
            }

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }

            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $tickets = $result->fetch_all(MYSQLI_ASSOC);

            $result->free();
            $stmt->close();

        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve tickets: " . $e->getMessage());
        }

        return $tickets;
    }


    public function getAllTicketsCount($condition_name = '', $mark_id = null)
    {
        try {
            $whereClauses = [];
            $params = [];
            $types = '';

            $markConditions = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $markConditions = array_map('intval', $markConditions);
                $markConditions = array_values(array_unique(array_filter($markConditions)));

                if (!empty($markConditions)) {
                    $whereClauses[] = "ticket_id IN (" . implode(',', $markConditions) . ")";
                }
            }

            $companyConditions = $this->getCookieConditions('company', 'company_id');

            if (!empty($companyConditions)) {
                $companyConditions = array_map('intval', $companyConditions);
                $companyConditions = array_values(array_unique(array_filter($companyConditions)));

                if (!empty($companyConditions)) {
                    $whereClauses[] = "company_id IN (" . implode(',', $companyConditions) . ")";
                }
            }

            $unitConditions = $this->getCookieConditions('unit', 'unit_id');

            if (!empty($unitConditions)) {
                $unitConditions = array_map('intval', $unitConditions);
                $unitConditions = array_values(array_unique(array_filter($unitConditions)));

                if (!empty($unitConditions)) {
                    $whereClauses[] = "unit_id IN (" . implode(',', $unitConditions) . ")";
                }
            }

            $typeConditions = $this->getCookieConditions('type', 'type_id');

            if (!empty($typeConditions)) {
                $typeConditions = array_map('intval', $typeConditions);
                $typeConditions = array_values(array_unique(array_filter($typeConditions)));

                if (!empty($typeConditions)) {
                    $whereClauses[] = "type_id IN (" . implode(',', $typeConditions) . ")";
                }
            }

            $statusConditions = $this->getConditionNamesByIds(
                $this->getCookieConditions('condition', 'status')
            );

            if (!empty($statusConditions)) {
                $placeholders = implode(',', array_fill(0, count($statusConditions), '?'));
                $whereClauses[] = "ticket_status IN ($placeholders)";

                foreach ($statusConditions as $statusCondition) {
                    $params[] = $statusCondition;
                    $types .= 's';
                }
            }

            if (!empty($condition_name)) {
                $whereClauses[] = "ticket_status = ?";
                $params[] = $condition_name;
                $types .= 's';
            }

            $sql = "
            SELECT COUNT(*) AS cnt
            FROM {$this->ticketGridView}
        ";

            if (!empty($whereClauses)) {
                $sql .= " WHERE " . implode(' AND ', $whereClauses);
            }

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
            throw new \Exception("Failed to retrieve tickets count: " . $e->getMessage());
        }
    }
    public function getAllTicketsSearchCount($condition_name = '', $mark_id = null, $searchWhere = null)
    {
        $tickets = [];

        try {
            $sql = "SELECT COUNT(*) as cnt FROM " . $this->ticketGridView;

            $whereClauses = [];
            $params = [];

            $companyConditions = $this->getCookieConditions('company', 'company_id');
            $unitConditions = $this->getCookieConditions('unit', 'unit_id');
            $typeConditions = $this->getCookieConditions('type', 'type_id');
            $statusConditions = $this->getConditionNamesByIds($this->getCookieConditions('condition', 'status'));

            $markConditions = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $whereClauses[] = "ticket_id IN (" . implode(',', array_map('intval', $markConditions)) . ")";
            }

            if (!empty($companyConditions)) {
                $whereClauses[] = "company_id IN (" . implode(',', array_map('intval', $companyConditions)) . ")";
            }

            if (!empty($unitConditions)) {
                $whereClauses[] = "unit_id IN (" . implode(',', array_map('intval', $unitConditions)) . ")";
            }

            if (!empty($typeConditions)) {
                $whereClauses[] = "type_id IN (" . implode(',', array_map('intval', $typeConditions)) . ")";
            }

            if (!empty($statusConditions)) {
                $whereClauses[] = "ticket_status IN ('" . implode("','", array_map('addslashes', $statusConditions)) . "')";
            }

            if (!empty($whereClauses)) {
                $sql .= " WHERE " . implode(' AND ', $whereClauses);

                if (!empty($searchWhere)) {
                    $sql .= " AND (" . $searchWhere . ")";
                }
            } else {
                if (!empty($searchWhere)) {
                    $sql .= " WHERE (" . $searchWhere . ")";
                }
            }

            if (!empty($condition_name)) {
                $sql .= (stripos($sql, ' WHERE ') === false) ? " WHERE " : " AND ";
                $sql .= "ticket_status = ?";
                $params[] = $condition_name;
            }

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }

            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $tickets = $result->fetch_all(MYSQLI_ASSOC);

            $result->free();
            $stmt->close();

        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve tickets: " . $e->getMessage());
        }

        return intval($tickets[0]['cnt'] ?? 0);
    }


    public function getUniqueSectionElementIds($AdminId)
    {
        try {
            $sqlQuery = "SELECT DISTINCT section_element_id 
                     FROM status_forwards_detailed_view 
                     WHERE receiver_person_id = ?  ";

            // آماده‌سازی و اجرای دستور SQL
            $stmt = $this->conn->prepare($sqlQuery);
            if ($stmt === false) {
                throw new \Exception("Error preparing statement: " . $this->conn->error);
            }

            $stmt->bind_param("i", $AdminId); // فرض می‌شود که receiver_person_id از نوع integer است

            $stmt->execute();
            $result = $stmt->get_result();

            $sectionElementIds = [];
            while ($row = $result->fetch_assoc()) {
                $sectionElementIds[] = $row['section_element_id'];
            }

            $stmt->close();
            return $sectionElementIds; // برگرداندن آرایه‌ای از section_element_idهای غیر تکراری
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve unique section element IDs: " . $e->getMessage());
        }
    }

    public function getAllForwardTickets($forward_receiver_id, $condition_name = '')
    {
        $tickets = [];
        try {
            // Base SQL query with JOIN to filter by forward_receiver_id
            $sql = "SELECT DISTINCT t.*
                    FROM " . $this->ticketGridView . "  t
                    JOIN general_forward_view f ON t.ticket_id = f.forward_ticket_id
                    WHERE f.forward_receiver_id = ?";

            $params = [$forward_receiver_id]; // Prepare parameters for binding

            $companyConditions = $this->getCookieConditions('company', 'company_id');
            $unitConditions = $this->getCookieConditions('unit', 'unit_id');
            $typeConditions = $this->getCookieConditions('type', 'type_id');
            $statusConditions = $this->getConditionNamesByIds($this->getCookieConditions('condition', 'status'));

            // Building additional WHERE clauses
            $whereClauses = [];
            if (!empty($companyConditions)) {
                $whereClauses[] = "t.company_id IN (" . implode(',', array_map('intval', $companyConditions)) . ")";
            }
            if (!empty($unitConditions)) {
                $whereClauses[] = "t.unit_id IN (" . implode(',', array_map('intval', $unitConditions)) . ")";
            }
            if (!empty($typeConditions)) {
                $whereClauses[] = "t.type_id IN (" . implode(',', array_map('intval', $typeConditions)) . ")";
            }
            if (!empty($statusConditions)) {
                $whereClauses[] = "t.ticket_status IN ('" . implode("','", array_map('addslashes', $statusConditions)) . "')";
            }

            // Append any additional WHERE clauses
            if (!empty($whereClauses)) {
                $sql .= " AND " . implode(' AND ', $whereClauses);
            }

            // Add condition_name filter if provided
            if (!empty($condition_name)) {
                $sql .= " AND t.ticket_status = ?";
                $params[] = $condition_name; // Add the parameter to the params array
            }



            // Prepare and execute the SQL statement
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }

            // Bind parameters
            $types = str_repeat('s', count($params)); // 's' for string (if ticket_id is integer, change to 'i')
            $stmt->bind_param($types, ...$params);

            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch all unique tickets at once
            $tickets = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve tickets: " . $e->getMessage());
        }

        return $tickets; // Return the array of unique tickets
    }

    public function getAllAdminForwardTickets($forward_sender_id, $condition_name = '', $searchWhere = null, $limit = null, $orderBy = null, $mark_id = null)
    {
        try {
            $allForwardTicketIds = $this->getDistinctForwardTicketIdsBySender($forward_sender_id);

            if (empty($allForwardTicketIds)) {
                return [];
            }

            $ticketIds = array_map('intval', $allForwardTicketIds);
            $ticketIds = array_values(array_unique(array_filter($ticketIds)));

            if (empty($ticketIds)) {
                return [];
            }

            $whereClauses = [];
            $params = [];
            $types = '';

            $whereClauses[] = "t.ticket_id IN (" . implode(',', $ticketIds) . ")";

            $markConditions = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $markConditions = array_map('intval', $markConditions);
                $markConditions = array_values(array_unique(array_filter($markConditions)));

                if (!empty($markConditions)) {
                    $whereClauses[] = "t.ticket_id IN (" . implode(',', $markConditions) . ")";
                }
            }

            $companyConditions = $this->getCookieConditions('company', 'company_id');

            if (!empty($companyConditions)) {
                $companyConditions = array_map('intval', $companyConditions);
                $companyConditions = array_values(array_unique(array_filter($companyConditions)));

                if (!empty($companyConditions)) {
                    $whereClauses[] = "t.company_id IN (" . implode(',', $companyConditions) . ")";
                }
            }

            $unitConditions = $this->getCookieConditions('unit', 'unit_id');

            if (!empty($unitConditions)) {
                $unitConditions = array_map('intval', $unitConditions);
                $unitConditions = array_values(array_unique(array_filter($unitConditions)));

                if (!empty($unitConditions)) {
                    $whereClauses[] = "t.unit_id IN (" . implode(',', $unitConditions) . ")";
                }
            }

            $typeConditions = $this->getCookieConditions('type', 'type_id');

            if (!empty($typeConditions)) {
                $typeConditions = array_map('intval', $typeConditions);
                $typeConditions = array_values(array_unique(array_filter($typeConditions)));

                if (!empty($typeConditions)) {
                    $whereClauses[] = "t.type_id IN (" . implode(',', $typeConditions) . ")";
                }
            }

            $statusConditions = $this->getConditionNamesByIds(
                $this->getCookieConditions('condition', 'status')
            );

            if (!empty($statusConditions)) {
                $placeholders = implode(',', array_fill(0, count($statusConditions), '?'));
                $whereClauses[] = "t.ticket_status IN ($placeholders)";

                foreach ($statusConditions as $statusCondition) {
                    $params[] = $statusCondition;
                    $types .= 's';
                }
            }

            if (!empty($searchWhere)) {
                $whereClauses[] = "(" . $searchWhere . ")";
            }

            if (!empty($condition_name)) {
                $whereClauses[] = "t.ticket_status = ?";
                $params[] = $condition_name;
                $types .= 's';
            }

            $sql = "
            SELECT DISTINCT t.*
            FROM {$this->ticketGridView} t
            WHERE " . implode(' AND ', $whereClauses);

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

            $result = $stmt->get_result();
            $tickets = $result->fetch_all(MYSQLI_ASSOC);

            if ($result) {
                $result->free();
            }

            $stmt->close();

            return $tickets ?: [];
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve tickets: " . $e->getMessage());
        }
    }

    public function getAllAdminForwardTicketsCount($forward_sender_id, $condition_name = '', $mark_id = null)
    {
        try {
            $allForwardTicketIds = $this->getDistinctForwardTicketIdsBySender($forward_sender_id);

            if (empty($allForwardTicketIds)) {
                return 0;
            }

            $ticketIds = array_map('intval', $allForwardTicketIds);
            $ticketIds = array_values(array_unique(array_filter($ticketIds)));

            if (empty($ticketIds)) {
                return 0;
            }

            $whereClauses = [];
            $params = [];
            $types = '';

            $whereClauses[] = "t.ticket_id IN (" . implode(',', $ticketIds) . ")";

            $markConditions = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $markConditions = array_map('intval', $markConditions);
                $markConditions = array_values(array_unique(array_filter($markConditions)));

                if (!empty($markConditions)) {
                    $whereClauses[] = "t.ticket_id IN (" . implode(',', $markConditions) . ")";
                }
            }

            $companyConditions = $this->getCookieConditions('company', 'company_id');

            if (!empty($companyConditions)) {
                $companyConditions = array_map('intval', $companyConditions);
                $companyConditions = array_values(array_unique(array_filter($companyConditions)));

                if (!empty($companyConditions)) {
                    $whereClauses[] = "t.company_id IN (" . implode(',', $companyConditions) . ")";
                }
            }

            $unitConditions = $this->getCookieConditions('unit', 'unit_id');

            if (!empty($unitConditions)) {
                $unitConditions = array_map('intval', $unitConditions);
                $unitConditions = array_values(array_unique(array_filter($unitConditions)));

                if (!empty($unitConditions)) {
                    $whereClauses[] = "t.unit_id IN (" . implode(',', $unitConditions) . ")";
                }
            }

            $typeConditions = $this->getCookieConditions('type', 'type_id');

            if (!empty($typeConditions)) {
                $typeConditions = array_map('intval', $typeConditions);
                $typeConditions = array_values(array_unique(array_filter($typeConditions)));

                if (!empty($typeConditions)) {
                    $whereClauses[] = "t.type_id IN (" . implode(',', $typeConditions) . ")";
                }
            }

            $statusConditions = $this->getConditionNamesByIds(
                $this->getCookieConditions('condition', 'status')
            );

            if (!empty($statusConditions)) {
                $placeholders = implode(',', array_fill(0, count($statusConditions), '?'));
                $whereClauses[] = "t.ticket_status IN ($placeholders)";

                foreach ($statusConditions as $statusCondition) {
                    $params[] = $statusCondition;
                    $types .= 's';
                }
            }

            if (!empty($condition_name)) {
                $whereClauses[] = "t.ticket_status = ?";
                $params[] = $condition_name;
                $types .= 's';
            }

            $sql = "
            SELECT COUNT(*) AS cnt
            FROM {$this->ticketGridView} t
            WHERE " . implode(' AND ', $whereClauses);

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
            throw new \Exception("Failed to retrieve tickets count: " . $e->getMessage());
        }
    }

    public function getAllAdminForwardTicketsSearchCount($forward_sender_id, $condition_name = '', $searchWhere = null, $mark_id = null)
    {
        try {
            $allForwardTicketIds = $this->getDistinctForwardTicketIdsBySender($forward_sender_id);

            if (empty($allForwardTicketIds)) {
                return 0;
            }

            $ticketIds = array_map('intval', $allForwardTicketIds);
            $ticketIds = array_values(array_unique(array_filter($ticketIds)));

            if (empty($ticketIds)) {
                return 0;
            }

            $whereClauses = [];
            $params = [];
            $types = '';

            $whereClauses[] = "t.ticket_id IN (" . implode(',', $ticketIds) . ")";

            $markConditions = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $markConditions = array_map('intval', $markConditions);
                $markConditions = array_values(array_unique(array_filter($markConditions)));

                if (!empty($markConditions)) {
                    $whereClauses[] = "t.ticket_id IN (" . implode(',', $markConditions) . ")";
                }
            }

            $companyConditions = $this->getCookieConditions('company', 'company_id');

            if (!empty($companyConditions)) {
                $companyConditions = array_map('intval', $companyConditions);
                $companyConditions = array_values(array_unique(array_filter($companyConditions)));

                if (!empty($companyConditions)) {
                    $whereClauses[] = "t.company_id IN (" . implode(',', $companyConditions) . ")";
                }
            }

            $unitConditions = $this->getCookieConditions('unit', 'unit_id');

            if (!empty($unitConditions)) {
                $unitConditions = array_map('intval', $unitConditions);
                $unitConditions = array_values(array_unique(array_filter($unitConditions)));

                if (!empty($unitConditions)) {
                    $whereClauses[] = "t.unit_id IN (" . implode(',', $unitConditions) . ")";
                }
            }

            $typeConditions = $this->getCookieConditions('type', 'type_id');

            if (!empty($typeConditions)) {
                $typeConditions = array_map('intval', $typeConditions);
                $typeConditions = array_values(array_unique(array_filter($typeConditions)));

                if (!empty($typeConditions)) {
                    $whereClauses[] = "t.type_id IN (" . implode(',', $typeConditions) . ")";
                }
            }

            $statusConditions = $this->getConditionNamesByIds(
                $this->getCookieConditions('condition', 'status')
            );

            if (!empty($statusConditions)) {
                $placeholders = implode(',', array_fill(0, count($statusConditions), '?'));
                $whereClauses[] = "t.ticket_status IN ($placeholders)";

                foreach ($statusConditions as $statusCondition) {
                    $params[] = $statusCondition;
                    $types .= 's';
                }
            }

            if (!empty($searchWhere)) {
                $whereClauses[] = "(" . $searchWhere . ")";
            }

            if (!empty($condition_name)) {
                $whereClauses[] = "t.ticket_status = ?";
                $params[] = $condition_name;
                $types .= 's';
            }

            $sql = "
            SELECT COUNT(*) AS cnt
            FROM {$this->ticketGridView} t
            WHERE " . implode(' AND ', $whereClauses);

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
            throw new \Exception("Failed to retrieve tickets search count: " . $e->getMessage());
        }
    }

    public function getAllAdminForwardTicketsWithExcept($forward_sender_id, $condition_name = '')
    {
        $tickets = [];
        try {
            // Get the last forward_ticket_ids for the given forward_sender_id
            $lastForwardTicketIds = $this->getLastForwardTicketIdsByAdmin($forward_sender_id);

            // If no ticket ids found, return empty array
            if (empty($lastForwardTicketIds)) {
                return $tickets;
            }

            // Base SQL query to get ticket details
            $sql = "SELECT DISTINCT t.*
                    FROM " . $this->ticketGridView . "  t
                    WHERE t.ticket_id IN (" . implode(',', array_map('intval', $lastForwardTicketIds)) . ")"; // Use the last forward ticket IDs

            // Additional conditions based on cookies
            $companyConditions = $this->getCookieConditions('company', 'company_id');
            $unitConditions = $this->getCookieConditions('unit', 'unit_id');
            $typeConditions = $this->getCookieConditions('type', 'type_id');
            $statusConditions = $this->getConditionNamesByIds($this->getCookieConditions('condition', 'status'));

            // Building additional WHERE clauses
            $whereClauses = [];
            if (!empty($companyConditions)) {
                $whereClauses[] = "t.company_id IN (" . implode(',', array_map('intval', $companyConditions)) . ")";
            }
            if (!empty($unitConditions)) {
                $whereClauses[] = "t.unit_id IN (" . implode(',', array_map('intval', $unitConditions)) . ")";
            }
            if (!empty($typeConditions)) {
                $whereClauses[] = "t.type_id IN (" . implode(',', array_map('intval', $typeConditions)) . ")";
            }
            if (!empty($statusConditions)) {
                $whereClauses[] = "t.ticket_status IN ('" . implode("','", array_map('addslashes', $statusConditions)) . "')";
            }

            // Append any additional WHERE clauses
            if (!empty($whereClauses)) {
                $sql .= " AND " . implode(' AND ', $whereClauses);
            }

            // Add condition_name filter if provided
            if (!empty($condition_name)) {
                $sql .= " AND t.ticket_status != ?";
                $params[] = $condition_name; // Add the parameter to the params array
            }


            // Prepare and execute the SQL statement
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }

            // Bind parameters if condition_name is provided
            if (!empty($params)) {
                $types = str_repeat('s', count($params)); // Assuming all parameters are strings
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch all unique tickets at once
            $tickets = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve tickets: " . $e->getMessage());
        }

        return $tickets; // Return the array of unique tickets
    }


    public function getAllAdminNoActionTickets($forward_receiver_id, $condition_name = '', $searchWhere = null, $limit = null, $orderBy = null, $mark_id = null)
    {
        $tickets = [];
        try {
            // Get the last forward_ticket_ids for the given forward_receiver_id
            $lastForwardTicketIds = $this->getLastReceiveTicketIdsByAdmin($forward_receiver_id);

            // If no ticket ids found, return empty array
            if (empty($lastForwardTicketIds)) {
                return $tickets;
            }

            // Base SQL query to get ticket details
            $sql = "SELECT DISTINCT t.*
                    FROM " . $this->ticketGridView . "  t
                    WHERE t.ticket_id IN (" . implode(',', array_map('intval', $lastForwardTicketIds)) . ")"; // Use the last forward ticket IDs

            $companyConditions = $this->getCookieConditions('company', 'company_id');
            $unitConditions = $this->getCookieConditions('unit', 'unit_id');
            $typeConditions = $this->getCookieConditions('type', 'type_id');
            $statusConditions = $this->getConditionNamesByIds($this->getCookieConditions('condition', 'status'));

            // Building additional WHERE clauses
            $whereClauses = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $whereClauses[] = "ticket_id IN (" . implode(',', array_map('intval', $markConditions)) . ")";
            }

            if (!empty($companyConditions)) {
                $whereClauses[] = "t.company_id IN (" . implode(',', array_map('intval', $companyConditions)) . ")";
            }
            if (!empty($unitConditions)) {
                $whereClauses[] = "t.unit_id IN (" . implode(',', array_map('intval', $unitConditions)) . ")";
            }
            if (!empty($typeConditions)) {
                $whereClauses[] = "t.type_id IN (" . implode(',', array_map('intval', $typeConditions)) . ")";
            }
            if (!empty($statusConditions)) {
                $whereClauses[] = "t.ticket_status IN ('" . implode("','", array_map('addslashes', $statusConditions)) . "')";
            }

            // Append any additional WHERE clauses
            // Append WHERE clause if any conditions exist
            if (!empty($whereClauses)) {
                $sql .= " AND " . implode(' AND ', $whereClauses);
                if (!empty($searchWhere)) {
                    $sql .= " AND (" . $searchWhere . ")";
                }
            } else {

                if (!empty($searchWhere)) {
                    $sql .= " AND " . "(" . $searchWhere . ")";
                }

            }

            // Add condition_name filter if provided
            if (!empty($condition_name)) {
                $sql .= " AND t.ticket_status = ?";
                $params[] = $condition_name; // Add the parameter to the params array
            }


            if (!empty($orderBy)) {
                $sql .= $orderBy;
            }

            if (!empty($limit)) {
                $sql .= $limit;
            }

            // Prepare and execute the SQL statement
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }

            // Bind parameters if condition_name is provided
            if (!empty($params)) {
                $types = str_repeat('s', count($params)); // Assuming all parameters are strings
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch all unique tickets at once
            $tickets = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve tickets: " . $e->getMessage());
        }

        return $tickets; // Return the array of unique tickets
    }


    public function getAllAdminNoActionTicketsCount($forward_receiver_id, $condition_name = '', $mark_id = null)
    {
        $tickets = [];
        try {
            // Get the last forward_ticket_ids for the given forward_receiver_id
            $lastForwardTicketIds = $this->getLastReceiveTicketIdsByAdmin($forward_receiver_id);

            // If no ticket ids found, return empty array
            if (empty($lastForwardTicketIds)) {
                return $tickets;
            }

            // Base SQL query to get ticket details
            $sql = "SELECT DISTINCT COUNT(*) as cnt
                    FROM " . $this->ticketGridView . "  t
                    WHERE t.ticket_id IN (" . implode(',', array_map('intval', $lastForwardTicketIds)) . ")"; // Use the last forward ticket IDs

            $companyConditions = $this->getCookieConditions('company', 'company_id');
            $unitConditions = $this->getCookieConditions('unit', 'unit_id');
            $typeConditions = $this->getCookieConditions('type', 'type_id');
            $statusConditions = $this->getConditionNamesByIds($this->getCookieConditions('condition', 'status'));

            // Building additional WHERE clauses
            $whereClauses = [];
            if (!empty($companyConditions)) {
                $whereClauses[] = "t.company_id IN (" . implode(',', array_map('intval', $companyConditions)) . ")";
            }
            if (!empty($unitConditions)) {
                $whereClauses[] = "t.unit_id IN (" . implode(',', array_map('intval', $unitConditions)) . ")";
            }
            if (!empty($typeConditions)) {
                $whereClauses[] = "t.type_id IN (" . implode(',', array_map('intval', $typeConditions)) . ")";
            }
            if (!empty($statusConditions)) {
                $whereClauses[] = "t.ticket_status IN ('" . implode("','", array_map('addslashes', $statusConditions)) . "')";
            }

            // Append any additional WHERE clauses
            if (!empty($whereClauses)) {
                $sql .= " AND " . implode(' AND ', $whereClauses);
            }

            // Add condition_name filter if provided
            if (!empty($condition_name)) {
                $sql .= " AND t.ticket_status = ?";
                $params[] = $condition_name; // Add the parameter to the params array
            }


            // Prepare and execute the SQL statement
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }

            // Bind parameters if condition_name is provided
            if (!empty($params)) {
                $types = str_repeat('s', count($params)); // Assuming all parameters are strings
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch all unique tickets at once
            $tickets = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve tickets: " . $e->getMessage());
        }

        return intval($tickets[0]['cnt']);  // Return the array of unique tickets
    }


    public function getAllAdminNoActionTicketsSearchCount($forward_receiver_id, $condition_name = '', $searchWhere = null, $mark_id = null)
    {
        $tickets = [];
        try {
            // Get the last forward_ticket_ids for the given forward_receiver_id
            $lastForwardTicketIds = $this->getLastReceiveTicketIdsByAdmin($forward_receiver_id);

            // If no ticket ids found, return empty array
            if (empty($lastForwardTicketIds)) {
                return $tickets;
            }

            // Base SQL query to get ticket details
            $sql = "SELECT DISTINCT COUNT(*) as cnt
                    FROM " . $this->ticketGridView . "  t
                    WHERE t.ticket_id IN (" . implode(',', array_map('intval', $lastForwardTicketIds)) . ")"; // Use the last forward ticket IDs

            $companyConditions = $this->getCookieConditions('company', 'company_id');
            $unitConditions = $this->getCookieConditions('unit', 'unit_id');
            $typeConditions = $this->getCookieConditions('type', 'type_id');
            $statusConditions = $this->getConditionNamesByIds($this->getCookieConditions('condition', 'status'));

            // Building additional WHERE clauses
            $whereClauses = [];
            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $whereClauses[] = "ticket_id IN (" . implode(',', array_map('intval', $markConditions)) . ")";
            }
            if (!empty($companyConditions)) {
                $whereClauses[] = "t.company_id IN (" . implode(',', array_map('intval', $companyConditions)) . ")";
            }
            if (!empty($unitConditions)) {
                $whereClauses[] = "t.unit_id IN (" . implode(',', array_map('intval', $unitConditions)) . ")";
            }
            if (!empty($typeConditions)) {
                $whereClauses[] = "t.type_id IN (" . implode(',', array_map('intval', $typeConditions)) . ")";
            }
            if (!empty($statusConditions)) {
                $whereClauses[] = "t.ticket_status IN ('" . implode("','", array_map('addslashes', $statusConditions)) . "')";
            }

            // Append any additional WHERE clauses
            if (!empty($whereClauses)) {
                $sql .= " AND " . implode(' AND ', $whereClauses);
                if (!empty($searchWhere)) {
                    $sql .= " AND (" . $searchWhere . ")";
                }
            } else {

                if (!empty($searchWhere)) {
                    $sql .= " AND " . "(" . $searchWhere . ")";
                }

            }

            // Add condition_name filter if provided
            if (!empty($condition_name)) {
                $sql .= " AND t.ticket_status = ?";
                $params[] = $condition_name; // Add the parameter to the params array
            }


            // Prepare and execute the SQL statement
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }

            // Bind parameters if condition_name is provided
            if (!empty($params)) {
                $types = str_repeat('s', count($params)); // Assuming all parameters are strings
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch all unique tickets at once
            $tickets = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve tickets: " . $e->getMessage());
        }

        return intval($tickets[0]['cnt']);  // Return the array of unique tickets
    }

    public function getAllAdminNoActionTicketsWithExcept($forward_receiver_id, $array_condition_name = [], $searchWhere = null, $limit = null, $orderBy = null, $mark_id = null)
    {
        try {
            $lastForwardTicketIds = $this->getLastReceiveTicketIdsByAdmin($forward_receiver_id);

            if (empty($lastForwardTicketIds)) {
                return [];
            }

            $ticketIds = array_map('intval', $lastForwardTicketIds);
            $ticketIds = array_values(array_unique(array_filter($ticketIds)));

            if (empty($ticketIds)) {
                return [];
            }

            $whereClauses = [];
            $params = [];
            $types = '';

            $whereClauses[] = "t.ticket_id IN (" . implode(',', $ticketIds) . ")";

            $markConditions = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $markConditions = array_map('intval', $markConditions);
                $markConditions = array_values(array_unique(array_filter($markConditions)));

                if (!empty($markConditions)) {
                    $whereClauses[] = "t.ticket_id IN (" . implode(',', $markConditions) . ")";
                }
            }

            $companyConditions = $this->getCookieConditions('company', 'company_id');

            if (!empty($companyConditions)) {
                $companyConditions = array_map('intval', $companyConditions);
                $companyConditions = array_values(array_unique(array_filter($companyConditions)));

                if (!empty($companyConditions)) {
                    $whereClauses[] = "t.company_id IN (" . implode(',', $companyConditions) . ")";
                }
            }

            $unitConditions = $this->getCookieConditions('unit', 'unit_id');

            if (!empty($unitConditions)) {
                $unitConditions = array_map('intval', $unitConditions);
                $unitConditions = array_values(array_unique(array_filter($unitConditions)));

                if (!empty($unitConditions)) {
                    $whereClauses[] = "t.unit_id IN (" . implode(',', $unitConditions) . ")";
                }
            }

            $typeConditions = $this->getCookieConditions('type', 'type_id');

            if (!empty($typeConditions)) {
                $typeConditions = array_map('intval', $typeConditions);
                $typeConditions = array_values(array_unique(array_filter($typeConditions)));

                if (!empty($typeConditions)) {
                    $whereClauses[] = "t.type_id IN (" . implode(',', $typeConditions) . ")";
                }
            }

            $statusConditions = $this->getConditionNamesByIds(
                $this->getCookieConditions('condition', 'status')
            );

            if (!empty($statusConditions)) {
                $placeholders = implode(',', array_fill(0, count($statusConditions), '?'));
                $whereClauses[] = "t.ticket_status IN ($placeholders)";

                foreach ($statusConditions as $statusCondition) {
                    $params[] = $statusCondition;
                    $types .= 's';
                }
            }

            if (!empty($searchWhere)) {
                $whereClauses[] = "(" . $searchWhere . ")";
            }

            if (!empty($array_condition_name)) {
                if (is_array($array_condition_name)) {
                    $placeholders = implode(',', array_fill(0, count($array_condition_name), '?'));
                    $whereClauses[] = "t.ticket_status NOT IN ($placeholders)";

                    foreach ($array_condition_name as $conditionName) {
                        $params[] = $conditionName;
                        $types .= 's';
                    }
                } else {
                    $whereClauses[] = "t.ticket_status != ?";
                    $params[] = $array_condition_name;
                    $types .= 's';
                }
            }

            $sql = "
            SELECT DISTINCT t.*
            FROM {$this->ticketGridView} t
            WHERE " . implode(' AND ', $whereClauses);

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

            $result = $stmt->get_result();
            $tickets = $result->fetch_all(MYSQLI_ASSOC);

            if ($result) {
                $result->free();
            }

            $stmt->close();

            return $tickets ?: [];
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve tickets: " . $e->getMessage());
        }
    }

    public function getAllAdminNoActionTicketsWithExceptCount($forward_receiver_id, $array_condition_name = [], $mark_id = null)
    {
        try {
            $lastForwardTicketIds = $this->getLastReceiveTicketIdsByAdmin($forward_receiver_id);

            if (empty($lastForwardTicketIds)) {
                return 0;
            }

            $ticketIds = array_map('intval', $lastForwardTicketIds);
            $ticketIds = array_values(array_unique(array_filter($ticketIds)));

            if (empty($ticketIds)) {
                return 0;
            }

            $whereClauses = [];
            $params = [];
            $types = '';

            $whereClauses[] = "t.ticket_id IN (" . implode(',', $ticketIds) . ")";

            $markConditions = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $markConditions = array_map('intval', $markConditions);
                $markConditions = array_values(array_unique(array_filter($markConditions)));

                if (!empty($markConditions)) {
                    $whereClauses[] = "t.ticket_id IN (" . implode(',', $markConditions) . ")";
                }
            }

            $companyConditions = $this->getCookieConditions('company', 'company_id');

            if (!empty($companyConditions)) {
                $companyConditions = array_map('intval', $companyConditions);
                $companyConditions = array_values(array_unique(array_filter($companyConditions)));

                if (!empty($companyConditions)) {
                    $whereClauses[] = "t.company_id IN (" . implode(',', $companyConditions) . ")";
                }
            }

            $unitConditions = $this->getCookieConditions('unit', 'unit_id');

            if (!empty($unitConditions)) {
                $unitConditions = array_map('intval', $unitConditions);
                $unitConditions = array_values(array_unique(array_filter($unitConditions)));

                if (!empty($unitConditions)) {
                    $whereClauses[] = "t.unit_id IN (" . implode(',', $unitConditions) . ")";
                }
            }

            $typeConditions = $this->getCookieConditions('type', 'type_id');

            if (!empty($typeConditions)) {
                $typeConditions = array_map('intval', $typeConditions);
                $typeConditions = array_values(array_unique(array_filter($typeConditions)));

                if (!empty($typeConditions)) {
                    $whereClauses[] = "t.type_id IN (" . implode(',', $typeConditions) . ")";
                }
            }

            $statusConditions = $this->getConditionNamesByIds(
                $this->getCookieConditions('condition', 'status')
            );

            if (!empty($statusConditions)) {
                $placeholders = implode(',', array_fill(0, count($statusConditions), '?'));
                $whereClauses[] = "t.ticket_status IN ($placeholders)";

                foreach ($statusConditions as $statusCondition) {
                    $params[] = $statusCondition;
                    $types .= 's';
                }
            }

            if (!empty($array_condition_name)) {
                if (is_array($array_condition_name)) {
                    $placeholders = implode(',', array_fill(0, count($array_condition_name), '?'));
                    $whereClauses[] = "t.ticket_status NOT IN ($placeholders)";

                    foreach ($array_condition_name as $conditionName) {
                        $params[] = $conditionName;
                        $types .= 's';
                    }
                } else {
                    $whereClauses[] = "t.ticket_status != ?";
                    $params[] = $array_condition_name;
                    $types .= 's';
                }
            }

            $sql = "
            SELECT COUNT(*) AS cnt
            FROM {$this->ticketGridView} t
            WHERE " . implode(' AND ', $whereClauses);

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
            throw new \Exception("Failed to retrieve tickets count: " . $e->getMessage());
        }
    }
    public function getAllAdminNoActionTicketsWithExceptSearchCount($forward_receiver_id, $array_condition_name = [], $searchWhere = null, $mark_id = null)
    {
        try {
            $lastForwardTicketIds = $this->getLastReceiveTicketIdsByAdmin($forward_receiver_id);

            if (empty($lastForwardTicketIds)) {
                return 0;
            }

            $ticketIds = array_map('intval', $lastForwardTicketIds);
            $ticketIds = array_values(array_unique(array_filter($ticketIds)));

            if (empty($ticketIds)) {
                return 0;
            }

            $whereClauses = [];
            $params = [];
            $types = '';

            $whereClauses[] = "t.ticket_id IN (" . implode(',', $ticketIds) . ")";

            $markConditions = [];

            if (!empty($mark_id)) {
                $markConditions = $this->getAllMarking($mark_id);
            }

            if (!empty($markConditions)) {
                $markConditions = array_map('intval', $markConditions);
                $markConditions = array_values(array_unique(array_filter($markConditions)));

                if (!empty($markConditions)) {
                    $whereClauses[] = "t.ticket_id IN (" . implode(',', $markConditions) . ")";
                }
            }

            $companyConditions = $this->getCookieConditions('company', 'company_id');

            if (!empty($companyConditions)) {
                $companyConditions = array_map('intval', $companyConditions);
                $companyConditions = array_values(array_unique(array_filter($companyConditions)));

                if (!empty($companyConditions)) {
                    $whereClauses[] = "t.company_id IN (" . implode(',', $companyConditions) . ")";
                }
            }

            $unitConditions = $this->getCookieConditions('unit', 'unit_id');

            if (!empty($unitConditions)) {
                $unitConditions = array_map('intval', $unitConditions);
                $unitConditions = array_values(array_unique(array_filter($unitConditions)));

                if (!empty($unitConditions)) {
                    $whereClauses[] = "t.unit_id IN (" . implode(',', $unitConditions) . ")";
                }
            }

            $typeConditions = $this->getCookieConditions('type', 'type_id');

            if (!empty($typeConditions)) {
                $typeConditions = array_map('intval', $typeConditions);
                $typeConditions = array_values(array_unique(array_filter($typeConditions)));

                if (!empty($typeConditions)) {
                    $whereClauses[] = "t.type_id IN (" . implode(',', $typeConditions) . ")";
                }
            }

            $statusConditions = $this->getConditionNamesByIds(
                $this->getCookieConditions('condition', 'status')
            );

            if (!empty($statusConditions)) {
                $placeholders = implode(',', array_fill(0, count($statusConditions), '?'));
                $whereClauses[] = "t.ticket_status IN ($placeholders)";

                foreach ($statusConditions as $statusCondition) {
                    $params[] = $statusCondition;
                    $types .= 's';
                }
            }

            if (!empty($searchWhere)) {
                $whereClauses[] = "(" . $searchWhere . ")";
            }

            if (!empty($array_condition_name)) {
                if (is_array($array_condition_name)) {
                    $placeholders = implode(',', array_fill(0, count($array_condition_name), '?'));
                    $whereClauses[] = "t.ticket_status NOT IN ($placeholders)";

                    foreach ($array_condition_name as $conditionName) {
                        $params[] = $conditionName;
                        $types .= 's';
                    }
                } else {
                    $whereClauses[] = "t.ticket_status != ?";
                    $params[] = $array_condition_name;
                    $types .= 's';
                }
            }

            $sql = "
            SELECT COUNT(*) AS cnt
            FROM {$this->ticketGridView} t
            WHERE " . implode(' AND ', $whereClauses);

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
            throw new \Exception("Failed to retrieve tickets search count: " . $e->getMessage());
        }
    }

    public function getAdminNoActionTicketsWithExceptCount($forward_receiver_id, $array_condition_name = [])
    {
        try {
            $lastForwardTicketIds = $this->getLastReceiveTicketIdsByAdmin($forward_receiver_id);

            if (empty($lastForwardTicketIds)) {
                return 0;
            }

            $ticketIds = array_map('intval', $lastForwardTicketIds);
            $ticketIds = array_unique($ticketIds);
            $ticketIds = array_filter($ticketIds, function ($id) {
                return $id > 0;
            });

            if (empty($ticketIds)) {
                return 0;
            }

            $idsList = implode(',', $ticketIds);

            $sql = "
            SELECT COUNT(DISTINCT t.ticket_id) AS ticket_count
            FROM {$this->ticketGridView} t
            WHERE t.ticket_id IN ($idsList)
        ";

            $params = [];

            if (!empty($array_condition_name)) {
                if (is_array($array_condition_name)) {
                    $placeholders = implode(',', array_fill(0, count($array_condition_name), '?'));
                    $sql .= " AND t.ticket_status NOT IN ($placeholders)";
                    $params = array_values($array_condition_name);
                } else {
                    $sql .= " AND t.ticket_status != ?";
                    $params[] = $array_condition_name;
                }
            }

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();

            $result = $stmt->get_result();

            $count = 0;

            if ($row = $result->fetch_assoc()) {
                $count = (int) $row['ticket_count'];
            }

            $result->free();
            $stmt->close();

            return $count;
        } catch (\Exception $e) {
            throw new \Exception("Failed to count tickets: " . $e->getMessage());
        }
    }



    public function getAdminNoActionTicketsCount($forward_receiver_id)
    {
        try {
            $lastForwardTicketIds = $this->getLastReceiveTicketIdsByAdmin($forward_receiver_id);

            if (empty($lastForwardTicketIds)) {
                return 0;
            }

            $ticketIds = array_map('intval', $lastForwardTicketIds);
            $ticketIds = array_unique($ticketIds);
            $ticketIds = array_filter($ticketIds, function ($id) {
                return $id > 0;
            });

            if (empty($ticketIds)) {
                return 0;
            }

            $idsList = implode(',', $ticketIds);

            $sql = "
            SELECT COUNT(DISTINCT t.ticket_id) AS ticket_count
            FROM {$this->ticketGridView} t
            WHERE t.ticket_id IN ($idsList)
        ";

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            $stmt->execute();

            $result = $stmt->get_result();

            $count = 0;

            if ($row = $result->fetch_assoc()) {
                $count = (int) $row['ticket_count'];
            }

            $result->free();
            $stmt->close();

            return $count;
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve tickets count: " . $e->getMessage());
        }
    }

    public function getAdminNoActionTicketsWithExceptCountDay($forward_receiver_id, $array_condition_name = [])
    {
        $count = 0;
        try {
            $lastForwardTicketIds = $this->getLastReceiveTicketIdsByAdmin($forward_receiver_id);

            if (empty($lastForwardTicketIds)) {
                return $count;
            }

            $placeholders = implode(',', array_fill(0, count($lastForwardTicketIds), '?'));

            $sql = "SELECT COUNT(DISTINCT t.ticket_id) AS ticket_count
                    FROM " . $this->ticketGridView . "  t
                    WHERE t.ticket_id IN ($placeholders)";

            $params = $lastForwardTicketIds;

            // اضافه کردن شرایط وضعیت
            if (!empty($array_condition_name)) {
                if (is_array($array_condition_name)) {
                    $statusPlaceholders = implode(',', array_fill(0, count($array_condition_name), '?'));
                    $sql .= " AND t.ticket_status NOT IN ($statusPlaceholders)";
                    $params = array_merge($params, $array_condition_name);
                } else {
                    $sql .= " AND t.ticket_status != ?";
                    $params[] = $array_condition_name;
                }
            }

            $sql .= " AND t.ticket_creation_date BETWEEN ? AND ?";
            $params[] = $this->start_date;
            $params[] = $this->end_date;

            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            // تعیین نوع پارامترها برای bind_param
            $types = str_repeat('i', count($lastForwardTicketIds)) . str_repeat('s', count($params) - count($lastForwardTicketIds));
            $stmt->bind_param($types, ...$params);

            $stmt->execute();
            $result = $stmt->get_result();

            $row = $result->fetch_assoc();
            $count = $row['ticket_count'] ?? 0;
            $result->free();
        } catch (\Exception $e) {
            throw new \Exception("Failed to count tickets: " . $e->getMessage());
        }

        return $count;
    }


    public function getAdminNoActionTicketsCountDay($forward_receiver_id)
    {
        $count = 0;
        try {
            $lastForwardTicketIds = $this->getLastReceiveTicketIdsByAdmin($forward_receiver_id);

            if (empty($lastForwardTicketIds)) {
                return $count;
            }

            $placeholders = implode(',', array_fill(0, count($lastForwardTicketIds), '?'));

            $sql = "SELECT COUNT(DISTINCT t.ticket_id) AS ticket_count
                    FROM " . $this->ticketGridView . "  t
                    WHERE t.ticket_id IN ($placeholders)";

            $params = $lastForwardTicketIds;

            $sql .= " AND t.ticket_creation_date BETWEEN ? AND ?";
            $params[] = $this->start_date;
            $params[] = $this->end_date;

            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            // تعیین نوع پارامترها برای bind_param
            $types = str_repeat('i', count($lastForwardTicketIds)) . str_repeat('s', count($params) - count($lastForwardTicketIds));
            $stmt->bind_param($types, ...$params);

            $stmt->execute();
            $result = $stmt->get_result();

            $row = $result->fetch_assoc();
            $count = $row['ticket_count'] ?? 0;
            $result->free();
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve tickets count: " . $e->getMessage());
        }

        return $count;
    }




    public function getLastReceiveTicketIdsByAdmin($forward_receiver_id)
    {
        $ticket_ids = [];

        try {
            $sql = "
            SELECT g.forward_ticket_id
            FROM general_forward_view g
            INNER JOIN (
                SELECT forward_ticket_id, MAX(forward_id) AS max_forward_id
                FROM general_forward_view
                GROUP BY forward_ticket_id
            ) last_forward
                ON last_forward.forward_ticket_id = g.forward_ticket_id
                AND last_forward.max_forward_id = g.forward_id
            WHERE g.forward_receiver_id = ?
        ";

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            $stmt->bind_param('i', $forward_receiver_id);
            $stmt->execute();

            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $ticket_ids[] = (int) $row['forward_ticket_id'];
            }

            if ($result) {
                $result->free();
            }

            $stmt->close();

            return array_values(array_unique($ticket_ids));
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve forward ticket IDs: " . $e->getMessage());
        }
    }

    public function getLastForwardTicketIdsByAdmin($forward_sender_id)
    {
        $ticket_ids = [];
        try {
            // SQL query to get the latest forward_ticket_id for each forward_ticket_id
            $sql = "SELECT forward_ticket_id 
                FROM general_forward_view 
                WHERE forward_id IN (
                    SELECT MAX(forward_id)
                    FROM general_forward_view
                    GROUP BY forward_ticket_id
                ) AND forward_sender_id = ?";

            // Prepare parameters for binding
            $params = [$forward_sender_id];

            // Prepare and execute the SQL statement
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }

            // Bind parameters
            $stmt->bind_param('i', $forward_sender_id); // Assuming forward_receiver_id is an integer

            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch all forward_ticket_ids
            while ($row = $result->fetch_assoc()) {
                $ticket_ids[] = $row['forward_ticket_id'];
            }

            $result->free();
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve forward ticket IDs: " . $e->getMessage());
        }

        return $ticket_ids; // Return the array of ticket IDs
    }


    public function getDistinctForwardTicketIdsBySender($forward_sender_id)
    {
        $ticket_ids = [];

        try {
            $stmt = $this->conn->prepare("CALL GetDistinctForwardTicketIdsBySender(?)");

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            $stmt->bind_param("i", $forward_sender_id);
            $stmt->execute();

            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $ticket_ids[] = (int) $row['forward_ticket_id'];
            }

            $result->free();
            $stmt->close();

            while ($this->conn->more_results() && $this->conn->next_result()) {
                if ($extraResult = $this->conn->store_result()) {
                    $extraResult->free();
                }
            }

            return array_values(array_unique($ticket_ids));
        } catch (\Exception $e) {
            throw new \Exception("Failed to get forward ticket ids: " . $e->getMessage());
        }
    }


    public function getAdminForwardTicketCount($forward_sender_id)
    {
        try {
            $allForwardTicketIds = $this->getDistinctForwardTicketIdsBySender($forward_sender_id);

            if (empty($allForwardTicketIds)) {
                return 0;
            }

            $ticketIds = array_map('intval', $allForwardTicketIds);
            $ticketIds = array_unique($ticketIds);
            $ticketIds = array_filter($ticketIds, function ($id) {
                return $id > 0;
            });

            if (empty($ticketIds)) {
                return 0;
            }

            $idsList = implode(',', $ticketIds);

            $sql = "
            SELECT COUNT(DISTINCT t.ticket_id) AS ticket_count
            FROM {$this->ticketGridView} t
            WHERE t.ticket_id IN ($idsList)
        ";

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            $stmt->execute();

            $result = $stmt->get_result();

            $ticketCount = 0;

            if ($row = $result->fetch_assoc()) {
                $ticketCount = (int) $row['ticket_count'];
            }

            $result->free();
            $stmt->close();

            return $ticketCount;
        } catch (\Exception $e) {
            throw new \Exception("Failed to count tickets: " . $e->getMessage());
        }
    }

    public function getAdminForwardTicketCountDay($forward_sender_id)
    {
        $ticketCount = 0; // متغیر برای شمارش تیکت‌ها
        try {
            // دریافت تمام شناسه‌های distinct تیکت‌های forward برای forward_sender_id مشخص
            $allForwardTicketIds = $this->getDistinctForwardTicketIdsBySender($forward_sender_id);

            // اگر هیچ شناسه‌ای پیدا نشد، شمارش صفر برگردانده می‌شود
            if (empty($allForwardTicketIds)) {
                return $ticketCount;
            }

            // ایجاد کوئری اولیه SQL
            $placeholders = implode(',', array_fill(0, count($allForwardTicketIds), '?'));
            $sql = "SELECT COUNT(DISTINCT t.ticket_id) AS ticket_count
                FROM " . $this->ticketGridView . "  t
                WHERE t.ticket_id IN ($placeholders)";

            $params = $allForwardTicketIds;

            $sql .= " AND t.ticket_creation_date BETWEEN ? AND ?";
            $params[] = $this->start_date;
            $params[] = $this->end_date;

            // آماده‌سازی و اجرای کوئری
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }

            // اتصال پارامترها به کوئری
            $types = str_repeat('i', count($allForwardTicketIds)) . str_repeat('s', count($params) - count($allForwardTicketIds));
            $stmt->bind_param($types, ...$params);

            $stmt->execute();
            $result = $stmt->get_result();

            // دریافت شمارش تیکت‌ها
            if ($row = $result->fetch_assoc()) {
                $ticketCount = (int) $row['ticket_count']; // تبدیل به عدد صحیح
            }

            $result->free();
        } catch (\Exception $e) {
            throw new \Exception("Failed to count tickets: " . $e->getMessage());
        }

        return $ticketCount; // بازگشت شمارش تیکت‌ها
    }




    public function getLastReceiverPersonName($forward_ticket_id)
    {
        $query = "SELECT IFNULL(receiver_name, '-') AS receiver_name
                  FROM general_forward_view 
                  WHERE forward_ticket_id = ? 
                  ORDER BY forward_id DESC
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return '-';
        }

        $stmt->bind_param("i", $forward_ticket_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();
        $stmt->close();

        return $reply ? $reply['receiver_name'] : '-';
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

    public function getConditionNamesByIds($conditionIds)
    {
        $conditionNames = [];

        if (!empty($conditionIds)) {
            $placeholders = implode(',', array_fill(0, count($conditionIds), '?'));
            $sqlQuery = "SELECT id, condition_name FROM conditions WHERE id IN ($placeholders)";

            $stmt = $this->conn->prepare($sqlQuery);
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($this->conn->error));
            }

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





    public function getTotalTicket($isEntry = 0, $conditions_name = '', $referred = 0)
    {

        if ($isEntry == 0 and isset($_SESSION["admin_id"])) {
            return count($this->newTicketAdminForward($_SESSION["admin_id"], $isEntry, $conditions_name, 'a', $referred));
        } else {
            $sqlWhere = '';
            $sqlWhere = $this->generateSqlWhereFromSession();

            if ($conditions_name != '')
                $sqlWhere .= " AND status = '" . $conditions_name . "'";

            $stmt = $this->conn->prepare("SELECT count(*) AS total
            FROM " . $this->ticketsTable . " 
            WHERE  $sqlWhere");
            $stmt->execute();
            $result = $stmt->get_result();
            $reply = $result->fetch_assoc();
            $stmt->close();
            return $reply['total'];
        }
    }

    public function getTotalTicketByDay($nav = 3600)
    {
        // اعتبارسنجی پارامتر nav
        $nav = filter_var($nav, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 3650] // محدودیت منطقی
        ]);

        if ($nav === false) {
            $nav = 3600; // مقدار پیش‌فرض
        }

        // دریافت شرط‌های session
        $sqlWhere = $this->generateSqlWhereFromSession();

        // پارامترها و نوع‌ها
        $params = [];
        $paramTypes = '';

        // اضافه کردن شرط nav با پارامتر ایمن
        if ($nav > 0) {
            $sqlWhere .= " AND t.last_updated_date >= DATE_SUB(NOW(), INTERVAL ? DAY)";
            $params[] = $nav;
            $paramTypes .= 'i';
        }

        // اگر هیچ شرطی وجود نداشت
        if (empty($sqlWhere)) {
            $sqlWhere = "1=1";
        }

        // ساخت کوئری با prepared statement
        $query = "SELECT COUNT(*) AS total FROM " . $this->ticketsTable . " t WHERE " . $sqlWhere;

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            error_log("Query preparation failed: " . $this->conn->error);
            return 0;
        }

        // بایند کردن پارامترها اگر وجود دارند
        if (!empty($params)) {
            $stmt->bind_param($paramTypes, ...$params);
        }

        $stmt->execute();

        if ($stmt->error) {
            error_log("Query execution failed: " . $stmt->error);
            $stmt->close();
            return 0;
        }

        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();
        $stmt->close();

        return $reply['total'] ?? 0;
    }

    function getConditionsStatus($part, $nav, $user_id = "")
    {
        // اعتبارسنجی اولیه
        $part = trim($part);
        $nav = max(1, min(3650, (int) $nav));

        // ساخت شرط‌های پویا
        $joinConditions = [];
        $whereConditions = ["c.condition_part = ?"];
        $params = [$part];
        $paramTypes = "s";

        if (!empty($user_id)) {
            $joinConditions[] = "INNER JOIN forwards f ON f.ticket_id = t.id 
                            AND f.receiver_rbac_id = ? 
                            AND f.receiver_type = 'a'
                            AND f.is_active = 1";
            $params[] = trim($user_id);
            $paramTypes .= "s";
        } else {
            $whereConditions[] = "t.last_updated_date >= DATE_SUB(NOW(), INTERVAL ? DAY)";
            $params[] = $nav;
            $paramTypes .= "i";
        }

        // ساخت کوئری نهایی
        $query = "
        SELECT 
            c.condition_name, 
            c.condition_color, 
            COUNT(DISTINCT t.id) AS num_tickets
        FROM conditions c
        INNER JOIN tickets t ON t.status = c.condition_name
        " . (!empty($joinConditions) ? implode(" ", $joinConditions) : "") . "
        WHERE " . implode(" AND ", $whereConditions) . "
        GROUP BY c.condition_name, c.condition_color
        HAVING num_tickets > 0
        ORDER BY COUNT(t.id) DESC
    ";

        $stmt = $this->conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param($paramTypes, ...$params);
            $stmt->execute();

            if (!$stmt->errno) {
                $result = $stmt->get_result();
                $stmt->close();
                return $result;
            }

            $stmt->close();
        }

        return false;
    }


    public function getRequestsByPersonId($user_type, $array_except = [])
    {
        if ($user_type == 'a') {
            $sqlWhere = "re.receiver_person_id = ? AND re.response_view = 0";
        }

        if (!empty($array_except)) {
            $placeholders = implode(',', array_fill(0, count($array_except), '?'));
            $sqlWhere .= " AND t.status NOT IN ($placeholders)";
        }

        $stmt = $this->conn->prepare("SELECT re.*, t.ticket_title, t.priority , t.ticket_number 
                                  FROM requests AS re
                                  LEFT JOIN tickets AS t ON t.id = re.section_element_id
                                  WHERE " . $sqlWhere . " ORDER BY t.ticket_number DESC");

        $params = [];

        if ($user_type == 'a') {
            $adminId = $_SESSION["admin_id"];
            $params[] = $adminId;
        }

        if (!empty($array_except)) {
            $params = array_merge($params, $array_except);
        }

        $types = '';
        if ($user_type == 'a') {
            $types .= 'i';
        }
        if (!empty($array_except)) {
            $types .= str_repeat('s', count($array_except));
        }


        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function getResponseByPersonId($user_type, $array_except = [])
    {
        if ($user_type == 'a') {
            $sqlWhere = "re.sender_person_id = ? AND re.request_view = 0 AND re.response != ''";
        }

        if (!empty($array_except)) {
            $placeholders = implode(',', array_fill(0, count($array_except), '?'));
            $sqlWhere .= " AND t.status NOT IN ($placeholders)";
        }

        $stmt = $this->conn->prepare("SELECT re.*, t.ticket_title, t.priority , t.ticket_number 
                                      FROM requests AS re
                                      LEFT JOIN tickets AS t ON t.id = re.section_element_id
                                      WHERE " . $sqlWhere . " ORDER BY t.ticket_number DESC");

        $params = [];
        if ($user_type == 'a') {
            $params[] = $_SESSION["admin_id"]; // اضافه کردن admin_id به پارامترها
        }
        if (!empty($array_except)) {
            $params = array_merge($params, $array_except); // ادغام پارامترهای دیگر
        }

        // تعیین نوع پارامترها
        $types = '';
        if ($user_type == 'a') {
            $types .= 'i'; // admin_id به عنوان integer
        }
        if (!empty($array_except)) {
            $types .= str_repeat('s', count($array_except)); // array_except به عنوان string
        }

        // چک کردن تعداد پارامترها و نوع آنها
        if (count($params) !== strlen($types)) {
            throw new \Exception('Parameter count does not match type definition.');
        }

        $stmt->bind_param($types, ...$params); // اتصال پارامترها به بیان

        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function getKaban($person_id, $user_type, $board_tag = '')
    {
        if ($user_type == 'a') {
            $sqlWhere = "kb.admin_id = ?";
        }
        if ($user_type == 'u') {
            $sqlWhere = "kb.user_id = ?";
        }


        if ($board_tag != '') {
            $sqlWhere .= " AND kb.board_tag = '" . $board_tag . "'";
        }

        $stmt = $this->conn->prepare("SELECT kb.*,t.ticket_title , t.priority,t.status,t.id as ticket_id
                FROM kanban_board AS kb   
                LEFT JOIN tickets AS t ON t.id = kb.part_id           
                WHERE " . $sqlWhere);


        if ($user_type == 'a') {
            $stmt->bind_param("i", $_SESSION["admin_id"]);
        }
        if ($user_type == 'u') {
            $stmt->bind_param("i", $_SESSION["user_id"]);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
    public function newTicketAdminForward($admin_id, $is_entry = 0, $condition_name = '', $receiver_type = "a", $referred = 0)
    {
        $sqlQuery = "SELECT DISTINCT ticket_id, ticket_title, ticket_number,priority , status, creation_date,
                        type_name, name, unit_name, type_group, company_name
                 FROM ticket_admin_view";

        $whereClauses = [];
        $hasWhereClause = false; // Flag to track if we have a WHERE clause

        $companyConditions = $this->getCookieConditions('company', 'company_id');
        if (!empty($companyConditions)) {
            $whereClauses[] = "company_id IN (" . implode(',', array_map('intval', $companyConditions)) . ")";
            $hasWhereClause = true;
        }

        $unitConditions = $this->getCookieConditions('unit', 'unit_id');
        if (!empty($unitConditions)) {
            $whereClauses[] = "unit_id IN (" . implode(',', array_map('intval', $unitConditions)) . ")";
            $hasWhereClause = true;
        }

        $typeConditions = $this->getCookieConditions('type', 'type_id');
        if (!empty($typeConditions)) {
            $whereClauses[] = "type_id IN (" . implode(',', array_map('intval', $typeConditions)) . ")";
            $hasWhereClause = true;
        }

        $statusConditions = $this->getConditionNamesByIds($this->getCookieConditions('condition', 'status'));
        if (!empty($statusConditions)) {
            $whereClauses[] = "status IN ('" . implode("','", array_map('addslashes', $statusConditions)) . "')";
            $hasWhereClause = true;
        }

        // Only add WHERE if there are any conditions
        if (!empty($whereClauses)) {
            $sqlQuery .= " WHERE " . implode(' AND ', $whereClauses);
        }

        // Add receiver conditions
        if ($is_entry == '' || $is_entry != 1) {
            $sqlQuery .= ($hasWhereClause ? " AND " : " WHERE ") . "(receiver_person_id = ? AND receiver_type = ?)";
        }

        // Add condition_name filter if provided
        if (!empty($condition_name)) {
            $sqlQuery .= ($hasWhereClause || $is_entry == '' || $is_entry != 1 ? " AND " : " WHERE ") . "status = ?";
        }

        $sqlQuery .= " ORDER BY ticket_id DESC";

        $stmt = $this->conn->prepare($sqlQuery);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }

        // Binding parameters based on conditions
        if ($is_entry == '' || $is_entry != 1) {
            if (!empty($condition_name)) {
                $stmt->bind_param("iss", $admin_id, $receiver_type, $condition_name);
            } else {
                $stmt->bind_param("is", $admin_id, $receiver_type);
            }
        } else {
            if (!empty($condition_name)) {
                $stmt->bind_param("s", $condition_name);
            }
        }

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result === false) {
            die('Execute failed: ' . htmlspecialchars($stmt->error));
        }

        $stmt->close();



        $allResult = array();

        while ($ticket_data = $result->fetch_assoc()) {

            if (($is_entry == '' || $is_entry != 1)) {

                if ($this->getLastReceiverPersonId($ticket_data['ticket_id'], 'tickets') != $admin_id) {
                    continue;
                } else {
                    $allResult[] = $ticket_data;
                }
            } else {
                $allResult[] = $ticket_data;
            }

        }

        return $allResult;
    }

    public function getUserIdFromSession()
    {
        return $_SESSION["user_id"] ?? $_SESSION["admin_id"] ?? 0;
    }

    function generateSqlWhereFromSession()
    {
        if (isset($_SESSION["user_id"])) {
            $user_id = $_SESSION["user_id"];
            return "user_id = " . $user_id;
        } elseif (isset($_SESSION["admin_id"])) {
            return " 1 ";
        } else {
            return null;
        }
    }

    public function getRequestsById($section_element_id, $section_part_name, $request)
    {

        $sqlWhere = "re.section_element_id = ? AND re.section_part_name = ? AND re.request = ?";

        $stmt = $this->conn->prepare("SELECT re.*,a.name 
                FROM requests AS re   
                LEFT JOIN admins AS a ON a.id = re.receiver_person_id           
                WHERE " . $sqlWhere . " ORDER BY re.id DESC LIMIT 1");

        $stmt->bind_param("iss", $section_element_id, $section_part_name, $request);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
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

    public function checkStatusTable($part_id, $part_name, $type = 'a')
    {
        // بررسی موجود بودن رکورد در جدول status
        $sql = "SELECT id FROM $this->statusTable WHERE part_id = ? AND part_name = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $part_id, $part_name);
        $stmt->execute();
        $result = $stmt->get_result();

        // اگر رکوردی وجود نداشت، یک رکورد جدید درج می‌شود
        if ($result->num_rows < 1) {
            $columns = ($type == 'a') ? "`admin_id`, `rbac_id`" : "`user_id`, `rbac_id`";
            $values = "?, ?";
            $sqlQuery = "INSERT INTO " . $this->statusTable . " (`part_id`, `part_name`, $columns) VALUES (?, ?, $values)";

            $stmt = $this->conn->prepare($sqlQuery);

            if ($stmt === false) {
                return false;
            }

            $id = ($type == 'a') ? $_SESSION["admin_id"] : $_SESSION["user_id"];
            $stmt->bind_param("isii", $part_id, $part_name, $id, $_SESSION["rbac_id"]);
            $executionResult = $stmt->execute();
            $stmt->close();
            return $executionResult;
        }

        // در صورت موجود بودن رکورد، false برگردانده می‌شود
        $stmt->close();
        return false;
    }

    public function checkStatusTableStatusArray($part_id, $part_name, $array_status)
    {
        $array_status = array_map('strtolower', $array_status);

        $placeholders = implode(',', array_fill(0, count($array_status), '?'));

        $sql = "SELECT status_name 
            FROM $this->statusTable 
            WHERE part_id = ? AND part_name = ? AND LOWER(status_name) IN ($placeholders)";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->conn->error));
        }

        $types = str_repeat('s', count($array_status));
        $types = "is" . $types;

        $params = array_merge([$part_id, $part_name], $array_status);
        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->num_rows > 0;
    }


    public function getTimeDifference($part_id, $part_name)
    {
        // SQL query to fetch creation dates from the view
        $sql = "SELECT creation_date 
                FROM status_forwards_detailed_view 
                WHERE section_element_id = ? AND section_part_name = ? 
                ORDER BY creation_date ASC";

        // Prepare and execute the SQL statement
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $part_id, $part_name);
        $stmt->execute();

        $result = $stmt->get_result();

        // Fetch all creation dates
        $dates = [];
        while ($row = $result->fetch_assoc()) {
            $dates[] = $row['creation_date'];
        }

        $stmt->close();

        // If there are fewer than two dates, we cannot calculate the difference
        if (count($dates) < 2) {
            return [
                'hours' => 0,
                'status' => 'error',
                'message' => 'Not enough data to calculate time difference'
            ];
        }

        // Convert the dates to DateTime objects
        $firstDate = new \DateTime($dates[0]);
        $lastDate = new \DateTime($dates[count($dates) - 1]);

        // Calculate the difference
        $interval = $firstDate->diff($lastDate);

        // Calculate the total difference in hours
        $hours = ($interval->days * 24) + $interval->h;
        $hours += $interval->i / 60; // Include minutes converted to hours

        // If hours is empty or zero, return 0
        if (empty($hours) || $hours == 0) {
            return [
                'status' => 'success',
                'hours' => 0
            ];
        }

        return [
            'status' => 'success',
            'hours' => $hours
        ];
    }


    public function getTicketById($id)
    {
        if (!$this->conn) {
            return false;
        }

        $sql = "
            SELECT 
                tickets.*, 
                ty.type_name, 
                ty.type_group,
                COALESCE(ud.user_id, ad.admin_id) AS user_id, 
                COALESCE(ud.company_id, ad.company_id) AS company_id,
                COALESCE(ud.company_name, ad.company_name) AS company_name
            FROM 
                idesk.tickets AS tickets
            LEFT JOIN 
                idesk.types ty ON tickets.type_id = ty.id
            LEFT JOIN 
                idesk.users_details_view ud ON ud.user_id = tickets.user_id
            LEFT JOIN 
                idesk.admins_details_view ad ON ad.admin_id = tickets.admin_id
               
            WHERE 
                tickets.id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }

        $result = $stmt->get_result();
        if (!$result || $result->num_rows === 0) {
            $stmt->close();
            return null;
        }

        $ticket = $result->fetch_assoc();
        $stmt->close();

        $this->checkStatusTable($id, $this->ticketsTable);
        return $ticket;
    }

    public function checkTicketAccess($ticket_id, $admin_id, $is_entry = 0, $receiver_type = "a")
    {
        $ticket = $this->getTicketById($ticket_id);

        if ($ticket === false) {
            return ['error' => 'Ticket not found'];
        }

        $adminTickets = $this->getUniqueSectionElementIds($admin_id);

        if (in_array($ticket_id, $adminTickets)) {
            return $ticket;
        } else {
            return 0;
        }


    }

    public function getTicketIdByNumber($ticket_number)
    {
        $sql = "SELECT id FROM $this->ticketsTable WHERE ticket_number = ?";

        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("s", $ticket_number);

            $stmt->execute();

            $result = $stmt->get_result();

            $reply = $result->fetch_assoc();

            $stmt->close();

            return $reply['id'] ?? null;
        } else {
            return null;
        }
    }


    public function getUserSystemInfo()
    {
        $internet_ip = $_SERVER['REMOTE_ADDR'];
        $local_ip = gethostbyname(gethostname());
        $system_info = php_uname();

        $user_info = [
            'local_ip' => $local_ip,
            'system_info' => $system_info,
            'internet_ip' => $internet_ip,
        ];

        return $user_info;
    }

    public function insertViewBy($element_id, $person_type = 1)
    {
        $systemInfo = $this->getUserSystemInfo();
        $person_id = $this->getUserIdFromSession();
        $part_name = $this->ticketsTable;



        $checkQuery = "SELECT 1 FROM " . $this->viewTable . " WHERE person_id = ? AND person_type = ? AND element_id = ? AND part_name = ? LIMIT 1";
        $stmtCheck = $this->conn->prepare($checkQuery);

        if ($stmtCheck === false) {
            return false;
        }



        $stmtCheck->bind_param("iiis", $person_id, $person_type, $element_id, $part_name);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->close();
            return false;
        }

        $stmtCheck->close();

        $local_ip = htmlspecialchars(strip_tags($systemInfo['local_ip']));
        $internet_ip = htmlspecialchars(strip_tags($systemInfo['internet_ip']));
        $system_info = htmlspecialchars(strip_tags($systemInfo['system_info']));

        $sqlQuery = "INSERT INTO " . $this->viewTable . " (`person_id`, `person_type`, `element_id`, `part_name`, `local_ip`,`internet_ip`,`system_info`) VALUES (?, ?, ?, ?,?,?,?)";
        $stmt = $this->conn->prepare($sqlQuery);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("iiissss", $person_id, $person_type, $element_id, $part_name, $local_ip, $internet_ip, $system_info);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function changeStatusIsEntry($ticket_id, $change_status)
    {
        try {
            $admin_id = $this->getUserIdFromSession();

            $this->conn->begin_transaction();

            $sqlUpdate = "UPDATE $this->ticketsTable SET status = ? WHERE id = ? AND status = 'condition_pending'";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            if ($stmtUpdate === false) {
                throw new \Exception(_lang['query_preparation_failed']);
            }
            $stmtUpdate->bind_param("si", $change_status, $ticket_id);

            if ($stmtUpdate->execute() && $stmtUpdate->affected_rows > 0) {
                $stmtUpdate->close();

                $sqlInsert = "INSERT INTO $this->statusTable (`part_id`, `part_name`, `status_name`, `admin_id`, `rbac_id`) VALUES (?, ?, ?, ?, ?)";
                $stmtInsert = $this->conn->prepare($sqlInsert);

                $stmtInsert->bind_param("issii", $ticket_id, $this->ticketsTable, $change_status, $admin_id, $_SESSION["rbac_id"]);
                $stmtInsert->execute();
                $stmtInsert->close();
            } else {
                $stmtUpdate->close();
            }

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }


    public function changeStatusIsNotEntry($ticket_id, $change_status)
    {
        try {
            $admin_id = $this->getUserIdFromSession();

            $this->conn->begin_transaction();

            $sqlUpdate = "UPDATE $this->ticketsTable SET status = ? WHERE id = ? AND (status = 'condition_under_review' OR  status = 'Condition_reject_test')";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            if ($stmtUpdate === false) {
                throw new \Exception(_lang['query_preparation_failed']);
            }
            $stmtUpdate->bind_param("si", $change_status, $ticket_id);

            if ($stmtUpdate->execute() && $stmtUpdate->affected_rows > 0) {
                $stmtUpdate->close();

                $sqlInsert = "INSERT INTO $this->statusTable (`part_id`, `part_name`, `status_name`, `admin_id`, `rbac_id`) VALUES (?, ?, ?, ?, ?)";
                $stmtInsert = $this->conn->prepare($sqlInsert);

                $stmtInsert->bind_param("issii", $ticket_id, $this->ticketsTable, $change_status, $admin_id, $_SESSION["rbac_id"]);
                $stmtInsert->execute();
                $stmtInsert->close();
            } else {
                $stmtUpdate->close();
            }

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    public function getViewBy($element_id, $person_type = 1)
    {
        $part_name = $this->ticketsTable;

        $checkQuery = "
        SELECT v.*, a.name AS person_name 
        FROM " . $this->viewTable . " v
        LEFT JOIN " . $this->adminTable . " a ON v.person_id = a.id
        WHERE v.person_type = ? AND v.element_id = ? AND v.part_name = ? 
        ORDER BY v.creation_date";

        $stmtCheck = $this->conn->prepare($checkQuery);

        if ($stmtCheck === false) {
            return false;
        }

        $stmtCheck->bind_param("iis", $person_type, $element_id, $part_name);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        $stmtCheck->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function getTotalViewsByPerson($person_type = 1)
    {
        $viewTable = $this->viewTable;
        $adminTable = $this->adminTable;

        $query = "
            SELECT 
                v.person_id, 
                a.name AS person_name, 
                COUNT(v.element_id) AS total_views
            FROM 
                $viewTable v
            INNER JOIN 
                $adminTable a ON v.person_id = a.id
            WHERE 
                v.person_type = ?
            GROUP BY 
                v.person_id, a.name
            HAVING 
                total_views > 0
            ORDER BY 
                total_views DESC";

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("i", $person_type);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    public function getCountForward($id, $part)
    {
        $query = " SELECT Count(id) as total
        FROM " . $this->forwardsTable . " f
        WHERE f.section_element_id = ? AND f.section_part_name = ?";

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


    public function getLastReceiverPersonId($id, $part)
    {
        $query = "SELECT f.receiver_person_id  AS id
              FROM " . $this->forwardsTable . " f
              LEFT JOIN admins a ON f.receiver_person_id = a.id
              WHERE f.section_element_id = ? AND f.section_part_name = ?
              ORDER BY f.id DESC
              LIMIT 1";

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return '-';
        }

        $stmt->bind_param("is", $id, $part);
        $stmt->execute();
        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();
        $stmt->close();

        return $reply ? $reply['id'] : '-';
    }

    public function getCountManHour($id, $part)
    {
        $query = " SELECT Count(id) as total
        FROM " . $this->manHourTable . " m
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
        return $reply['total'] ? $reply['total'] : 0;
    }


    function getTimeDifferenceArray($start_time, $end_time)
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

    public function getStatusAndForwards($part_id, $part_name)
    {
        // SQL query to fetch data from the view
        $sql = "SELECT * FROM status_forwards_detailed_view 
                WHERE section_element_id = ? AND section_part_name = ? 
                ORDER BY creation_date ASC";

        // Prepare and execute the SQL statement
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $part_id, $part_name);
        $stmt->execute();

        $result = $stmt->get_result();

        // Initialize an array to store the formatted results
        $formattedResults = [];
        $counter = 0;

        // Loop through the results and assign 'right' or 'left' alternately
        while ($row = $result->fetch_assoc()) {
            $row['position'] = ($counter % 2 === 0) ? 'right' : 'left';
            $formattedResults[] = $row;
            $counter++;
        }

        $stmt->close();

        // Return the formatted results
        return $formattedResults;
    }

    public function getStatusHistory($part_id, $part_name)
    {
        $sql = "SELECT * FROM status_history_view 
                WHERE section_element_id = ? AND section_part_name = ? 
                ORDER BY creation_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $part_id, $part_name);
        $stmt->execute();

        $result = $stmt->get_result();
        $history = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $history;
    }

    public function getTicketBeforStatusById($part_id)
    {
        $sql = "SELECT status_name, person_name 
                FROM all_tickets_before_status_forwards 
                WHERE person_name is not null AND status_name is not null AND section_element_id = ?  ";

        // Prepare and execute the SQL statement
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param("i", $part_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc(); // Fetch the result as an associative array

        $stmt->close();

        // If no result is found, return null
        return $row ? $row : null;
    }

    function getSchedulingInfo($section_part_name, $section_element_id)
    {
        $stmt = $this->conn->prepare("
        SELECT sp.*, fw.* , admins.name as admin_name
        FROM $section_part_name sp
        LEFT JOIN $this->scheduleTable fw ON sp.id = fw.section_element_id
        LEFT JOIN rbac ON fw.rbac_id = rbac.id
        LEFT JOIN admins ON fw.admin_id = admins.id
        WHERE sp.id = ?
        AND (fw.id IS NULL OR fw.section_part_name = ?)
    ");

        $stmt->bind_param("is", $section_element_id, $section_part_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function getFileingInfo($section_part_name, $section_element_id)
    {
        $stmt = $this->conn->prepare("
            SELECT sp.*, fw.*, 
            CASE 
                WHEN fw.user_id != 0 THEN users.name 
                WHEN fw.admin_id != 0 THEN admins.name 
                ELSE NULL 
            END AS creator_name
            FROM $section_part_name sp
            LEFT JOIN $this->fileManageTable fw ON sp.id = fw.part_id 
            LEFT JOIN users ON fw.user_id = users.id
            LEFT JOIN admins ON fw.admin_id = admins.id
            WHERE sp.id = ?
            AND fw.part_name = ?
        ");

        $stmt->bind_param("is", $section_element_id, $section_part_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    function getLastFinanceStatus($part_id, $part_name)
    {

        $sql = "SELECT status_name FROM status_history_view as shv join conditions as con ON shv.status_name = con.condition_name
                WHERE shv.section_element_id = ? AND shv.section_part_name = ?  AND con.finance = 1
                ORDER BY shv.creation_date DESC limit 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $part_id, $part_name);
        $stmt->execute();

        $result = $stmt->get_result();
        $history = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $history;

    }

    function getLastNonFinanceStatus($part_id, $part_name)
    {

        $sql = "SELECT status_name FROM status_history_view as shv join conditions as con ON shv.status_name = con.condition_name
                WHERE shv.section_element_id = ? AND shv.section_part_name = ?  AND con.finance != 1
                ORDER BY shv.creation_date DESC limit 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $part_id, $part_name);
        $stmt->execute();

        $result = $stmt->get_result();
        $history = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $history;

    }


    public function getAllKabanTag()
    {


        $stmt = $this->conn->prepare("SELECT board_tag,id
                FROM kanban_board_tags AS kbt            
                WHERE admin_id = ? ORDER BY id DESC ");
        $stmt->bind_param("i", $_SESSION["admin_id"]);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    public function getCountKabanTag()
    {


        $stmt = $this->conn->prepare("SELECT count(id) as count_id
                FROM kanban_board_tags            
                WHERE admin_id = ?  ");
        $stmt->bind_param("i", $_SESSION["admin_id"]);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllKabanByTagId($tag_id)
    {
        $stmt = $this->conn->prepare("SELECT kb.*,t.ticket_title , t.ticket_number,t.priority,t.status,t.id as ticket_id ,ty.type_group 
                FROM kanban_board AS kb   
                LEFT JOIN tickets AS t ON t.id = kb.part_id   
                LEFT JOIN types   AS ty ON t.type_id = ty.id      
                WHERE kb.board_tag_id = ?  ");

        $stmt->bind_param("i", $tag_id);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
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
        $adminId = (int) $_SESSION['admin_id'];

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
          AND mt.admin_id = ?
    ";

        $stmt = $this->conn->prepare($sql);

        $types = str_repeat('i', count($ticketIds)) . 'i';
        $params = [...$ticketIds, $adminId];

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
        $adminId = (int) $_SESSION['admin_id'];

        $sql = "
        SELECT
            mt.id,
            mt.marking_tag
        FROM marking_tags mt
        INNER JOIN marking m
            ON m.marking_tag_id = mt.id
        WHERE m.part_id = ?
          AND mt.admin_id = ?
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $ticketId, $adminId);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc() ?: null;
    }


}







