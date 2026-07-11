<?php
///template/bi/bi_users.php
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
                            <?php echo (_lang['user_status']); ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <?php if ($permissionStatistics) { ?>

                <div class="row">
                    <div class="col-xl-3 col-lg-3">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="header-title">
                                    <?php echo _lang['top_performing']; ?>
                                </h4>
                                <div class="dropdown">


                                </div>
                            </div>

                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm table-nowrap table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <?php echo _lang['user']; ?>
                                                </th>
                                                <th>
                                                    <?php echo _lang['enter']; ?>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php

                                            while ($topPerformingDetails = $topPerformingResult->fetch_assoc()) {
                                                ?>
                                                <tr>

                                                    <td>
                                                        <h5 class="font-15 mb-1 fw-normal">
                                                            <?php echo $topPerformingDetails["name"]; ?>
                                                        </h5>
                                                        <span class="text-muted font-13">
                                                            <?php echo $topPerformingDetails["unit_name"]; ?>
                                                        </span>
                                                        <span class="text-muted font-11">
                                                            <?php echo $topPerformingDetails["company_name"]; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php echo $topPerformingDetails["login_count"]; ?>
                                                    </td>

                                                </tr>
                                            <?php } ?>

                                        </tbody>
                                    </table>
                                </div> <!-- end table-responsive-->

                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div>
                    <!-- end col-->

                    <div class="col-xl-9 col-lg-9">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="header-title">
                                    <?php echo _lang['last_user_login']; ?>
                                </h4>

                            </div>

                            <div class="card-body pt-2">
                                <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped mb-0 w-100" id="alternative-page-datatable">
                                        <thead class="table-light">
                                            <tr>
                                                <th><?php echo ("#"); ?></th>
                                                <th><?php echo (_lang['name']); ?></th>
                                                <th><?php echo (_lang['unit']); ?></th>
                                                <th><?php echo (_lang['company']); ?></th>
                                                <th><?php echo (_lang['last_user_login']); ?></th>
                                                <th><?php echo (_lang['local_ip']); ?></th>
                                                <th><?php echo (_lang['internet_ip']); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php

                                            while ($lastLoginUsersDetails = $lastLoginUsersResult->fetch_assoc()) { ?>
                                                <tr>
                                                    <td><?php echo $lastLoginUsersDetails["user_id"]; ?></td>
                                                    <td> <?php echo $lastLoginUsersDetails["name"]; ?></td>
                                                    <td><?php echo $lastLoginUsersDetails["unit_name"]; ?></td>
                                                    <td><?php echo $lastLoginUsersDetails["company_name"]; ?></td>
                                                    <td><?php if ($lastLoginUsersDetails["last_login_time"] != '')
                                                        $dateConverter = new DateConverter($lastLoginUsersDetails["last_login_time"], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?></td>
                                                    <td><?php echo $lastLoginUsersDetails["local_ip"]; ?></td>
                                                    <td><?php echo $lastLoginUsersDetails["internet_ip"]; ?></td>
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