$(document).ready(function () {
    //@todo вынести функционал в функции, убрать дублирование кода, мб сделать из функций класс-библиотеку

    console.log($('#sidebar-menu').find('li.active ul'));

    // if($.cookie('toggle-menu')=='hide'){
    //     $('#sidebar-menu').find('li.active ul').hide();
    //     $('#sidebar-menu').find('li.active').addClass('active-sm').removeClass('active');
    // }else if($.cookie('toggle-menu')=='show'){
    //     $('#sidebar-menu').find('li.active-sm ul').show();
    //     $('#sidebar-menu').find('li.active-sm').addClass('active').removeClass('active-sm');
    // }

    if ($.cookie('toggle-menu')=='show') {
        $('#sidebar-menu').find('li.active-sm ul').show();
        $('#sidebar-menu').find('li.active-sm').addClass('active').removeClass('active-sm');
        $('body').removeClass('nav-sm');
        $('body').addClass('nav-md');
    } else if ($.cookie('toggle-menu')=='hide') {
        // $('#sidebar-menu').find('li.active ul').hide();
        // $('#sidebar-menu').find('li.active').addClass('active-sm').removeClass('active');
        $('body').removeClass('nav-md');
        $('body').addClass('nav-sm');
    }

    //begin добавление компании
    $('#js-company-add').on('click', function () {
        submitForm($('#js-company-form'));
        return false;
    });
    //end добавление компании

    //begin добавление кассы
    $('#js-kkt-add').on('click', function () {
        submitForm($('#js-kkt-form'));
        return false;
    });
    //end добавление кассы

    //begin изменение компании
    // $('#js-company-edit').on('click', function () {
    //     submitForm($('#js-company-form'));
    //     return false;
    // });
    //end изменение компании

    //begin изменение кассы
    // $('#js-kkt-edit').on('click', function () {
    //     submitForm($('#js-kkt-form'));
    //     return false;
    // });
    //end изменение кассы

    //begin удаление элемента списка
    $('.js-office-element-remove')
        .on('click', function () {
            if (confirm('Вы уверены, что хотите удалить?')) {
                var ele = $(this);
                $.ajax({
                    url: $(ele).data('url'),
                    method: 'DELETE',
                    success: function (_json) {
                        $(location).attr('href', '');
                    },
                    error: function () {
                        alert('Ошибка сервиса');
                    }
                })
            }
        });
    //end удаление элемента списка

    //begin добавление БД товаров
    $('.js-office-db-new').on('click', function () {
        $('.js-office-db-set').attr('data-url', $(this).data('url'));
        $('#itemDbModal input[name="name"]').val('');
        $('#itemDbModal input[name="maxCount"]').val('1000');
        $('.js-add-modal-title').text('Добавление');
    });
    $('.js-office-db-set').on('click', function () {
        var nameVal = $('[name="name"]').val();
        var maxCountVal = $('[name="maxCount"]').val();
        if (nameVal.length === 0 || maxCountVal.length === 0) {
            console.log($('#db-name').parent());
            $('.js-modal-error').detach();
            //remove('.js-modal-error');
            $('#db-name').after('<span class="js-modal-error" style="color:red;font-weight:bolder;">поле не заполнено</span>');
            return;
        }
        $('#addDbModal').modal('hide');
        $.ajax({
            url: $(this).data('url'),
            method: 'POST',
            data: {
                name: nameVal,
                maxCount: maxCountVal
            },
            success: function (_json) {
                if (_json.url) {
                    $(location).attr('href', _json.url);
                }
                if (_json.error) {
                    $('#itemDbModal .alert.alert-danger.alert-dismissible.fade.in').detach();
                    var htmlString = '';
                    for (var err in _json.error) {
                        htmlString += '<div class="alert alert-danger alert-dismissible fade in" role="alert">\n' +
                            '    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\n' +
                            _json.error[err] +
                            '</div>';
                    }
                    $('.modal-body').before(htmlString);
                }
            },
            error: function () {
                alert('Ошибка сервиса');
            }
        });
    });
    //end добавление БД товаров

    //begin изменение БД товаров
    $('.js-office-db-item').on('click', function () {
        //$('.js-office-db-set').attr('data-url', $(this).data('url'));
        $('.js-add-modal-title').text('Изменение');

        $.ajax({
            url: $(this).data('url'),
            method: 'GET',
            success: function (_json) {
                try {
                    if (!_json.db) {
                        throw new SyntaxError("Ошибка в данных");
                    }
                    if (!_json.url) {
                        throw new SyntaxError("Ошибка в данных");
                    }
                    if (_json.error) {
                        $('#itemDbModal .alert.alert-danger.alert-dismissible.fade.in').detach();
                        var htmlString = '';
                        for (var err in _json.error) {
                            htmlString += '<div class="alert alert-danger alert-dismissible fade in" role="alert">\n' +
                                '    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\n' +
                                _json.error[err] +
                                '</div>';
                        }
                        $('.modal-body').before(htmlString);
                    }
                    $('.js-office-db-set').attr('data-url', _json.url);
                    $('#itemDbModal input[name="name"]').val(_json.db.name);
                    $('#itemDbModal input[name="maxCount"]').val(_json.db.maxCount);
                } catch (e) {
                    if (e.name == "SyntaxError") {
                        alert("Извините, в данных ошибка");
                    } else {
                        throw e;
                    }
                }
            },
            error: function () {
                alert('Ошибка сервиса');
            }
        });
    });
    //end изменение БД товаров

    //begin добавление товара
    $('.js-office-product-new').on('click', function () {
        $('#productSet')[0].reset();
        $('#productSet').attr('action', $(this).data('url'));
        $('.js-add-modal-title').text('Добавление');
    });
    $('.js-office-product-set').on('click', function () {
        submitForm($('#productSet'));
        // $('#productSet')[0].reset();
        // $('#itemProductModal').modal('hide');
        return false;
    });
    //end добавление товара

    //begin изменение товара
    $('.js-office-product-item').on('click', function () {
        $('.js-add-modal-title').text('Изменение');
        $.ajax({
            url: $(this).data('url'),
            method: 'GET',
            success: function (_json) {
                try {
                    if (!_json.product) {
                        throw new SyntaxError("Ошибка в данных");
                    }
                    if (!_json.url) {
                        throw new SyntaxError("Ошибка в данных");
                    }
                    $('#productSet').attr("action", _json.url);
                    $('#itemProductModal input[name="strih"]').val(_json.product.strih);
                    $('#itemProductModal input[name="name"]').val(_json.product.name);
                    $('#itemProductModal input[name="count"]').val(_json.product.count);
                    $('#itemProductModal input[name="unitMeasure"]').val(_json.product.unitMeasure);
                    $('#itemProductModal input[name="section"]').val(_json.product.section);
                    $('#itemProductModal input[name="price"]').val(_json.product.price);
                } catch (e) {
                    if (e.name == "SyntaxError") {
                        alert("Извините, в данных ошибка");
                    } else {
                        throw e;
                    }
                }
            },
            error: function () {
                alert('Ошибка сервиса');
            }
        });
    });
    //end изменение товара

    //begin импорт товаров из таблицы
    var files;
    $('#productsImport input[type=file]').change(function () {
        files = this.files;
    });
    $('.js-office-import-modal').on('click', function () {
        $('#productsImport').attr('action', $(this).data('url'));
    });
    $('.js-office-product-import').on('click', function () {
        $('.js-office-product-import').append();
        if (typeof files == 'undefined') {
            return;
        }
        var fdata = new FormData();
        fdata.append('file', files[0]);
        $.each(files, function (key, value) {
            fdata.append('file', value);
        });
        var url = $('#productsImport').attr('action');
        $('.modal .import-wait-msg').remove();
        $('.js-log').empty();
        $(this).before('<div class="import-wait-msg" style="text-align:center; font-size:larger; color:#169F85; padding-bottom:10px;">Проводится импорт данных. Пожалуйста, подождите...</div>');
        $.ajax({
            url: url,
            type: 'POST',
            data: fdata,
            cache: false,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (_json) {
                if (_json.url) {
                    $(location).attr('href', _json.url);
                }
                if (_json.error) {
                    console.log(_json.error);
                    $('.modal .alert.alert-danger.alert-dismissible.fade').remove();
                    var htmlString = '';
                    for (var err in _json.error) {
                        htmlString += '<div class="alert alert-danger alert-dismissible fade in" role="alert">\n' +
                            '    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\n' +
                            _json.error[err] +
                            '</div>';
                    }
                    if ($('div').is('.modal')) {
                        $('.modal-body').before(htmlString);
                    } else {
                        $('.bs-example-popovers').html(htmlString);
                    }
                }
                if (_json.log) {
                    for (var stringNumber in _json.log) {
                        for (var msgIndex in _json.log[stringNumber]) {
                            $('.js-log').append(
                                '<div ' +
                                'class="import-wait-msg" ' +
                                'style="'+
                                'color:rgba(231, 76, 60, 0.88); ">' +
                                'Строка '+
                                stringNumber+
                                ' '+
                                _json.log[stringNumber][msgIndex] +
                                '</div>'
                            );
                        }
                    }
                } else {
                    throw new SyntaxError("Ошибка в данных");
                }
                return false;
            },
            error: function (jqXHR, status, errorThrown) {
                alert("Ошибка в данных");
                console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
            }
        });
        return false;
    });

    //end импорт товаров из таблицы

    //begin очищение модального окна
    $('.modal').on('hide.bs.modal', function () {
        $(':root').find('.modal input').val('');
        $('.modal .alert.alert-danger.alert-dismissible.fade').remove();
    });

    //end очищение модального окна

    function submitForm(form)
    {
        var url = form.attr("action");
        var formData = $(form).serializeArray();
        $.post(url, formData).done(function (data) {
            if (data.error) {
                $('.modal .alert.alert-danger.alert-dismissible.fade').remove();
                var htmlString = '';
                for (var err in data.error) {
                    htmlString += '<div class="alert alert-danger alert-dismissible fade in" role="alert">\n' +
                        '    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\n' +
                        data.error[err] +
                        '</div>';
                }
                if ($('div').is('.modal')) {
                    $('.modal-body').before(htmlString);
                } else {
                    $('.bs-example-popovers').html(htmlString);
                }
                return false;
            } else if (data.url) {
                $(location).attr('href', data.url);
            } else {
                alert('Ошибка сервиса');
            }
        });
    }

    function deleteElement()
    {

    }
});