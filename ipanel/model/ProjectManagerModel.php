<?php
namespace ipanel\model;

class ProjectManagerModel
{
    private $projectsTable = 'projects';
    private $projectMemberTable = 'project_members';
    private $scheduleTable = 'schedule';
    private $adminTable = 'admins';
    private $fileTable = 'file_manage';

    private $conn;
    private $adminId;

    protected $defaultFileName = "avatar-1.jpg";
    protected $defaultFilePath = "./itheme/panel/images/users/";

    public function __construct($db)
    {
        $this->conn = $db;
        $this->adminId = $_SESSION["admin_id"] ?? 0;
    }

    public function getAllProjects()
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->projectsTable} ORDER BY id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getProjectById($project_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->projectsTable} WHERE id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }


    public function getAdminIdFromProjectById($project_id)
    {
        $stmt = $this->conn->prepare("SELECT admin_id FROM {$this->projectsTable} WHERE id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['admin_id'] ?? null;
    }

    public function cheakProjectAdmin($project_id)
    {
        return $this->adminId == $this->getAdminIdFromProjectById($project_id);
    }

    public function getAllProjectMembers($project_id)
    {
        $stmt = $this->conn->prepare("SELECT member_id FROM {$this->projectMemberTable} WHERE element_id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $members = [];
        while ($row = $result->fetch_assoc()) {
            $members[] = $row['member_id'];
        }

        return $members;
    }


    public function getCountProjectMembers($project_id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(member_id) AS member_count FROM {$this->projectMemberTable} WHERE element_id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $row = $result->fetch_assoc();
        return $row ? (int) $row['member_count'] : 0;
    }


    public function checkMemberInProject($project_id)
    {
        $allMembers = $this->getAllProjectMembers($project_id);
        return in_array($this->adminId, $allMembers) || $this->getAdminIdFromProjectById($project_id) == $this->adminId;
    }

    public function getSchedule($element_id)
    {
        $sqlQuery = "SELECT * FROM $this->scheduleTable WHERE section_part_name = 'projects' AND section_element_id = ? ORDER BY date_time ASC";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $element_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCountSchedule($element_id)
    {
        $sqlQuery = "SELECT COUNT(*) AS total FROM $this->scheduleTable WHERE section_part_name = 'projects' AND section_element_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $element_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $row = $result->fetch_assoc();
        return $row['total'];
    }


    public function getProjectAdminInfoWithImage($project_id, $limit = 3)
    {
        $memberIds = $this->getAllProjectMembers($project_id);

        if (empty($memberIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($memberIds), '?'));

        $sqlQuery = "SELECT admins.id AS id, admins.name AS name, 
                 COALESCE(file_manage.file_name, ?) AS file_name, 
                 COALESCE(file_manage.file_path, ?) AS file_path
                 FROM {$this->adminTable} AS admins
                 LEFT JOIN {$this->fileTable} AS file_manage 
                 ON admins.id = file_manage.admin_id AND file_manage.part_name = 'admin_profile'
                 WHERE admins.id IN ($placeholders) LIMIT ?";

        $stmt = $this->conn->prepare($sqlQuery);

        $types = "ss" . str_repeat('i', count($memberIds)) . "i";
        $params = array_merge([$this->defaultFileName, $this->defaultFilePath], $memberIds, [$limit]);

        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_all(MYSQLI_ASSOC);
    }




}
