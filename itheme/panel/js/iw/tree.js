
$(document).ready(function () {
    // Load admin tree
    // Function to get URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Get the 'id' parameter from URL
    var id = getUrlParameter('id');

    // Load admin tree
    $('#admin-tree').jstree({
        plugins: ["wholerow", "checkbox", "types"],
        core: {
            themes: { responsive: false },
            data: {
                url: function (node) {
                    return '../icore/json/get_parts_tree.php?type=admin&id=' + id;
                },
                dataType: 'json'
            }
        },
        types: { default: { icon: "ri-folder-line text-warning" }, file: { icon: "ri-article-line text-warning" } }
    });

    // Load user tree
    $('#user-tree').jstree({
        plugins: ["wholerow", "checkbox", "types"],
        core: {
            themes: { responsive: false },
            data: {
                url: function (node) {
                    return '../icore/json/get_parts_tree.php?type=user&id=' + id;
                },
                dataType: 'json'
            }
        },
        types: { default: { icon: "ri-folder-line text-warning" }, file: { icon: "ri-article-line text-warning" } }
    });


    $('#submit-tree-btn').on('click', function () {
        var selectedAdminNodes = $('#admin-tree').jstree("get_checked", true);
        var selectedUserNodes = $('#user-tree').jstree("get_checked", true);

        var adminSelections = selectedAdminNodes.map(function (node) {
            return { id: node.id, category: node.original.category };
        });

        var userSelections = selectedUserNodes.map(function (node) {
            return { id: node.id, category: node.original.category };
        });

        var rbacId = $('#rbac_id').val();

        var formData = {
            rbac_id: rbacId,
            admin_selections: adminSelections,
            user_selections: userSelections
        };

        $.ajax({
            url: '../icore/json/parts_tree_add.php',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json; charset=utf-8',
            success: function (response) {
                alert('Data submitted successfully!');
            },
            error: function (error) {
                console.error('Error submitting data:', error);
            }
        });
    });

    $('#edit-tree-btn').on('click', function () {
        var selectedAdminNodes = $('#admin-tree').jstree("get_checked", true);
        var selectedUserNodes = $('#user-tree').jstree("get_checked", true);

        var adminSelections = selectedAdminNodes.map(function (node) {
            return { id: node.id, category: node.original.category };
        });

        var userSelections = selectedUserNodes.map(function (node) {
            return { id: node.id, category: node.original.category };
        });

        var rbacId = $('#rbac_id').val();

        var formData = {
            rbac_id: rbacId,
            admin_selections: adminSelections,
            user_selections: userSelections
        };

        $.ajax({
            url: '../icore/json/parts_tree_edit.php', // Assuming this is your edit endpoint
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json; charset=utf-8',
            success: function (response) {
                alert('Data updated successfully!');
            },
            error: function (error) {
                console.error('Error updating data:', error);
            }
        });
    });

});
