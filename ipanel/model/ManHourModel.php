<?php
namespace ipanel\model;

class ManHourModel extends \Configuration
{
    private $manHourTable = 'man_hour';

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

    public function getTotalManHourPart()
    {
        $sql = "SELECT SUM(man_hour_number) AS total
            FROM " . $this->manHourTable . " 
            WHERE part_name = ? AND part_id = ?";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new \Exception("Prepare failed: " . htmlspecialchars($this->conn->error));
        }

        $stmt->bind_param("si", $this->part_name, $this->part_id);

        $stmt->execute();
        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();

        return $reply['total'] ?? 0; 
    }


public function getManHourPart()
{
    $defaultFileName = $this->getDefaultFileName();
    $defaultFilePath = $this->getDefaultFilePath();

   $query = "
    SELECT 
        mh.*,
        COALESCE(u.name, a.name) AS name,
        COALESCE(
            (SELECT fa.file_name FROM {$this->fileTable} fa 
             WHERE fa.part_id = a.id AND fa.part_name = 'admin_profile' LIMIT 1),
            (SELECT fu.file_name FROM {$this->fileTable} fu 
             WHERE fu.part_id = u.id AND fu.part_name = 'user_profile' LIMIT 1),
            ?
        ) AS file_name,
        COALESCE(
            (SELECT fa.file_path FROM {$this->fileTable} fa 
             WHERE fa.part_id = a.id AND fa.part_name = 'admin_profile' LIMIT 1),
            (SELECT fu.file_path FROM {$this->fileTable} fu 
             WHERE fu.part_id = u.id AND fu.part_name = 'user_profile' LIMIT 1),
            ?
        ) AS file_path
    FROM man_hour mh
    LEFT JOIN users u ON mh.user_id = u.id
    LEFT JOIN admins a ON mh.admin_id = a.id
    WHERE mh.parent_id IS NULL
      AND mh.part_name = ?
      AND mh.part_id = ?
    ORDER BY mh.id
";

    $stmt = $this->conn->prepare($query);

    // ترتیب پارامترها: defaultFileName, defaultFilePath, part_name, part_id
    $stmt->bind_param(
        "sssi", 
        $defaultFileName, 
        $defaultFilePath, 
        $this->part_name, 
        $this->part_id
    );

    $stmt->execute();
    return $stmt->get_result();
}




    public function getLastManHourByAdminId($AdminId)
    {
        $sql = "SELECT admin_id 
            FROM " . $this->manHourTable . " 
            WHERE part_name = ? AND part_id = ?
            ORDER BY id DESC 
            LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            throw new \Exception("Prepare failed: " . htmlspecialchars($this->conn->error));
        }

        $stmt->bind_param("si", $this->part_name, $this->part_id);

        $stmt->execute();
        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();

        return isset($reply['admin_id']) && $reply['admin_id'] == $AdminId;
    }



    function getManHourInfo($section_part_name, $section_element_id)
    {
        $stmt = $this->conn->prepare("
        SELECT sp.*, fw.* , admins.name as admin_name
        FROM $section_part_name sp
        LEFT JOIN $this->manHourTable fw ON sp.id = fw.part_id
        LEFT JOIN admins ON fw.admin_id = admins.id
        WHERE sp.id = ?
        AND (fw.id IS NULL OR fw.part_name = ?)
    ");

        $stmt->bind_param("is", $section_element_id, $section_part_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }



    public function getManHourPartByParentId($parent_id)
    {
        $defaultFileName = $this->getDefaultFileName();
        $defaultFilePath = $this->getDefaultFilePath();

        $sqlWhere = "man_hour.parent_id = '" . $parent_id . "' AND  man_hour.part_name = '" . $this->part_name . "' AND man_hour.part_id = " . $this->part_id;

        $stmt = $this->conn->prepare("
            SELECT man_hour.*, 
                IFNULL(users.name, admins.name) AS name,
                COALESCE(file_manage.file_name, ?) AS file_name,
                COALESCE(file_manage.file_path, ?) AS file_path
            FROM man_hour
            LEFT JOIN users ON man_hour.user_id = users.id
            LEFT JOIN admins ON man_hour.admin_id = admins.id
            LEFT JOIN " . $this->fileTable . " AS file_manage ON
                (man_hour.admin_id != '' AND man_hour.admin_id = admins.id AND file_manage.part_name = 'admin_profile') OR
                (man_hour.user_id != '' AND man_hour.user_id = users.id AND file_manage.part_name = 'user_profile')
            WHERE $sqlWhere
            ORDER BY man_hour.id;
        ");

        $stmt->bind_param("ss", $defaultFileName, $defaultFilePath);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }


}
?>