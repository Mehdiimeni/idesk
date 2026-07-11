<?php
///template/project/projects_details.php
?>
<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">
                                    <?php echo _lang['project_details']; ?>
                                </li>
                                <li class="breadcrumb-item"><a href="./projects">
                                        <?php echo _lang['projects']; ?>
                                    </a></li>
                            </ol>
                        </div>
                        <h4 class="page-title">
                            <?php echo _lang['project_details']; ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">

                <div class="col-xxl-8 col-lg-6">
                    <!-- project card -->
                    <div class="card d-block ribbon-box">

                        <div class="card-body border">
                            <?php echo ($showPriority); ?>

                            <h4 class="m-1">
                                <?php echo $projectDetails['name']; ?>
                            </h4>

                            <p class="text-muted mb-2">
                                <?php
                                $projectDescription = $projectDetails['description'];
                                echo "<span style=\"white-space: pre-wrap;\">" . $projectDescription . "</span>";
                                ?>
                            </p>

                            <div class="row order-secondary border">
                            
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <h5>
                                            <?php echo _lang['added_date']; ?>
                                        </h5>
                                        <p>
                                            <?php $dateConverter = new DateConverter($projectDetails['creation_date'], $config->getNowLanguage('a'));
                                            echo $dateConverter->convertToShamsi(); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <h5>
                                            <?php echo _lang['last_uapdate_date']; ?>
                                        </h5>
                                        <p>
                                            <?php $dateConverter = new DateConverter($projectDetails['last_updated_date'], $config->getNowLanguage('a'));
                                            echo $dateConverter->convertToShamsi(); ?>
                                        </p>
                                    </div>
                                </div>

                            </div>

                      
                                <div id="tooltip-container">
                                    <h5>
                                        <?php echo (_lang['members']); ?>
                                    </h5>
                                    <?php 
                                    $theFirstThreeMembers = $projectsModel->getProjectAdminInfoWithImage($projectDetails['id'],25);
                                    foreach ($theFirstThreeMembers as $memberDetails) { ?>
                                        <a href="javascript:void(0);" data-bs-container="#tooltip-container"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="<?php echo $memberDetails['name']; ?>" class="d-inline-block">
                                            <img src="<?php echo ("." . $memberDetails['file_path'] . $memberDetails['file_name']); ?>"
                                                class="rounded-circle avatar-xs" alt="<?php echo $memberDetails['name']; ?>">
                                        </a>
                                    <?php } ?>
                                </div>
                            


                        </div> <!-- end card-body-->

                    </div> <!-- end card-->

                    <?php if ($rbacClass->checkPermissionOperationByName('view_comment_operation')) { ?>

                        <div class="card border">
                            <div class="card-body">
                                <h4 class="mt-0 mb-3">
                                    <?php $parent_id = $_GET['parent_id'] ?? null;
                                    $creator_id = $_GET['creator_id'] ?? null;
                                    echo _lang['comments']; ?>
                                </h4>
                                <form validate action="./projects?id=<?php echo ($_GET['id']); ?>"
                                    method="post">
                                    <input type="hidden" name="parent_id" id="parent_id"
                                        value="<?php echo ($parent_id); ?>">
                                    <input type="hidden" name="creator_id" id="creator_id"
                                        value="<?php echo ($creator_id); ?>">
                                    <textarea class="form-control form-control-light mb-2" placeholder="<?php if (!isset($_GET['parent_id'])) {
                                        echo _lang['write_message'];
                                    } else {
                                        echo _lang['your_answer'];
                                    } ?>" id="example-textarea" required rows="3" name="comment_text"
                                        id="comment_text"></textarea>

                                    <?php if ($rbacClass->checkPermissionOperationByName('local_comment_operation')) { ?>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="local" class="form-check-input" id="local">
                                                <label class="form-check-label" for="checkbox-signup">
                                                    <?php echo _lang['local']; ?> <a href="#" class="text-muted">
                                                        <?php echo _lang['just_local_comment']; ?>
                                                    </a>
                                                </label>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($rbacClass->checkPermissionOperationByName('add_comment_operation')) { ?>

                                        <div class="text-end">

                                            <div class="btn-group mb-2 ms-2">
                                                <button type="submit" name="submit" class="btn btn-primary btn-sm">
                                                    <?php if (!isset($_GET['parent_id'])) {
                                                        echo _lang['submit'];
                                                    } else {
                                                        echo _lang['answer'];
                                                    } ?>
                                                </button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </form>

                                <?php while ($commentDetail = $allComments->fetch_assoc()) {

                                    if ($commentDetail['local'] != 0 && $commentDetail['company_id'] != $_SESSION['company_id'])
                                        continue;

                                    if ($commentDetail['user_id'] != '') {
                                        $creator_id = $commentDetail['user_id'];
                                    } else {
                                        $creator_id = $commentDetail['admin_id'];
                                    }

                                    ?>

                                    <div class="d-flex align-items-start mt-2">

                                        <div class="d-flex">
                                            <img class="me-2 rounded-circle"
                                                src="<?php echo ('.' . $commentDetail['file_path'] . $commentDetail['file_name']); ?>"
                                                alt="<?php echo ($commentDetail['name']); ?>" height="32">
                                        </div>
                                        <div class="w-100 overflow-hidden">
                                            <h5 class="mt-0">
                                                <?php echo ($commentDetail['name']); ?> <small class="text-muted float-end">
                                                    <?php $dateConverter = new DateConverter($commentDetail['creation_date'], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?>
                                                </small>
                                            </h5>
                                            <?php $commentTextShow = $commentDetail['comment_text'];

                                            echo "<span style=\"white-space: pre-wrap;\">" . $commentTextShow . "</span>";
                                            ?>

                                            <?php if ($rbacClass->checkPermissionOperationByName('reply_comment_operation')) { ?>
                                                <a href="./projects?id=<?php echo ($_GET['id']); ?>&parent_id=<?php echo ($commentDetail['id']); ?>&creator_id=<?php echo ($creator_id); ?>"
                                                    class="text-muted font-13 d-inline-block mt-2"><i class="mdi mdi-reply"></i>
                                                    <?php echo _lang['reply']; ?>
                                                </a>
                                            <?php } ?>
                                            <?php $replyComments = $commentModel->getCommentPartByParentId($commentDetail['id']);
                                            while ($replyDetail = $replyComments->fetch_assoc()) {
                                                if ($replyDetail['local'] != 0 && $replyDetail['company_id'] != $_SESSION['company_id'])
                                                    continue;
                                                ?>
                                                <div class="d-flex align-items-start mt-3">
                                                    <div class="pe-3"></div>

                                                    <div class="w-100 overflow-hidden">

                                                        <h5 class="mt-0"><img class="me-2 rounded-circle"
                                                                src="<?php echo ('.' . $replyDetail['file_path'] . $replyDetail['file_name']); ?>"
                                                                alt="<?php echo ($replyDetail['name']); ?>" height="32">
                                                            <?php echo ($replyDetail['name']); ?> <small
                                                                class="text-muted float-end">
                                                                <?php $dateConverter = new DateConverter($replyDetail['creation_date'], $config->getNowLanguage('a'));
                                                                echo $dateConverter->convertToShamsi(); ?>
                                                            </small>
                                                        </h5>
                                                        <?php $commentReplyTextShow = $replyDetail['comment_text'];

                                                        echo "<span style=\"white-space: pre-wrap;\">" . $commentReplyTextShow . "</span>";
                                                        ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div> <!-- end card-body-->
                        </div>
                    <?php } ?>

                    <?php if ($rbacClass->checkPermissionOperationByName('view_schedule_operation')) { ?>
                        <div class="card ">

                            <div class="accordion" id="accordionPanelsStayOpenExample">

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false"
                                            aria-controls="panelsStayOpen-collapseTwo">
                                            <h4 class="mt-0 mb-3">
                                                <?php echo _lang['schedule_list']; ?>
                                            </h4>
                                        </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse"
                                        aria-labelledby="panelsStayOpen-headingTwo">
                                        <div class="accordion-body">


                                            <table class="table table-hover table-centered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <?php echo _lang['date_time']; ?>
                                                        </th>

                                                        <th>
                                                            <?php echo _lang['description']; ?>
                                                        </th>
                                                        <th>
                                                            <?php echo _lang['status']; ?>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($allSchedule as $schedule) { ?>
                                                        <tr>
                                                            <td><span class="badge <?php
                                                            if (strtotime($schedule['date_time']) <= strtotime('today')) {
                                                                echo 'bg-danger';
                                                            } elseif (strtotime($schedule['date_time']) <= strtotime('+3 days')) {
                                                                echo 'bg-warning';
                                                            } else {
                                                                echo 'bg-primary';
                                                            }

                                                            ?>">

                                                                    <?php $dateConverter = new DateConverter($schedule['date_time'], $config->getNowLanguage('a'));
                                                                    echo $dateConverter->convertToShamsi(); ?>
                                                                </span>
                                                            </td>

                                                            <td>
                                                                <?php echo $textToolsClass->truncateText($schedule['description']); ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($rbacClass->checkPermissionOperationByName('condition_operation')) { ?>
                                                                    <div class="dropdown">
                                                                        <a href="#" class="dropdown-toggle arrow-none card-drop"
                                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                                            <i class="ri-more-fill"></i>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-end">
                                                                            <!-- item-->

                                                                            <?php while ($scheduleConditions = $allScheduleConditions->fetch_assoc()) { ?>
                                                                                <?php if ($rbacClass->checkPermissionOperationByName($scheduleConditions['condition_name'])) { ?>
                                                                                    <a href="javascript:void(0);"
                                                                                        id="<?php echo $textToolsClass->capitalizeFirstLetter($scheduleConditions['condition_name']); ?>"
                                                                                        class="dropdown-item operation-link"
                                                                                        data-operation="<?php echo $textToolsClass->capitalizeFirstLetter($scheduleConditions['condition_name']); ?>"
                                                                                        data-tableset="<?php echo $scheduleConditions['condition_part']; ?>"
                                                                                        data-id="<?php echo $schedule['id']; ?>">
                                                                                        <?php echo _lang[$scheduleConditions['condition_name']]; ?>
                                                                                    </a>
                                                                                <?php } ?>
                                                                            <?php } ?>



                                                                        </div>
                                                                    </div>


                                                                <?php } ?>


                                                                <?php $scheduleCondition = $structureModel->getConditionsByName($schedule['status']); ?>
                                                                <div
                                                                    class="badge bg-<?php echo $scheduleCondition['condition_color']; ?> mb-3">
                                                                    <?php echo _lang[$scheduleCondition['condition_name']]; ?>
                                                                </div>



                                                            </td>


                                                        </tr>
                                                    <?php } ?>

                                                </tbody>
                                            </table>


                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    <?php } ?>

                    <!-- end card-->
                </div> <!-- end col -->

                <div class="col-lg-6 col-xxl-4">
                    <?php if ($rbacClass->checkPermissionOperationByName('assign_other_operation') or $rbacClass->checkPermissionOperationByName('schedule_operation')) { ?>
                        <div class="card mb-3">
                            <div class=" border h-100 w-100 rounded d-flex ">

                                <?php if ($rbacClass->checkPermissionOperationByName('schedule_operation')) { ?>
                                    <a href="javascript:void(0);" class="text-center text-muted p-2" data-bs-toggle="modal"
                                        data-bs-target="#Schedule">

                                        <button type="button" tabindex="0" class="btn btn-success btn-sm"
                                            data-bs-toggle="popover" data-bs-trigger="hover"
                                            data-bs-content="<?php echo _lang['schedule_note']; ?>"
                                            title="<?php echo _lang['schedule']; ?>">
                                            <?php echo _lang['schedule']; ?>
                                        </button>

                                    </a>
                                <?php } ?>

                                <?php if ($rbacClass->checkPermissionOperationByName('kanban_board_operation')) { ?>
                                    <a href="javascript:void(0);" class="text-center text-muted p-2" data-bs-toggle="modal"
                                        data-bs-target="#KanbanBoard">



                                        <button type="button" tabindex="0" class="btn btn-warning btn-sm"
                                            data-bs-toggle="popover" data-bs-trigger="hover"
                                            data-bs-content="<?php echo _lang['kanban_board_note']; ?>"
                                            title="<?php echo _lang['kanban_board']; ?>">
                                            <?php echo _lang['kanban_board']; ?>
                                        </button>

                                    </a>
                                <?php } ?>

                                <?php if ($rbacClass->checkPermissionOperationByName('priority_operation')) { ?>
                                    <a href="javascript:void(0);" class="text-center text-muted p-2" data-bs-toggle="modal"
                                        data-bs-target="#Priority">



                                        <button type="button" tabindex="0" class="btn btn-primary  btn-sm"
                                            data-bs-toggle="popover" data-bs-trigger="hover"
                                            data-bs-content="<?php echo _lang['priority_note']; ?>"
                                            title="<?php echo _lang['priority']; ?>">
                                            <?php echo _lang['priority']; ?>
                                        </button>

                                    </a>
                                <?php } ?>
                            </div>
                        </div> <!-- end card-body -->
                    <?php } ?>

                    <?php if ($rbacClass->checkPermissionOperationByName('man_hour_view_operation')) { ?>
                        <!-- man-hour-->
                        <div class="card border">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="header-title">
                                    <?php echo (_lang['man_hour']); ?>
                                </h4>
                                <code> <?php echo (_lang['registered']); ?> : <?php echo ($manhourModel->getTotalManHourPart()); ?>                                                                                             <?php echo (_lang['hour']); ?></code>
                            </div>

                            <div class="card-body py-0 mb-3" data-simplebar style="max-height: 100px;">
                                <div class="timeline-alt py-0">

                                    <?php while ($manHourDetail = $allManHour->fetch_assoc()) {
                                        if ($manHourDetail['company_id'] != '') {
                                            if ($manHourDetail['company_id'] != $structureModel->getCompanyByUnitId($_SESSION['unit_id']))
                                                continue;
                                        }

                                        ?>
                                        <div class="timeline-item">
                                            <i class="mdi mdi-clock-edit bg-info-lighten text-info timeline-icon"></i>
                                            <div class="timeline-item-info">
                                                <a href="#" class="text-info fw-bold mb-1 d-block">
                                                    <?php echo ($manHourDetail['name']); ?>
                                                </a>

                                                <p class="mb-0 pb-0">
                                                    <span class="badge badge-outline-warning pt-1">
                                                        <?php echo (_lang[$manHourDetail['todo']]); ?>
                                                    </span> <small class="text-muted">
                                                        <?php echo ($manHourDetail['subject']); ?>
                                                    </small>

                                                </p>
                                                <p class="mb-0 pb-2">
                                                    <small class="text-muted">
                                                        <?php echo ($manHourDetail['man_hour_number']); ?>
                                                        <?php echo (_lang['hour']); ?>
                                                    </small> <small>
                                                        (
                                                        <?php echo ($manHourDetail['creation_date']); ?>)
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                    <?php } ?>


                                </div>
                                <!-- end timeline -->


                            </div> <!-- end conversation-->

                            <div class="card-body pt-0 ">
                                <?php if ($rbacClass->checkPermissionOperationByName('man_hour_add_operation')) { ?>
                                    <form class="needs-validation" validate name="man-hour" id="man-hour"
                                        action="./projects?id=<?php echo ($_GET['id']); ?>" method="post"
                                        onsubmit="return validateForm();">
                                        <div class="row align-items-start">
                                            <div class="mb-1">
                                                <div class="input-group">
                                                    <button class="btn btn-primary dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                        id="dropdownTodoMenuButton">
                                                        <?php echo (_lang['subject']); ?>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownTodoMenuButton">
                                                        <?php
                                                        while ($todoListsDetails = $todoListsResult->fetch_assoc()) {
                                                            ?>
                                                            <a class="dropdown-item dropdown-todo" href="#"
                                                                data-value="<?php echo $todoListsDetails['todo_list_name']; ?>">
                                                                <?php echo _lang[$todoListsDetails['todo_list_name']]; ?>
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                    <input required type="hidden" name="selected_todo_list"
                                                        id="selectedTodoList">
                                                    <input required type="text" class="form-control" name="subject"
                                                        placeholder="<?php echo (_lang['enter_description']); ?>"
                                                        aria-label="<?php echo (_lang['enter_description']); ?>"
                                                        aria-describedby="basic-addon1">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <input type="number" min="0" name="man_hour" class="form-control chat-input"
                                                    placeholder="<?php echo (_lang['enter_man_hour']); ?>" required>
                                            </div>
                                            <div class="col-auto d-grid">
                                                <button type="submit" name="hourSubmit" class="btn btn-danger chat-send">
                                                    <?php echo (_lang['add']); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <script>

                                        function validateForm() {
                                            const selectedTodoList = document.getElementById('selectedTodoList').value;
                                            if (!selectedTodoList) {
                                                alert("<?php echo _lang['please_select_todo_list']; ?>");
                                                return false;
                                            }
                                            return true;
                                        }
                                    </script>

                                <?php } ?>
                            </div>


                        </div> <!-- end card-->
                    <?php } ?>

                    <?php if ($rbacClass->checkPermissionOperationByName('attach_view_operation')) { ?>

                        <div class="card border">
                            <div class="card-body">
                                <?php if ($rbacClass->checkPermissionOperationByName('attach_add_operation')) { ?>

                                    <form validate action="./projects?id=<?php echo ($_GET['id']); ?>"
                                        method="post" enctype="multipart/form-data">
                                        <div class="mb-3 mt-3 mt-xl-0">
                                            <label for="attach_file" class="mb-0">
                                                <?php echo _lang['attach_file']; ?>
                                            </label>

                                            <div class="input-group mb-1">

                                                <input type="text" class="form-control" name="file_title" required
                                                    id="file_title" aria-describedby="basic-addon1"
                                                    placeholder="<?php echo _lang['file_title']; ?>">
                                            </div>

                                            <div class="input-group mb-1">
                                                <input type="file" name="attach_file" required id="attach_file"
                                                    class="form-control">

                                                <button class="btn btn-outline-secondary" type="submit">
                                                    <?php echo _lang['attach']; ?>
                                                </button>
                                            </div>

                                            <div class="input-group mb-1">
                                                <?php if ($rbacClass->checkPermissionOperationByName('attach_local_operation')) { ?>

                                                    <input type="checkbox" id="switch3" name="local" checked
                                                        data-switch="success" />

                                                    <label for="switch3" data-on-label="<?php echo _lang['no']; ?>"
                                                        data-off-label="<?php echo _lang['yes']; ?>"></label>


                                                    <span class="mb-1" style="margin-right: 5px; margin-left:5px;">
                                                        <?php echo _lang['global_send']; ?>
                                                    </span>

                                                <?php } ?>
                                            </div>
                                        </div>
                                    </form>
                                <?php } ?>



                                <h5 class="card-title mb-3">
                                    <?php echo (_lang['sended_files']); ?>
                                </h5>
                                <?php foreach ($allFileInfo as $fileInfo) {

                                    if (!empty($fileInfo['CompanyId']) && $fileInfo['CompanyId'] != $structureModel->getCompanyByUnitId($_SESSION['unit_id'])) {
                                        continue;
                                    }

                                    $fileDownloadUrl = "./projects?id=" . $_GET['id'] . "&file=." . $fileInfo['downloadLink'];
                                    ?>
                                    <div class="card mb-1 shadow-none border">
                                        <div class="p-2">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="avatar-sm">
                                                        <span class="avatar-title rounded">
                                                            .
                                                            <?php echo $fileInfo['fileType']; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col ps-0">
                                                    <a href="<?php echo ($fileDownloadUrl); ?>" class="text-muted fw-bold">
                                                        <?php echo empty($fileInfo['fileTitle']) ? $fileInfo['fileName'] : $fileInfo['fileTitle']; ?>
                                                    </a>
                                                    <p class="mb-0">
                                                        <?php echo $fileInfo['fileSize']; ?>
                                                    </p>
                                                </div>
                                                <div class="col-auto">
                                                    <!-- Button -->
                                                    <a href="<?php echo ($fileDownloadUrl); ?>"
                                                        class="btn btn-link btn-lg text-muted">
                                                        <i class="ri-download-2-line"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php } ?>

                            </div>
                        </div>

                    <?php } ?>


                    <!-- Schedule Modal -->

                    <div class="modal fade" id="Schedule" tabindex="-1" aria-labelledby="ScheduleLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ScheduleLabel">
                                        <?php echo _lang['schedule']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-3">
                                    <form validate action="./projects?id=<?php echo ($_GET['id']); ?>"
                                        method="post" enctype="multipart/form-data">

                                        <input type="hidden" value="projects" id="section_part_name"
                                            name="section_part_name">
                                        <input type="hidden" value="<?php echo ($_GET['id']); ?>"
                                            id="section_element_id" name="section_element_id">

                                        <div class="mb-3">
                                            <label for="date_time" class="form-label">
                                                <?php echo _lang['date_time']; ?>
                                            </label>
                                            <input type="text" id="datetime-datepicker"
                                                class="form-control persianDatepicker" name="date_time"
                                                placeholder="<?php echo _lang['date_time']; ?>" required>

                                        </div>


                                        <div class="mb-3">
                                            <label for="description" class="form-label">
                                                <?php echo _lang['description']; ?>
                                            </label>
                                            <textarea class="form-control" id="description" name="description"
                                                rows="4"></textarea>
                                        </div>


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                        <?php echo _lang['close']; ?>
                                    </button>
                                    <button type="submit" name="schedule" value="projects" class="btn btn-primary">
                                        <?php echo _lang['assign']; ?>
                                    </button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <!-- Kaban board modal -->
                    <div class="modal fade" id="KanbanBoard" tabindex="-1" aria-labelledby="KanbanBoardLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="KanbanBoardLabel">
                                        <?php echo _lang['kanban_board']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-3">
                                    <form id="kabanForm"
                                        action="./projects?id=<?php echo ($_GET['id']); ?>" method="post"
                                        enctype="multipart/form-data">

                                        <input type="hidden" value="projects" id="part_name" name="part_name">
                                        <input type="hidden" value="<?php echo ($_GET['id']); ?>" id="part_id"
                                            name="part_id">


                                        <div class="mb-3">
                                            <label class="form-label"><?php echo (_lang['tag']); ?></label>
                                            <select name="board_tag" class="form-select form-control-light" required>
                                                <option value="" selected><?php echo (_lang['select']); ?></option>
                                                <option value="important"><?php echo (_lang['important']); ?>
                                                </option>
                                                <option value="follow_up"><?php echo (_lang['follow_up']); ?>
                                                </option>
                                                <option value="review"><?php echo (_lang['review']); ?></option>
                                                <option value="in_progress"><?php echo (_lang['in_progress']); ?>
                                                </option>
                                            </select>
                                        </div>



                                        <div class="mb-3">
                                            <label for="description"
                                                class="form-label"><?php echo (_lang['description']); ?></label>
                                            <textarea name="description" required
                                                class="form-control form-control-light" id="description"
                                                rows="3"></textarea>
                                        </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                        <?php echo _lang['close']; ?>
                                    </button>
                                    <button type="submit" name="kanban_board" id="submitKaban" class="btn btn-primary">
                                        <?php echo _lang['add']; ?>
                                    </button>


                                </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <!-- priority modal -->
                    <div class="modal fade" id="Priority" tabindex="-1" aria-labelledby="PriorityLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="PriorityLabel">
                                        <?php echo _lang['priority']; ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-3">
                                    <form id="priorityForm"
                                        action="./projects?id=<?php echo ($_GET['id']); ?>" method="post"
                                        enctype="multipart/form-data">

                                        <input type="hidden" value="projects" id="part_name" name="part_name">
                                        <input type="hidden" value="<?php echo ($_GET['id']); ?>" id="part_id"
                                            name="part_id">


                                        <div class="mb-3">
                                            <label class="form-label"><?php echo (_lang['priority']); ?></label>
                                            <select name="priority" class="form-control form-select" id="priority"
                                                required>
                                                <option selected value="low"><?php echo (_lang['low']); ?></option>
                                                <option value="medium"><?php echo (_lang['medium']); ?></option>
                                                <option value="high"><?php echo (_lang['high']); ?></option>
                                            </select>
                                        </div>


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                        <?php echo _lang['close']; ?>
                                    </button>
                                    <button type="submit" name="submitPriority" id="submitPriority"
                                        class="btn btn-primary">
                                        <?php echo _lang['add']; ?>
                                    </button>


                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.getElementById('submitKaban').addEventListener('click', function (event) {
                            var description = document.getElementById('description').value;
                            var receiver = document.getElementById('board_tag').value;

                            if (description.trim() === '' || receiver === null || receiver.length === 0) {
                                event.preventDefault();
                                alert('لطفا تمامی فیلدها را پر کنید.');
                            }

                            if (description.trim() === '' || description.trim().length < 5) {
                                event.preventDefault();
                                alert('توضیحات نباید کوتاه باشد.');
                                return;
                            }
                        });
                    </script>


                </div>
            </div>
            <!-- end row -->




        </div> <!-- container -->

    </div> <!-- content -->