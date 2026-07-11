<?php
// model/ManagerialModel.php

namespace ipanel\model;

class ManagerialModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    public function getAllDailyReport($admin_id)
    {
        try {
            $subManagerIds = $this->getAllSubManagerIdsRecursive($admin_id);

            $ids = array_merge([$admin_id], $subManagerIds);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $types = str_repeat('i', count($ids));

            $query = "
            SELECT 
                dr.*,
                a.name,
                a.role,
                a.unit_id,
                r.rbac_name,
                r.manager_id,

                CASE 
    WHEN EXISTS (
        SELECT 1
        FROM daily_report_approval_logs al
        WHERE al.report_id = dr.id
        AND al.approval_status = 'approved'
    ) THEN 1
    ELSE 0
END AS is_approved,

CASE 
    WHEN EXISTS (
        SELECT 1
        FROM daily_report_progress_logs l
        WHERE l.report_id = dr.id
        AND l.admin_id <> dr.admin_id
    ) THEN 1
    ELSE 0
END AS has_other_user_work

            FROM daily_reports dr
            LEFT JOIN admins a ON dr.admin_id = a.id
            LEFT JOIN rbac r ON a.rbac_id = r.id
            WHERE dr.admin_id IN ($placeholders)
            ORDER BY dr.creation_date DESC
        ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param($types, ...$ids);
            $stmt->execute();

            $result = $stmt->get_result();

            $reports = [];

            while ($row = $result->fetch_assoc()) {
                $reports[] = $row;
            }

            $stmt->close();

            return $reports;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return [];
        }
    }

    public function getDailyReport($id)
    {
        try {
            $query = "SELECT * FROM daily_reports WHERE id = ?";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $stmt->close();

            return $row;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return [];
        }
    }

    public function getTodoByPercentage($person_id, $progress_percentage)
    {
        try {
            $query = "
                SELECT * 
                FROM daily_reports 
                WHERE admin_id = ? 
                AND progress_percentage < ?
            ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param("ii", $person_id, $progress_percentage);
            $stmt->execute();

            $result = $stmt->get_result();

            $stmt->close();

            return $result;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return null;
        }
    }

    public function getDailyReportProgressLogs($report_id)
    {
        try {
            $query = "
                SELECT 
                    l.*, 
                    a.name AS admin_name
                FROM daily_report_progress_logs l
                LEFT JOIN admins a ON a.id = l.admin_id
                WHERE l.report_id = ?
                ORDER BY l.created_at DESC
            ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $report_id);
            $stmt->execute();

            $result = $stmt->get_result();

            $stmt->close();

            return $result;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return null;
        }
    }

    public function getAdminRbacId($admin_id)
    {
        try {
            $query = "
                SELECT rbac_id 
                FROM admins 
                WHERE id = ? 
                LIMIT 1
            ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $admin_id);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $stmt->close();

            return $row ? (int) $row['rbac_id'] : 0;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return 0;
        }
    }

    public function getSubManagerIds($admin_id)
    {
        try {
            $query = "
                SELECT child_admin.id
                FROM admins current_admin
                INNER JOIN rbac child_rbac 
                    ON child_rbac.manager_id = current_admin.rbac_id
                INNER JOIN admins child_admin 
                    ON child_admin.rbac_id = child_rbac.id
                WHERE current_admin.id = ?
                AND child_admin.status = 'Active'
                ORDER BY child_admin.name ASC
            ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $admin_id);
            $stmt->execute();

            $result = $stmt->get_result();

            $ids = [];

            while ($row = $result->fetch_assoc()) {
                $ids[] = (int) $row['id'];
            }

            $stmt->close();

            return $ids;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return [];
        }
    }

    public function getAllSubManagerIdsRecursive($admin_id)
    {
        try {
            $allIds = [];
            $directIds = $this->getSubManagerIds($admin_id);

            foreach ($directIds as $childAdminId) {
                if (!in_array($childAdminId, $allIds)) {
                    $allIds[] = $childAdminId;
                }

                $childIds = $this->getAllSubManagerIdsRecursive($childAdminId);

                foreach ($childIds as $subChildAdminId) {
                    if (!in_array($subChildAdminId, $allIds)) {
                        $allIds[] = $subChildAdminId;
                    }
                }
            }

            return $allIds;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return [];
        }
    }

    public function hasSubManagers($admin_id)
    {
        try {
            $query = "
                SELECT COUNT(*) AS total
                FROM admins current_admin
                INNER JOIN rbac child_rbac 
                    ON child_rbac.manager_id = current_admin.rbac_id
                INNER JOIN admins child_admin 
                    ON child_admin.rbac_id = child_rbac.id
                WHERE current_admin.id = ?
                AND child_admin.status = 'Active'
            ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $admin_id);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $stmt->close();

            return ((int) $row['total'] > 0);

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return false;
        }
    }

    public function getManagementStructure()
    {
        try {
            $query = "
                SELECT 
                    a.id AS admin_id,
                    a.name AS admin_name,
                    a.email,
                    a.mobile,
                    a.role,
                    a.status,
                    a.unit_id,
                    a.rbac_id,

                    r.id AS rbac_id_real,
                    r.rbac_name,
                    r.rbac_description,
                    r.manager_id,
                    r.activity_id,
                    r.company_id,
                    r.unit_id AS rbac_unit_id,
                    r.tag_id,

                    manager_admin.id AS manager_admin_id,
                    manager_admin.name AS manager_name,
                    manager_admin.email AS manager_email,
                    manager_admin.rbac_id AS manager_rbac_id
                FROM admins a
                LEFT JOIN rbac r 
                    ON a.rbac_id = r.id
                LEFT JOIN admins manager_admin 
                    ON manager_admin.rbac_id = r.manager_id
                ORDER BY 
                    CASE 
                        WHEN r.manager_id IS NULL OR r.manager_id = 0 THEN 0
                        ELSE 1
                    END,
                    r.manager_id ASC,
                    a.name ASC
            ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->execute();

            $result = $stmt->get_result();

            $items = [];

            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }

            $stmt->close();

            return $items;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return [];
        }
    }

    public function getManagementTree()
    {
        try {
            $items = $this->getManagementStructure();

            $children = [];
            $rbacIds = [];

            foreach ($items as $item) {
                if (!empty($item['rbac_id'])) {
                    $rbacIds[] = (int) $item['rbac_id'];
                }
            }

            foreach ($items as $item) {
                $managerRbacId = !empty($item['manager_id']) ? (int) $item['manager_id'] : 0;

                if ($managerRbacId > 0 && !in_array($managerRbacId, $rbacIds)) {
                    $managerRbacId = 0;
                }

                $children[$managerRbacId][] = $item;
            }

            $buildTree = function ($parentRbacId) use (&$buildTree, &$children) {
                $branch = [];

                if (!isset($children[$parentRbacId])) {
                    return $branch;
                }

                foreach ($children[$parentRbacId] as $person) {
                    $personRbacId = !empty($person['rbac_id']) ? (int) $person['rbac_id'] : 0;
                    $person['children'] = $buildTree($personRbacId);
                    $branch[] = $person;
                }

                return $branch;
            };

            return $buildTree(0);

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return [];
        }
    }

    public function isManagerOfPerson($manager_admin_id, $person_admin_id)
    {
        try {
            $query = "
                SELECT child_admin.id
                FROM admins manager_admin
                INNER JOIN rbac child_rbac 
                    ON child_rbac.manager_id = manager_admin.rbac_id
                INNER JOIN admins child_admin 
                    ON child_admin.rbac_id = child_rbac.id
                WHERE manager_admin.id = ?
                AND child_admin.id = ?
                LIMIT 1
            ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param("ii", $manager_admin_id, $person_admin_id);
            $stmt->execute();

            $result = $stmt->get_result();

            $hasAccess = ($result->num_rows > 0);

            $stmt->close();

            return $hasAccess;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return false;
        }
    }

    public function isManagerOfPersonRecursive($manager_admin_id, $person_admin_id)
    {
        try {
            $subManagerIds = $this->getAllSubManagerIdsRecursive($manager_admin_id);

            return in_array((int) $person_admin_id, $subManagerIds);

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return false;
        }
    }

    public function getDailyReportCountByUsers($admin_id)
    {
        try {
            $subManagerIds = $this->getAllSubManagerIdsRecursive($admin_id);

            $ids = array_merge([$admin_id], $subManagerIds);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $types = str_repeat('i', count($ids));

            $query = "
            SELECT 
                a.id AS admin_id,
                a.name AS admin_name,
                a.role,
                r.rbac_name,
                COUNT(dr.id) AS total_reports,
                SUM(CASE WHEN dr.progress_percentage < 100 THEN 1 ELSE 0 END) AS open_reports,
                SUM(CASE WHEN dr.progress_percentage = 100 THEN 1 ELSE 0 END) AS done_reports
            FROM admins a
            LEFT JOIN rbac r ON a.rbac_id = r.id
            LEFT JOIN daily_reports dr ON dr.admin_id = a.id
            WHERE a.id IN ($placeholders)
            GROUP BY 
                a.id,
                a.name,
                a.role,
                r.rbac_name
            ORDER BY 
                CASE WHEN a.id = ? THEN 0 ELSE 1 END,
                a.name ASC
        ";

            $types .= 'i';
            $ids[] = $admin_id;

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param($types, ...$ids);
            $stmt->execute();

            $result = $stmt->get_result();

            $counts = [];

            while ($row = $result->fetch_assoc()) {
                $counts[] = $row;
            }

            $stmt->close();

            return $counts;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return [];
        }
    }

    public function hasOtherUserWorkedOnReport($report_id)
    {
        try {
            $query = "
            SELECT COUNT(*) AS total
            FROM daily_report_progress_logs l
            INNER JOIN daily_reports dr ON dr.id = l.report_id
            WHERE l.report_id = ?
            AND l.admin_id <> dr.admin_id
        ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $report_id);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $stmt->close();

            return ((int) $row['total'] > 0) ? 1 : 0;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return 0;
        }
    }

    public function approveDailyReport($report_id, $manager_admin_id)
    {
        try {
            $query = "
            INSERT INTO daily_report_approval_logs
            (report_id, manager_admin_id, approval_status)
            VALUES (?, ?, 'approved')
        ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param("ii", $report_id, $manager_admin_id);
            $stmt->execute();
            $stmt->close();

            return true;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return false;
        }
    }

    public function isDailyReportApproved($report_id)
    {
        try {
            $query = "
            SELECT COUNT(*) AS total
            FROM daily_report_approval_logs
            WHERE report_id = ?
            AND approval_status = 'approved'
        ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $report_id);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $stmt->close();

            return ((int) $row['total'] > 0) ? 1 : 0;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return 0;
        }
    }

    public function getDailyReportApprovalLog($report_id)
    {
        try {
            $query = "
            SELECT 
                al.*,
                a.name AS manager_name
            FROM daily_report_approval_logs al
            LEFT JOIN admins a ON a.id = al.manager_admin_id
            WHERE al.report_id = ?
            ORDER BY al.created_at DESC
            LIMIT 1
        ";

            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new \Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param("i", $report_id);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $stmt->close();

            return $row;

        } catch (\Exception $e) {
            echo "Query Error: " . $e->getMessage();
            return null;
        }
    }
}