<?php
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

// Read-only, company-scoped organizational structure for internal IPanel admins.
$orgCompanyId = (int) ($_SESSION['company_id'] ?? 0);
$orgUnits = [];
$orgCompanyName = '';
$orgPeopleCount = 0;
if ($orgCompanyId > 0) {
    $orgStmt = $db->prepare('SELECT company_name FROM company_profiles WHERE id = ? LIMIT 1');
    $orgStmt->bind_param('i', $orgCompanyId);
    $orgStmt->execute();
    $orgCompany = $orgStmt->get_result()->fetch_assoc();
    $orgCompanyName = (string) ($orgCompany['company_name'] ?? '');
    $orgStmt->close();

    $orgStmt = $db->prepare('SELECT id, unit_name, unit_type FROM units WHERE company_id = ? ORDER BY unit_name, id');
    $orgStmt->bind_param('i', $orgCompanyId);
    $orgStmt->execute();
    foreach ($orgStmt->get_result()->fetch_all(MYSQLI_ASSOC) as $orgUnit) {
        $orgUnit['managers'] = [];
        $orgUnit['experts'] = [];
        $orgUnit['unknown'] = [];
        $orgUnits[(int) $orgUnit['id']] = $orgUnit;
    }
    $orgStmt->close();

    $orgStmt = $db->prepare("SELECT a.id, a.name, a.status, a.unit_id, a.rbac_id,
                  r.rbac_name, r.manager_id, parent.rbac_name AS parent_name
              FROM admins a
              INNER JOIN units u ON u.id = a.unit_id AND u.company_id = ?
              LEFT JOIN rbac r ON r.id = a.rbac_id AND r.company_id = u.company_id
              LEFT JOIN rbac parent ON parent.id = r.manager_id
              ORDER BY a.name, a.id");
    $orgStmt->bind_param('i', $orgCompanyId);
    $orgStmt->execute();
    foreach ($orgStmt->get_result()->fetch_all(MYSQLI_ASSOC) as $person) {
        $unitId = (int) $person['unit_id'];
        if (!isset($orgUnits[$unitId])) { continue; }
        $name = trim((string) ($person['rbac_name'] ?? ''));
        if ($name === '') { $bucket = 'unknown'; }
        elseif (mb_stripos($name, 'کارشناس') !== false || stripos($name, 'expert') !== false) { $bucket = 'experts'; }
        else { $bucket = 'managers'; }
        $orgUnits[$unitId][$bucket][] = $person;
        ++$orgPeopleCount;
    }
    $orgStmt->close();
}
