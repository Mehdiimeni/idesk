

// delete
$(document).ready(function () {
    var deleteItemId;
    var deleteTableName;

    $('.delete-item').on('click', function () {
        deleteItemId = $(this).data('id');
        deleteTableName = $(this).data('table');
    });

    $('.confirm-delete').on('click', function () {
        $.ajax({
            type: 'POST',
            url: './icore/json/table_delete.php',
            data: { id: deleteItemId, table: deleteTableName },
            success: function (response) {
                console.log(response);
                $('#deleteModal').modal('hide');
                setTimeout(x => {
                    location.reload();
                }, 2002)
            },
            error: function (error) {
                $('#dangerModal').modal('show');
            }
        });
    });
});

// active
$(document).ready(function () {
    var activeItemId;
    var activeTableName;

    $('.active-item').on('click', function () {
        activeItemId = $(this).data('id');
        activeTableName = $(this).data('table');
    });

    $('.confirm-active').on('click', function () {
        $.ajax({
            type: 'POST',
            url: './icore/json/table_active.php',
            data: { id: activeItemId, table: activeTableName },
            success: function (response) {
                console.log(response);
                $('#activeModal').modal('hide');
                setTimeout(x => {
                    location.reload();
                }, 2002)
            },
            error: function (error) {
                $('#dangerModal').modal('show');
            }
        });
    });
});
// inactive
$(document).ready(function () {
    var inactiveItemId;
    var inactiveTableName;

    $('.inactive-item').on('click', function () {
        inactiveItemId = $(this).data('id');
        inactiveTableName = $(this).data('table');
    });

    $('.confirm-inactive').on('click', function () {
        $.ajax({
            type: 'POST',
            url: './icore/json/table_inactive.php',
            data: { id: inactiveItemId, table: inactiveTableName },
            success: function (response) {
                console.log(response);
                $('#inactiveModal').modal('hide');
                setTimeout(x => {
                    location.reload();
                }, 2002)
            },
            error: function (error) {
                $('#dangerModal').modal('show');
            }
        });
    });
});

// add table

$(document).ready(function () {
    $(document).on('click', '#addDataBtn', function () {
        var formAddData = {};
        $('.addField').each(function () {
            var fieldName = $(this).attr('name');
            var fieldValue = $(this).val();
            formAddData[fieldName] = fieldValue;
        });

        $.ajax({
            type: 'POST',
            url: './icore/json/table_add.php',
            data: { formAddData: JSON.stringify(formAddData) },
            success: function (response) {
                console.log(response);
                $('#addModal').modal('hide');
                setTimeout(x => {
                    location.reload();
                }, 2002)

            },
            error: function (error) {
                $('#danger-alert-modal').modal('show');
            }
        });
    });
});



// edit get details
$(document).ready(function () {
    $('.edit-item').on('click', function () {
        var tableId = $(this).data('id');
        var tableName = $(this).data('table');

        $.ajax({
            type: 'POST',
            url: './icore/json/get_table_details.php',
            data: { tableId: tableId, tableName: tableName },
            success: function (response) {
                var dataDetails = JSON.parse(response);

                $('.editField').each(function () {
                    var fieldName = $(this).attr('name');

                    if (fieldName !== 'unique_fields') {
                        $(this).val(dataDetails[fieldName]);
                    }
                });

                $('#editModal').modal('show');
            },
            error: function (error) {
                $('#danger-alert-modal').modal('show');
            }
        });
    });

    // edit
    $('#editDataBtn').on('click', function () {
        var formEditData = {};
        $('.editField').each(function () {
            var fieldName = $(this).attr('name');
            var fieldValue = $(this).val();
            formEditData[fieldName] = fieldValue;
        });

        $.ajax({
            type: 'POST',
            url: './icore/json/table_edit.php',
            data: { formEditData: JSON.stringify(formEditData) },
            success: function (response) {
                console.log(response);
                $('#editModal').modal('hide');

                setTimeout(x => {
                    location.reload();
                }, 2002)

            },
            error: function (error) {
                $('#danger-alert-modal').modal('show');

            }
        });
    });
});












