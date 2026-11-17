<?php
///template/global/menu.php
?>
<!-- ========== Horizontal Menu Start ========== -->
<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <?php renderMenu($rbacClass, $permissions); ?>
                    <?php if ($rbacClass->checkPermissionSubpartByName('users', 'members', 'systems')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="./customer_portal_admin">
                                <i class="uil-shield-check"></i><?php echo _lang['cp_admin_menu']; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($rbacClass->checkPermissionSubpartByName('admins', 'members', 'systems')): ?>
                        <li class="nav-item"><a class="nav-link" href="./organization_chart"><i class="uil-sitemap"></i>چارت سازمانی</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!-- ========== Horizontal Menu End ========== -->
<div class="content-page">