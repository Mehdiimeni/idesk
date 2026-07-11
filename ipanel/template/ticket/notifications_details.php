<?php
///template/ticket/notifications_details.php
?>
<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page email-title -->
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">

                        <h4 class="page-title"><?php echo (_lang['view_message']); ?></h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <!-- end page email-title -->

            <div class="row">

                <!-- Right Sidebar -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <!-- Left sidebar -->
                            <div class="page-aside-left">
                                <?php if ($rbacClass->checkPermissionOperationByName('send_message_operation')) { ?>

                                    <div class="d-grid">
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#compose-modal"><?php echo (_lang['send_message']); ?></button>
                                    </div>
                                <?php } ?>

                                <div class="email-menu-list mt-3">
                                    <a href="./notifications?filter=rm" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'rm') { ?> class="text-danger fw-bold" <?php } ?>><i class="ri-inbox-line me-2"></i><?php echo (_lang['inbox_message']); ?></a>
                                    <a href="./notifications?filter=fm" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'fm') { ?> class="text-danger fw-bold" <?php } ?>><i class="ri-star-line me-2"></i><?php echo (_lang['assign']); ?></a>
                                    <a href="./notifications?filter=imp" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'imp') { ?> class="text-danger fw-bold" <?php } ?>><i class="ri-price-tag-3-line me-2"></i><?php echo (_lang['important_message']); ?></a>
                                    <a href="./notifications?filter=sm" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'sm') { ?> class="text-danger fw-bold" <?php } ?>><i class="ri-mail-send-line me-2"></i><?php echo (_lang['sent_message']); ?></a>
                                </div>



                            </div>
                            <!-- End Left sidebar -->

                            <div class="page-aside-right">

                                <div class="mt-3">
                                    <h5 class="font-18"><?php echo $messageDetails['msg_subject']; ?></h5>

                                    <hr />

                                    <div class="d-flex mb-3 mt-1">

                                        <div class="w-100 overflow-hidden">
                                            <small class="float-end"><?php echo $messageDetails['creation_date']; ?></small>
                                            <h6 class="m-0 font-14"><?php echo $messageDetails['sender_name']; ?></h6>
                                        </div>
                                    </div>

                                    <p> <?php echo $messageDetails['message']; ?> </p>
                                    <hr />


                                    <!-- 
                                                <div class="mt-5">
                                                    <a href="#" class="btn btn-secondary me-2"><i class="mdi mdi-reply me-1"></i> Reply</a>
                                                    <a href="#" class="btn btn-light">Forward <i class="mdi mdi-forward ms-1"></i></a>
                                                </div>

-->

                                </div>
                                <!-- end .mt-4 -->

                            </div>
                            <!-- end inbox-rightbar-->
                        </div>

                        <div class="clearfix"></div>
                    </div> <!-- end card-box -->

                </div> <!-- end Col -->
            </div><!-- End row -->

        </div> <!-- container -->

    </div> <!-- content -->

    <!-- Compose Modal -->
    <div id="compose-modal" class="modal fade" tabindex="-1" id="addModal" role="dialog" aria-labelledby="compose-header-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-colored-header text-bg-primary">
                    <h4 class="modal-title" id="compose-header-modalLabel"><?php echo (_lang['new_message']); ?></h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="p-1">
                    <div class="modal-body px-3 pt-3 pb-0">
                        <form id="addForm" name="addForm" class="form-control">
                            <input type="hidden" class="form-control addField" name="table_set" id="tableSet" value="messages">
                            <input class="form-control addField" type="hidden" name="unique_fields" id="unique_fields" value="<?php echo $unique_fields; ?>">
                            <input class="form-control addField" type="hidden" name="msg_from" id="msg_from" value="<?php echo $notificationModel->getAdminIdFromSession(); ?>">
                            <div class="mb-2">
                                <label for="important" class="form-label"><?php echo (_lang['type']); ?></label>
                                <select class=" form-control  addField" name="important" data-placeholder="<?php echo _lang['choose']; ?>">
                                    <option value="0" selected>
                                        <?php echo (_lang['normal']); ?>
                                    </option>

                                    <option value="1">
                                        <?php echo (_lang['important']); ?>
                                    </option>

                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="msg_to" class="form-label"><?php echo (_lang['to']); ?></label>
                                <select class=" form-control  addField" name="msg_to" data-placeholder="<?php echo _lang['choose']; ?>">
                                    <option selected>
                                        <?php echo (_lang['select']); ?>
                                    </option>
                                    <?php
                                    $rbacAdminsInfo = $rbacClass->getAdminsByOperationName('send_message_operation');
                                    while ($rbacAdminsInfoDetail = $rbacAdminsInfo->fetch_assoc()) {
                                    ?>
                                        <option value="<?php echo $rbacAdminsInfoDetail['id']; ?>">
                                            <?php echo $rbacAdminsInfoDetail['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="msg_subject" class="form-label "><?php echo (_lang['subject']); ?></label>
                                <input type="text" name="msg_subject" id="msg_subject" class="form-control addField">
                            </div>
                            <div class="write-mdg-box mb-3">
                                <label class="form-label"><?php echo (_lang['message']); ?></label>
                                <textarea id="message" name="message" class="form-control addField"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="px-3 pb-3">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="addDataBtn"><i class="mdi mdi-send me-1"></i><?php echo (_lang['send']); ?></button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?php echo (_lang['cancel']); ?></button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->




</div>