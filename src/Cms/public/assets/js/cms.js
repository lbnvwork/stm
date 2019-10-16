//begin добавление компании
$('#js-user-add').on('click', function () {
    submitForm($('#js-user-form'));
    return false;
});

//end добавление компании

function submitForm(form)
{
    var url = form.attr("action");
    var formData = $(form).serializeArray();
    $.post(url, formData).done(function (data) {
        if (data.error) {
            var htmlString = '';
            for (var err in data.error) {
                htmlString += '<div class="alert alert-danger alert-dismissible fade in" role="alert">\n' +
                    '    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\n' +
                    data.error[err] +
                    '</div>';
            }
            $('.bs-example-popovers').html(htmlString);
            return false;
        } else if (data.url) {
            $(location).attr('href', data.url);
        } else {
            alert('Ошибка сервиса');
        }
    });
    return false;
}