<?php
namespace iweb\model;

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
        try {
            $defaultFileName = $this->getDefaultFileName();
            $defaultFilePath = $this->getDefaultFilePath();

            $sql = "
            SELECT 
                c.*, 
                IFNULL(u.name, a.name) AS name,
                COALESCE(f.file_name, ?) AS file_name,
                COALESCE(f.file_path, ?) AS file_path
            FROM comments c
            LEFT JOIN users u 
                ON c.user_id = u.id
            LEFT JOIN admins a 
                ON c.admin_id = a.id
            LEFT JOIN {$this->fileTable} f 
                ON (
                    (f.part_name = 'admin_profile' AND f.part_id = a.id)
                    OR
                    (f.part_name = 'user_profile' AND f.part_id = u.id)
                )
            WHERE c.parent_id IS NULL
              AND c.part_name = ?
              AND c.part_id = ?
            GROUP BY c.id
            ORDER BY c.id DESC
        ";

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            $stmt->bind_param(
                "sssi",
                $defaultFileName,
                $defaultFilePath,
                $this->part_name,
                $this->part_id
            );

            $stmt->execute();

            return $stmt->get_result();
        } catch (\Exception $e) {
            throw new \Exception("Failed to get comment part: " . $e->getMessage());
        }
    }



    public function getCommentPartByParentId($parent_id)
    {
        try {
            $defaultFileName = $this->getDefaultFileName();
            $defaultFilePath = $this->getDefaultFilePath();

            $sql = "
            SELECT 
                c.*, 
                IFNULL(u.name, a.name) AS name,
                COALESCE(f.file_name, ?) AS file_name,
                COALESCE(f.file_path, ?) AS file_path
            FROM comments c
            LEFT JOIN users u 
                ON c.user_id = u.id
            LEFT JOIN admins a 
                ON c.admin_id = a.id
            LEFT JOIN {$this->fileTable} f 
                ON (
                    (f.part_name = 'admin_profile' AND f.part_id = a.id)
                    OR
                    (f.part_name = 'user_profile' AND f.part_id = u.id)
                )
            WHERE c.parent_id = ?
              AND c.part_name = ?
              AND c.part_id = ?
            GROUP BY c.id
            ORDER BY c.id DESC
        ";

            $stmt = $this->conn->prepare($sql);

            if ($stmt === false) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }

            $stmt->bind_param(
                "ssisi",
                $defaultFileName,
                $defaultFilePath,
                $parent_id,
                $this->part_name,
                $this->part_id
            );

            $stmt->execute();

            return $stmt->get_result();
        } catch (\Exception $e) {
            throw new \Exception("Failed to get comment part by parent id: " . $e->getMessage());
        }
    }

}
?>