<?php
///template/global/page_js.php
$arrayComponents = $_SESSION['arrayComponents'];

?>

<!-- Vendor js -->
<script src="./itheme/panel/js/vendor.min.js"></script>


<?php if (in_array('select2', $arrayComponents)) { ?>
    <script src="./itheme/panel/vendor/select2/js/select2.min.js"></script>
    <script src="./itheme/panel/js/jquery.multi-select.js"></script>
<?php } ?>
<?php if (in_array('flat', $arrayComponents)) { ?>
    <script src="./itheme/panel/vendor/flatpickr/flatpickr.min.js"></script>
<?php } ?>

<?php if (in_array('table', $arrayComponents)) { ?>
    <!-- table -->
    <script src="./itheme/panel/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="./itheme/panel/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="./itheme/panel/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="./itheme/panel/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
    <script src="./itheme/panel/vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="./itheme/panel/vendor/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
    <script src="./itheme/panel/vendor/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="./itheme/panel/vendor/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="./itheme/panel/vendor/datatables.net-buttons/js/jszip.min.js"></script>
    <script src="./itheme/panel/js/iw/datatable.js"></script>
<?php } ?>
<?php if (in_array('table_limit', $arrayComponents)) { ?>
    <script src="./itheme/panel/js/iw/datatable_limit.js"></script>
<?php } ?>

<?php if (in_array('editor', $arrayComponents)) { ?>
    <!-- SimpleMDE js -->
    <script src="./itheme/panel/vendor/simplemde/simplemde.min.js"></script>
    <script src="./itheme/panel/js/pages/demo.inbox.js"></script>
<?php } ?>

<?php if (in_array('calendar', $arrayComponents)) { ?>
    <!-- Fullcalendar js -->
    <script src="./itheme/panel/vendor/fullcalendar/main.min.js"></script>
    <script src="./itheme/panel/js/pages/demo.calendar.js"></script>
<?php } ?>
<?php if (in_array('date', $arrayComponents)) { ?>
    <!-- date js -->
    <script src="./itheme/panel/vendor/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
    <script src="./itheme/panel/js/pages/demo.timepicker.js"></script>

<?php } ?>

<?php if (in_array('editor', $arrayComponents)) { ?>
    <!-- SimpleMDE js -->
    <script src="./itheme/panel/vendor/simplemde/simplemde.min.js"></script>

<?php } ?>

<?php if (in_array('kanban_board', $arrayComponents)) { ?>
    <!-- dragula js-->
    <script src="./itheme/panel/vendor/dragula/dragula.min.js"></script>
    <!-- demo js -->
    <script src="./itheme/panel/js/ui/component.dragula.js"></script>
    <script src="./itheme/panel/js/iw/drag.js"></script>
<?php } ?>

<!-- App js -->
<script src="./itheme/panel/js/app.min.js"></script>

<?php if (in_array('tree', $arrayComponents)) { ?>
    <script src="./itheme/panel/vendor/jstree/jstree.min.js"></script>
    <script src="./itheme/panel/js/iw/tree.js"></script>
<?php } ?>

<?php if (in_array('cookie_list_user', $arrayComponents)) { ?>
    <script src="./itheme/panel/js/iw/cookie_list_user.js"></script>
<?php } ?>
<?php if (in_array('operation', $arrayComponents)) { ?>
    <script src="./itheme/panel/js/iw/operation.js"></script>
<?php } ?>

<?php if (in_array('main', $arrayComponents)) { ?>
    <script src="./itheme/panel/js/iw/main.js"></script>
<?php } ?>
<?php if (in_array('lang', $arrayComponents)) { ?>
    <script src="./itheme/panel/js/iw/lang.js"></script>
<?php } ?>
<?php if (in_array('lang_user', $arrayComponents)) { ?>
    <script src="./itheme/panel/js/iw/lang_user.js"></script>
<?php } ?>
<?php if (in_array('status_user', $arrayComponents)) { ?>
    <script src="./itheme/panel/js/iw/status_user.js"></script>
<?php } ?>
<?php if (in_array('multi', $arrayComponents)) { ?>
    <script src="./itheme/panel/js/iw/multi.js"></script>
<?php } ?>
<?php if (in_array('todo', $arrayComponents)) { ?>
    <script src="./itheme/panel/js/iw/todo.js"></script>
<?php } ?>
<?php if (in_array('chat', $arrayComponents)) { ?>
    <script src="./itheme/panel/js/iw/chat_user.js"></script>
<?php } ?>

</body>

</html>