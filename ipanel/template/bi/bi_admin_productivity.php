<?php
///template/bi/bi_admin_productivity.php
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
                            <?php echo (_lang['report_admins_productivity']); ?>
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
                                                <th><?php echo (_lang['total_tickets']); ?></th>
                                                <th><?php echo (_lang['condition_done']); ?></th>
                                                <th><?php echo (_lang['confirmation_test']); ?></th>
                                                <th><?php echo (_lang['condition_acepted_test_auto']); ?></th>
                                                <th><?php echo (_lang['condition_final_done']); ?></th>
                                                <th><?php echo (_lang['condition_reject_test']); ?></th>
                                                <th><?php echo (_lang['response_time_compliance']); ?></th>
                                                <th><?php echo (_lang['delayed_tickets_percentage']); ?></th>
                                                <th><?php echo (_lang['productivity']); ?></th>
                                                <th><?php echo (_lang['kpi']); ?></th>
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


                                                    <td>

                                                        <?php
                                                     
                                                            $resultAssign = $kpiModel->countAllTicketAdminAssign($lastLoginAdminsDetails["admin_id"]);
                                                        

                                                        echo ($resultAssign['total']); ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                
                                                            $resultDone = $kpiModel->countAllTicketAdminConfirmationDone($lastLoginAdminsDetails["admin_id"]);
                                                        

                                                        echo ($resultDone);

                                                        ?>
                                                    </td>

                                                    <td>
                                                        <?php
                                                  
                                                            $resultConfirmationTest = $kpiModel->countAllTicketAdminConfirmationTest($lastLoginAdminsDetails["admin_id"]);
                                                        

                                                        echo ($resultConfirmationTest);

                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                      
                                                            $resultConfirmationTestAuto = $kpiModel->countAllTicketAdminConfirmationTestAuto($lastLoginAdminsDetails["admin_id"]);
                                                        

                                                        echo ($resultConfirmationTestAuto);

                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                     
                                                            $resultFinalDone = $kpiModel->countAllTicketAdminConfirmationFinalDone($lastLoginAdminsDetails["admin_id"]);
                                                        

                                                        echo ($resultFinalDone);

                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                     
                                                            $resultRejectTest = $kpiModel->countAllTicketAdminRejectTest($lastLoginAdminsDetails["admin_id"]);
                                                        

                                                        echo ($resultRejectTest);

                                                        ?>
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
                                                        <?php echo ($kpiModel->calAdminTicketProductivity($isEntry,$resultAssign,$resultDone,$result2['response_time_compliance'],$result2['delayed_time_compliance'], $resultConfirmationTest, $resultConfirmationTestAuto,$resultFinalDone, $resultRejectTest)['productivity']) . '%'; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo ($kpiModel->calAdminTicketProductivity($isEntry,$resultAssign,$resultDone,$result2['response_time_compliance'],$result2['delayed_time_compliance'], $resultConfirmationTest, $resultConfirmationTestAuto,$resultFinalDone, $resultRejectTest)['kpi']); ?>
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