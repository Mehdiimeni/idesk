<?php
///template/project/projects.php
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
                            <?php echo _lang['projects_list']; ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <?php if ($rbacClass->checkPermissionOperationByName('add_project_operation')) { ?>
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <a href="./projects?add=r" class="btn btn-danger rounded-pill mb-3"><i class="mdi mdi-plus"></i>
                            <?php echo _lang['create_project']; ?>
                        </a>
                    </div>
                </div>
            <?php } ?>
            <!-- end row-->

            <div class="row">

                <?php foreach ($allProjects as $projectDetails) {
                    if (!$projectsModel->checkMemberInProject($projectDetails['id']) && !$rbacClass->checkPermissionOperationByName('observer_project_operation'))
                        continue;
                    ?>
                    <div class="col-md-6 col-xxl-3">
                        <!-- project card -->
                        <div class="card d-block">
                            <div class="card-body">
                                <?php if ($rbacClass->checkPermissionOperationByName('delete_project_operation')) { ?>

                                    <div class="dropdown card-widgets">
                                        <a href="#" class="dropdown-toggle arrow-none" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ri-more-fill"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">

                                            <a href="javascript:void(0);" class="dropdown-item"><i
                                                    class="mdi mdi-delete me-1"></i>
                                                <?php echo _lang['delete']; ?>
                                            </a>

                                        </div>
                                    </div>
                                <?php } ?>
                                <!-- project title-->
                                <h4 class="mt-0">
                                    <?php if ($rbacClass->checkPermissionOperationByName('view_project_operation')) { ?>
                                        <a href="./projects?id=<?php echo $encryptorClass->encrypt($projectDetails['id']); ?>"
                                            class="text-title">
                                            <?php echo $projectDetails['name']; ?>
                                        </a>
                                    <?php } else { ?>
                                        <?php echo $projectDetails['name']; ?>
                                    <?php } ?>
                                </h4>
                                <div class="badge bg-success">
                                    <?php echo $projectDetails['priority']; ?>
                                </div>

                                <p class="text-muted font-13 my-3">
                                    <?php echo $textToolsClass->truncateText($projectDetails['description'], 160); ?>
                                </p>

                                <!-- project detail-->
                                <p class="mb-1">
                                    <span class="pe-2 text-nowrap mb-2 d-inline-block">
                                        <i class="mdi mdi-account-box text-muted"></i>
                                        <b>
                                            <?php echo $adminModel->getAdminNameById($projectDetails['admin_id'])['name']; ?>
                                        </b>
                                    </span>
                                    <span class="pe-2 text-nowrap mb-2 d-inline-block">
                                        <i class="mdi mdi-format-list-bulleted-type text-muted"></i>
                                        <b>
                                            <?php echo ($projectsModel->getCountSchedule($projectDetails['id'])); ?>
                                        </b>
                                        <?php echo _lang['schedule']; ?>
                                    </span>
                                    <span class="text-nowrap mb-2 d-inline-block">
                                        <i class="mdi mdi-comment-multiple-outline text-muted"></i>
                                        <b>
                                            <?php echo ($commentModel->getCountCommentByElementId($projectDetails['id'],'projects')); ?>
                                        </b>
                                        <?php echo _lang['comments']; ?>
                                    </span>
                                </p>
                                <div id="tooltip-container">

                                    <?php 
                                    $theFirstThreeMembers = $projectsModel->getProjectAdminInfoWithImage($projectDetails['id'],6);
                                    foreach ($theFirstThreeMembers as $memberDetails) { ?>
                                        <a href="javascript:void(0);" data-bs-container="#tooltip-container"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="<?php echo $memberDetails['name']; ?>" class="d-inline-block">
                                            <img src="<?php echo ("." . $memberDetails['file_path'] . $memberDetails['file_name']); ?>"
                                                class="rounded-circle avatar-xs" alt="<?php echo $memberDetails['name']; ?>">
                                        </a>
                                    <?php } ?>
                                    <a href="javascript:void(0);" class="d-inline-block text-muted fw-bold ms-2">
                                    <?php echo ($projectsModel->getCountProjectMembers($projectDetails['id'])); ?>
                                        <?php echo _lang['member']; ?>
                                    </a>
                                </div>
                            </div> <!-- end card-body-->
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item p-3">
                                    <!-- project progress-->
                                    <p class="mb-2 fw-bold">
                                        <?php echo _lang['progress']; ?><span class="float-end">
                                            <?php echo $projectDetails['progress_percentage'] ?>%
                                        </span>
                                    </p>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar" role="progressbar"
                                            aria-valuenow="<?php echo $projectDetails['progress_percentage'] ?>"
                                            aria-valuemin="0" aria-valuemax="100"
                                            style="width: <?php echo $projectDetails['progress_percentage'] ?>%;">
                                        </div><!-- /.progress-bar -->
                                    </div><!-- /.progress -->
                                </li>
                            </ul>
                        </div> <!-- end card-->
                    </div> <!-- end col -->
                <?php } ?>

            </div>
            <!-- end row-->



        </div> <!-- container -->

    </div> <!-- content -->
</div>