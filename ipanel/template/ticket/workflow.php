<?php
///template/ticket/workflow.php
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="header-title"><?php echo _lang['workflow']; ?></h4>
                <?php if ($rbacClass->checkPermissionOperationByName('search_operation')) { ?>

                    <p class="text-muted font-14">
                    <div class="mb-3">
                        <label class="form-label"><?php echo _lang['ticket']; ?></label>
                        <form action="./workflow" method="get">
                            <div class="input-group">
                                <input type="text" id="ticket_number" name="ticket_number" class="form-control"
                                    placeholder="<?php echo _lang['ticket_number']; ?>"
                                    aria-label="<?php echo _lang['ticket_number']; ?>">
                                <button class="btn btn-dark" type="submit"><?php echo _lang['search']; ?></button>
                            </div>
                        </form>

                    </div>
                    </p>
                <?php } ?>

                <?php if (isset($_GET['ticket_number']) && $_GET['ticket_number'] != '') { ?>

                    <div class="col-12">
                        <div class="timeline" dir="ltr">

                            <div class="timeline-show mb-3 text-center">
                                <h5 class="m-0 time-show-name"><?php echo ($_GET['ticket_number']); ?></h5>
                            </div>
                            <?php $forwardCount = 0;
                            $setDiff = 0;
                            $arrayTime = array();
                            foreach ($resultTicket as $ticket_data) {
                                $arrayTime[$forwardCount] = $ticket_data['creation_date'];
                                if ($forwardCount > 0) {
                                    $setDiff = 1;
                                    $stepDiffTime = $ticketModel->getTimeDifferenceArray($arrayTime[$forwardCount - 1], $arrayTime[$forwardCount]);
                                }

                                $forwardCount++

                                    ?>
                                <div class="timeline-lg-item timeline-item-<?php echo $ticket_data['position']; ?>">
                                    <div class="timeline-desk">
                                        <div class="timeline-box">
                                            <span class="arrow-alt"></span>
                                            <span class="timeline-icon"><i class="mdi mdi-adjust"></i></span>
                                            <?php if ($ticket_data['source_type'] == 'status') { ?>
                                                <h4 class="mt-0 mb-1 font-16"><?php echo $ticket_data['person_name']; ?> </h4>
                                            <?php } ?>
                                            <?php if ($ticket_data['source_type'] == 'priority') { ?>
                                                <h4 class="mt-0 mb-1 font-16"><?php echo $ticket_data['person_name']; ?> </h4>
                                            <?php } ?>
                                            <?php if ($ticket_data['source_type'] == 'forward') { ?>
                                                <h4 class="mt-0 mb-1 font-16"> <?php echo $ticket_data['receiver_name']; ?> <i
                                                        class="mdi mdi-step-forward-2"></i>
                                                    <?php echo $ticket_data['person_name']; ?>
                                                </h4>
                                            <?php } ?>
                                            <p class="text-muted">
                                                <small><?php $dateConverter = new DateConverter($ticket_data['creation_date'], $config->getNowLanguage('a'));
                                                echo $dateConverter->convertToShamsi(); ?></small>
                                            </p>
                                            <p><?php echo $ticket_data['forwards_description']; ?> </p>
                                            <p><?php echo $ticket_data['status_description']; ?> </p>

                                            <?php if ($ticket_data['status_name'] != '') {
                                                $condition = $structureModel->getConditionsByName($ticket_data['status_name']); ?>
                                                <a
                                                    class="alert alert-<?php echo $condition['condition_color']; ?> ">
                                                    <?php echo _lang[$condition['condition_name']]; ?>
                                                </a><?php } ?>
                                            <?php if ($ticket_data['source_type'] == 'priority') {

                                                $priority = $ticket_data['priority'];
                                                if ($priority == 'low') {
                                                    echo '<span class="btn btn-sm badge-primary-lighten ">' . _lang[$priority] . '</span>';
                                                }
                                                if ($priority == 'medium') {
                                                    echo '<span class="btn btn-sm badge-warning-lighten ">' . _lang[$priority] . '</span>';
                                                }
                                                if ($priority == 'high') {
                                                    echo '<span class="btn btn-sm badge-danger-lighten ">' . _lang[$priority] . '</span>';
                                                }


                                            } ?>
                                            <?php if ($ticket_data['source_type'] == 'forward') { ?> <a
                                                    href="javascript: void(0);" class="btn btn-sm btn-light"><i
                                                        class="mdi mdi-forwardburger"></i></a><?php } ?>
                                            <?php if ($ticket_data['source_type'] == 'priority') { ?> <a
                                                    href="javascript: void(0);" class="btn btn-sm btn-light"><i
                                                        class="mdi mdi-sort-variant"></i></a><?php } ?>
                                            <?php if ($ticket_data['source_type'] == 'status') { ?> <a
                                                    href="javascript: void(0);" class="btn btn-sm btn-light"><i
                                                        class="mdi mdi-list-status"></i></a><?php } ?>
                                            <?php if ($ticket_data['view_to']) { ?> <a href="javascript: void(0);"
                                                    class="btn btn-sm btn-light"><i class="mdi mdi-eye"></i></a><?php } ?>
                                            <?php if ($setDiff) { ?><span
                                                    class="btn btn-sm text-dark bg-light"><?php echo (_lang['day'] . ' : ' . $stepDiffTime['days'] . '/' . _lang['hour'] . ' : ' . $stepDiffTime['hours'] . '/' . _lang['minute'] . ' : ' . $stepDiffTime['minutes']); ?></span><?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="timeline-show my-3 text-center">
                                <h5 class="m-0 time-show-name">
                                    <?php echo (_lang['total'] . ' : ' . round($timeDifference['hours']) . ' ' . _lang['hour']); ?>
                                </h5>
                            </div>

                        </div>
                        <!-- end timeline -->
                    </div> <!-- end col -->
                <?php } ?>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div> <!-- end row-->