<?php
///template/bi/bi_ticket_admins_kpi.php
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
                            <?php echo (_lang['report_ticket_admins_kpi']); ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <?php if ($permissionStatistics) { ?>

                <div class="row">

                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            

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
                                                <th><?php echo (_lang['response_time_compliance']); ?></th>
                                                <th><?php echo (_lang['delayed_tickets_percentage']); ?></th>
                                                <th><?php echo (_lang['average_completion_time']); ?></th>
                                                <th><?php echo (_lang['manhour_working_time']); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            $arrExcept = ['condition_archive', 'condition_final_done', 'condition_pendency'];
                                            while ($lastLoginAdminsDetails = $lastLoginAdminsResult->fetch_assoc()) {
                                                $isEntry = $rbacClass->checkPermissionOperationByNameAndId('pointer_operation',$lastLoginAdminsDetails["admin_id"]) ? 1 : 0;

                                                ?>
                                                <tr>
                                                    <td><?php echo $lastLoginAdminsDetails["admin_id"]; ?></td>
                                                    <td> <?php echo $lastLoginAdminsDetails["name"]; ?></td>
                                                    <td><?php echo $lastLoginAdminsDetails["unit_name"]; ?></td>

                                                    
                                                    <td><i class="mdi mdi-arrow-down-bold"></i>

                                                        <?php
                                                    
                                                            $result = $ticketModel->getAdminNoActionTicketsWithExceptCountDay($lastLoginAdminsDetails["admin_id"], $arrExcept);
                                                       

                                                        echo ($result); ?>
                                                    </td>
                                                    <td><i class="mdi mdi-arrow-up-bold"></i>
                                                        <?php
                                                       
                                                            $result = $ticketModel->getAdminForwardTicketCountDay($lastLoginAdminsDetails["admin_id"]);
                                                        

                                                        echo ($result); ?>

                                                    </td>
                                                    <td>
                                                        <?php
                                                       
                                                            $result2 = $kpiModel->getTicketIdsWithDetailsInMonth($lastLoginAdminsDetails["admin_id"],$isEntry);
                                                       

                                                        echo ($result2['response_time_compliance'] . '%');

                                                        ?>
                                                    </td>
                                                    
                                                    <td>
                                                        <?php echo ($result2['delayed_time_compliance'] . '%'); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo (round($result2['average_time_difference']) . ' ' . _lang['hour']); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo (round($result2['manhour_working_time']) . ' ' . _lang['hour']); ?>
                                                    </td>
                                                    


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