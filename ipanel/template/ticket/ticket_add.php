<?php
///template/ticket/tickets_add.php
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
                                    <?php echo _lang['new_ticket']; ?>
                                </li>
                                <li class="breadcrumb-item"><a href="./tickets">
                                        <?php echo _lang['my_tickets']; ?>
                                    </a></li>
                            </ol>
                        </div>
                        <h4 class="page-title">
                            <?php echo _lang['new_ticket']; ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <form action="./tickets?add=r" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    
                                    <div class="col-xl-6">
                                        <div class="mb-3 mt-3 mt-xl-0">
                                            <label for="ticket_title" class="form-label">
                                                <?php echo _lang['title']; ?>
                                            </label>
                                            <input type="text" id="ticket_title" name="ticket_title" required
                                                class="form-control" placeholder="<?php echo _lang['enter_title']; ?>">
                                        </div>

                                    </div> <!-- end col-->
                                    <div class="col-xl-6">
                                        <div class="mb-3 mt-3 mt-xl-0">
                                            <label for="company_id" class="form-label">
                                                <?php echo _lang['company']; ?>
                                            </label>
                                            <select class="select2 form-control select2-multiple" data-toggle="select2" name="company_id" id="company_id" required>
                                                <option selected><?php echo (_lang['select']); ?></option>

                                                <?php
                                                while ($company_profilesDetails = $company_profilesResult->fetch_assoc()) {
                                                    $company_id = $company_profilesDetails['id'];
                                                    ?>
                                                    <option value="<?php echo ($company_id); ?>">
                                                        <?php echo ($company_profilesDetails['company_name']); ?></option>
                                                <?php } ?>
                                                </select>
                                        </div>
                                    </div>

                                    <div class="col-xl-6">

                                        <div class="mb-3 mt-3 mt-xl-0">
                                            <label for="type_id" class="form-label">
                                                <?php echo _lang['type']; ?>
                                            </label>

                                            <select class="form-control select2" data-toggle="select2" name="type_id"
                                                id="type_id">
                                                <option selected><?php echo (_lang['select']); ?></option>
                                                <?php

                                                foreach ($types as $group => $typeList) {
                                                    echo '<optgroup label="' . htmlspecialchars($group) . '">';
                                                    foreach ($typeList as $type) {
                                                        echo '<option value="' . htmlspecialchars($type['id']) . '">' . htmlspecialchars($group) . ' - ' . htmlspecialchars($type['type_name']) . '</option>';
                                                    }
                                                    echo '</optgroup>';
                                                }
                                                ?>
                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="mb-3 mt-3 mt-xl-0">
                                            <label for="priority"
                                                class="form-label"><?php echo (_lang['priority']); ?></label>
                                            <select name="priority" class="form-control form-select" id="priority"
                                                required>
                                                <option selected value="low"><?php echo (_lang['low']); ?></option>
                                                <option value="medium"><?php echo (_lang['medium']); ?></option>
                                                <option value="high"><?php echo (_lang['high']); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="mb-3 mt-3 mt-xl-0">
                                            <label for="indicator_number" class="form-label">
                                                <?php echo _lang['indicator_number']; ?>
                                            </label>
                                            <input type="text" id="indicator_number" name="indicator_number" 
                                                class="form-control" placeholder="<?php echo _lang['indicator_number']; ?>">
                                        </div>

                                    </div> <!-- end col-->
                                    <div class="col-xl-6">

                                        <div class="mb-3 mt-3 mt-xl-0">
                                            <label for="attach_file" class="form-label">
                                                <?php echo _lang['attach_file']; ?>
                                            </label>

                                            <input type="file" name="attach_file" id="attach_file" class="form-control">

                                        </div>

                                    </div> <!-- end col-->
                                    <div class="col-xl-12">

                                        <div class="mb-3 mt-3 mt-xl-0">
                                            <label for="AssignTask" class="form-label">
                                                <?php echo _lang['assign']; ?>
                                                <?php echo _lang['admins']; ?>
                                            </label>
                                            <select class="select2 form-control select2-multiple" data-toggle="select2" id="members"
                                                name="members[]"  
                                                data-placeholder="<?php echo _lang['choose']; ?>" required>
                                                <option selected><?php echo (_lang['select']); ?>
                                                </option>
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
                                    </div>

                                </div>
                                <div class="row ">
                                    <div class="col-xl-12 ">

                                        <div class="mb-3 tab-pane show active">
                                            <label for="ticket_description" class="form-label">
                                                <?php echo _lang['description']; ?>
                                            </label>
                                            <textarea class="form-control" name="ticket_description" rows="5" required
                                                placeholder="<?php echo _lang['enter_description']; ?>"></textarea>
                                        </div>

                                    </div> <!-- end col-->
                                    <?php if ($rbacClass->checkPermissionOperationByName('add_ticket_operation')) { ?>

                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-primary mb-2">
                                                <?php echo _lang['submit']; ?>
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                        <!-- end row -->
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
        <!-- end row-->

    </div> <!-- container -->

</div> <!-- content -->