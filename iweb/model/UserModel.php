<?php

namespace iweb\model;

class UserModel extends \Configuration
{
	public $email;
	public $password;
	private $userTable = 'users';
	private $fileTable = 'file_manage';
	private $companyTable = 'company_profiles';
	private $unitTable = 'units';
	private $userLogTable = 'user_log';
	private $conn;

	public function __construct($db)
	{
		$this->conn = $db;
	}



	public function user_log($action)
	{

		$systemInfo = $this->getUserSystemInfo();
		$sqlQuery = "INSERT INTO " . $this->userLogTable . "(`user_id`, `action`, `local_ip`,`internet_ip`,`system_info`) VALUES (?,?,?,?,?)";
		$stmt = $this->conn->prepare($sqlQuery);

		$action = htmlspecialchars(strip_tags($action));
		$local_ip = htmlspecialchars(strip_tags($systemInfo['local_ip']));
		$internet_ip = htmlspecialchars(strip_tags($systemInfo['internet_ip']));
		$system_info = htmlspecialchars(strip_tags($systemInfo['system_info']));

		$stmt->bind_param("issss", $_SESSION["user_id"], $action, $local_ip, $internet_ip, $system_info);

		if ($stmt->execute()) {
			return true;
		}

		return false;
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

<<<<<<< HEAD
	private function setUserSession(array $user): void
	{
		$_SESSION["user_id"] = $user['id'];
		$_SESSION["rbac_id"] = $user['rbac_id'];
		$_SESSION["role"] = $user['role'];
		$_SESSION["mobile"] = $user['mobile'];
		$_SESSION["name"] = $user['name'];
		$_SESSION["email"] = $user['email'];
		$_SESSION["user_company"] = $user['user_company'];
		$_SESSION["user_unit"] = $user['user_unit'];
		$_SESSION["unit_id"] = $user['unit_id'];
		$_SESSION["company_id"] = $user['company_id'];

		$sqlQuery = "
        SELECT file_name, file_path
        FROM file_manage
        WHERE part_name = 'user_profile'
          AND part_id = ?
          AND user_id = ?
    ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("ii", $_SESSION["user_id"], $_SESSION["user_id"]);
		$stmt->execute();

		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			$imageProfile = $result->fetch_assoc();

			$_SESSION["profile_image_name"] = $imageProfile['file_name'];
			$_SESSION["profile_image_path"] = $imageProfile['file_path'];
		} else {
			$_SESSION["profile_image_name"] = $this->getDefaultFileName();
			$_SESSION["profile_image_path"] = $this->getDefaultFilePath();
		}
	}

	private function getActiveUserById(int $userId): ?array
	{
		$sqlQuery = "
        SELECT user.*, 
               unit.unit_name AS user_unit, 
               unit.id AS unit_id,
               company.company_name AS user_company, 
               company.id AS company_id
        FROM " . $this->userTable . " user
        LEFT JOIN " . $this->unitTable . " unit 
            ON user.unit_id = unit.id
        JOIN " . $this->companyTable . " company 
            ON unit.company_id = company.id
        WHERE user.id = ?
          AND user.status = 'Active'
        LIMIT 1
    ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("i", $userId);
		$stmt->execute();

		$result = $stmt->get_result();

		if ($result->num_rows <= 0) {
			return null;
		}

		return $result->fetch_assoc();
	}

	public function loginByRememberToken(string $rememberToken): bool
	{
		if (strpos($rememberToken, ':') === false) {
			return false;
		}

		[$selector, $validator] = explode(':', $rememberToken, 2);

		$sqlQuery = "
        SELECT user_id, token_hash
        FROM user_remember_tokens
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

		$user = $this->getActiveUserById((int) $tokenData['user_id']);

		if (!$user) {
			return false;
		}

		$this->setUserSession($user);
		$this->user_log('remember_login');

		return true;
	}

=======
>>>>>>> 5591029... some change
	public function login()
	{
		if (!$this->email || !$this->password) {
			return 0;
		}

		$sqlQuery = "
<<<<<<< HEAD
        SELECT user.*, 
               unit.unit_name AS user_unit, 
               unit.id AS unit_id,
               company.company_name AS user_company, 
               company.id AS company_id
        FROM " . $this->userTable . " user
        LEFT JOIN " . $this->unitTable . " unit 
            ON user.unit_id = unit.id
        JOIN " . $this->companyTable . " company 
            ON unit.company_id = company.id
        WHERE user.email = ?
          AND user.status = 'Active'
        LIMIT 1
    ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("s", $this->email);
		$stmt->execute();

		$result = $stmt->get_result();

		if ($result->num_rows <= 0) {
			return 0;
		}

		$user = $result->fetch_assoc();

		if (!password_verify($this->password, $user['password'])) {
			return 0;
		}

		$this->setUserSession($user);
		$this->user_log('login');

		return 1;
=======
			SELECT user.*, unit.unit_name AS user_unit, unit.id AS unit_id, 
				   company.company_name AS user_company, company.id AS company_id 
			FROM " . $this->userTable . " user
			LEFT JOIN " . $this->unitTable . " unit ON user.unit_id = unit.id
			JOIN " . $this->companyTable . " company ON unit.company_id = company.id
			WHERE user.email = ? AND user.status = 'Active' ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("s", $this->email);  // ایمیل کاربر را به عنوان ورودی می‌دهیم
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			$user = $result->fetch_assoc();



			if (password_verify($this->password, $user['password'])) {

				$_SESSION["user_id"] = $user['id'];
				$_SESSION["rbac_id"] = $user['rbac_id'];
				$_SESSION["role"] = $user['role'];
				$_SESSION["mobile"] = $user['mobile'];
				$_SESSION["name"] = $user['name'];
				$_SESSION["email"] = $user['email'];
				$_SESSION["user_company"] = $user['user_company'];
				$_SESSION["user_unit"] = $user['user_unit'];
				$_SESSION["unit_id"] = $user['unit_id'];
				$_SESSION["company_id"] = $user['company_id'];

				$sqlQuery2 = "SELECT file_name, file_path 
							  FROM file_manage 
							  WHERE part_name = 'user_profile' 
							  AND part_id = ? 
							  AND user_id = ?";
				$stmt2 = $this->conn->prepare($sqlQuery2);
				$stmt2->bind_param("ii", $_SESSION["user_id"], $_SESSION["user_id"]);
				$stmt2->execute();
				$result2 = $stmt2->get_result();

				if ($result2->num_rows > 0) {
					$image_profile = $result2->fetch_assoc();
					$_SESSION["profile_image_name"] = $image_profile['file_name'];
					$_SESSION["profile_image_path"] = $image_profile['file_path'];
				} else {
					$_SESSION["profile_image_name"] = $this->getDefaultFileName();
					$_SESSION["profile_image_path"] = $this->getDefaultFilePath();
				}
				$this->user_log('login');

				return 1;
			}
		}

		return 0;
>>>>>>> 5591029... some change
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
            COUNT(DISTINCT ul.id) AS login_count,
            COALESCE(t.ticket_count, 0) AS tickets
        FROM users u
        INNER JOIN units ON u.unit_id = units.id
        LEFT JOIN company_profiles cp ON units.company_id = cp.id
        LEFT JOIN user_log ul ON u.id = ul.user_id AND ul.action = 'login'
        LEFT JOIN (
            SELECT 
                user_id, 
                COUNT(*) AS ticket_count 
            FROM tickets 
            GROUP BY user_id
        ) t ON u.id = t.user_id
        GROUP BY u.id
        ORDER BY login_count DESC
        LIMIT 10;
    ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		return $stmt->get_result();
	}

	function getLastLoginUsers($limit = 14, $orderByLogin = false)
	{
		$orderClause = $orderByLogin
			? "ORDER BY last_login_time DESC NULLS LAST, u.id ASC"
			: "ORDER BY u.id ASC";

		$sqlQuery = "
        SELECT
            u.id AS user_id,
            u.name AS name,
            last_login.max_time AS last_login_time,
            last_login.local_ip,
            last_login.internet_ip,
            units.unit_name,
            cp.company_name
        FROM users u
        LEFT JOIN (
            SELECT 
                ul1.user_id,
                MAX(ul1.timestamp) AS max_time,
                SUBSTRING_INDEX(GROUP_CONCAT(ul1.local_ip ORDER BY ul1.timestamp DESC), ',', 1) AS local_ip,
                SUBSTRING_INDEX(GROUP_CONCAT(ul1.internet_ip ORDER BY ul1.timestamp DESC), ',', 1) AS internet_ip
            FROM user_log ul1
            WHERE ul1.action = 'login'
            GROUP BY ul1.user_id
        ) last_login ON u.id = last_login.user_id
        LEFT JOIN units ON u.unit_id = units.id
        LEFT JOIN company_profiles cp ON units.company_id = cp.id
        $orderClause
        LIMIT ?;
    ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('i', $limit);
		$stmt->execute();
		return $stmt->get_result();
	}


	public function loggedIn()
	{
<<<<<<< HEAD
		return !empty($_SESSION["user_id"]) ? 1 : 0;
=======
		if (isset($_SESSION["user_id"]) and !empty($_SESSION["user_id"])) {
			return 1;
		} else {
			return 0;
		}
>>>>>>> 5591029... some change
	}


	public function getUsers()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->userTable;
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;

	}
	public function getUserImageById($id)
	{
		$defaultFileName = $this->getDefaultFileName();
		$defaultFilePath = $this->getDefaultFilePath();

		$sqlQuery = "SELECT users.name, users.email, users.mobile,users.company_name, COALESCE(file_manage.file_name, ?) AS file_name, COALESCE(file_manage.file_path, ?) AS file_path
			FROM users_details_view as users
			LEFT JOIN " . $this->fileTable . " AS file_manage ON users.user_id = file_manage.user_id AND file_manage.part_name = 'user_profile'
			WHERE users.user_id = ?";

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


<<<<<<< HEAD
	public function createRememberToken(): string
	{
		$userId = $_SESSION['user_id'] ?? null;

		if (empty($userId)) {
			return '';
		}

		$selector = bin2hex(random_bytes(8));
		$validator = bin2hex(random_bytes(32));
		$tokenHash = password_hash($validator, PASSWORD_DEFAULT);
		$expiresAt = date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60);

		$sql = "
        INSERT INTO user_remember_tokens 
        (user_id, selector, token_hash, expires_at)
        VALUES (?, ?, ?, ?)
    ";

		$stmt = $this->conn->prepare($sql);
		$stmt->bind_param('isss', $userId, $selector, $tokenHash, $expiresAt);
		$stmt->execute();

		return $selector . ':' . $validator;
	}

	public function deleteRememberToken(string $rememberToken): bool
	{
		if (strpos($rememberToken, ':') === false) {
			return false;
		}

		[$selector] = explode(':', $rememberToken, 2);

		$sqlQuery = "
        DELETE FROM user_remember_tokens
        WHERE selector = ?
    ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("s", $selector);

		return $stmt->execute();
	}
=======
>>>>>>> 5591029... some change

}
?>