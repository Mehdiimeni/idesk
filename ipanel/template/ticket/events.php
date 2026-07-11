<?php
///template/ticket/events.php
?>

<div class="content-page">
    <div class="content">
        <!-- Start Content -->
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title"><?php echo _lang['events']; ?></h4>
                    </div>
                </div>
            </div>
            <!-- Page Title End -->

            <!-- Main Content -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="d-grid">
                                        <button class="btn btn-lg font-16 btn-danger" id="btn-new-event">
                                            <i class="mdi mdi-plus-circle-outline"></i>
                                            <?php echo _lang['add_event']; ?>
                                        </button>
                                    </div>
                                    <div id="external-events" class="mt-3">
                                        <p class="text-muted"><?php echo _lang['events']; ?></p>
                                        <div class="external-event bg-success-lighten text-success"
                                            data-class="bg-success">
                                            <i class="mdi mdi-checkbox-blank-circle me-2 vertical-middle"></i>
                                            <?php echo _lang['meeting']; ?>
                                        </div>
                                        <div class="external-event bg-info-lighten text-info" data-class="bg-info">
                                            <i class="mdi mdi-checkbox-blank-circle me-2 vertical-middle"></i>
                                            <?php echo _lang['training_course']; ?>
                                        </div>
                                        <div class="external-event bg-warning-lighten text-warning"
                                            data-class="bg-warning">
                                            <i class="mdi mdi-checkbox-blank-circle me-2 vertical-middle"></i>
                                            <?php echo _lang['presentation']; ?>
                                        </div>
                                        <div class="external-event bg-danger-lighten text-danger"
                                            data-class="bg-danger">
                                            <i class="mdi mdi-checkbox-blank-circle me-2 vertical-middle"></i>
                                            <?php echo _lang['celebration']; ?>
                                        </div>
                                        <div class="external-event bg-dark-lighten text-dark" data-class="bg-dark">
                                            <i class="mdi mdi-checkbox-blank-circle me-2 vertical-middle"></i>
                                            <?php echo _lang['other']; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="mt-4 mt-lg-0">
                                        <div id="calendar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main Content End -->

            <!-- Event Modal -->
            <div class="modal fade" id="event-modal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="needs-validation" id="form-event" novalidate>
                            <div class="modal-header py-3 px-4 border-bottom-0">
                                <h5 class="modal-title" id="modal-title"><?php echo _lang['add_edit_event']; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-4 pb-4 pt-0">
                                <input type="hidden" id="event-id" name="id"> <!-- Hidden field for storing event ID -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label
                                                class="control-label form-label"><?php echo _lang['event_name']; ?></label>
                                            <input class="form-control" placeholder="<?php echo _lang['event_name']; ?>"
                                                type="text" name="title" id="event-title" required />
                                            <div class="invalid-feedback"><?php echo _lang['event_name_provide']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label
                                                class="control-label form-label"><?php echo _lang['category']; ?></label>
                                            <select class="form-select" name="category" id="event-category" required>
                                                <option value="bg-danger"><?php echo _lang['celebration']; ?></option>
                                                <option value="bg-success"><?php echo _lang['meeting']; ?></option>
                                                <option value="bg-info"><?php echo _lang['training_course']; ?></option>
                                                <option value="bg-dark"><?php echo _lang['other']; ?></option>
                                                <option value="bg-warning"><?php echo _lang['presentation']; ?></option>
                                            </select>
                                            <div class="invalid-feedback"><?php echo _lang['category_select']; ?></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label
                                                class="control-label form-label"><?php echo _lang['start_date']; ?></label>
                                            <input class="form-control" type="datetime-local" name="start_date"
                                                id="start-date" required />
                                            <div class="invalid-feedback"><?php echo _lang['start_date_provide']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label
                                                class="control-label form-label"><?php echo _lang['end_date']; ?></label>
                                            <input class="form-control" type="datetime-local" name="end_date"
                                                id="end-date" />
                                            <div class="invalid-feedback"><?php echo _lang['end_date_provide']; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-danger"
                                            id="btn-delete-event"><?php echo _lang['delete']; ?></button>
                                    </div>
                                    <div class="col-6 text-end">
                                        <button type="button" class="btn btn-light me-1"
                                            data-bs-dismiss="modal"><?php echo _lang['close']; ?></button>
                                        <button type="submit" class="btn btn-success"
                                            id="btn-save-event"><?php echo _lang['save']; ?></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Event Modal End -->

        </div> <!-- container -->
    </div> <!-- content -->
</div> <!-- content-page -->