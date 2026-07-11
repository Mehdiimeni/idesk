<?php
///template/ticket/notifications.php
?>

<style>
    .notifications-card {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.08);
        overflow: hidden;
    }

    .notifications-sidebar {
        background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 0.85rem;
        padding: 1rem;
    }

    .email-menu-list a {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.7rem 0.85rem;
        margin-bottom: 0.35rem;
        border-radius: 0.75rem;
        color: #6c757d;
        transition: all 0.2s ease-in-out;
    }

    .email-menu-list a:hover {
        color: #dc3545;
        background-color: rgba(220, 53, 69, 0.08);
    }

    .email-menu-list a.text-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .notifications-table thead th,
    .notifications-table tbody td {
        vertical-align: middle;
    }

    .notifications-table thead th {
        white-space: nowrap;
        font-weight: 700;
    }

    .table-action .action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background-color: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
        transition: all 0.2s ease-in-out;
    }

    .table-action .action-icon:hover {
        background-color: #0d6efd;
        color: #ffffff;
    }

    .modal-content {
        border: 0;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.18);
    }

    .modal-header {
        padding: 1rem 1.25rem;
    }

    .modal-body .form-label {
        font-weight: 600;
    }
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">
                            <?php echo _lang['notifications']; ?>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-3 col-12 mb-3">
                    <div class="notifications-sidebar">

                        <?php if ($rbacClass->checkPermissionOperationByName('send_message_operation')) { ?>
                                <div class="d-grid mb-3">
                                    <button
                                        type="button"
                                        class="btn btn-danger rounded-pill shadow-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#compose-modal">
                                        <?php echo _lang['send_message']; ?>
                                    </button>
                                </div>
                        <?php } ?>

                        <div class="email-menu-list">
                            <a href="./notifications?filter=fm" class="<?php echo activeNotificationMenu('fm', $currentFilter); ?>">
                                <i class="ri-star-line me-2"></i>
                                <?php echo _lang['assign']; ?>
                            </a>

                            <a href="./notifications?filter=com" class="<?php echo activeNotificationMenu('com', $currentFilter); ?>">
                                <i class="uil uil-comment-message font-16 me-2"></i>
                                <?php echo _lang['comments']; ?>
                            </a>

                            <a href="./notifications?filter=rm" class="<?php echo activeNotificationMenu('rm', $currentFilter); ?>">
                                <i class="ri-inbox-line me-2"></i>
                                <?php echo _lang['inbox_message']; ?>
                            </a>

                            <a href="./notifications?filter=imp" class="<?php echo activeNotificationMenu('imp', $currentFilter); ?>">
                                <i class="ri-price-tag-3-line me-2"></i>
                                <?php echo _lang['important_message']; ?>
                            </a>

                            <a href="./notifications?filter=sm" class="<?php echo activeNotificationMenu('sm', $currentFilter); ?>">
                                <i class="ri-mail-send-line me-2"></i>
                                <?php echo _lang['sent_message']; ?>
                            </a>
                        </div>

                    </div>
                </div>

                <div class="col-lg-9 col-12">
                    <div class="card notifications-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table
                                    class="table table-centered table-hover w-100 dt-responsive nowrap notifications-table"
                                    id="alternative-page-datatable">

                                    <?php if ($currentFilter == 'fm') { ?>

                                            <thead class="table-light">
                                                <tr>
                                                    <th class="all">#</th>
                                                    <th class="all"><?php echo _lang['title']; ?></th>
                                                    <th class="all"><?php echo _lang['type']; ?></th>
                                                    <th class="all"><?php echo _lang['user']; ?></th>
                                                    <th><?php echo _lang['added_date']; ?></th>
                                                    <th><?php echo _lang['status']; ?></th>
                                                    <th style="width: 85px;"><?php echo _lang['action']; ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php while ($forwarderTicket = $allForwarderTicket->fetch_assoc()) { ?>
                                                        <tr>
                                                            <td><?php echo $forwarderTicket['id']; ?></td>

                                                            <td>
                                                                <?php echo $textToolsClass->truncateText($forwarderTicket['forwards_description'], 85); ?>
                                                            </td>

                                                            <td>
                                                                <i class="ri-star-line me-2"></i>
                                                                <?php echo _lang['assign']; ?>
                                                            </td>

                                                            <td><?php echo $forwarderTicket['sender_name']; ?></td>

                                                            <td>
                                                                <?php
                                                                $dateConverter = new DateConverter($forwarderTicket['creation_date'], $config->getNowLanguage('a'));
                                                                echo $dateConverter->convertToShamsi();
                                                                ?>
                                                            </td>

                                                            <td>
                                                                <?php
                                                                $badgeClass = $forwarderTicket['view_to'] == 0 ? 'bg-warning' : 'bg-success';
                                                                $badgeText = $forwarderTicket['view_to'] == 0 ? _lang['set_not_view'] : _lang['view_to'];
                                                                echo "<span class='badge rounded-pill $badgeClass px-3 py-2'>$badgeText</span>";
                                                                ?>
                                                            </td>

                                                            <td class="table-action">
                                                                <?php if ($rbacClass->checkPermissionOperationByName('view_operation')) { ?>
                                                                        <a
                                                                            href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($forwarderTicket['section_element_id']); ?>&fid=<?php echo $forwarderTicket['id']; ?>"
                                                                            class="action-icon">
                                                                            <i class="mdi mdi-eye"></i>
                                                                        </a>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                <?php } ?>
                                            </tbody>

                                    <?php } elseif ($currentFilter == 'com') { ?>

                                            <thead class="table-light">
                                                <tr>
                                                    <th class="all">#</th>
                                                    <th class="all"><?php echo _lang['title']; ?></th>
                                                    <th class="all"><?php echo _lang['ticket']; ?></th>
                                                    <th><?php echo _lang['added_date']; ?></th>
                                                    <th style="width: 85px;"><?php echo _lang['action']; ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php while ($NewTicketsComment = $allNewTicketsComment->fetch_assoc()) { ?>
                                                        <tr>
                                                            <td><?php echo $NewTicketsComment['id']; ?></td>

                                                            <td>
                                                                <?php echo $textToolsClass->truncateText($NewTicketsComment['comment_text'], 85); ?>
                                                            </td>

                                                            <td><?php echo $NewTicketsComment['part_id']; ?></td>

                                                            <td>
                                                                <?php
                                                                $dateConverter = new DateConverter($NewTicketsComment['creation_date'], $config->getNowLanguage('a'));
                                                                echo $dateConverter->convertToShamsi();
                                                                ?>
                                                            </td>

                                                            <td class="table-action">
                                                                <?php if ($rbacClass->checkPermissionOperationByName('view_operation')) { ?>
                                                                        <a
                                                                            href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($NewTicketsComment['part_id']); ?>&cid=<?php echo $NewTicketsComment['id']; ?>"
                                                                            class="action-icon">
                                                                            <i class="mdi mdi-eye"></i>
                                                                        </a>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                <?php } ?>
                                            </tbody>

                                    <?php } elseif ($currentFilter == 'imp' || $currentFilter == 'rm') { ?>

                                            <thead class="table-light">
                                                <tr>
                                                    <th class="all">#</th>
                                                    <th class="all"><?php echo _lang['title']; ?></th>
                                                    <th class="all"><?php echo _lang['type']; ?></th>
                                                    <th class="all"><?php echo _lang['send']; ?></th>
                                                    <th><?php echo _lang['added_date']; ?></th>
                                                    <th><?php echo _lang['status']; ?></th>
                                                    <th style="width: 85px;"><?php echo _lang['action']; ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php while ($messages = $allMessages->fetch_assoc()) { ?>
                                                        <tr>
                                                            <td><?php echo $messages['id']; ?></td>

                                                            <td>
                                                                <?php echo $textToolsClass->truncateText($messages['msg_subject'], 85); ?>
                                                            </td>

                                                            <td>
                                                                <?php
                                                                $iconClass = $messages['important'] ? 'ri-price-tag-3-line' : 'ri-inbox-line';
                                                                $messageType = $messages['important'] ? _lang['important_message'] : _lang['inbox_message'];
                                                                ?>

                                                                <i class="<?php echo $iconClass; ?> me-2"></i>
                                                                <?php echo $messageType; ?>
                                                            </td>

                                                            <td><?php echo $messages['sender_name']; ?></td>

                                                            <td>
                                                                <?php
                                                                $dateConverter = new DateConverter($messages['creation_date'], $config->getNowLanguage('a'));
                                                                echo $dateConverter->convertToShamsi();
                                                                ?>
                                                            </td>

                                                            <td>
                                                                <?php
                                                                $badgeClass = $messages['view_to'] == 0 ? 'bg-warning' : 'bg-success';
                                                                $badgeText = $messages['view_to'] == 0 ? _lang['set_not_view'] : _lang['view_to'];
                                                                echo "<span class='badge rounded-pill $badgeClass px-3 py-2'>$badgeText</span>";
                                                                ?>
                                                            </td>

                                                            <td class="table-action">
                                                                <?php if ($rbacClass->checkPermissionOperationByName('view_operation')) { ?>
                                                                        <a href="./notifications?id=<?php echo $messages['id']; ?>" class="action-icon">
                                                                            <i class="mdi mdi-eye"></i>
                                                                        </a>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                <?php } ?>
                                            </tbody>

                                    <?php } elseif ($currentFilter == 'sm') { ?>

                                            <thead class="table-light">
                                                <tr>
                                                    <th class="all">#</th>
                                                    <th class="all"><?php echo _lang['title']; ?></th>
                                                    <th class="all"><?php echo _lang['type']; ?></th>
                                                    <th class="all"><?php echo _lang['to']; ?></th>
                                                    <th><?php echo _lang['added_date']; ?></th>
                                                    <th><?php echo _lang['status']; ?></th>
                                                    <th style="width: 85px;"><?php echo _lang['action']; ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php while ($messages = $allMessages->fetch_assoc()) { ?>
                                                        <tr>
                                                            <td><?php echo $messages['id']; ?></td>

                                                            <td>
                                                                <?php echo $textToolsClass->truncateText($messages['msg_subject'], 85); ?>
                                                            </td>

                                                            <td>
                                                                <?php
                                                                $iconClass = $messages['important'] ? 'ri-price-tag-3-line' : 'ri-inbox-line';
                                                                $messageType = $messages['important'] ? _lang['important_message'] : _lang['inbox_message'];
                                                                ?>

                                                                <i class="<?php echo $iconClass; ?> me-2"></i>
                                                                <?php echo $messageType; ?>
                                                            </td>

                                                            <td><?php echo $messages['sender_name']; ?></td>

                                                            <td>
                                                                <?php
                                                                $dateConverter = new DateConverter($messages['creation_date'], $config->getNowLanguage('a'));
                                                                echo $dateConverter->convertToShamsi();
                                                                ?>
                                                            </td>

                                                            <td>
                                                                <?php
                                                                $badgeClass = $messages['view_to'] == 0 ? 'bg-warning' : 'bg-success';
                                                                $badgeText = $messages['view_to'] == 0 ? _lang['set_not_view'] : _lang['view_to'];
                                                                echo "<span class='badge rounded-pill $badgeClass px-3 py-2'>$badgeText</span>";
                                                                ?>
                                                            </td>

                                                            <td class="table-action">
                                                                <?php if ($rbacClass->checkPermissionOperationByName('view_operation')) { ?>
                                                                        <a href="./notifications?id=<?php echo $messages['id']; ?>" class="action-icon">
                                                                            <i class="mdi mdi-eye"></i>
                                                                        </a>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                <?php } ?>
                                            </tbody>

                                    <?php } ?>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div
        id="compose-modal"
        class="modal fade"
        tabindex="-1"
        role="dialog"
        aria-labelledby="compose-header-modalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header modal-colored-header text-bg-primary">
                    <h4 class="modal-title" id="compose-header-modalLabel">
                        <?php echo _lang['new_message']; ?>
                    </h4>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>

                <div class="p-1">
                    <div class="modal-body px-3 px-lg-4 pt-3 pb-0">

                        <form id="addForm" name="addForm" class="form-control rounded-3">

                            <input
                                type="hidden"
                                class="form-control addField rounded-3"
                                name="table_set"
                                id="tableSet"
                                value="messages">

                            <input
                                type="hidden"
                                class="form-control addField rounded-3"
                                name="unique_fields"
                                id="unique_fields"
                                value="<?php echo $unique_fields; ?>">

                            <input
                                type="hidden"
                                class="form-control addField rounded-3"
                                name="msg_from"
                                id="msg_from"
                                value="<?php echo $notificationModel->getAdminIdFromSession(); ?>">

                            <div class="mb-2">
                                <label for="important" class="form-label">
                                    <?php echo _lang['type']; ?>
                                </label>

                                <select
                                    class="form-control addField rounded-3"
                                    name="important"
                                    id="important"
                                    data-placeholder="<?php echo _lang['choose']; ?>">
                                    <option value="0" selected>
                                        <?php echo _lang['normal']; ?>
                                    </option>

                                    <option value="1">
                                        <?php echo _lang['important']; ?>
                                    </option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label for="msg_to" class="form-label">
                                    <?php echo _lang['to']; ?>
                                </label>

                                <select
                                    class="form-control addField rounded-3"
                                    name="msg_to"
                                    id="msg_to"
                                    data-placeholder="<?php echo _lang['choose']; ?>">
                                    <option selected>
                                        <?php echo _lang['select']; ?>
                                    </option>

                                    <?php
                                    $rbacAdminsInfo = $rbacClass->getAdminsByOperationName('send_message_operation');

                                    while ($rbacAdminsInfoDetail = $rbacAdminsInfo->fetch_assoc()) {
                                        if ($notificationModel->getAdminIdFromSession() == $rbacAdminsInfoDetail['id']) {
                                            continue;
                                        }
                                        ?>
                                            <option value="<?php echo $rbacAdminsInfoDetail['id']; ?>">
                                                <?php echo $rbacAdminsInfoDetail['name']; ?>
                                            </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label for="msg_subject" class="form-label">
                                    <?php echo _lang['subject']; ?>
                                </label>

                                <input
                                    type="text"
                                    name="msg_subject"
                                    id="msg_subject"
                                    class="form-control addField rounded-3">
                            </div>

                            <div class="write-mdg-box mb-3">
                                <label for="message" class="form-label">
                                    <?php echo _lang['message']; ?>
                                </label>

                                <textarea
                                    id="message"
                                    name="message"
                                    class="form-control addField rounded-3"></textarea>
                            </div>

                        </form>

                    </div>

                    <div class="px-3 px-lg-4 pb-4 d-flex gap-2 justify-content-end">
                        <button
                            type="button"
                            class="btn btn-primary rounded-pill px-4"
                            data-bs-dismiss="modal"
                            id="addDataBtn">
                            <i class="mdi mdi-send me-1"></i>
                            <?php echo _lang['send']; ?>
                        </button>

                        <button
                            type="button"
                            class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">
                            <?php echo _lang['cancel']; ?>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>