<?php // chat model admin

namespace ipanel\model;
class ChatModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function setChatViewSide($receiver_id, $receiver_type, $sender_id, $sender_type)
    {
        $sql = "UPDATE chat_box 
            SET view_side = 1 
            WHERE receiver_id = ? 
              AND receiver_type = ? 
              AND sender_id = ? 
              AND sender_type = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isis", $receiver_id, $receiver_type, $sender_id, $sender_type);

        if ($stmt->execute()) {
            return true; 
        } else {
            return false; 
        }
    }
}