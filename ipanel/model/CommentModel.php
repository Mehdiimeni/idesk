<?php
namespace ipanel\model;

class CommentModel extends \Configuration
{
    private $fileTable = 'file_manage';
    private $conn;
    public $part_name;
    public $part_id;


    public function __construct($db)
    {
        $this->conn = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

    }

public function getCommentPart()
{
    $defaultFileName = $this->getDefaultFileName();
    $defaultFilePath = $this->getDefaultFilePath();

    $sql = "
        SELECT c.*, 
            IFNULL(u.name, a.name) AS name,
            COALESCE(f.file_name, ?) AS file_name,
            COALESCE(f.file_path, ?) AS file_path
        FROM comments c
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN admins a ON c.admin_id = a.id
        LEFT JOIN " . $this->fileTable . " f ON (
            (f.part_name = 'admin_profile' AND f.part_id = a.id) OR
            (f.part_name = 'user_profile' AND f.part_id = u.id)
        )
        WHERE c.parent_id IS NULL 
          AND c.part_name = ?
          AND c.part_id = ?
        GROUP BY c.id
        ORDER BY c.id DESC
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ssss", $defaultFileName, $defaultFilePath, $this->part_name, $this->part_id);
    $stmt->execute();
    return $stmt->get_result();
}



public function getCommentPartByParentId($parent_id)
{
    $defaultFileName = $this->getDefaultFileName();
    $defaultFilePath = $this->getDefaultFilePath();

    $sql = "
        SELECT c.*, 
            IFNULL(u.name, a.name) AS name,
            COALESCE(
                (SELECT fm.file_name FROM " . $this->fileTable . " fm 
                 WHERE (fm.part_name = 'admin_profile' AND fm.part_id = a.id) OR
                       (fm.part_name = 'user_profile' AND fm.part_id = u.id)
                 LIMIT 1),
                ?
            ) AS file_name,
            COALESCE(
                (SELECT fm.file_path FROM " . $this->fileTable . " fm 
                 WHERE (fm.part_name = 'admin_profile' AND fm.part_id = a.id) OR
                       (fm.part_name = 'user_profile' AND fm.part_id = u.id)
                 LIMIT 1),
                ?
            ) AS file_path
        FROM comments c
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN admins a ON c.admin_id = a.id
        WHERE c.parent_id = ? 
          AND c.part_name = ?
          AND c.part_id = ?
        ORDER BY c.id DESC;
    ";

    $stmt = $this->conn->prepare($sql);
    
    // اصلاح مهم: تعداد پارامترها باید 5 تا باشد (sssss)
    $stmt->bind_param(
        "sssss", // 5 پارامتر از نوع string
        $defaultFileName, 
        $defaultFilePath, 
        $parent_id, 
        $this->part_name, 
        $this->part_id
    );
    
    $stmt->execute();
    return $stmt->get_result();
}

    public function getCountCommentByElementId($part_id, $part_name)
    {
        $sqlQuery = "SELECT COUNT(*) AS total FROM comments WHERE part_id = ? AND part_name = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("is", $part_id, $part_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $row = $result->fetch_assoc();
        return $row['total'];  
    }


}
?>