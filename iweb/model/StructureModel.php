<?php
namespace iweb\model;
class StructureModel
{

	private $activitiesTable = 'activities';
	private $rbacTable = 'rbac';
	private $todoListTable = 'todo_list';
	private $conditionsTable = 'conditions';
	private $companyTable = 'company_profiles';
	private $userPartsTable = 'users_parts';
	private $userGroupsTable = 'users_groups';
	private $adminPartsTable = 'admins_parts';
	private $adminGroupsTable = 'admins_groups';
	private $unitTable = 'units';
	private $tagTable = 'tags';
	private $typeTable = 'types';
	private $statusTable = 'status';
	private $conn;

	public function __construct($db)
	{
		$this->conn = $db;
	}


	public function getActivities()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->activitiesTable;

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}


	public function getActivityById($id)
	{

		$sqlQuery = "SELECT * FROM " . $this->activitiesTable . " WHERE id = ?";
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

	public function getRBAC($all = false)
	{
		$sqlQuery = "
			SELECT
				rbac.*,
				{$this->activitiesTable}.activity_name,
				{$this->companyTable}.company_name,
				{$this->unitTable}.unit_name,
				{$this->tagTable}.tag_name
			FROM
				{$this->rbacTable} AS rbac
			LEFT JOIN
				{$this->activitiesTable} ON rbac.activity_id = {$this->activitiesTable}.id
			LEFT JOIN
				{$this->companyTable} ON rbac.company_id = {$this->companyTable}.id
			LEFT JOIN
				{$this->unitTable} ON rbac.unit_id = {$this->unitTable}.id
			LEFT JOIN
				{$this->tagTable} ON rbac.tag_id = {$this->tagTable}.id";

		// اضافه کردن شرط به صورت شرطی
		$sqlQuery .= $all ? " WHERE rbac.all_rbac = 0" : "";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}
	public function getRBACById($id)
	{

		$sqlQuery = "SELECT * FROM " . $this->rbacTable . " WHERE id = ?";
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

	public function getTodoLists()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->todoListTable . " ORDER BY todo_list_name DESC";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}
	public function getConditionsByPart($part_name)
	{
		$sqlQuery = "SELECT *
        FROM " . $this->conditionsTable . " WHERE condition_part = ? ORDER BY condition_name DESC";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("s", $part_name);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	public function getConditionsByName($name)
	{
		$name = strtolower($name);
		$sqlQuery = "SELECT *
        FROM " . $this->conditionsTable . " WHERE condition_name = ?";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("s", $name);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			return $result->fetch_assoc();
		} else {
			return null;
		}
	}
	public function getLastConditionDescription($part_id, $part_name)
	{
		$sqlQuery = "SELECT status_description , view_to , id,creation_date
                 FROM " . $this->statusTable . " 
                 WHERE part_id = ? AND part_name = ? AND status_name != 'condition_in_progress' AND status_name != 'Condition_referral_to_manager'
                 ORDER BY id DESC 
                 LIMIT 1";
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("is", $part_id, $part_name);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			$stmt->close();
			return $result->fetch_assoc();

		} else {
			$stmt->close();
			return null;
		}
	}

	public function getCompanyById($id)
	{
		$sqlQuery = "SELECT * FROM " . $this->companyTable . " WHERE id = ?";
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

	public function getUserGroupById($id)
	{

		$sqlQuery = "SELECT * FROM " . $this->userGroupsTable . " WHERE id = ?";
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

	public function getUserPartsById($id)
	{

		$sqlQuery = "SELECT * FROM " . $this->userPartsTable . " WHERE id = ?";
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

	public function getAdminGroups()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->adminGroupsTable;

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	public function getAdminGroupById($id)
	{

		$sqlQuery = "SELECT * FROM " . $this->adminGroupsTable . " WHERE id = ?";
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

	public function getAdminPartsById($id)
	{

		$sqlQuery = "SELECT * FROM " . $this->adminPartsTable . " WHERE id = ?";
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

	public function getAdminPartByGroups($id)
	{
		$sqlQuery = "SELECT *
        FROM " . $this->adminPartsTable . " WHERE admins_groups_id = " . $id;

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	public function getCompaniesByActivity($id)
	{
		$sqlQuery = "SELECT *
        FROM " . $this->companyTable . " WHERE activity_id = " . $id;

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}
	public function getCompanyByUnitId($unit_id)
	{
		$sqlQuery = "SELECT company_id
        FROM " . $this->unitTable . " WHERE id = ?";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("i", $unit_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$reply = $result->fetch_assoc();
		return $reply['company_id'];
	}
	public function getUnits()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->unitTable . " ORDER BY company_id ASC";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}
	public function getUnitById($id)
	{
		if (!is_numeric($id)) {
			throw new \InvalidArgumentException("Invalid unit ID.");
		}

		$sqlQuery = "SELECT * FROM " . $this->unitTable . " WHERE id = ? LIMIT 1";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();

		return $result->fetch_assoc();
	}

	public function getUnitsByCompany($id)
	{
		if (!is_numeric($id)) {
			throw new \InvalidArgumentException("Invalid company ID.");
		}

		$sqlQuery = "SELECT * FROM " . $this->unitTable . " WHERE company_id = ? ORDER BY unit_name ASC";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param('i', $id); // بایند کردن شناسه شرکت به عبارت SQL
		$stmt->execute();
		$result = $stmt->get_result();

		return $result;
	}

	public function getTypes()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->typeTable . " ORDER BY type_group DESC ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	public function getTypesGroupedByTypeGroup()
	{
		$sqlQuery = "SELECT type_group, type_name, id
                 FROM " . $this->typeTable . "
                 ORDER BY type_group, type_name";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();

		$types = [];
		while ($row = $result->fetch_assoc()) {
			$types[$row['type_group']][] = $row;
		}

		return $types;
	}

	public function getAllCompaniesMembersUser($company_id)
	{
		try {
			$sqlQuery = "SELECT * 
                     FROM company_member_view 
                     WHERE company_id = ?  OR company_id = 2 
                     ORDER BY company_name, name";

			// Prepare the SQL statement
			$stmt = $this->conn->prepare($sqlQuery);
			if ($stmt === false) {
				throw new \Exception('Failed to prepare the query: ' . $this->conn->error);
			}

			// Bind the parameter
			$stmt->bind_param('i', $company_id);

			// Execute the query
			$stmt->execute();

			// Get the result
			$result = $stmt->get_result();

			// Fetch and organize the data
			$companies = [];
			while ($row = $result->fetch_assoc()) {
				$companies[$row['company_id']]['company_name'] = $row['company_name'];
				$companies[$row['company_id']]['members'][] = [
					'name' => $row['name'],
					'member_type' => $row['member_type'],
					'unit_name' => $row['unit_name'],
					'member_id' => $row['member_id']
				];
			}

			// Close the statement and return the data
			$stmt->close();
			return $companies;
		} catch (\Exception $e) {
			return [];
		}
	}

	public function getCompaniesMemberById($id, $type)
	{
		$sqlQuery = "SELECT *
                 FROM company_member_view
                 WHERE member_id = ? AND member_type = ?";

		$stmt = $this->conn->prepare($sqlQuery);

		$stmt->bind_param("is", $id, $type);

		$stmt->execute();

		$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		return $result;
	}


	public function getAllMarkingTags(int $user_id, int $company_id)
	{
		$sqlQuery = "
        SELECT id,marking_tag
        FROM marking_tags
        WHERE company_id = ?
          AND (
                user_id = ?
                OR isGeneral = 1
              )
        ORDER BY marking_tag ASC
    ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("ii", $company_id, $user_id);
		$stmt->execute();

		return $stmt->get_result();
	}

	public function getUserMarkingTags(int $user_id, int $company_id)
	{
		$sqlQuery = "
        SELECT *
        FROM marking_tags
        WHERE (user_id = ? AND company_id = ?)
        ORDER BY id DESC
    ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("ii", $user_id, $company_id);
		$stmt->execute();

		return $stmt->get_result();
	}


}
