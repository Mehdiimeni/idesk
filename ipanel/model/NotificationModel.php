<?php
namespace ipanel\model;

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

    public function getAdminIdFromSession()
    {
        return $_SESSION["admin_id"] ?? 0;
    }

    public function setViewMessage($Id)
    {
        $adminId = $this->getAdminIdFromSession();
        try {
            $query = 'SELECT msg_from, msg_to FROM ' . $this->messageTable . ' WHERE id = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $Id);
            $stmt->execute();
            $result = $stmt->get_result();
            $message = $result->fetch_assoc();

            if ($message) {
                if ($message['msg_from'] == $adminId) {
                    $updateQuery = 'UPDATE ' . $this->messageTable . ' SET view_from = 1 WHERE id = ?';
                } elseif ($message['msg_to'] == $adminId) {
                    $updateQuery = 'UPDATE ' . $this->messageTable . ' SET view_to = 1 WHERE id = ?';
                } else {
                    return false;
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


    public function setViewForwards($Id)
    {
        $adminId = $this->getAdminIdFromSession();
        try {
            $query = 'SELECT sender_person_id, receiver_person_id FROM ' . $this->forwardsTable . ' WHERE id = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $Id);
            $stmt->execute();
            $result = $stmt->get_result();
            $message = $result->fetch_assoc();

            if ($message) {
                if ($message['sender_person_id'] == $adminId) {
                    $updateQuery = 'UPDATE ' . $this->forwardsTable . ' SET view_from = 1 WHERE id = ?';
                } elseif ($message['receiver_person_id'] == $adminId) {
                    $updateQuery = 'UPDATE ' . $this->forwardsTable . ' SET view_to = 1 WHERE id = ?';
                } else {
                    return false;
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


    public function setViewComments($Id)
    {
        $adminId = $this->getAdminIdFromSession();
        try {
            $query = 'SELECT user_id, admin_id FROM ' . $this->commentTable . ' WHERE id = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $Id);
            $stmt->execute();
            $result = $stmt->get_result();
            $message = $result->fetch_assoc();

            if ($message) {
                if ($message['user_id'] == $adminId || $message['admin_id'] == $adminId) {
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


    public function findTicketIdByAdminId($adminId)
    {
        return $this->findTableAllRowWithColumn($this->ticketsTable, $adminId, 'user_id');
    }

    public function noteCommentTicket($view_to = 0, $local = 1, $view_from = 0, $limit = 100)
    {
        $adminId = $this->getAdminIdFromSession();
        $allTicketUserCreator = $this->findTicketIdByAdminId($this->getAdminIdFromSession());
        $query = 'SELECT * FROM ' . $this->commentTable . ' WHERE part_name = ?';
        $params = [$this->ticketsTable];

        if (count($allTicketUserCreator) > 0) {
            $ticketIds = implode(',', array_map('intval', array_column($allTicketUserCreator, 'id')));
            $query .= ' AND part_id IN (' . $ticketIds . ')';
        }


        if ($adminId) {
            $query .= ' AND IF(admin_id IS NOT NULL, admin_id != ?, 1) ';
            $params[] = $adminId;
        }

        if ($view_to != 2) {
            $query .= ' AND view_to = ?';
            $params[] = $view_to;
        }

        if ($local == 0) {
            $query .= ' AND local = 0';
        }

        $query .= ' ORDER BY last_updated_date DESC Limit '. $limit;

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            throw new \Exception('Query preparation failed: ' . $this->conn->error);
        }

        $stmt->bind_param(str_repeat('i', count($params)), ...$params);

        $stmt->execute();

        return $stmt->get_result();
    }




    public function noteCalendar($view_to = 0)
    {

        $today = date('Y-m-d');
        $query = "SELECT * FROM calendar WHERE DATE(start_date) = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $today);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result;
        } else {
            return array('status' => 'error', 'message' => 'Error executing query: ' . $stmt->error);
        }
    }



    public function noteForwarderTicket($view_to = 0 , $limit = 100)
    {
        $adminId = $this->getAdminIdFromSession();

        try {
            $query = 'SELECT f.*, a.name AS sender_name 
                      FROM ' . $this->forwardsTable . ' f
                      LEFT JOIN ' . $this->adminsTable . ' a ON f.sender_person_id = a.id
                      WHERE f.receiver_person_id = ? 
                      AND f.section_part_name = ? ';

            if ($view_to != 2) {
                $query .= ' AND f.view_to = ?';
            }

            $query .= ' ORDER BY f.creation_date DESC LIMIT '. $limit;

            $stmt = $this->conn->prepare($query);
            if ($stmt === false) {
                throw new \Exception('Failed to prepare the query: ' . $this->conn->error);
            }

            $part_name = $this->ticketsTable;

            if ($view_to != 2) {
                $stmt->bind_param('iis', $adminId, $part_name, $view_to);
            } else {

                $stmt->bind_param('is', $adminId, $part_name);
            }

            $stmt->execute();

            return $stmt->get_result();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function noteMessages($important = 0, $sent = 0, $view_to = 0, $view_from = 0)
    {
        $adminId = $this->getAdminIdFromSession();

        try {
            // ساختار اصلی کوئری
            $query = 'SELECT m.*, a.name AS sender_name 
                      FROM ' . $this->messageTable . ' m
                      LEFT JOIN ' . $this->adminsTable . ' a ON ';
            $query .= ($sent == 1) ? 'm.msg_to = a.id WHERE ' : 'm.msg_from = a.id WHERE ';

            // ساختن شروط کوئری
            $conditions = [];
            $params = [];
            $types = '';

            // شرط پیام‌های مهم
            if ($important == 1) {
                $conditions[] = 'm.important = ?';
                $params[] = $important;
                $types .= 'i';
            }

            // شرط ارسال شده یا دریافتی
            $field = ($sent == 1) ? 'm.msg_from = ?' : 'm.msg_to = ?';
            $conditions[] = $field;
            $params[] = $adminId;
            $types .= 'i';

            // ترکیب شروط و نهایی‌سازی کوئری
            if (!empty($conditions)) {
                $query .= implode(' AND ', $conditions);
            }

            if ($view_to == 0) {
                $query .= ' AND m.view_to = 0';
            }
            // اضافه کردن مرتب‌سازی نهایی
            $query .= '  ORDER BY m.creation_date DESC';

            // آماده‌سازی کوئری
            $stmt = $this->conn->prepare($query);
            if ($stmt === false) {
                throw new \Exception('Query preparation failed: ' . $this->conn->error);
            }

            // بایند کردن پارامترها
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            // اجرای کوئری
            $stmt->execute();

            // نتیجه را برمی‌گردانیم
            return $stmt->get_result();
        } catch (\Exception $e) {
            // مدیریت خطا و نمایش پیام خطا
            echo 'Query Error: ' . $e->getMessage();
            return [];
        }
    }

    public function noteNewMessages()
    {
        return $this->noteMessages(0, 0, 0);
    }

    public function noteNewImportantMessages()
    {
        return $this->noteMessages(1, 0, 0);
    }

    public function getMessageDetails($id)
    {
        try {
            // Prepare the query to fetch message details along with the sender's name
            $query = 'SELECT m.*, a.name AS sender_name 
                      FROM ' . $this->messageTable . ' m
                      LEFT JOIN ' . $this->adminsTable . ' a ON m.msg_from = a.id
                      WHERE m.id = ?
                      ORDER BY m.creation_date DESC, m.view_to ASC';

            $stmt = $this->conn->prepare($query);
            if ($stmt === false) {
                throw new \Exception('Failed to prepare the query: ' . $this->conn->error);
            }

            $stmt->bind_param('i', $id);

            $stmt->execute();

            $result = $stmt->get_result();
            $message = $result->fetch_assoc();

            $stmt->free_result();
            $stmt->close();

            return $message ?: []; // Return an empty array if no result is found
        } catch (\Exception $e) {
            return [];
        }
    }


    public function noteNewChatMessages($member_id, $member_type)
    {
        try {
            $query = 'SELECT * 
                      FROM chat_box 
                      WHERE receiver_id = ? AND receiver_type = ? AND view_side = 0
                      GROUP BY sender_id 
                      ORDER BY id DESC';

            // Prepare the SQL statement
            $stmt = $this->conn->prepare($query);
            if ($stmt === false) {
                throw new \Exception('Failed to prepare the query: ' . $this->conn->error);
            }

            // Bind the parameters to the query
            $stmt->bind_param('is', $member_id, $member_type);

            // Execute the query
            $stmt->execute();

            // Fetch and return the result
            $result = $stmt->get_result();

            if ($result === false) {
                throw new \Exception('Error in fetching result: ' . $this->conn->error);
            }

            // Fetch data as associative array
            $messages = $result->fetch_all(MYSQLI_ASSOC);

            // Free result and close the statement
            $result->free();
            $stmt->close();

            return $messages ?: []; // Return an empty array if no result is found
        } catch (\Exception $e) {
            return [];
        }
    }


}

?>