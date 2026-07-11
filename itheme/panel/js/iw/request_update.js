$(document).ready(function() {
    // هنگام کلیک بر روی دکمه ارسال
    $('.send-button').on('click', function() {
        var form = $(this).closest('tr').find('.updateForm'); // پیدا کردن فرم مربوطه
        var id = form.find('input[name="id"]').val(); // دریافت مقدار ID

        var person_hour_response = form.find('input[name="person_hour_response"]').val();
        var delivery_time_response = form.find('input[name="delivery_time_response"]').val();

        if (person_hour_response || delivery_time_response) {
            $.ajax({
                url: '../icore/json/update_request.php',
                type: 'POST',
                data: { person_hour_response: person_hour_response, delivery_time_response: delivery_time_response, id: id }, 
                success: function(response) {
                    console.log('Request updated successfully!');
                    setTimeout(function() {
                        location.reload(); // صفحه بعد از 2 ثانیه رفرش می‌شود
                    }, 2002);
                },
                error: function(xhr, status, error) {
                    console.log('An error occurred: ' + error);
                    console.log(xhr.responseText);
                }
            });
        } else {
            alert("لطفاً یکی از فیلدها را پر کنید.");
        }
    });
});

