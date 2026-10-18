$(function () {
    $(document)
        .off('click.dashboardRequestConfirmation', '.confirmation-button')
        .on('click.dashboardRequestConfirmation', '.confirmation-button', function () {
            var button = $(this);
            var form = button.closest('.confirmationForm');

            if (!form.length) {
                return;
            }

            var id = form.find('input[name="id"]').val();
            if (!id) {
                return;
            }

            var originalHtml = button.html();
            button.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
                '<span>...</span>'
            );

            $.ajax({
                url: '../icore/json/confirmation_request.php',
                type: 'POST',
                data: { id: id }
            })
                .done(function () {
                    window.location.reload();
                })
                .fail(function (xhr, status, error) {
                    console.error('Request confirmation failed:', status, error, xhr.responseText);
                    button.prop('disabled', false).html(originalHtml);
                    alert('تأیید درخواست انجام نشد. لطفاً دوباره تلاش کنید.');
                });
        });
});
