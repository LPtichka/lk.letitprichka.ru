let body = $('body');
let redColor = "#d9534f";

body.delegate('.delete', 'click', function (e) {
    e.preventDefault();

    if ($('[name="selection[]"]:checked').length > 0) {
        swal({
            confirmButtonColor: redColor,
            title: "Внимание",
            html: true,
            confirmButtonText: 'Подтвердить',
            cancelButtonText: 'Отменить',
            showCancelButton: true,
            text: 'Вы дейстивтельно хотите удалить выбрраные типы оплат?'
        }, function () {
            let paymentIds = $('[name="selection[]"]').serialize();
            $.ajax({
                url: '/payment-type/delete',
                data: paymentIds,
                dataType: 'json',
                type: 'POST',
                success: function (data) {
                    if (data.status) {
                        swal({
                            title: data.title,
                            text: 'Выбранные типы оплаты были успешно удалены',
                            confirmButtonText: 'Закрыть',
                        }, function () {
                            location.reload();
                        });
                    } else {
                        swal({
                            title: data.title
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
            title: "Внимание",
            html: true,
            text: 'Вы не выбрали ни один элемент?'
        });
    }
});

body.delegate('.export', 'click', function (e) {
    e.preventDefault();

    let params = window.getAllUrlParams();
    $.ajax({
        url: '/payment-type/export',
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

body.delegate('.import', 'click', function (e) {
    e.preventDefault();
    $('[name="import"]').trigger('click');
});

body.delegate('[name="import"]', 'change', function () {
    window.parseXML();
});

// Получение GET параметров
window.getAllUrlParams = function (url) {
    // извлекаем строку из URL или объекта window
    var queryString = url ? url.split('?')[1] : window.location.search.slice(1);

    // объект для хранения параметров
    var obj = {};

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

window.parseXML = function () {
    let formData = new FormData();
    let counter = 0;

    $.each($('[name="import"]')[0].files, function (i, file) {
        formData.append('xml', file);
    });

    $.ajax({
        url: '/payment-type/import',
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
