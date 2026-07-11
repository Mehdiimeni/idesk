<?php
namespace iweb\model;
class NotificationModel
{
    private $conn;
    private $messageTable = 'messages';
    private $commentTable = 'comments';
    private $forwardsTable = 'forwards';
    private $ticketsTable = 'tickets';
    private $adminsTable = 'admins';
    private $statusTable = 'status';

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

    public function setViewComments($Id)
    {
        $userId = $this->getUserIdFromSession();
        try {
            $query = 'SELECT user_id, admin_id FROM ' . $this->commentTable . ' WHERE id = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $Id);
            $stmt->execute();
            $result = $stmt->get_result();
            $message = $result->fetch_assoc();

            if ($message) {
                if ($message['user_id'] == $userId) {
                    $updateQuery = 'UPDATE ' . $this->commentTable . ' SET view_from = 1 WHERE id = ?';
                } else {
                    $updateQuery = 'UPDATE ' . $this->commentTable . ' SET view_to = 1 WHERE id = ?';
                }

                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bind_param('i', $Id);
                $updateStmt->execute();

                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            echo 'Query Error: ' . $e->getMessage();
            return null;
        }
    }

    public function setViewStatusUser($Id)
    {
        $userId = $this->getUserIdFromSession();
        try {
            $query = 'SELECT user_id, admin_id FROM ' . $this->statusTable . ' WHERE id = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $Id);
            $stmt->execute();
            $result = $stmt->get_result();
            $message = $result->fetch_assoc();

            if ($message) {
                if ($message['user_id'] == $userId) {
                    $updateQuery = 'UPDATE ' . $this->statusTable . ' SET view_from = 1 WHERE id = ?';
                } else {
                    $updateQuery = 'UPDATE ' . $this->statusTable . ' SET view_to = 1 WHERE id = ?';
                }

                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bind_param('i', $Id);
                $updateStmt->execute();

                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            echo 'Query Error: ' . $e->getMessage();
            return null;
        }
    }


    public function findTableAllRowWithColumn($tableName, $columnValue, $tableColumn)
    {
        try {
            $query = 'SELECT * FROM ' . $tableName . ' WHERE ' . $tableColumn . ' = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $columnValue);
            $stmt->execute();

            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\PDOException $e) {
            echo 'Query Error: ' . $e->getMessage();
            return null;
        }
    }

    public function findTicketIdByUserId($userId)
    {
        return $this->findTableAllRowWithColumn($this->ticketsTable, $userId, 'user_id');
    }

    public function noteCommentTicket($view_to = 0, $local = 1, $view_from = 0, $limit = 10)
    {
        $userId = $this->getUserIdFromSession();
        $limit = (int) $limit;

        if ($limit <= 0) {
            $limit = 10;
        }

        try {
            $allTicketUserCreator = $this->findTicketIdByUserId($userId);

            $query = "
            SELECT *
            FROM {$this->commentTable}
            WHERE part_name = ?
        ";

            $params = [$this->ticketsTable];
            $types = "s";

            if (!empty($allTicketUserCreator)) {
                $ticketIds = array_map('intval', array_column($allTicketUserCreator, 'id'));
                $ticketIds = array_unique(array_filter($ticketIds));

                if (!empty($ticketIds)) {
                    $query .= " AND part_id IN (" . implode(',', $ticketIds) . ")";
                }
            }

            if ($userId) {
                $query .= " AND IF(user_id IS NOT NULL, user_id != ?, 1)";
                $params[] = $userId;
                $types .= "i";
            }

            if ($view_to != 2) {
                $query .= " AND view_to = ?";
                $params[] = $view_to;
                $types .= "i";
            }

            if ($local == 0) {
                $query .= " AND local = 0";
            }

            $query .= " ORDER BY last_updated_date DESC LIMIT {$limit}";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception('Query preparation failed: ' . $this->conn->error);
            }

            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            return $stmt->get_result();
        } catch (\Exception $e) {
            throw new \Exception("Failed to get ticket comments note: " . $e->getMessage());
        }
    }

    public function noteNewChatMessages($member_id, $member_type, $limit = 10)
    {
        try {
            $member_id = (int) $member_id;
            $limit = (int) $limit;

            if ($limit <= 0) {
                $limit = 10;
            }

            $query = "
            SELECT cb.*
            FROM chat_box cb
            INNER JOIN (
                SELECT sender_id, MAX(id) AS max_id
                FROM chat_box
                WHERE receiver_id = ?
                  AND receiver_type = ?
                  AND view_side = 0
                GROUP BY sender_id
            ) last_msg
                ON last_msg.sender_id = cb.sender_id
                AND last_msg.max_id = cb.id
            ORDER BY cb.id DESC
            LIMIT {$limit}
        ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception('Failed to prepare the query: ' . $this->conn->error);
            }

            $stmt->bind_param('is', $member_id, $member_type);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result === false) {
                throw new \Exception('Error in fetching result: ' . $this->conn->error);
            }

            $messages = $result->fetch_all(MYSQLI_ASSOC);

            $result->free();
            $stmt->close();

            return $messages ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }
    
}

?>