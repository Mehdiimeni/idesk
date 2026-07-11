<?php

class DatabaseHandler
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function serializeField($field, $value)
    {
        return $field == 'password' ? password_hash(htmlspecialchars(strip_tags($value)), PASSWORD_BCRYPT) : serialize(htmlspecialchars($value));
    }


    private function bindValues($types, $values)
    {
        $bindValues = [];
        $bindValues[] = $types;
        foreach ($values as $key => $value) {
            $bindValues[] = &$values[$key];
        }
        return $bindValues;
    }


    public function insertData($table_set, $arrData, $unique_fields = '')
    {
        $insertFields = [];
        $insertValues = [];
        $types = "";

        foreach ($arrData as $field => $value) {
            if (in_array($field, ['password', 'stracture', 'operation', 'parts']) && !empty($value)) {
                $insertFields[] = $field;
                $insertValues[] = $this->serializeField($field, $value);
                $types .= 's';
            } elseif ($field === $unique_fields && !empty($value)) {
                foreach ($value as $uniqueField) {
                    if (isset($arrData[$uniqueField])) {
                        $insertFields[] = $uniqueField;
                        $insertValues[] = htmlspecialchars(strip_tags($arrData[$uniqueField]));
                        $types .= stripos($uniqueField, 'id') !== false ? 'i' : 's';
                    } else {
                        return "Error: Unique field $uniqueField is missing in data.";
                    }
                }
            } elseif (!in_array($field, [$table_set, $unique_fields])) {
                $insertFields[] = $field;
                $insertValues[] = htmlspecialchars(strip_tags($value));
                $types .= stripos($field, 'id') !== false ? 'i' : 's';
            }
        }

        $placeholders = rtrim(str_repeat('?, ', count($insertFields)), ', ');
        $fields = implode(', ', $insertFields);

        $stmt = $this->db->prepare("INSERT INTO $table_set ($fields) VALUES ($placeholders)");

        if ($stmt === false) {
            return "Error in preparing SQL statement.";
        }

        $bindParamsRefs = $this->bindValues($types, $insertValues);
        call_user_func_array([$stmt, 'bind_param'], $bindParamsRefs);

        if ($stmt->execute()) {

            $insertedId = $stmt->insert_id;
            $stmt->close();

            return array('message' => 'Data inserted successfully.', 'insert_id' => $insertedId);

        } else {
            return "Error in executing SQL statement.";
        }
    }

    public function updateData($table_set, $arrData, $whereCondition, $unique_fields = '')
    {
        $updateFields = [];
        $types = "";
        $updateValues = [];

        foreach ($arrData as $field => $value) {
            if (in_array($field, ['password', 'stracture', 'operation', 'parts']) && !empty($value)) {
                $updateFields[] = "$field = ?";
                $updateValues[] = $this->serializeField($field, $value);
                $types .= 's';
            } elseif ($field === $unique_fields && !empty($value)) {
                foreach ($value as $uniqueField) {
                    if (isset($arrData[$uniqueField])) {
                        $updateFields[] = "$uniqueField = ?";
                        $updateValues[] = htmlspecialchars(strip_tags($arrData[$uniqueField]));
                        $types .= stripos($uniqueField, 'id') !== false ? 'i' : 's';
                    } else {
                        return "Error: Unique field $uniqueField is missing in data.";
                    }
                }
            } elseif (!in_array($field, [$table_set, $unique_fields])) {
                $updateFields[] = "$field = ?";
                $updateValues[] = htmlspecialchars(strip_tags($value));
                $types .= stripos($field, 'id') !== false ? 'i' : 's';
            }
        }

        $fields = implode(', ', $updateFields);

        $stmt = $this->db->prepare("UPDATE $table_set SET $fields WHERE $whereCondition");

        if ($stmt === false) {
            return "Error in preparing SQL statement.";
        }

        $bindParams = $this->bindValues($types, $updateValues);
        call_user_func_array([$stmt, 'bind_param'], $bindParams);

        if ($stmt->execute()) {
            $stmt->close();
            return "Data updated successfully.";
        } else {
            return "Error in executing SQL statement.";
        }
    }


    public function deleteData($table_set, $whereCondition)
    {
        $stmt = $this->db->prepare("DELETE FROM $table_set WHERE $whereCondition");

        if ($stmt === false) {
            return "Error in preparing SQL statement.";
        }

        if ($stmt->execute()) {
            return "Data deleted successfully.";
        } else {
            return "Error in executing SQL statement.";
        }
    }
}

?>