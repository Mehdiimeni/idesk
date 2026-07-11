<?php
class RBAC
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function checkPermission($userId, $permission, $type)
    {
        $userDetails = $this->getUserDetails($userId);

        if ($userDetails) {
            $rbac_id = $userDetails["rbac_id"];

            if ($userDetails["all_rbac"])
                return true;

            switch ($type) {
                case 'S':
                    return $this->checkPermissionStructure($rbac_id, $permission);
                case 'P':
                    return $this->checkPermissionPart($rbac_id, $permission);
                case 'O':
                    return $this->checkPermissionOperation($rbac_id, $permission);
                default:
                    return false;
            }
        } else {
            return false;
        }
    }

    private function checkPermissionStructure($rbac_id, $permission)
    {
        $structure = $this->getPermissionData('permissions_structure', 'structure', $rbac_id);
        return $this->checkPermissionInData($structure, $permission);
    }

    private function checkPermissionPart($rbac_id, $permission, $type = 'a')
    {

        $parts = $this->getPermissionData('permissions_operation', 'parts', $rbac_id);

        return $this->checkPermissionInDataType($parts, $permission, $type);
    }

    private function checkPermissionOperation($rbac_id, $permission, $type = 'a')
    {
        $operation = $this->getPermissionData('permissions_operation', 'operation', $rbac_id);
        return $this->checkPermissionInData($operation, $permission);
    }

    public function getRbacInfoByOperationName($operationName)
    {
        $sqlQuery = "SELECT id FROM operations WHERE operations.operation_name = ?";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("s", $operationName);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        $operationsResult = $result->fetch_assoc();

        if ($result->num_rows > 0) {

            $operationId = $operationsResult['id'];


            $sqlQuery = "select operation,rbac_id from permissions_operation";

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();

            $result = $stmt->get_result();
            $stmt->close();

            $arrRbacId = array();

            while ($permissionsOperations = $result->fetch_assoc()) {

                if ($this->checkPermissionInData($permissionsOperations['operation'], $operationId)) {
                    $arrRbacId[] = $permissionsOperations['rbac_id'];
                }
            }


            if (!empty($arrRbacId)) {
                $placeholders = implode(",", array_fill(0, count($arrRbacId), "?"));
                $sqlQuery = "SELECT id, rbac_name FROM rbac WHERE id IN ($placeholders)";
            } else {
                return null;;
            }


            $stmt = $this->conn->prepare($sqlQuery);

            // bind parameters
            $types = str_repeat('i', count($arrRbacId));  // 'i' represents integer
            $stmt->bind_param($types, ...$arrRbacId);

            $stmt->execute();

            $result = $stmt->get_result();
            $stmt->close();

            return $result;
        } else {
            return null;
        }
    }


    public function getUsersByOperationName($operationName)
    {
        $sqlQuery = "SELECT id FROM operations WHERE operations.operation_name = ?";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("s", $operationName);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        $operationsResult = $result->fetch_assoc();
        if ($result->num_rows > 0) {

            $operationId = $operationsResult['id'];


            $sqlQuery = "select operation,rbac_id from permissions_operation";

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();

            $result = $stmt->get_result();
            $stmt->close();

            $arrRbacId = array();
            while ($permissionsOperations = $result->fetch_assoc()) {

                if ($this->checkPermissionInData($permissionsOperations['operation'], $operationId)) {
                    $arrRbacId[] = $permissionsOperations['rbac_id'];
                }
            }

            $placeholders = implode(",", array_fill(0, count($arrRbacId), "?"));

            $sqlQuery = "SELECT id, name FROM users WHERE rbac_id IN ($placeholders)";

            $stmt = $this->conn->prepare($sqlQuery);

            // bind parameters
            $types = str_repeat('i', count($arrRbacId));  // 'i' represents integer
            $stmt->bind_param($types, ...$arrRbacId);

            $stmt->execute();

            $result = $stmt->get_result();
            $stmt->close();

            return $result;
        } else {
            return null;
        }
    }

    public function insertOperationIfNotExists($operationName)
    {
        $sqlCheckQuery = "SELECT * FROM operations WHERE operation_name = ?";
        $stmtCheck = $this->conn->prepare($sqlCheckQuery);

        if ($stmtCheck === false) {
            return false;
        }

        $stmtCheck->bind_param("s", $operationName);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($result->num_rows > 0) {
            $stmtCheck->close();
            return false;
        }

        $stmtCheck->close();
        $sqlInsertQuery = "INSERT INTO operations (operation_name, operation_description) VALUES (?, ?)";
        $stmtInsert = $this->conn->prepare($sqlInsertQuery);

        if ($stmtInsert === false) {
            return false;
        }

        $operationDescription = 'not define !';
        $stmtInsert->bind_param("ss", $operationName, $operationDescription);

        if ($stmtInsert->execute()) {
            $stmtInsert->close();
            return true;
        }

        $stmtInsert->close();
        return false;
    }


    public function getAdminsByOperationName($operationName)
    {
        $currentAdminId = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : 0;

        $sqlQuery = "SELECT id FROM operations WHERE operation_name = ? LIMIT 1";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("s", $operationName);
        $stmt->execute();

        $result = $stmt->get_result();
        $operationsResult = $result->fetch_assoc();
        $stmt->close();

        if (!$operationsResult) {
            $this->insertOperationIfNotExists($operationName);
            return null;
        }

        $operationId = intval($operationsResult['id']);

        $sqlQuery = "SELECT operation, rbac_id FROM permissions_operation";

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();

        $result = $stmt->get_result();

        $arrRbacId = [];

        while ($permissionsOperations = $result->fetch_assoc()) {
            if ($this->checkPermissionInData($permissionsOperations['operation'], $operationId)) {
                $arrRbacId[] = intval($permissionsOperations['rbac_id']);
            }
        }

        $stmt->close();

        $arrRbacId = array_values(array_unique($arrRbacId));

        if (empty($arrRbacId)) {
            return false;
        }

        $placeholders = implode(",", array_fill(0, count($arrRbacId), "?"));

        $sqlQuery = "
        SELECT id, name 
        FROM admins 
        WHERE rbac_id IN ($placeholders)
          AND status = 'Active'
          AND id <> ?
        ORDER BY name
    ";

        $stmt = $this->conn->prepare($sqlQuery);

        $types = str_repeat('i', count($arrRbacId)) . 'i';
        $params = array_merge($arrRbacId, [$currentAdminId]);

        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        return $result->num_rows > 0 ? $result : false;
    }

    public function checkPermissionOperationByName($operation_name, $type = 'a')
    {
        $this->insertOperationIfNotExists($operation_name);
        if ($type == 'u') {
            $userDetails = $this->getUserDetails($_SESSION['user_id']);
            if ($userDetails["all_rbac"])
                return true;
        }

        if ($type == 'a') {
            $adminDetails = $this->getAdminDetails($_SESSION['admin_id']);
            if ($adminDetails["all_rbac"])
                return true;
        }

        $sqlQuery = "SELECT id FROM operations WHERE operation_name = ?";
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bind_param("s", $operation_name);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        $operationsResult = $result->fetch_assoc();
        if ($result->num_rows > 0) {
            $operationId = $operationsResult['id'];


            return $this->checkPermissionOperation($_SESSION["rbac_id"], $operationId, $type);
        } else {
            return null;
        }
    }

    public function checkPermissionOperationByNameAndId($operation_name, $admin_id)
    {


      
            $adminDetails = $this->getAdminDetailsRback($admin_id);
            if ($adminDetails["all_rbac"])
                return true;
        

        $sqlQuery = "SELECT id FROM operations WHERE operation_name = ?";
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bind_param("s", $operation_name);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        $operationsResult = $result->fetch_assoc();
        if ($result->num_rows > 0) {
            $operationId = $operationsResult['id'];


            return $this->checkPermissionOperation($adminDetails["rbac_id"], $operationId, "a");
        } else {
            return null;
        }
    }


    function checkIdInArrayExists($array, $id)
    {
        foreach ($array as $item) {
            if (isset($item['id']) && $item['id'] === $id) {
                return true;
            }
        }
        return false;
    }


    /// group part

    public function getGroupIdByName($group, $type)
    {
        $this->checkGroupCaption($group, $type);

        $prefix = $type == "a" ? "admins" : "users";

        $sqlQuery = "SELECT id FROM " . $prefix . "_groups WHERE " . $prefix . "_groups_caption = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("s", $group);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        $groupResult = $result->fetch_assoc();
        return $groupResult["id"];
    }

    public function checkGroupCaption($caption, $type)
    {
        $prefix = $type == "a" ? "admins" : "users";

        $sqlQuery = "SELECT id FROM " . $prefix . "_groups WHERE " . $prefix . "_groups_caption = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("s", $caption);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 0) {
            $sqlQuery = "INSERT INTO " . $prefix . "_groups (`" . $prefix . "_groups_name`, `" . $prefix . "_groups_caption`, `" . $prefix . "_groups_description`) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sqlQuery);
            if ($stmt === false) {
                return false;
            }

            $defaultDescription = 'not define !';
            $stmt->bind_param("sss", $caption, $caption, $defaultDescription);

            if ($stmt->execute()) {
                $stmt->close();
                return true;
            }
        }
        return true;
    }

    public function checkPermissionGroupTable($groupId, $type)
    {
        $rbacId = $_SESSION["rbac_id"];
        $prefix = $type == "a" ? "ag" : "ug";


        $sqlQuery = "SELECT parts FROM permissions_operation WHERE rbac_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $rbacId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $permissionResult = $result->fetch_assoc();
            $allParts = unserialize($permissionResult['parts']);

            return $this->checkIdInArrayExists($allParts, $prefix . $groupId);
        }
        return false;
    }

    public function checkPermissionGroupByName($caption, $type = 'a')
    {
        $this->checkGroupCaption($caption, $type);

        $sqlQuery = "";
        if ($type == 'u') {
            $userDetails = $this->getUserDetails($_SESSION['user_id']);
            if ($userDetails["all_rbac"]) return true;
            $sqlQuery = "SELECT id FROM users_groups WHERE users_groups_caption = ?";
        } else if ($type == 'a') {
            $adminDetails = $this->getAdminDetails($_SESSION['admin_id']);
            if ($adminDetails["all_rbac"]) return true;
            $sqlQuery = "SELECT id FROM admins_groups WHERE admins_groups_caption = ?";
        }

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("s", $caption);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $groupResult = $result->fetch_assoc();
            $groupId = $groupResult['id'];
            return $this->checkPermissionGroupTable($groupId, $type);
        }
        return false;
    }
    /// part part

    public function getPartIdByName($part, $group, $type)
    {
        $groupId = $this->getGroupIdByName($group, $type);
        $this->checkPartCaption($part, $groupId, $type);

        $prefix = $type == "a" ? "admins" : "users";

        $sqlQuery = "SELECT id FROM " . $prefix . "_parts WHERE " . $prefix . "_parts_caption = ? and " . $prefix . "_groups_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("si", $part, $groupId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        $groupResult = $result->fetch_assoc();
        return $groupResult["id"];
    }
    public function checkPartCaption($caption, $groupId, $type)
    {
        $prefix = $type == "a" ? "admins" : "users";

        $sqlQuery = "SELECT id FROM " . $prefix . "_parts WHERE " . $prefix . "_parts_caption = ? and " . $prefix . "_groups_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("si", $caption, $groupId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 0) {
            $sqlQuery = "INSERT INTO " . $prefix . "_parts (`" . $prefix . "_parts_name`, `" . $prefix . "_parts_caption`, `" . $prefix . "_parts_description`, `" . $prefix . "_groups_id`) VALUES (?, ?, ?,?)";
            $stmt = $this->conn->prepare($sqlQuery);
            if ($stmt === false) {
                return false;
            }

            $defaultDescription = 'not define !';
            $stmt->bind_param("sssi", $caption, $caption, $defaultDescription, $groupId);

            if ($stmt->execute()) {
                $stmt->close();
                return true;
            }
        }
        return true;
    }

    public function checkPermissionPartTable($partId, $type)
    {
        $rbacId = $_SESSION["rbac_id"];
        $prefix = $type == "a" ? "ap" : "up";

        $sqlQuery = "SELECT parts FROM permissions_operation WHERE rbac_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $rbacId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $permissionResult = $result->fetch_assoc();
            $allParts = unserialize($permissionResult['parts']);

            return $this->checkIdInArrayExists($allParts, $prefix . $partId);
        }
        return false;
    }

    public function checkPermissionPartByName($caption, $group, $type = 'a')
    {
        $groupId = $this->getGroupIdByName($group, $type);
        $this->checkPartCaption($caption, $groupId, $type);

        $sqlQuery = "";
        if ($type == 'u') {
            $userDetails = $this->getUserDetails($_SESSION['user_id']);
            if ($userDetails["all_rbac"]) return true;
            $sqlQuery = "SELECT id FROM users_parts WHERE users_parts_caption = ? and users_groups_id = ?";
        } else if ($type == 'a') {
            $adminDetails = $this->getAdminDetails($_SESSION['admin_id']);
            if ($adminDetails["all_rbac"]) return true;
            $sqlQuery = "SELECT id FROM admins_parts WHERE admins_parts_caption = ? and  admins_groups_id = ?";
        }

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("si", $caption, $groupId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $partResult = $result->fetch_assoc();
            $partId = $partResult['id'];
            return $this->checkPermissionPartTable($partId, $type);
        }
        return false;
    }

    ///subpart part
    public function checkSubpartCaption($caption, $partId, $type)
    {
        $prefix = $type == "a" ? "admins" : "users";

        $sqlQuery = "SELECT id FROM " . $prefix . "_subparts WHERE " . $prefix . "_subparts_caption = ? and " . $prefix . "_parts_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("si", $caption, $partId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 0) {
            $sqlQuery = "INSERT INTO " . $prefix . "_subparts (`" . $prefix . "_subparts_name`, `" . $prefix . "_subparts_caption`, `" . $prefix . "_subparts_description`, `" . $prefix . "_parts_id`) VALUES (?, ?, ?,?)";
            $stmt = $this->conn->prepare($sqlQuery);
            if ($stmt === false) {
                return false;
            }

            $defaultDescription = 'not define !';
            $stmt->bind_param("sssi", $caption, $caption, $defaultDescription, $partId);

            if ($stmt->execute()) {
                $stmt->close();
                return true;
            }
        }
        return true;
    }

    public function checkPermissionSubpartTable($subpartId, $type)
    {
        $rbacId = $_SESSION["rbac_id"];
        $prefix = $type == "a" ? "as" : "us";

        $sqlQuery = "SELECT parts FROM permissions_operation WHERE rbac_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("i", $rbacId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $permissionResult = $result->fetch_assoc();
            $allParts = unserialize($permissionResult['parts']);

            return $this->checkIdInArrayExists($allParts, $prefix . $subpartId);
        }
        return false;
    }

    public function checkPermissionSubpartByName($caption, $part, $group, $type = 'a')
    {
        $partId = $this->getPartIdByName($part, $group, $type);

        $this->checkSubpartCaption($caption, $partId, $type);

        $sqlQuery = "";
        if ($type == 'u') {
            $userDetails = $this->getUserDetails($_SESSION['user_id']);
            if ($userDetails["all_rbac"]) return true;
            $sqlQuery = "SELECT id FROM users_subparts WHERE users_subparts_caption = ? and users_parts_id = ?";
        } else if ($type == 'a') {
            $adminDetails = $this->getAdminDetails($_SESSION['admin_id']);
            if ($adminDetails["all_rbac"]) return true;
            $sqlQuery = "SELECT id FROM admins_subparts WHERE admins_subparts_caption = ? and admins_parts_id = ?";
        }

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("si", $caption, $partId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $subpartResult = $result->fetch_assoc();
            $subpartId = $subpartResult['id'];
            return $this->checkPermissionSubpartTable($subpartId, $type);
        }
        return false;
    }


    private function checkPermissionInData($serializedData, $permission)
    {

        if ($serializedData != null) {
            $decodedData = unserialize($serializedData);
            if (is_array($decodedData)) {
                return in_array($permission, $decodedData);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    private function checkPermissionInDataType($serializedData, $permission, $type)
    {

        if ($serializedData != null) {
            $decodedData = unserialize($serializedData);
            if (is_array($decodedData)) {
                return in_array($type . $permission, $decodedData);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }


    private function getPermissionData($table, $field, $rbac_id)
    {
        $sqlQuery = "SELECT $field FROM $table WHERE rbac_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bind_param("i", $rbac_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        $arrResult = $result->fetch_assoc();
        if ($result->num_rows > 0) {
            return $arrResult[$field];
        } else {
            return null;
        }
    }

    public function getPermissionById($permission_id)
    {
        $sqlQuery = "SELECT * FROM permissions_operation WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bind_param("i", $permission_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }



    public function getUserDetails($userId)
    {

        $sqlQuery = "SELECT u.status, u.rbac_id, r.all_rbac, u.unit_id, u.role 
        FROM users u 
        JOIN rbac r ON u.rbac_id = r.id 
        WHERE u.id = ?";
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();


        $arrResult = $result->fetch_assoc();

        if ($result->num_rows > 0) {
            if ($arrResult['status'] === 'Active') {
                $_SESSION['userDetails'] = $arrResult;
                return $arrResult;
            } else {
                return false;
            }
        } else {
            return null;
        }
    }

    public function getAdminDetails($adminId)
    {

        $sqlQuery = "SELECT u.status, u.rbac_id, r.all_rbac, u.unit_id, u.role 
        FROM admins u 
        JOIN rbac r ON u.rbac_id = r.id 
        WHERE u.id = ?";
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bind_param("i", $adminId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();


        $arrResult = $result->fetch_assoc();

        if ($result->num_rows > 0) {
            if ($arrResult['status'] === 'Active') {
                $_SESSION['adminDetails'] = $arrResult;
                return $arrResult;
            } else {
                return false;
            }
        } else {
            return null;
        }
    }

    public function getAdminDetailsRback($adminId)
    {

        $sqlQuery = "SELECT u.status, u.rbac_id, r.all_rbac, u.unit_id, u.role 
        FROM admins u 
        JOIN rbac r ON u.rbac_id = r.id 
        WHERE u.id = ?";
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bind_param("i", $adminId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();


        $arrResult = $result->fetch_assoc();

        if ($result->num_rows > 0) {
            if ($arrResult['status'] === 'Active') {
                return $arrResult;
            } else {
                return false;
            }
        } else {
            return null;
        }
    }
}
