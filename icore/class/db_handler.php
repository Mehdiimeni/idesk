<?php

class DatabaseHandler
{
    private $db;

    private $specialFields = [
        'password',
        'stracture',
        'operation',
        'parts'
    ];

    private $specialFieldsMap = [];

    private $identifierCache = [];

    public function __construct($db)
    {
        $this->db = $db;

        /*
         * استفاده از isset به‌جای in_array
         * برای بررسی سریع‌تر فیلدهای خاص
         */
        foreach ($this->specialFields as $specialField) {
            $this->specialFieldsMap[$specialField] = true;
        }
    }

    private function sanitizeValue($value)
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            $jsonValue = json_encode(
                $value,
                JSON_UNESCAPED_UNICODE
            );

            return htmlspecialchars(
                $jsonValue,
                ENT_QUOTES,
                'UTF-8'
            );
        }

        $value = strip_tags((string) $value);

        return htmlspecialchars(
            $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    private function serializeField($field, $value)
    {
        $sanitizedValue = $this->sanitizeValue($value);

        if ($field === 'password') {
            return password_hash(
                $sanitizedValue,
                PASSWORD_BCRYPT
            );
        }

        return serialize($sanitizedValue);
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

    private function bindValues($types, &$values)
    {
        $bindValues = [];
        $bindValues[] = $types;

        foreach ($values as $key => &$value) {
            $bindValues[] = &$value;
        }

        unset($value);

        return $bindValues;
    }

    private function isValidIdentifier($name)
    {
        if (!is_string($name)) {
            return false;
        }

        /*
         * جلوگیری از اجرای مکرر preg_match
         * برای نام‌های تکراری جدول و فیلد
         */
        if (array_key_exists($name, $this->identifierCache)) {
            return $this->identifierCache[$name];
        }

        $isValid = preg_match(
            '/^[a-zA-Z0-9_]+$/',
            $name
        ) === 1;

        $this->identifierCache[$name] = $isValid;

        return $isValid;
    }

    private function buildDataParts($arrData, $unique_fields = '', $mode = 'insert')
    {
        $fields = [];
        $values = [];
        $types = '';
        $usedFields = [];

        $isUpdate = $mode === 'update';

        foreach ($arrData as $field => $value) {

            if (
                $field === $unique_fields &&
                !empty($value) &&
                is_array($value)
            ) {
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

                    if ($isUpdate) {
                        $fields[] = $uniqueField . ' = ?';
                    } else {
                        $fields[] = $uniqueField;
                    }

                    $fieldValue = $arrData[$uniqueField];

                    $values[] = $this->sanitizeValue($fieldValue);
                    $types .= $this->detectType(
                        $uniqueField,
                        $fieldValue
                    );

                    $usedFields[$uniqueField] = true;
                }

                continue;
            }

            if (
                $field === $unique_fields ||
                isset($usedFields[$field])
            ) {
                continue;
            }

            if (!$this->isValidIdentifier($field)) {
                return "Error: Invalid field name $field.";
            }

            if ($isUpdate) {
                $fields[] = $field . ' = ?';
            } else {
                $fields[] = $field;
            }

            if (
                isset($this->specialFieldsMap[$field]) &&
                !empty($value)
            ) {
                $values[] = $this->serializeField(
                    $field,
                    $value
                );

                $types .= 's';
            } else {
                $values[] = $this->sanitizeValue($value);
                $types .= $this->detectType(
                    $field,
                    $value
                );
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

        $parts = $this->buildDataParts(
            $arrData,
            $unique_fields,
            'insert'
        );

        if (is_string($parts)) {
            return $parts;
        }

        if (empty($parts['fields'])) {
            return "Error: No valid fields to insert.";
        }

        $fieldCount = count($parts['fields']);

        $fields = implode(', ', $parts['fields']);

        $placeholders = implode(
            ', ',
            array_fill(0, $fieldCount, '?')
        );

        $sql = "INSERT INTO $table_set ($fields) VALUES ($placeholders)";

        $stmt = $this->db->prepare($sql);

        if ($stmt === false) {
            return "Error in preparing SQL statement: " .
                $this->db->error;
        }

        $bindParamsRefs = $this->bindValues(
            $parts['types'],
            $parts['values']
        );

        $bindResult = call_user_func_array(
            [$stmt, 'bind_param'],
            $bindParamsRefs
        );

        if ($bindResult === false) {
            $error = $stmt->error;
            $stmt->close();

            return "Error in binding SQL parameters: " . $error;
        }

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

        $parts = $this->buildDataParts(
            $arrData,
            $unique_fields,
            'update'
        );

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
            return "Error in preparing SQL statement: " .
                $this->db->error;
        }

        $bindParamsRefs = $this->bindValues(
            $parts['types'],
            $parts['values']
        );

        $bindResult = call_user_func_array(
            [$stmt, 'bind_param'],
            $bindParamsRefs
        );

        if ($bindResult === false) {
            $error = $stmt->error;
            $stmt->close();

            return "Error in binding SQL parameters: " . $error;
        }

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

        $sql = "DELETE FROM $table_set WHERE $whereCondition";

        $stmt = $this->db->prepare($sql);

        if ($stmt === false) {
            return "Error in preparing SQL statement: " .
                $this->db->error;
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