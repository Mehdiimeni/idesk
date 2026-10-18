$(function () {
    $(document)
        .off('click.dashboardRequestUpdate', '.send-button')
        .on('click.dashboardRequestUpdate', '.send-button', function () {
            var button = $(this);
            var form = button.closest('.updateForm');

            if (!form.length) {
                return;
            }

            var id = form.find('input[name="id"]').val();
            var personHourResponse = $.trim(form.find('input[name="person_hour_response"]').val() || '');
            var deliveryTimeResponse = $.trim(form.find('input[name="delivery_time_response"]').val() || '');

            if (!personHourResponse && !deliveryTimeResponse) {
                alert('لطفاً پاسخ درخواست را وارد کنید.');
                return;
            }

            var originalHtml = button.html();
            button.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
                '<span>...</span>'
            );

            $.ajax({
                url: '../icore/json/update_request.php',
                type: 'POST',
                data: {
                    person_hour_response: personHourResponse,
                    delivery_time_response: deliveryTimeResponse,
                    id: id
                }
            })
                .done(function () {
                    window.location.reload();
                })
                .fail(function (xhr, status, error) {
                    console.error('Request update failed:', status, error, xhr.responseText);
                    button.prop('disabled', false).html(originalHtml);
                    alert('ثبت پاسخ انجام نشد. لطفاً دوباره تلاش کنید.');
                });
        });
});
