<?php

class DatabaseHandler
{
    private $db;

    private $specialFields = ['password', 'stracture', 'operation', 'parts'];

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function sanitizeValue($value)
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return htmlspecialchars(
                json_encode($value, JSON_UNESCAPED_UNICODE),
                ENT_QUOTES,
                'UTF-8'
            );
        }

        return htmlspecialchars(
            strip_tags((string) $value),
            ENT_QUOTES,
            'UTF-8'
        );
    }

    private function serializeField($field, $value)
    {
        if ($field === 'password') {
            return password_hash($this->sanitizeValue($value), PASSWORD_BCRYPT);
        }

        return serialize($this->sanitizeValue($value));
    }

    private function detectType($field, $value)
    {
        if ($value === null) {
            return 's';
        }

        if (stripos($field, 'id') !== false || is_int($value)) {
            return 'i';
        }

        if (is_float($value)) {
            return 'd';
        }

        return 's';
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

    private function isValidIdentifier($name)
    {
        return is_string($name) && preg_match('/^[a-zA-Z0-9_]+$/', $name);
    }

    private function buildDataParts($arrData, $unique_fields = '', $mode = 'insert')
    {
        $fields = [];
        $values = [];
        $types = '';
        $usedFields = [];

        foreach ($arrData as $field => $value) {

            if ($field === $unique_fields && !empty($value) && is_array($value)) {
                foreach ($value as $uniqueField) {

                    if (!array_key_exists($uniqueField, $arrData)) {
                        return "Error: Unique field $uniqueField is missing in data.";
                    }

                    if (!$this->isValidIdentifier($uniqueField)) {
                        return "Error: Invalid field name $uniqueField.";
                    }

                    if (isset($usedFields[$uniqueField])) {
                        continue;
                    }

                    $fields[] = $mode === 'update'
                        ? "$uniqueField = ?"
                        : $uniqueField;

                    $fieldValue = $arrData[$uniqueField];

                    $values[] = $this->sanitizeValue($fieldValue);
                    $types .= $this->detectType($uniqueField, $fieldValue);

                    $usedFields[$uniqueField] = true;
                }

                continue;
            }

            if ($field === $unique_fields || isset($usedFields[$field])) {
                continue;
            }

            if (!$this->isValidIdentifier($field)) {
                return "Error: Invalid field name $field.";
            }

            $fields[] = $mode === 'update'
                ? "$field = ?"
                : $field;

            if (in_array($field, $this->specialFields, true) && !empty($value)) {
                $values[] = $this->serializeField($field, $value);
                $types .= 's';
            } else {
                $values[] = $this->sanitizeValue($value);
                $types .= $this->detectType($field, $value);
            }

            $usedFields[$field] = true;
        }

        return [
            'fields' => $fields,
            'values' => $values,
            'types' => $types
        ];
    }

    public function insertData($table_set, $arrData, $unique_fields = '')
    {
        if (!$this->isValidIdentifier($table_set)) {
            return "Error: Invalid table name.";
        }

        if (empty($arrData) || !is_array($arrData)) {
            return "Error: Invalid data.";
        }

        $parts = $this->buildDataParts($arrData, $unique_fields, 'insert');

        if (is_string($parts)) {
            return $parts;
        }

        if (empty($parts['fields'])) {
            return "Error: No valid fields to insert.";
        }

        $fields = implode(', ', $parts['fields']);
        $placeholders = implode(', ', array_fill(0, count($parts['fields']), '?'));

        $sql = "INSERT INTO $table_set ($fields) VALUES ($placeholders)";

        $stmt = $this->db->prepare($sql);

        if ($stmt === false) {
            return "Error in preparing SQL statement: " . $this->db->error;
        }

        $bindParamsRefs = $this->bindValues($parts['types'], $parts['values']);

        call_user_func_array([$stmt, 'bind_param'], $bindParamsRefs);

        if ($stmt->execute()) {
            $insertedId = $stmt->insert_id;
            $stmt->close();

            return [
                'message' => 'Data inserted successfully.',
                'insert_id' => $insertedId
            ];
        }

        $error = $stmt->error;
        $stmt->close();

        return "Error in executing SQL statement: " . $error;
    }

    public function updateData($table_set, $arrData, $whereCondition, $unique_fields = '')
    {
        if (!$this->isValidIdentifier($table_set)) {
            return "Error: Invalid table name.";
        }

        if (empty($arrData) || !is_array($arrData)) {
            return "Error: Invalid data.";
        }

        if (empty($whereCondition)) {
            return "Error: Where condition is required.";
        }

        $parts = $this->buildDataParts($arrData, $unique_fields, 'update');

        if (is_string($parts)) {
            return $parts;
        }

        if (empty($parts['fields'])) {
            return "Error: No valid fields to update.";
        }

        $fields = implode(', ', $parts['fields']);

        $sql = "UPDATE $table_set SET $fields WHERE $whereCondition";

        $stmt = $this->db->prepare($sql);

        if ($stmt === false) {
            return "Error in preparing SQL statement: " . $this->db->error;
        }

        $bindParamsRefs = $this->bindValues($parts['types'], $parts['values']);

        call_user_func_array([$stmt, 'bind_param'], $bindParamsRefs);

        if ($stmt->execute()) {
            $stmt->close();
            return "Data updated successfully.";
        }

        $error = $stmt->error;
        $stmt->close();

        return "Error in executing SQL statement: " . $error;
    }

    public function deleteData($table_set, $whereCondition)
    {
        if (!$this->isValidIdentifier($table_set)) {
            return "Error: Invalid table name.";
        }

        if (empty($whereCondition)) {
            return "Error: Where condition is required.";
        }

        $stmt = $this->db->prepare("DELETE FROM $table_set WHERE $whereCondition");

        if ($stmt === false) {
            return "Error in preparing SQL statement: " . $this->db->error;
        }

        if ($stmt->execute()) {
            $stmt->close();
            return "Data deleted successfully.";
        }

        $error = $stmt->error;
        $stmt->close();

        return "Error in executing SQL statement: " . $error;
    }
}

?>