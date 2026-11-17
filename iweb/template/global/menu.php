<?php
///template/global/horizontal_menu.php
?>

<!-- ========== Horizontal Menu Start ========== -->
<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
            <ul class="navbar-nav">
                    <?php renderMenu($rbacClass, $permissions); ?>
                    <?php if (!empty($customerPortalMenuContext['company_workflow_enabled']) && !empty($customerPortalMenuContext['profile_valid'])): ?>
                        <?php
                        $cpMenuRole = (string)($customerPortalMenuContext['portal_role'] ?? '');
                        $cpMenuIsAdmin = $cpMenuRole === \iweb\model\CustomerPortalService::ROLE_ADMIN;
                        $cpMenuIsManager = $cpMenuRole === \iweb\model\CustomerPortalService::ROLE_MANAGER;
                        ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-customer-portal" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="uil-layer-group"></i><?php echo _lang['cp_title']; ?><div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-customer-portal">
                                <a href="./customer_portal" class="dropdown-item"><?php echo _lang['cp_dashboard']; ?></a>
                                <a href="./customer_portal?view=drafts" class="dropdown-item"><?php echo _lang['cp_requests']; ?></a>
                                <?php if (!empty($customerPortalMenuContext['can_create_draft'])): ?><a href="./customer_portal?view=new" class="dropdown-item"><?php echo _lang['cp_new_request']; ?></a><?php endif; ?>
                                <?php if ($cpMenuIsAdmin): ?>
                                    <a href="./customer_portal?view=sections" class="dropdown-item"><?php echo _lang['cp_sections']; ?></a>
                                    <a href="./customer_portal?view=company_tickets" class="dropdown-item"><?php echo _lang['cp_company_tickets']; ?></a>
                                <?php endif; ?>
                                <?php if ($cpMenuIsAdmin || $cpMenuIsManager): ?>
                                    <a href="./customer_portal?view=users" class="dropdown-item"><?php echo _lang['cp_users']; ?></a>
                                    <a href="./customer_portal?view=reports" class="dropdown-item"><?php echo _lang['cp_reports']; ?></a>
                                <?php endif; ?>
                                <?php if ($cpMenuIsAdmin): ?>
                                    <a href="./customer_portal?view=audit" class="dropdown-item"><?php echo _lang['cp_audit']; ?></a>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!-- ========== Horizontal Menu End ========== -->
<div class="content-page">