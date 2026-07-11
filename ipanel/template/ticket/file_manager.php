<?php
///template/ticket/file_manager.php
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <h4 class="header-title"><?php echo _lang['file_manager']; ?></h4>
                <?php if ($rbacClass->checkPermissionOperationByName('search_operation')) { ?>

                <p class="text-muted font-14">
                <div class="mb-3">
                    <label class="form-label"><?php echo _lang['ticket']; ?></label>
                    <form action="./file_manager" method="get">
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
                                        <th><?php echo _lang['file_title']; ?></th>
                                        <th><?php echo _lang['file_name']; ?></th>
                                        <th><?php echo _lang['inbox']; ?></th>
                                        <th><?php echo _lang['date_time']; ?></th>
                                        <th><?php echo _lang['file_size']; ?></th>
                                        <th><?php echo _lang['download']; ?></th>

                                    </tr>
                                </thead>


                                <tbody>
                                    <?php while ($file = $resultFileing->fetch_assoc()) {
                                        $fileData = $fileManager->getFileInfoFromPath("." . $file['file_path'] . $file['file_name'], $file['file_path'], $file['file_title']);
                                    ?>
                                        <tr>
                                            <td><?php echo $file['ticket_number']; ?></td>
                                            <td><?php echo $textToolsClass->truncateText($file['file_title']); ?></td>
                                            <td><?php echo $file['file_name']; ?></td>
                                            <td><?php echo $file['creator_name']; ?></td>
                                            <td><?php $dateConverter = new DateConverter($file['creation_date'], $config->getNowLanguage('a'));
                                                echo $dateConverter->convertToShamsi(); ?></td>
                                            <td><?php echo $fileData['fileSize']; ?></td>
                                            <td><a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($file['id']); ?>&file=<?php echo "." . $fileData['downloadLink']; ?>" class="btn btn-link btn-lg text-muted">
                                                    <i class="ri-download-2-line"></i>
                                                </a></td>
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