$(document).ready(function() {
    
    $('.confirmation-button').on('click', function() {
        var form = $(this).closest('tr').find('.confirmationForm'); // پیدا کردن فرم مربوطه
        var id = form.find('input[name="id"]').val(); // دریافت مقدار ID


            $.ajax({
                url: '../icore/json/confirmation_request.php',
                type: 'POST',
                data: { id: id }, 
                success: function(response) {
                    console.log('confirmation successfully!');
                    setTimeout(function() {
                        location.reload(); 
                    }, 2002);
                },
                error: function(xhr, status, error) {
                    console.log('An error occurred: ' + error);
                    console.log(xhr.responseText);
                }
            });
      
    });
});

