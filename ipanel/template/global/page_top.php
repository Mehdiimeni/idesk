<?php
///template/global/page_top.php
?>

<body dir="<?php echo ($_COOKIE['adminLanguageDir']); ?>">
    <div class="wrapper">

        <div class="navbar-custom intek-topbar">
            <div class="topbar container-fluid">

                <div class="d-flex align-items-center gap-lg-3 gap-2">

                    <div class="logo-topbar intek-logo-box">
                        <a href="./" class="logo-light">
                            <span class="logo-lg">
                                <img src="../itheme/panel/images/intek.png" alt="intek"
                                    title="<?php echo _lang['my_dashboard']; ?>">
                            </span>
                            <span class="logo-sm">
                                <img src="../itheme/panel/icon/apple-icon-57x57.png" alt="intek"
                                    title="<?php echo _lang['my_dashboard']; ?>">
                            </span>
                        </a>

                        <a href="./" class="logo-dark">
                            <span class="logo-lg">
                                <img src="../itheme/panel/images/intek.png" alt="intek"
                                    title="<?php echo _lang['my_dashboard']; ?>">
                            </span>
                            <span class="logo-sm">
                                <img src="../itheme/panel/icon/apple-icon-57x57.png" alt="intek"
                                    title="<?php echo _lang['my_dashboard']; ?>">
                            </span>
                        </a>
                    </div>

                    <button class="button-toggle-menu intek-icon-btn">
                        <i class="mdi mdi-menu"></i>
                    </button>

                    <button class="navbar-toggle intek-icon-btn" data-bs-toggle="collapse"
                        data-bs-target="#topnav-menu-content">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </button>

                    <?php if ($_SESSION['admin_id'] != 29) { ?>
                        <div class="company-status-box d-none d-lg-flex" id="companyBox">
                            <div class="company-status-name" id="companyName"></div>
                            <div class="company-status-messages">
                                <div id="devMsg" class="msg"></div>
                                <div id="supportMsg" class="msg"></div>
                            </div>
                        </div>
                    <?php } ?>

                </div>

                <ul class="topbar-menu d-flex align-items-center gap-2">

                    <?php if ($rbacClass->checkPermissionOperationByName('language_operation')) { ?>
                        <li class="dropdown">
                            <a class="nav-link dropdown-toggle arrow-none intek-nav-pill" data-bs-toggle="dropdown" href="#"
                                role="button">
                                <i class="ri-global-line"></i>
                                <span class="align-middle d-none d-lg-inline-block">
                                    <?php echo (_lang['language_selection']); ?>
                                </span>
                                <i class="mdi mdi-chevron-down d-none d-sm-inline-block align-middle"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated intek-dropdown">
                                <?php foreach ($allLanguages as $key => $value) { ?>
                                    <a href="javascript:void(0);" data-lang="<?php echo $key; ?>"
                                        class="lang-set dropdown-item">
                                        <i class="ri-translate-2 me-1"></i>
                                        <span><?php echo $value; ?></span>
                                    </a>
                                <?php } ?>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if ($rbacClass->checkPermissionOperationByName('note_operation')) { ?>
                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle arrow-none intek-icon-btn position-relative"
                                data-bs-toggle="dropdown" href="#" role="button">
                                <i class="ri-chat-3-line font-22"></i>
                                <?php if ($totalChatNote > 0) { ?>
                                    <span class="intek-badge-dot"></span>
                                <?php } ?>
                            </a>

                            <div
                                class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0 intek-dropdown">
                                <div class="intek-dropdown-header">
                                    <h6>
                                        <i class="ri-chat-3-line"></i>
                                        <?php echo (_lang['messages']); ?>
                                    </h6>
                                </div>

                                <div class="px-2 py-2" style="max-height: 300px;" data-simplebar>
                                    <?php foreach ($allNewChatMessages as $chatMessage) { ?>
                                        <a href="./chat_center?rId=<?php echo $chatMessage['sender_id']; ?>&rt=<?php echo $chatMessage['sender_type']; ?>"
                                            class="dropdown-item intek-notify-item">
                                            <div class="notify-icon-soft secondary">
                                                <i class="mdi mdi-eye-refresh"></i>
                                            </div>

                                            <div class="flex-grow-1 text-truncate">
                                                <div class="intek-notify-title">
                                                    <?php echo (_lang['message']); ?>
                                                    <small>
                                                        <?php
                                                        $dateConverter = new DateConverter($chatMessage['creation_date'], $config->getNowLanguage('a'));
                                                        echo $dateConverter->convertToShamsi();
                                                        ?>
                                                    </small>
                                                </div>
                                                <div class="intek-notify-subtitle">
                                                    <?php echo $chatMessage['sender_name']; ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                </div>

                                <a href="./chat_center"
                                    class="dropdown-item text-center text-primary border-top py-2 fw-bold">
                                    <?php echo (_lang['view_all']); ?>
                                </a>
                            </div>
                        </li>
                    <?php } ?>

                    <?php if ($rbacClass->checkPermissionOperationByName('note_operation')) { ?>
                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle arrow-none intek-icon-btn position-relative"
                                data-bs-toggle="dropdown" href="#" role="button">
                                <i class="ri-notification-3-line font-22"></i>
                                <?php if ($totalNote > 0) { ?>
                                    <span class="intek-badge-dot"></span>
                                <?php } ?>
                            </a>

                            <div
                                class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0 intek-dropdown">
                                <div class="intek-dropdown-header">
                                    <h6>
                                        <i class="ri-notification-3-line"></i>
                                        <?php echo (_lang['notifications']); ?>
                                    </h6>
                                </div>

                                <div class="px-2 py-2" style="max-height: 300px;" data-simplebar>

                                    <?php while ($forwarderTicket = $allNewForwarderTicket->fetch_assoc()) { ?>
                                        <a href="./notifications?filter=fm" class="dropdown-item intek-notify-item">
                                            <div class="notify-icon-soft info">
                                                <i class="mdi mdi-eye-refresh"></i>
                                            </div>
                                            <div class="flex-grow-1 text-truncate">
                                                <div class="intek-notify-title">
                                                    <?php echo (_lang['assign']); ?>
                                                    <small>
                                                        <?php
                                                        $dateConverter = new DateConverter($forwarderTicket['creation_date'], $config->getNowLanguage('a'));
                                                        echo $dateConverter->convertToShamsi();
                                                        ?>
                                                    </small>
                                                </div>
                                                <div class="intek-notify-subtitle">
                                                    <?php echo $textToolsClass->truncateText($forwarderTicket['forwards_description'], 50); ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>

                                    <?php foreach ($allNewChatMessages as $chatMessage) { ?>
                                        <a href="./chat_center?rId=<?php echo $chatMessage['sender_id']; ?>&rt=<?php echo $chatMessage['sender_type']; ?>"
                                            class="dropdown-item intek-notify-item">
                                            <div class="notify-icon-soft secondary">
                                                <i class="mdi mdi-eye-refresh"></i>
                                            </div>
                                            <div class="flex-grow-1 text-truncate">
                                                <div class="intek-notify-title">
                                                    <?php echo (_lang['message']); ?>
                                                    <small>
                                                        <?php
                                                        $dateConverter = new DateConverter($chatMessage['creation_date'], $config->getNowLanguage('a'));
                                                        echo $dateConverter->convertToShamsi();
                                                        ?>
                                                    </small>
                                                </div>
                                                <div class="intek-notify-subtitle">
                                                    <?php echo $chatMessage['sender_name']; ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>

                                    <?php while ($importantMessages = $allNewImportantMessages->fetch_assoc()) { ?>
                                        <a href="./notifications?filter=imp" class="dropdown-item intek-notify-item">
                                            <div class="notify-icon-soft danger">
                                                <i class="mdi mdi-exclamation-thick"></i>
                                            </div>
                                            <div class="flex-grow-1 text-truncate">
                                                <div class="intek-notify-title">
                                                    <?php echo (_lang['message']); ?>
                                                    <small>
                                                        <?php
                                                        $dateConverter = new DateConverter($importantMessages['creation_date'], $config->getNowLanguage('a'));
                                                        echo $dateConverter->convertToShamsi();
                                                        ?>
                                                    </small>
                                                </div>
                                                <div class="intek-notify-subtitle">
                                                    <?php echo $textToolsClass->truncateText($importantMessages['msg_subject'], 50); ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>

                                    <?php while ($newMessage = $allNewMessages->fetch_assoc()) { ?>
                                        <a href="./notifications?filter=rm" class="dropdown-item intek-notify-item">
                                            <div class="notify-icon-soft warning">
                                                <i class="mdi mdi-note-text-outline"></i>
                                            </div>
                                            <div class="flex-grow-1 text-truncate">
                                                <div class="intek-notify-title">
                                                    <?php echo (_lang['message']); ?>
                                                    <small>
                                                        <?php
                                                        $dateConverter = new DateConverter($newMessage['creation_date'], $config->getNowLanguage('a'));
                                                        echo $dateConverter->convertToShamsi();
                                                        ?>
                                                    </small>
                                                </div>
                                                <div class="intek-notify-subtitle">
                                                    <?php echo $textToolsClass->truncateText($newMessage['msg_subject'], 50); ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>

                                    <?php while ($ticketsNoteUser = $allNewTicketsNoteUser->fetch_assoc()) { ?>
                                        <a href="tickets?ticket_id=<?php echo $encryptorClass->encrypt($ticketsNoteUser['part_id']); ?>&cid=<?php echo ($ticketsNoteUser['id']); ?>"
                                            class="dropdown-item intek-notify-item">
                                            <div class="notify-icon-soft primary">
                                                <i class="mdi mdi-comment-account-outline"></i>
                                            </div>
                                            <div class="flex-grow-1 text-truncate">
                                                <div class="intek-notify-title">
                                                    <?php echo (_lang['comments']); ?>
                                                    <small>
                                                        <?php
                                                        $dateConverter = new DateConverter($ticketsNoteUser['creation_date'], $config->getNowLanguage('a'));
                                                        echo $dateConverter->convertToShamsi();
                                                        ?>
                                                    </small>
                                                </div>
                                                <div class="intek-notify-subtitle">
                                                    <?php echo $textToolsClass->truncateText($ticketsNoteUser['comment_text'], 50); ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>

                                    <?php while ($forwarderTicket = $allNewForwarderTicket->fetch_assoc()) { ?>
                                        <a href="./notifications?filter=fm" class="dropdown-item intek-notify-item">
                                            <div class="notify-icon-soft info">
                                                <i class="mdi mdi-eye-refresh"></i>
                                            </div>
                                            <div class="flex-grow-1 text-truncate">
                                                <div class="intek-notify-title">
                                                    <?php echo (_lang['assign']); ?>
                                                    <small>
                                                        <?php
                                                        $dateConverter = new DateConverter($forwarderTicket['creation_date'], $config->getNowLanguage('a'));
                                                        echo $dateConverter->convertToShamsi();
                                                        ?>
                                                    </small>
                                                </div>
                                                <div class="intek-notify-subtitle">
                                                    <?php echo $textToolsClass->truncateText($forwarderTicket['forwards_description'], 50); ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>

                                    <?php while ($calendar = $allNewCalendar->fetch_assoc()) { ?>
                                        <a href="./events" class="dropdown-item intek-notify-item">
                                            <div class="notify-icon-soft warning">
                                                <i class="mdi mdi-calendar"></i>
                                            </div>
                                            <div class="flex-grow-1 text-truncate">
                                                <div class="intek-notify-title">
                                                    <?php echo (_lang['event']); ?>
                                                </div>
                                                <div class="intek-notify-subtitle">
                                                    <?php echo $textToolsClass->truncateText($calendar['title'], 50); ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>

                                </div>

                                <a href="./notifications?filter=fm"
                                    class="dropdown-item text-center text-primary border-top py-2 fw-bold">
                                    <?php echo (_lang['view_all']); ?>
                                </a>
                            </div>
                        </li>
                    <?php } ?>

                    <li class="d-none d-sm-inline-block">
                        <div class="nav-link intek-icon-btn" id="light-dark-mode" data-bs-toggle="tooltip"
                            data-bs-placement="left" title="Theme Mode">
                            <i class="ri-moon-line font-22"></i>
                        </div>
                    </li>

                    <li class="d-none d-md-inline-block">
                        <a class="nav-link intek-icon-btn" href="#" data-toggle="fullscreen">
                            <i class="ri-fullscreen-line font-22"></i>
                        </a>
                    </li>

                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle arrow-none nav-user intek-user-menu"
                            data-bs-toggle="dropdown" href="#" role="button">
                            <div class="intek-user-avatar">
                                <?php echo mb_substr($_SESSION["name"], 0, 1, 'UTF-8'); ?>
                            </div>

                            <span class="d-lg-flex flex-column gap-1 d-none">
                                <h5 class="my-0"><?php echo ($_SESSION["name"]); ?></h5>
                                <h6 class="my-0 fw-normal"><?php echo ($_SESSION["mobile"]); ?></h6>
                            </span>

                            <i class="mdi mdi-chevron-down d-none d-lg-inline-block"></i>
                        </a>

                        <div
                            class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown intek-dropdown">
                            <a href="./myaccount" class="dropdown-item">
                                <i class="mdi mdi-account-circle me-1"></i>
                                <span><?php echo (_lang['my_account']); ?></span>
                            </a>

                            <a href="./logout" class="dropdown-item text-danger">
                                <i class="mdi mdi-logout me-1"></i>
                                <span><?php echo (_lang['logout']); ?></span>
                            </a>
                        </div>
                    </li>

                </ul>
            </div>
        </div>

        <script>
            const companyData = <?php echo json_encode($companyData, JSON_UNESCAPED_UNICODE); ?>;

            const box = document.getElementById('companyBox');
            const nameEl = document.getElementById('companyName');
            const devEl = document.getElementById('devMsg');
            const supEl = document.getElementById('supportMsg');

            if (box) {
                if (!companyData || companyData.length === 0) {
                    box.style.display = "none";
                } else {
                    box.style.display = "flex";
                    let i = 0;

                    function showCompany() {
                        const c = companyData[i];
                        nameEl.textContent = c.name;

                        if (c.devMessage) {
                            devEl.textContent = c.devMessage;
                            devEl.className = `msg ${c.devBgColor}`;
                            devEl.style.display = 'inline-flex';
                        } else {
                            devEl.style.display = 'none';
                        }

                        if (c.supportMessage) {
                            supEl.textContent = c.supportMessage;
                            supEl.className = `msg ${c.supportBgColor}`;
                            supEl.style.display = 'inline-flex';
                        } else {
                            supEl.style.display = 'none';
                        }

                        i = (i + 1) % companyData.length;
                    }

                    showCompany();
                    setInterval(showCompany, 10000);
                }
            }
        </script>

        <style>
            .intek-topbar {
                background: #ffffff;
                border-bottom: 1px solid #e8edf3;
                box-shadow: 0 8px 24px rgba(31, 45, 61, 0.06);
            }

            .intek-topbar .topbar {
                min-height: 72px;
            }

            .intek-logo-box img {
                max-height: 42px;
            }

            .intek-icon-btn {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                border: 1px solid #e8edf3;
                background: #f8fafc;
                color: #263859;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
                padding: 0;
            }

            .intek-icon-btn:hover {
                background: #eef3ff;
                color: #4b6cb7;
                transform: translateY(-1px);
            }

            .intek-nav-pill {
                min-height: 40px;
                border-radius: 12px;
                border: 1px solid #e8edf3;
                background: #f8fafc;
                color: #263859;
                display: flex;
                align-items: center;
                gap: 7px;
                padding: 0 12px;
                font-weight: 700;
            }

            .company-status-box {
                align-items: center;
                gap: 10px;
                background: #f8fafc;
                border: 1px solid #e8edf3;
                border-radius: 16px;
                padding: 8px 12px;
                min-height: 46px;
                max-width: 520px;
                overflow: hidden;
            }

            .company-status-name {
                color: #1f2d3d;
                font-size: 14px;
                font-weight: 800;
                white-space: nowrap;
            }

            .company-status-messages {
                display: flex;
                align-items: center;
                gap: 6px;
                overflow: hidden;
            }

            .company-status-messages .msg {
                border-radius: 999px;
                padding: 4px 9px;
                font-size: 11px;
                font-weight: 800;
                white-space: nowrap;
                max-width: 170px;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .intek-dropdown {
                border: 1px solid #e8edf3;
                border-radius: 16px;
                box-shadow: 0 18px 40px rgba(31, 45, 61, 0.14);
                overflow: hidden;
            }

            .intek-dropdown .dropdown-item {
                border-radius: 10px;
                margin: 3px 6px;
                width: auto;
            }

            .intek-dropdown-header {
                padding: 14px 16px;
                border-bottom: 1px solid #edf1f5;
                background: #fbfcfe;
            }

            .intek-dropdown-header h6 {
                margin: 0;
                font-weight: 800;
                color: #1f2d3d;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .intek-notify-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px !important;
                border: 1px solid #edf1f5;
                background: #fbfcfe;
                margin-bottom: 8px !important;
                border-radius: 14px !important;
                transition: all 0.2s ease;
            }

            .intek-notify-item:hover {
                background: #eef3ff;
                transform: translateY(-1px);
            }

            .notify-icon-soft {
                width: 38px;
                height: 38px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .notify-icon-soft.primary {
                background: #eef3ff;
                color: #4b6cb7;
            }

            .notify-icon-soft.secondary {
                background: #f1f3f5;
                color: #64748b;
            }

            .notify-icon-soft.info {
                background: #e8f7ff;
                color: #0dcaf0;
            }

            .notify-icon-soft.warning {
                background: #fff4db;
                color: #b7791f;
            }

            .notify-icon-soft.danger {
                background: #ffe8e8;
                color: #dc3545;
            }

            .intek-notify-title {
                font-size: 13px;
                font-weight: 800;
                color: #1f2d3d;
                display: flex;
                justify-content: space-between;
                gap: 8px;
            }

            .intek-notify-title small {
                color: #8a97a6;
                font-weight: 500;
                white-space: nowrap;
            }

            .intek-notify-subtitle {
                color: #64748b;
                font-size: 12px;
                margin-top: 4px;
            }

            .intek-badge-dot {
                position: absolute;
                top: 8px;
                right: 8px;
                width: 9px;
                height: 9px;
                background: #dc3545;
                border: 2px solid #ffffff;
                border-radius: 50%;
                box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.12);
            }

            .intek-user-menu {
                min-height: 46px;
                border-radius: 16px;
                background: #f8fafc;
                border: 1px solid #e8edf3;
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 6px 10px !important;
                color: #1f2d3d;
            }

            .intek-user-menu h5 {
                font-size: 13px;
                font-weight: 800;
                color: #1f2d3d;
            }

            .intek-user-menu h6 {
                font-size: 12px;
                color: #8a97a6;
            }

            .intek-user-avatar {
                width: 34px;
                height: 34px;
                border-radius: 12px;
                background: linear-gradient(135deg, #263859, #4b6cb7);
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
                box-shadow: 0 8px 18px rgba(75, 108, 183, 0.24);
            }

            @media (max-width: 767.98px) {
                .intek-topbar .topbar {
                    min-height: 64px;
                }

                .topbar-menu {
                    gap: 6px !important;
                }

                .intek-icon-btn {
                    width: 38px;
                    height: 38px;
                }

                .intek-user-menu {
                    padding: 6px !important;
                }
            }
        </style>