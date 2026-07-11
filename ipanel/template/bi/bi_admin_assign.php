<?php
///template/bi/bi_admin_assign.php
?>

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">
                            <?php echo (_lang['report_admin_assign']); ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <?php if ($permissionStatistics) { ?>

                <div class="row">

                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="header-title">
                                    <?php echo _lang['inbox']; ?>
                                </h4>

                            </div>

                            <div class="card-body pt-2">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm table-centered mb-0 font-14">
                                        <thead class="table-light">
                                            <tr>
                                                <th><?php echo ("#"); ?></th>
                                                <th><?php echo (_lang['name']); ?></th>
                                                <th><?php echo (_lang['unit']); ?></th>
                                                <th><?php echo (_lang['referred_from']); ?></th>
                                                <th><?php echo (_lang['referred_to']); ?></th>
                                                <th><?php echo (_lang['last_user_login']); ?></th>

                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            while ($lastLoginAdminsDetails = $lastLoginAdminsResult->fetch_assoc()) {
                                                $isEntry = $rbacClass->checkPermissionOperationByName('pointer_operation') ? 1 : 0;
                                                ?>
                                                <tr>
                                                    <td><?php echo $lastLoginAdminsDetails["admin_id"]; ?></td>
                                                    <td> <?php echo $lastLoginAdminsDetails["name"]; ?></td>
                                                    <td><?php echo $lastLoginAdminsDetails["unit_name"]; ?></td>
                                                    <td><i class="mdi mdi-arrow-down-bold"></i><?php
                                                    if ($isEntry) {
                                                        echo $ticketModel->getAdminNoActionTicketsWithExceptCount($lastLoginAdminsDetails["admin_id"], ['condition_archive', 'condition_final_done', 'condition_pendency']);
                                                    } else {
                                                        echo $ticketModel->getAdminNoActionTicketsCount($lastLoginAdminsDetails["admin_id"]);

                                                    }


                                                    ?></td>
                                                    <td><i
                                                            class="mdi mdi-arrow-up-bold"></i><?php echo $ticketModel->getAdminForwardTicketCount($lastLoginAdminsDetails["admin_id"]); ?>
                                                    </td>
                                                    <td><?php if ($lastLoginAdminsDetails["last_login_time"] != '')
                                                        $dateConverter = new DateConverter($lastLoginAdminsDetails["last_login_time"], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?></td>

                                                </tr>

                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div> <!-- end table-responsive-->

                            </div>
                            <!-- end card-body -->
                        </div>
                        <!-- end card-->
                    </div>
                    <!-- end col -->

                    <!-- end col -->
                </div>
                <!-- end row-->
            <?php } ?>

        </div> <!-- container -->

    </div> <!-- content -->

</div>