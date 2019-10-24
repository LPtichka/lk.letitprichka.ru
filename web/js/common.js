let body = $('body');
let redColor, colorDanger = "#d9534f";
let attentionTitle = "Внимание";
let noSelectionText = "Вы не выбрали ни один элемент?";
let approveButton = "Подтвердить";
let cancelButton = "Отменить";
let closeButton = "Закрыть";

body.delegate('.import', 'click', function (e) {
    e.preventDefault();
    $('[name="import"]').trigger('click');
});

body.delegate('[name="import"]', 'change', function () {
    let url = $(this).attr('data-href');
    window.parseXML(url);
});

body.delegate('.export', 'click', function (e) {
    e.preventDefault();

    let href = $(this).attr('data-href');
    let params = window.getAllUrlParams();
    $.ajax({
        url: href,
        dataType: 'json',
        data: params,
        type: 'POST',
        success: function (data) {
            location.href = '/' + data.url;
        },
        error: function (data) {
            console.log(data)
        }
    });
});

body.delegate('.delete', 'click', function (e) {
    e.preventDefault();

    let title = $(this).attr('data-title');
    let href = $(this).attr('data-href');

    if ($('[name="selection[]"]:checked').length > 0) {
        swal({
            confirmButtonColor: redColor,
            title: attentionTitle,
            html: true,
            confirmButtonText: approveButton,
            cancelButtonText: cancelButton,
            showCancelButton: true,
            text: title
        }, function () {
            let paymentIds = $('[name="selection[]"]').serialize();
            $.ajax({
                url: href,
                data: paymentIds,
                dataType: 'json',
                type: 'POST',
                success: function (data) {
                    if (data.status) {
                        swal({
                            title: data.title,
                            text: data.description,
                            confirmButtonText: closeButton,
                        }, function () {
                            location.reload();
                        });
                    } else {
                        swal({
                            title: data.title,
                            text: data.description,
                        }, function () {
                            location.reload();
                        });
                    }
                },
                error: function (data) {
                    console.log(data)
                }
            });
        });
    } else {
        swal({
            confirmButtonColor: redColor,
            title: attentionTitle,
            html: true,
            text: noSelectionText
        });
    }
});

window.parseXML = function (url) {
    let formData = new FormData();

    $.each($('[name="import"]')[0].files, function (i, file) {
        formData.append('xml', file);
    });

    $.ajax({
        url: url,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        type: 'POST',
        success: function (data) {
            window.location.reload();
        },
        error: function (error) {
            swal({
                confirmButtonColor: colorDanger,
                title: "Ошибка",
                text: error.responseJSON.message
            });
        }
    });
};

// Получение GET параметров
window.getAllUrlParams = function (url) {
    // извлекаем строку из URL или объекта window
    let queryString = url ? url.split('?')[1] : window.location.search.slice(1);

    // объект для хранения параметров
    let obj = {};

    // если есть строка запроса
    if (queryString) {

        // данные после знака # будут опущены
        queryString = queryString.split('#')[0];

        // разделяем параметры
        var arr = queryString.split('&');

        for (var i = 0; i < arr.length; i++) {
            // разделяем параметр на ключ => значение
            var a = arr[i].split('=');

            // обработка данных вида: list[]=thing1&list[]=thing2
            var paramNum = undefined;
            var paramName = a[0].replace(/\[\d*\]/, function (v) {
                paramNum = v.slice(1, -1);
                return '';
            });

            // передача значения параметра ('true' если значение не задано)
            var paramValue = typeof(a[1]) === 'undefined' ? true : a[1];

            // преобразование регистра
            paramName = paramName.toLowerCase();
            paramValue = paramValue.toLowerCase();

            // если ключ параметра уже задан
            if (obj[paramName]) {
                // преобразуем текущее значение в массив
                if (typeof obj[paramName] === 'string') {
                    obj[paramName] = [obj[paramName]];
                }
                // если не задан индекс...
                if (typeof paramNum === 'undefined') {
                    // помещаем значение в конец массива
                    obj[paramName].push(paramValue);
                }
                // если индекс задан...
                else {
                    // размещаем элемент по заданному индексу
                    obj[paramName][paramNum] = paramValue;
                }
            }
            // если параметр не задан, делаем это вручную
            else {
                obj[paramName] = paramValue;
            }
        }
    }

    return obj;
};