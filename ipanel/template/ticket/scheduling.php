<?php
///template/ticket/scheduling.php
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="header-title"><?php echo _lang['scheduling']; ?></h4>
                <?php if ($rbacClass->checkPermissionOperationByName('search_operation')) { ?>

                    <p class="text-muted font-14">
                    <div class="mb-3">
                        <label class="form-label"><?php echo _lang['ticket']; ?></label>
                        <form action="./scheduling" method="get">
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
                    <div class="tab-content">
                        <div class="tab-pane show active" id="buttons-table-preview">
                            <table id="scroll-vertical-datatable" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th><?php echo _lang['ticket_number']; ?></th>
                                        <th><?php echo _lang['title']; ?></th>
                                        <th><?php echo _lang['description']; ?></th>
                                        <th><?php echo _lang['inbox']; ?></th>
                                        <th><?php echo _lang['date_time']; ?></th>
                                        <th><?php echo _lang['status']; ?></th>

                                    </tr>
                                </thead>


                                <tbody>
                                    <?php while ($scheduling = $resultScheduling->fetch_assoc()) {

                                        if ($scheduling['date_time'] == '') {
                                            continue;
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $scheduling['ticket_number']; ?></td>
                                            <td><?php echo $textToolsClass->truncateText($scheduling['ticket_title']); ?></td>
                                            <td><?php echo $textToolsClass->truncateText($scheduling['description']); ?></td>
                                            <td><?php echo $scheduling['admin_name']; ?></td>
                                            <td><span
                                                    class="badge <?php
                                                    if (strtotime($scheduling['date_time']) <= strtotime('today')) {
                                                        echo 'bg-danger';
                                                    } elseif (strtotime($scheduling['date_time']) <= strtotime('+3 days')) {
                                                        echo 'bg-warning';
                                                    } else {
                                                        echo 'bg-primary';
                                                    }

                                                    ?>"><?php $dateConverter = new DateConverter($scheduling['creation_date'], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?></span>
                                            </td>
                                            <td>
                                                <?php $condition = $structureModel->getConditionsByName($scheduling['status']); ?>
                                                <span
                                                    class="alert alert-<?php echo $condition['condition_color']; ?> ">
                                                    <?php echo @_lang[$condition['condition_name']]; ?>
                                                </span>
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