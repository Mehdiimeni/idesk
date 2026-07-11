<?php
///template/project/projects_add.php
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
                                <li class="breadcrumb-item"><a href="./projects"><?php echo _lang['projects']; ?></a>
                                </li>
                            </ol>
                        </div>
                        <h4 class="page-title"><?php echo _lang['create_project']; ?></h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="./projects?add=r" method="post" enctype="multipart/form-data">

                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label for="projectname"
                                                class="form-label"><?php echo _lang['name']; ?></label>
                                            <input type="text" id="projectname" name="name" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="project-overview"
                                                class="form-label"><?php echo _lang['description']; ?></label>
                                            <textarea class="form-control" id="project-overview" rows="5"
                                                name="description" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="priority"
                                                class="form-label"><?php echo (_lang['priority']); ?></label>
                                            <select name="priority" class="form-control form-select" id="priority"
                                                required>
                                                <option selected value="low"><?php echo (_lang['low']); ?></option>
                                                <option value="medium"><?php echo (_lang['medium']); ?></option>
                                                <option value="high"><?php echo (_lang['high']); ?></option>
                                            </select>
                                        </div>


                                    </div> <!-- end col-->

                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label for="projectname"
                                                class="form-label"><?php echo _lang['attachment']; ?></label>
                                            <input type="file" name="attach_file" id="attach_file" class="form-control">
                                        </div>


                                        <!-- Date View -->
                                        <div class="mb-3 position-relative" id="datepicker1">
                                            <label class="form-label"><?php echo (_lang['start_date']); ?></label>
                                            <input type="text" id="datetime-datepicker"
                                                data-date-container="#datepicker1"
                                                class="form-control persianDatepicker" name="start_date" required>
                                        </div>

                                        <!-- Date View -->
                                        <div class="mb-3 position-relative" id="datepicker2">
                                            <label class="form-label"><?php echo (_lang['end_date']); ?></label>
                                            <input type="text" id="datetime-datepicker2"
                                                data-date-container="#datepicker2"
                                                class="form-control persianDatepicker" name="end_date">
                                        </div>


                                        <div class="mb-3">
                                            <label for="AssignTask" class="form-label">
                                                <?php echo _lang['assign']; ?>
                                                <?php echo _lang['admins']; ?>
                                            </label>
                                            <select class="select2 form-control select2-multiple" id="members"
                                                name="members[]" data-toggle="select2" multiple="multiple"
                                                data-placeholder="<?php echo _lang['choose']; ?>" required>
                                                <?php
                                                $rbacAdminsInfo = $rbacClass->getAdminsByOperationName('assign_other_operation');
                                                if ($rbacAdminsInfo) {
                                                    while ($rbacAdminsInfoDetail = $rbacAdminsInfo->fetch_assoc()) {
                                                        ?>
                                                        <option value="<?php echo $rbacAdminsInfoDetail['id']; ?>">
                                                            <?php echo $rbacAdminsInfoDetail['name']; ?>
                                                        </option>
                                                    <?php }
                                                } ?>
                                            </select>
                                        </div>

                                        <?php if ($rbacClass->checkPermissionOperationByName('add_project_operation')) { ?>

                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-primary mb-2">
                                                    <?php echo _lang['submit']; ?>
                                                </button>
                                            </div>
                                        <?php } ?>

                                    </div> <!-- end col-->
                                </div>
                            </form>
                            <!-- end row -->

                        </div> <!-- end card-body -->
                    </div> <!-- end card-->
                </div> <!-- end col-->
            </div>
            <!-- end row-->

        </div> <!-- container -->

    </div> <!-- content -->
</div>