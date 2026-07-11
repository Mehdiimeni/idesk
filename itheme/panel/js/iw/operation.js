document.addEventListener('DOMContentLoaded', function() {
    var submitTreeBtn = document.getElementById('submit-operation-btn');
    
    // Only attach event listener if the button exists
    if (submitTreeBtn) {
        submitTreeBtn.addEventListener('click', function() {
            var rbacId = document.getElementById('rbac_id').value;
            
            // دریافت تمام checkbox های انتخاب شده
            var operationCheckboxes = document.querySelectorAll('input[name="operation[]"]:checked');
            var operations = [];
            operationCheckboxes.forEach(function(checkbox) {
                operations.push(checkbox.value);
            });

            var data = {
                rbac_id: rbacId,
                operations: operations
            };

            fetch('../icore/json/add_operation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Operation added successfully');
                    window.location.href = './user_view';
                } else {
                    alert('Failed to add operation: ' + (data.message || 'Unknown error'));
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Error occurred while adding operation');
            });
        });
    }
});