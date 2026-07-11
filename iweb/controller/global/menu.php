<?php
///controller/global/horizontal_menu.php
$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$rbacClass = new RBAC($db);

$permissions = [

    'inbox' => [
        'icon' => 'uil-briefcase',
        'parts' => [
            'tickets' => './tickets',
            'kanban_board' => './kanban_board',
            'priority_list' => './priority_list',
            'chat_center' => './chat_center',
            'marking_tags' => './marking_tags',
        ]
    ]
];

function checkPermissions($rbacClass, $permissions)
{
    foreach ($permissions as $group => $parts) {
        if ($rbacClass->checkPermissionGroupByName($group, 'u')) {
            return true;
        }
        foreach ($parts as $part => $subparts) {
            if (is_array($subparts)) {
                if ($rbacClass->checkPermissionPartByName($part, $group, 'u')) {
                    return true;
                }
                foreach ($subparts as $subpart => $url) {
                    if ($rbacClass->checkPermissionSubpartByName($subpart, $part, $group, 'u')) {
                        return true;
                    }
                }
            } else {
                if ($rbacClass->checkPermissionPartByName($part, $group, 'u')) {
                    return true;
                }
            }
        }
    }
    return false;
}

function renderMenu($rbacClass, $permissions)
{
    foreach ($permissions as $group => $data) {
        if (checkPermissions($rbacClass, [$group => $data['parts']])) {
            echo '<li class="nav-item dropdown">';
            echo '<a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-' . $group . '" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            echo '<i class="' . $data['icon'] . '"></i>';
            echo _lang[$group];
            echo '<div class="arrow-down"></div>';
            echo '</a>';
            echo '<div class="dropdown-menu" aria-labelledby="topnav-' . $group . '">';
            foreach ($data['parts'] as $part => $subparts) {
                if (is_array($subparts)) {
                    if (checkPermissions($rbacClass, [$group => [$part => $subparts]])) {
                        echo '<div class="dropdown">';
                        echo '<a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-' . $part . '" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                        echo _lang[$part];
                        echo '<div class="arrow-down"></div>';
                        echo '</a>';
                        echo '<div class="dropdown-menu" aria-labelledby="topnav-' . $part . '">';
                        foreach ($subparts as $subpart => $url) {
                            if ($rbacClass->checkPermissionSubpartByName($subpart, $part, $group, 'u')) {
                                echo '<a href="' . $url . '" class="dropdown-item">';
                                echo _lang[$subpart];
                                echo '</a>';
                            }
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    if ($rbacClass->checkPermissionPartByName($part, $group, 'u')) {
                        echo '<a href="' . $subparts . '" class="dropdown-item">';
                        echo _lang[$part];
                        echo '</a>';
                    }
                }
            }
            echo '</div>';
            echo '</li>';
        }
    }
}
