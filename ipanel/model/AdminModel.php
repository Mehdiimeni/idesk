<?php

namespace ipanel\model;

class AdminModel extends \Configuration
{
    public $email;
    public $password;
    private $adminTable = 'admins';
    private $fileTable = 'file_manage';
    private $unitTable = 'units';
    private $adminLogTable = 'admin_log';
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function admin_log($action)
    {

        $systemInfo = $this->getAdminSystemInfo();
        $sqlQuery = "INSERT INTO " . $this->adminLogTable . "(`admin_id`, `action`, `local_ip`,`internet_ip`,`system_info`) VALUES (?,?,?,?,?)";
        $stmt = $this->conn->prepare($sqlQuery);

        $action = htmlspecialchars(strip_tags($action));
        $local_ip = htmlspecialchars(strip_tags($systemInfo['local_ip']));
        $internet_ip = htmlspecialchars(strip_tags($systemInfo['internet_ip']));
        $system_info = htmlspecialchars(strip_tags($systemInfo['system_info']));

        $stmt->bind_param("issss", $_SESSION["admin_id"], $action, $local_ip, $internet_ip, $system_info);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getAdminSystemInfo()
    {
        $internet_ip = $_SERVER['REMOTE_ADDR'];
        $local_ip = gethostbyname(gethostname());
        $system_info = php_uname();

        $admin_info = [
            'local_ip' => $local_ip,
            'system_info' => $system_info,
            'internet_ip' => $internet_ip,
        ];

        return $admin_info;
    }


    function getTopPerforming()
    {
        $sqlQuery = "
        SELECT
            u.id,
            u.name,
            u.unit_id,
            units.unit_name,
            cp.company_name,
            COALESCE(login_stats.login_count, 0) AS login_count
        FROM admins u
        INNER JOIN units ON u.unit_id = units.id
        LEFT JOIN company_profiles cp ON units.company_id = cp.id
        LEFT JOIN (
            SELECT 
                admin_id, 
                COUNT(*) AS login_count 
            FROM admin_log 
            WHERE action = 'login'
            GROUP BY admin_id
        ) login_stats ON u.id = login_stats.admin_id
        WHERE u.status = 'Active'
        ORDER BY login_count DESC, u.id ASC;
    ";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getLastLoginAdmins($limit = 25)
    {
        $sqlQuery = "
        SELECT 
            u.id AS admin_id,
            u.name AS name,
            latest_login.timestamp AS last_login_time,
            latest_login.local_ip AS local_ip,
            latest_login.internet_ip AS internet_ip,
            units.unit_name,
            cp.company_name
        FROM admins u
        LEFT JOIN (
            SELECT 
                a1.admin_id,
                a1.timestamp,
                a1.local_ip,
                a1.internet_ip
            FROM admin_log a1
            WHERE a1.action = 'login'
              AND NOT EXISTS (
                SELECT 1 
                FROM admin_log a2 
                WHERE a2.admin_id = a1.admin_id 
                  AND a2.action = 'login'
                  AND (a2.timestamp > a1.timestamp OR 
                      (a2.timestamp = a1.timestamp AND a2.id > a1.id))
              )
        ) latest_login ON u.id = latest_login.admin_id
        LEFT JOIN units ON u.unit_id = units.id
        LEFT JOIN company_profiles cp ON units.company_id = cp.id
        WHERE u.status = 'Active'
        ORDER BY COALESCE(latest_login.timestamp, '1970-01-01') DESC, u.id ASC
        LIMIT ?;
    ";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getCountDailyReportAdmins($limit = 25)
    {
        $sqlQuery = "
        SELECT
            dr.admin_id,
            MAX(u.name) AS name,
            MAX(units.unit_name) AS unit_name,
            COUNT(dr.id) AS report_count
        FROM daily_reports dr
        INNER JOIN admins u ON dr.admin_id = u.id AND u.status = 'Active'
        INNER JOIN units ON u.unit_id = units.id
        GROUP BY dr.admin_id
        ORDER BY report_count DESC
        LIMIT ?;
    ";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result();
    }


<<<<<<< HEAD
    

    private function getAdminById(int $adminId): ?array
    {
        $sql = "
        SELECT *
        FROM " . $this->adminTable . "
        WHERE id = ?
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $adminId);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            return null;
        }

        return $result->fetch_assoc();
    }



    private function setAdminSession(array $adminDetails): void
    {
        $_SESSION["admin_id"] = $adminDetails['id'];
        $_SESSION["role"] = $adminDetails['role'];
        $_SESSION["mobile"] = $adminDetails['mobile'];
        $_SESSION["name"] = $adminDetails['name'];
        $_SESSION["email"] = $adminDetails['email'];
        $_SESSION["unit_id"] = $adminDetails['unit_id'];
        $_SESSION["rbac_id"] = $adminDetails['rbac_id'];
        $_SESSION["company_id"] = $adminDetails['company_id'];

        $_SESSION["profile_image_name"] = $this->getDefaultFileName();
        $_SESSION["profile_image_path"] = $this->getDefaultFilePath();
    }

    private function getActiveAdminById(int $adminId): ?array
    {
        $sqlQuery = "
        SELECT a.*, u.company_id
        FROM " . $this->adminTable . " AS a
        JOIN units AS u ON a.unit_id = u.id
        WHERE a.id = ?
          AND a.status = 'Active'
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $adminId);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            return null;
        }

        return $result->fetch_assoc();
    }
=======
>>>>>>> 5591029... some change

    public function login()
    {
        if (!$this->email || !$this->password) {
            return 0;
        }

<<<<<<< HEAD
        $sqlQuery = "
        SELECT a.*, u.company_id
        FROM " . $this->adminTable . " AS a
        JOIN units AS u ON a.unit_id = u.id
        WHERE a.email = ?
          AND a.status = 'Active'
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            return 0;
        }

        $adminDetails = $result->fetch_assoc();

        if (!password_verify($this->password, $adminDetails['password'])) {
            return 0;
        }

        $this->setAdminSession($adminDetails);
        $this->admin_log('login');

        return 1;
    }

    public function loggedIn()
    {
        return !empty($_SESSION["admin_id"]) ? 1 : 0;
    }

    public function createRememberToken(): string
    {
        $adminId = $_SESSION['admin_id'] ?? null;

        if (empty($adminId)) {
            return '';
        }

        $selector = bin2hex(random_bytes(8));
        $validator = bin2hex(random_bytes(32));
        $tokenHash = password_hash($validator, PASSWORD_DEFAULT);
        $expiresAt = date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60);

        $sqlQuery = "
        INSERT INTO admin_remember_tokens
        (admin_id, selector, token_hash, expires_at)
        VALUES (?, ?, ?, ?)
    ";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("isss", $adminId, $selector, $tokenHash, $expiresAt);
        $stmt->execute();

        return $selector . ':' . $validator;
    }

    public function loginByRememberToken(string $rememberToken): bool
    {
        if (strpos($rememberToken, ':') === false) {
            return false;
        }

        [$selector, $validator] = explode(':', $rememberToken, 2);

        $sqlQuery = "
        SELECT admin_id, token_hash
        FROM admin_remember_tokens
        WHERE selector = ?
          AND expires_at > NOW()
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("s", $selector);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            return false;
        }

        $tokenData = $result->fetch_assoc();

        if (!password_verify($validator, $tokenData['token_hash'])) {
            return false;
        }

        $adminDetails = $this->getActiveAdminById((int) $tokenData['admin_id']);

        if (!$adminDetails) {
            return false;
        }

        $this->setAdminSession($adminDetails);
        $this->admin_log('remember_login');

        return true;
    }

    public function deleteRememberToken(string $rememberToken): bool
    {
        if (strpos($rememberToken, ':') === false) {
            return false;
        }

        [$selector] = explode(':', $rememberToken, 2);

        $sqlQuery = "
        DELETE FROM admin_remember_tokens
        WHERE selector = ?
    ";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("s", $selector);

        return $stmt->execute();
=======
        $sqlQuery = "SELECT a.*, u.company_id
                     FROM " . $this->adminTable . " AS a
                     JOIN units AS u ON a.unit_id = u.id
                     WHERE a.email = ? AND a.status = 'Active'";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $adminDetails = $result->fetch_assoc();

            if (password_verify($this->password, $adminDetails['password'])) {
                $_SESSION["admin_id"] = $adminDetails['id'];
                $_SESSION["role"] = $adminDetails['role'];
                $_SESSION["mobile"] = $adminDetails['mobile'];
                $_SESSION["name"] = $adminDetails['name'];
                $_SESSION["email"] = $adminDetails['email'];
                $_SESSION["unit_id"] = $adminDetails['unit_id'];
                $_SESSION["rbac_id"] = $adminDetails['rbac_id'];
                $_SESSION["company_id"] = $adminDetails['company_id'];

                $sqlQuery2 = "SELECT file_name, file_path FROM file_manage 
                              WHERE part_name = 'admin_profile' AND part_id = ? AND admin_id = ?";
                $stmt2 = $this->conn->prepare($sqlQuery2);
                $stmt2->bind_param("ii", $_SESSION["admin_id"], $_SESSION["admin_id"]);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
/*
                if ($result2->num_rows > 0) {
                    $profile_image = $result2->fetch_assoc();
                    $_SESSION["profile_image_name"] = $profile_image['file_name'];
                    $_SESSION["profile_image_path"] = $profile_image['file_path'];
                } else {
                    $_SESSION["profile_image_name"] = $this->getDefaultFileName();
                    $_SESSION["profile_image_path"] = $this->getDefaultFilePath();
                }
                    */

                $_SESSION["profile_image_name"] = $this->getDefaultFileName();
                    $_SESSION["profile_image_path"] = $this->getDefaultFilePath();
                $this->admin_log('login');
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }


    public function loggedIn()
    {
        if (!empty($_SESSION["admin_id"])) {
            return 1;
        } else {
            return 0;
        }
>>>>>>> 5591029... some change
    }


    public function getAdmins()
    {
        $sqlQuery = "SELECT *
        FROM " . $this->adminTable;

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;

    }

    public function getUnitNameById($id)
    {

        $sqlQuery = "SELECT unit_name,company_id FROM " . $this->unitTable . " WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }

    }


    public function getAdminImageById($id)
    {
        $defaultFileName = $this->getDefaultFileName();
        $defaultFilePath = $this->getDefaultFilePath();

        $sqlQuery = "SELECT admins.name, admins.email, admins.mobile, admins.company_name , COALESCE(file_manage.file_name, ?) AS file_name, COALESCE(file_manage.file_path, ?) AS file_path
					 FROM admins_details_view AS admins
					 LEFT JOIN " . $this->fileTable . " AS file_manage ON admins.admin_id = file_manage.admin_id AND file_manage.part_name = 'admin_profile'
					 WHERE admins.admin_id = ?";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("ssi", $defaultFileName, $defaultFilePath, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function getAdminEmailById($id)
    {
        $sqlQuery = "SELECT email FROM " . $this->adminTable . " WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function getAdminNameById($id)
    {
        $sqlQuery = "SELECT name FROM " . $this->adminTable . " WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }




}

