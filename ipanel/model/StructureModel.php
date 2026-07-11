<?php
namespace ipanel\model;
class StructureModel
{

	private $activitiesTable = 'activities';
	private $rolesTable = 'roles';
	private $rbacTable = 'rbac';
	private $operationsTable = 'operations';
	private $todoListTable = 'todo_list';
	private $markListTable = 'mark_list';
	private $conditionsTable = 'conditions';
	private $companyTable = 'company_profiles';
	private $userPartsTable = 'users_parts';
	private $userGroupsTable = 'users_groups';
	private $userSubPartsTable = 'users_subparts';
	private $adminPartsTable = 'admins_parts';
	private $adminGroupsTable = 'admins_groups';
	private $adminSubPartsTable = 'admins_subparts';
	private $unitTable = 'units';
	private $tagTable = 'tags';
	private $typeTable = 'types';
	private $statusTable = 'status';
	private $permissionsOperationTable = 'permissions_operation';
	private $permissionsStractureTable = 'permissions_stracture';
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

	public function getRoles()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->rolesTable;

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
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

	public function getRBACByIdWithCompany($rbac_id)
	{
		$sqlQuery = "
			SELECT
				rbac.rbac_name,
				rbac.id,
				{$this->activitiesTable}.activity_name,
				{$this->companyTable}.company_name,
				{$this->unitTable}.unit_name
			FROM
				{$this->rbacTable} AS rbac
			LEFT JOIN
				{$this->activitiesTable} ON rbac.activity_id = {$this->activitiesTable}.id
			LEFT JOIN
				{$this->companyTable} ON rbac.company_id = {$this->companyTable}.id
			LEFT JOIN
				{$this->unitTable} ON rbac.unit_id = {$this->unitTable}.id";

		$sqlQuery .= " WHERE rbac.id = ?";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("i", $rbac_id);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result->fetch_assoc();
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

	public function getRBACNotInOperation($all = false)
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
				{$this->tagTable} ON rbac.tag_id = {$this->tagTable}.id
			LEFT JOIN
				{$this->permissionsOperationTable} AS po ON rbac.id = po.rbac_id
			WHERE
				po.rbac_id IS NULL";

		$sqlQuery .= $all ? " AND rbac.all_rbac = 0" : "";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	public function getRBACNotInStracture($all = false)
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
				{$this->tagTable} ON rbac.tag_id = {$this->tagTable}.id
			LEFT JOIN
				{$this->permissionsStractureTable} AS ps ON rbac.id = ps.rbac_id
			";

		$sqlQuery .= $all ? " AND rbac.all_rbac = 0" : "";

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

	public function getUserOperation()
	{
		$sqlQuery = "SELECT
		permissions_operation.rbac_id,
		rbac.rbac_name,
		permissions_operation.last_updated_date,
		permissions_operation.id,
		permissions_operation.status

	FROM
		permissions_operation
	JOIN
		rbac ON permissions_operation.rbac_id = rbac.id WHERE rbac.all_rbac = 0 ORDER BY rbac.id DESC";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	public function getUserView()
	{
		$sqlQuery = "SELECT
		permissions_operation.*,
		rbac.rbac_name

	FROM
		permissions_operation
	JOIN
		rbac ON permissions_operation.rbac_id = rbac.id WHERE rbac.all_rbac = 0 ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	public function getUserStracture()
	{
		$sqlQuery = "SELECT
		permissions_stracture.rbac_id,
		rbac.rbac_name,
		permissions_stracture.last_updated_date,
		permissions_stracture.id
	FROM
		permissions_stracture
	JOIN
		rbac ON permissions_stracture.rbac_id = rbac.id";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	public function getOperations()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->operationsTable . " ORDER BY operation_name DESC";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
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

	public function getMarkLists()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->markListTable . " ORDER BY mark_list_name DESC";

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
                 WHERE part_id = ? AND part_name = ? 
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


	public function getLastForwardDescription($part_id, $part_name, $is_entry = 0)
	{
		$admin_id = $_SESSION['admin_id'];

		$sqlQuery = "SELECT person_name, forwards_description, creation_date
                 FROM status_forwards_detailed_view 
                 WHERE section_element_id = ? AND section_part_name = ?";

		if ($is_entry == 0) {
			$sqlQuery .= " AND receiver_person_id = ?";
		}

		$sqlQuery .= " ORDER BY id DESC LIMIT 1";

		$stmt = $this->conn->prepare($sqlQuery);
		if ($stmt === false) {
			throw new \Exception("Error preparing statement: " . $this->conn->error);
		}

		
		if ($is_entry == 0) {
			$stmt->bind_param("isi", $part_id, $part_name, $admin_id);
		} else {
			$stmt->bind_param("is", $part_id, $part_name); 
		}

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


	public function getAllForwardDescription($part_id, $part_name, $is_entry = 0, $limit = 0)
	{
		$admin_id = $_SESSION['admin_id'];


		$sqlQuery = "SELECT person_name, forwards_description, creation_date
					 FROM status_forwards_detailed_view 
					 WHERE section_element_id = ? AND section_part_name = ?";

		if ($is_entry == 0) {
			$sqlQuery .= " AND receiver_person_id = ?";
		}

		$sqlQuery .= " ORDER BY id DESC";

		// اضافه کردن محدودیت
		if ($limit > 0) {
			$sqlQuery .= " LIMIT ?, 20";
		}

		$stmt = $this->conn->prepare($sqlQuery);
		if ($stmt === false) {
			throw new \Exception("Error preparing statement: " . $this->conn->error);
		}

		if ($is_entry == 0) {
			if ($limit > 0) {
				$stmt->bind_param("isii", $part_id, $part_name, $admin_id, $limit);
			} else {
				$stmt->bind_param("isi", $part_id, $part_name, $admin_id);
			}
		} else {
			if ($limit > 0) {
				$stmt->bind_param("isi", $part_id, $part_name, $limit);
			} else {
				$stmt->bind_param("is", $part_id, $part_name);
			}
		}

		if (!$stmt->execute()) {
			throw new \Exception("Error executing statement: " . $stmt->error);
		}

		$result = $stmt->get_result();
		$descriptions = [];

		while ($row = $result->fetch_assoc()) {
			$descriptions[] = $row;
		}

		$stmt->close();
		return $descriptions;
	}



	public function getAllConditionDescription($part_id, $part_name, $limit = 0)
	{
		$sqlQuery = "SELECT status_description , view_to , id,creation_date
                 FROM " . $this->statusTable . " 
                 WHERE part_id = ? AND part_name = ? AND status_description != ''
                 ORDER BY id DESC";

		if ($limit > 0) {
			$sqlQuery .= " LIMIT ?, 20";
		}
		$stmt = $this->conn->prepare($sqlQuery);

		if ($limit > 0) {
			$stmt->bind_param("isi", $part_id, $part_name, $limit);
		} else {
			$stmt->bind_param("is", $part_id, $part_name);
		}
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

	public function getConditions()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->conditionsTable;

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
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

	public function getUserGroups()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->userGroupsTable;

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
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

	public function getUserParts()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->userPartsTable;

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
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

	public function getUserSubParts()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->userSubPartsTable;

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
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

	public function getAdminParts()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->adminPartsTable . " ORDER BY admins_parts_name DESC";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
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

	public function getAdminSubParts()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->adminSubPartsTable . " ORDER BY admins_subparts_name DESC";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}
	public function getCompanies()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->companyTable . " ORDER BY company_name ASC ";

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


	public function getAllUsersByCompanyId($id)
	{
		$sql = "
        (SELECT
            name AS member_name,
            company_id,
            admin_id AS member_id,
            company_name
         FROM admins_details_view
         WHERE company_id = ? AND status = 'Active')
        UNION ALL
        (SELECT
            name AS member_name,
            company_id,
            user_id AS member_id,
            company_name
         FROM users_details_view
         WHERE company_id = ? AND status = 'Active')
        ORDER BY member_name ASC
    ";

		$stmt = $this->conn->prepare($sql);
		$stmt->bind_param('ii', $id, $id);
		$stmt->execute();

		$result = $stmt->get_result();
		$rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

		$stmt->close();
		return $rows;
	}





	public function getTags()
	{
		$sqlQuery = "SELECT *
        FROM " . $this->tagTable;

		$stmt = $this->conn->prepare($sqlQuery);
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

	public function getAllCompaniesMembers()
	{
		$sqlQuery = "SELECT *
                 FROM company_member_view
                 ORDER BY company_name, name";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();

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

		return $companies;
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

	public function getPartByGroups($id)
	{
		$sqlQuery = "SELECT *
        FROM " . $this->userPartsTable . " WHERE users_groups_id = " . $id;

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}


	public function getAllMarkingTags(int $admin_id, int $company_id)
	{
		$sqlQuery = "
        SELECT id,marking_tag
        FROM marking_tags
        WHERE (admin_id = ? AND company_id = ?)
           OR isGeneral = 1
        ORDER BY id DESC
    ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("ii", $admin_id, $company_id);
		$stmt->execute();

		return $stmt->get_result();
	}

	public function getAdminMarkingTags(int $admin_id, int $company_id)
	{
		$sqlQuery = "
        SELECT *
        FROM marking_tags
        WHERE (admin_id = ? AND company_id = ?)
        ORDER BY id DESC
    ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bind_param("ii", $admin_id, $company_id);
		$stmt->execute();

		return $stmt->get_result();
	}


}
