<?php
///template/structure/user_view.php
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
                            <?php echo (_lang['user_view']); ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-centered w-100 dt-responsive nowrap" id="alternative-page-datatable">
                                    <thead>
                                        <tr>
                                        <th>
                                                <?php echo ("#"); ?>
                                            </th>
                                            <th>
                                                <?php echo (_lang['role']); ?>
                                            </th>

                                            <th>
                                                <?php echo (_lang['last_updated_date']); ?>
                                            </th>

                                            <th>
                                                <?php echo (_lang['settings']); ?>
                                            </th>
                                            <th style="width: 125px;">
                                                <?php echo (_lang['actions']); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        while ($userViewDetails = $userViewResult->fetch_assoc()) {

                                        ?>
                                            <tr>
                                            <td>
                                                    <?php echo $userViewDetails['id']; ?>
                                                </td>

                                                <td>
                                                    <?php echo $userViewDetails['rbac_name']; ?>
                                                </td>

                                                <td>
                                                    <?php $dateConverter = new DateConverter($userViewDetails['last_updated_date'], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?>
                                                </td>

                                                <td>
                                                    <?php if ($userViewDetails['operation'] != '') echo ('<i class="mdi mdi-check-circle"></i>'); ?>
                                                </td>
                                                <td>


                                                    <?php if ($rbacClass->checkPermissionOperationByName('edit_operation')) { ?>
                                                        <a href="./user_view?id=<?php echo $userViewDetails['id']; ?>" class="action-icon">
                                                            <i class="mdi mdi-square-edit-outline"></i>
                                                        </a>
                                                    <?php } ?>

                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>


                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div>
            <!-- end row -->

        </div> <!-- container -->

    </div> <!-- content -->


</div>