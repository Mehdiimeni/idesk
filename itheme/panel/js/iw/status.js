// edit status
$(document).ready(function () {
    $('.operation-link').click(function () {
        var operation = $(this).data('operation');
        var table_set = $(this).data('tableset');
        var id = $(this).data('id');
        var needsDescription = $(this).data('needs-description');

        if (needsDescription == 1) {
            $('#descriptionModal').modal('show');

            $('#submitDescription').click(function () {
                var description = $('#status_description').val(); 

                if (description) {
                    $.ajax({
                        type: "POST",
                        url: "../icore/json/edit_status.php", 
                        data: { operation: operation, table_set: table_set, id: id, description: description },
                        success: function (response) {
                            console.log(response);
                            setTimeout(x => {
                                location.reload();
                            }, 2002);
                        }
                    });
                } else {
                    alert("Please enter a description.");
                }
            });
        } else {
            $.ajax({
                type: "POST",
                url: "../icore/json/edit_status.php", 
                data: { operation: operation, table_set: table_set, id: id },
                success: function (response) {
                    console.log(response);
                    setTimeout(x => {
                        location.reload();
                    }, 2002);
                }
            });
        }
    });
});
