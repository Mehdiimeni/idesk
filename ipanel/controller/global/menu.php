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
            'role' => './user_access',
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
                'tag' => './tag',
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

function checkPermissions($rbacClass, $permissions)
{
    foreach ($permissions as $group => $parts) {
        if ($rbacClass->checkPermissionGroupByName($group)) {
            return true;
        }
        foreach ($parts as $part => $subparts) {
            if (is_array($subparts)) {
                if ($rbacClass->checkPermissionPartByName($part, $group)) {
                    return true;
                }
                foreach ($subparts as $subpart => $url) {
                    if ($rbacClass->checkPermissionSubpartByName($subpart, $part, $group)) {
                        return true;
                    }
                }
            } else {
                if ($rbacClass->checkPermissionPartByName($part, $group)) {
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
                            if ($rbacClass->checkPermissionSubpartByName($subpart, $part, $group)) {
                                echo '<a href="' . $url . '" class="dropdown-item">';
                                echo _lang[$subpart];
                                echo '</a>';
                            }
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    if ($rbacClass->checkPermissionPartByName($part, $group)) {
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
