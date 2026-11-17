<?php
///controller/global/menu.php

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();

$rbacClass = new RBAC($db);


// acl menu
$permissions = [
    'dashboard' => [
        'icon' => 'uil-dashboard',
        'parts' => [
            'bi_users' => './bi_users',
            'bi_admins' => './bi_admins',
            'bi_tickets' => './bi_tickets',
            'activity_report' => './activity_report',
            'sla_report' => './sla_report',
            'bi_ticket_admins_kpi' => './bi_ticket_admins_kpi',
            'bi_admin_productivity' => './bi_admin_productivity',
        ]
    ],
    'access' => [
        'icon' => 'uil-arrows-shrink-h',
        'parts' => [
            'user_access' => './user_access',
            'user_stracture' => './user_stracture',
            'user_operation' => './user_operation',
            'user_view' => './user_view'
        ]
    ],
    'workflow' => [
        'icon' => 'uil-servers',
        'parts' => [
            'ticket_workflow' => './workflow',
            'scheduling' => './scheduling',
            'file_manager' => './file_manager',
            'man_hour' => './man_hour',
        ]
    ],
    'inbox' => [
        'icon' => 'uil-briefcase',
        'parts' => [
            'tickets' => './tickets',
            'priority_list' => './priority_list',
            'notifications' => './notifications',
            'events' => './events',
            'kanban_board' => './kanban_board',
            'daily_report' => './daily_report',
            'chat_center' => './chat_center',
            'marking_tags' => './marking_tags',
            'department_current_works' => './department_current_works',
            
        ]
    ],
    'systems' => [
        'icon' => 'uil-game-structure',
        'parts' => [
            'operations' => './operations',
            'conditions' => './conditions',
            'todo_list' => './todo_list',
            'structure' => [
                'activity' => './activity',
                'company' => './company',
                'unit' => './unit',
                'type' => './type'
            ],
            'members' => [
                'users' => './users',
                'admins' => './admins'
            ],
            'users_parts' => [
                'users_groups' => './users_groups',
                'users_parts' => './users_parts',
                'users_subparts' => './users_subparts'
            ],
            'admins_parts' => [
                'admins_groups' => './admins_groups',
                'admins_parts' => './admins_parts',
                'admins_subparts' => './admins_subparts'
            ]
        ]
            ],
            'project' => [
                'icon' => 'uil-package',
                'parts' => [ 'projects' => './projects' ]
            ]
];

// Menu visibility uses the exact Part/Subpart permission required by the target page.
// A Group or parent Part alone does not grant access to its child pages.
function getAuthorizedMenuItems($rbacClass, $group, $parts)
{
    $visible = [];
    foreach ($parts as $part => $target) {
        if (is_array($target)) {
            $children = [];
            foreach ($target as $subpart => $url) {
                if ($rbacClass->checkPermissionSubpartByName($subpart, $part, $group)) {
                    $children[$subpart] = $url;
                }
            }
            if ($children) {
                $visible[$part] = $children;
            }
        } elseif ($rbacClass->checkPermissionPartByName($part, $group)) {
            $visible[$part] = $target;
        }
    }
    return $visible;
}

function renderMenu($rbacClass, $permissions)
{
    foreach ($permissions as $group => $data) {
        $visible = getAuthorizedMenuItems($rbacClass, $group, $data['parts']);
        if (!$visible) {
            continue;
        }
        echo '<li class="nav-item dropdown">';
        echo '<a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-' . htmlspecialchars($group, ENT_QUOTES, 'UTF-8') . '" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        echo '<i class="' . htmlspecialchars($data['icon'], ENT_QUOTES, 'UTF-8') . '"></i>';
        echo htmlspecialchars(_lang[$group] ?? $group, ENT_QUOTES, 'UTF-8');
        echo '<div class="arrow-down"></div></a>';
        echo '<div class="dropdown-menu" aria-labelledby="topnav-' . htmlspecialchars($group, ENT_QUOTES, 'UTF-8') . '">';
        foreach ($visible as $part => $target) {
            if (is_array($target)) {
                echo '<div class="dropdown">';
                echo '<a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-' . htmlspecialchars($group . '-' . $part, ENT_QUOTES, 'UTF-8') . '" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                echo htmlspecialchars(_lang[$part] ?? $part, ENT_QUOTES, 'UTF-8');
                echo '<div class="arrow-down"></div></a>';
                echo '<div class="dropdown-menu" aria-labelledby="topnav-' . htmlspecialchars($group . '-' . $part, ENT_QUOTES, 'UTF-8') . '">';
                foreach ($target as $subpart => $url) {
                    echo '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" class="dropdown-item">';
                    echo htmlspecialchars(_lang[$subpart] ?? $subpart, ENT_QUOTES, 'UTF-8');
                    echo '</a>';
                }
                echo '</div></div>';
            } else {
                echo '<a href="' . htmlspecialchars($target, ENT_QUOTES, 'UTF-8') . '" class="dropdown-item">';
                // Preserve the established menu label, while checking the real user_access permission.
                $labelKey = ($group === 'access' && $part === 'user_access') ? 'role' : $part;
                echo htmlspecialchars(_lang[$labelKey] ?? $labelKey, ENT_QUOTES, 'UTF-8');
                echo '</a>';
            }
        }
        echo '</div></li>';
    }
}
