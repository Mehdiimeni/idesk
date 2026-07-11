<?php
///template/ticket/kanban_board.php
?>
<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">

                        <h4 class="page-title"><?php echo (_lang['kanban_board']); ?>

                        </h4>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-5">

                    <a href="#" class="btn btn-danger mb-2" data-bs-toggle="modal" data-bs-target="#addModalTag">
                        <i class="mdi mdi-plus-circle me-2"></i>
                        <?php echo (_lang['add']); ?>
                    </a>
                </div>

            </div>

            <div class="row">
                <div class="col-12">
                    <div class="board">
                        <?php

                        $countTag = 0;

                        while ($kanban_tag = $allKanbanTag->fetch_assoc()) {
                            $countTag++;

                            $strPlugin = '';
                            if ($countTag == 1) {
                                $strPlugin = "data-plugin='dragula' data-containers='" . $allListTaskId . "'";
                            }
                            ?>
                            <div class="tasks" <?php echo $strPlugin; ?>>


                                <h5 class="mt-0 task-header"><?php echo ($kanban_tag["board_tag"]); ?>


                                    <a href="javascript:void(0);" class="action-icon edit-item align-middle btn-light"
                                        data-bs-toggle="modal" data-bs-target="#editModalTag" data-table="kanban_board_tags"
                                        data-id="<?php echo $kanban_tag['id']; ?>">
                                        <span class="btn btn-xs btn-outline-info rounded position-relative">
                                            <i class="mdi mdi-square-edit-outline"></i>
                                        </span>
                                    </a>

                                    <a href="javascript:void(0);" class="action-icon delete-item align-middle btn-light"
                                        data-bs-toggle="modal" data-bs-target="#deleteModalTag"
                                        data-table="kanban_board_tags" data-id="<?php echo $kanban_tag['id']; ?>">
                                        <span class="btn btn-xs btn-outline-warning rounded position-relative">
                                            <i class="mdi mdi-delete"></i>
                                        </span>
                                    </a>

                                </h5>
                                <!-- لیست تسک‌های ستون مهم (important) -->
                                <div id="task-list-<?php echo ($countTag); ?>" class="task-list-items"
                                    data-board-tag-id="<?php echo $kanban_tag['id']; ?>">
                                    <?php
                                    $AllKabanByTagId = $ticketModel->getAllKabanByTagId($kanban_tag['id']);
                                    while ($kaban_details = $AllKabanByTagId->fetch_assoc()) {
                                        ?>


                                        <!-- Task Item -->
                                        <div class="card mb-0" data-id="<?php echo $kaban_details['id']; ?>">
                                            <div class="card-body p-3">

                                                <?php $condition = $structureModel->getConditionsByName($kaban_details['status']); ?>
                                                <div class="alert alert-<?php echo $condition['condition_color']; ?>  mb-3">
                                                    <?php echo _lang[$condition['condition_name']]; ?>

                                                </div>

                                                <small class="float-end text-muted">
                                                    <?php $dateConverter = new DateConverter($kaban_details['last_updated_date'], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?>
                                                </small>

                                                <?php
                                                $showPriority = '';
                                                $priority = $kaban_details['priority'];
                                                if ($priority == 'low') {
                                                    $showPriority = '<span class="badge bg-primary">' . _lang[$priority] . '</span>';
                                                } elseif ($priority == 'medium') {
                                                    $showPriority = '<span class="badge bg-warning">' . _lang[$priority] . '</span>';
                                                } elseif ($priority == 'high') {
                                                    $showPriority = '<span class="badge bg-danger">' . _lang[$priority] . '</span>';
                                                }
                                                echo $showPriority;
                                                ?>

                                                <span
                                                    class="badge bg-soft-primary text-primary"><?php echo $kaban_details['type_group']; ?></span>

                                                <h5 class="mt-2 m-2">
                                                    <a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($kaban_details['part_id']); ?>"
                                                        class="text-body"><?php echo '( ' . $kaban_details['ticket_number'] . ' )' . $textToolsClass->truncateText($kaban_details['ticket_title'], 220); ?></a>
                                                </h5>
                                                <div class="dropdown float-end">
                                                    <a href="#" class="dropdown-toggle text-muted arrow-none"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="mdi mdi-dots-vertical font-18"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <!-- item-->

                                                        <a href="javascript:void(0);" class="dropdown-item delete-item"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                            data-table="kanban_board"
                                                            data-id="<?php echo $kaban_details['id']; ?>">
                                                            <i class="mdi mdi-delete me-1"></i><?php echo (_lang['delete']); ?>
                                                        </a>

                                                    </div>
                                                </div>

                                                <p class="mb-0">
                                                    <span
                                                        class="align-middle"><?php echo $kaban_details['description']; ?></span>
                                                </p>
                                            </div> <!-- end card-body -->
                                        </div> <!-- Task Item End -->
                                    <?php } ?>
                                </div> <!-- end task-list-one-->

                            </div>
                        <?php } ?>


                    </div> <!-- end .board-->
                </div> <!-- end col -->
            </div>
            <!-- end row-->

        </div> <!-- container -->


        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">
                            <?php echo (_lang['delete_item']); ?>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="<?php echo (_lang['close']); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>
                            <?php echo (_lang['delete_confirm']); ?>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <?php echo (_lang['close']); ?>
                        </button>
                        <button type="button" class="btn btn-danger confirm-delete">
                            <?php echo (_lang['delete']); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addModalTag" tabindex="-1" role="dialog" aria-labelledby="addModalTagLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalTagLabel">
                            <?php echo (_lang['add']); ?>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="<?php echo (_lang['close']); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Form for adding activity -->
                        <form id="addForm" name="addForm" class="form-control">

                            <input type="hidden" class="form-control addField" name="table_set" id="tableSet"
                                value="kanban_board_tags">
                            <input class="form-control addField" type="hidden" name="unique_fields" id="unique_fields"
                                value="<?php echo $unique_fields; ?>">
                            <div class="mb-3">
                                <label for="board_tag" class="form-label">
                                    <?php echo (_lang['name']); ?>
                                </label>
                                <input type="text" class="form-control addField" id="board_tag" name="board_tag"
                                    value="" required>

                            </div>
                            <input class="form-control addField" type="hidden" name="user_id" id="user_id"
                                value="<?php echo $_SESSION["user_id"]; ?>">
                            <button type="button" class="btn btn-primary" id="addDataBtn">
                                <?php echo (_lang['add']); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="deleteModalTag" tabindex="-1" role="dialog" aria-labelledby="deleteModalTagLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalTagLabel">
                            <?php echo (_lang['delete_item']); ?>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="<?php echo (_lang['close']); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>
                            <?php echo (_lang['delete_confirm']); ?>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <?php echo (_lang['close']); ?>
                        </button>
                        <button type="button" class="btn btn-danger confirm-delete">
                            <?php echo (_lang['delete']); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModalTag" tabindex="-1" role="dialog" aria-labelledby="editModalTagLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalTagLabel">
                            <?php echo (_lang['edit_item']); ?>
                        </h5>
                        <button type="button" class="close" data-bs-dismiss="modal"
                            aria-label="<?php echo (_lang['close']); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Form for editing activity -->
                        <form id="editForm" name="editForm" class="form-control">
                            <input type="hidden" class="form-control editField" name="table_set" id="table_set">
                            <input type="hidden" class="form-control editField" name="id" id="id">
                            <input class="form-control editField" type="hidden" name="unique_fields" id="unique_fields"
                                value="<?php echo $unique_fields; ?>">
                            <div class="mb-3">
                                <label for="board_tag" class="form-label">
                                    <?php echo (_lang['name']); ?>
                                </label>
                                <input type="text" class="form-control editField" id="board_tag" name="board_tag"
                                    required>
                            </div>

                            <button type="button" class="btn btn-primary" id="editDataBtn">
                                <?php echo (_lang['save_changes']); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- content -->

</div>

