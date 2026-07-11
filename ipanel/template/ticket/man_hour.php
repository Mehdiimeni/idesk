<?php
///template/ticket/man_hour.php
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="header-title"><?php echo _lang['man_hour']; ?></h4>
                <?php if ($rbacClass->checkPermissionOperationByName('search_operation')) { ?>

                <p class="text-muted font-14">
                <div class="mb-3">
                    <label class="form-label"><?php echo _lang['ticket']; ?></label>
                    <form action="./man_hour" method="get">
                        <div class="input-group">
                            <input type="text" id="ticket_number" name="ticket_number" class="form-control" placeholder="<?php echo _lang['ticket_number']; ?>" aria-label="<?php echo _lang['ticket_number']; ?>">
                            <button class="btn btn-dark" type="submit"><?php echo _lang['search']; ?></button>
                        </div>
                    </form>

                </div>
                </p>
                <?php } ?>

                <?php if (isset($_GET['ticket_number']) && $_GET['ticket_number'] != '') { ?>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="buttons-table-preview">
                            <table id="scroll-vertical-datatable" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th><?php echo _lang['ticket_number']; ?></th>
                                        <th><?php echo _lang['title']; ?></th>
                                        <th><?php echo _lang['subject']; ?></th>
                                        <th><?php echo _lang['description']; ?></th>
                                        <th><?php echo _lang['admin']; ?></th>
                                        <th><?php echo _lang['date_time']; ?></th>
                                        <th><?php echo _lang['hour']; ?></th>

                                    </tr>
                                </thead>


                                <tbody>
                                    <?php while ($manHour = $resultManHour->fetch_assoc()) {   ?>
                                        <tr>
                                            <td><?php echo $manHour['ticket_number']; ?></td>
                                            <td><?php echo  $textToolsClass->truncateText($manHour['ticket_title']); ?></td>
                                            <td><?php echo  _lang[$manHour['todo']]; ?></td>
                                            <td><?php echo  $textToolsClass->truncateText($manHour['subject']); ?></td>
                                            <td><?php echo $manHour['admin_name']; ?></td>
                                            <td><?php $dateConverter = new DateConverter($manHour['creation_date'], $config->getNowLanguage('a'));
                                                echo $dateConverter->convertToShamsi(); ?></td>
                                            <td>
                                                <?php echo $manHour['man_hour_number']; ?>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                            </table>
                        </div> <!-- end preview-->
                    </div> <!-- end tab-content-->
                <?php } ?>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div> <!-- end row-->