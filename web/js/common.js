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

body.delegate('#add-product', 'click', function () {
    window.addProduct();
});

body.delegate('.add-row-action', 'click', function () {
    let container = $('.' + $(this).data('block')),
        lastGroupId = container.find('[class*=' + $(this).data('row') + ']:last').prop('id'),
        lastIndex = 0;

    if (lastGroupId) {
        lastIndex = lastGroupId.split('-')[1];
    }

    $.get($(this).data('href') + '?counter=' + lastIndex, function (row) {
        container.append($(row));
    });
});

body.delegate('.delete-row-action', 'click', function () {
    let row = $(this).parents('[class*=' + $(this).data('row') + ']');
    if ($('[class*=' + $(this).data('row') + ']').length > 1) {
        row.remove();
    }
});

body.delegate('#add-address', 'click', function () {
    window.addAddress();
});

body.delegate('.delete-product', 'click', function () {
    let product = $(this).parents('[class*=product-row]');
    if ($('[class*=product-row]').length > 1) {
        product.remove();
    }
});

body.delegate('.delete-address', 'click', function () {
    let block = $(this).parents('[class*=address-row]');
    if (block.find('input:checked').length > 0) {
        swal({
            confirmButtonColor: redColor,
            title: 'Ошибка.',
            html: true,
            text: 'Вы не можете удалить адрес по умолчанию.'
        });
    } else {
        if ($('[class*=address-row]').length > 1) {
            block.remove();
        }
    }
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

body.delegate('.detailed-address-input', 'change', function (e) {
    if ($('#order-address_detailed').is(':checked')) {
        $('#full_address').val(makeFullAddress());
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

// Добавление строки продкута
window.addProduct = function () {
    let prodContainer = $('.products'),
        lastGroupId = prodContainer.find('[class*=product-row]:last').prop('id'),
        lastIndex = 0;

    if (lastGroupId) {
        lastIndex = lastGroupId.split('-')[1];
    }

    $.get('/product/get-row?counter=' + lastIndex, function (prodRow) {
        prodContainer.append($(prodRow));
    });
};

// Добавление строки продкута
window.addAddress = function () {
    let prodContainer = $('.addresses'),
        lastGroupId = prodContainer.find('[class*=address-row]:last').prop('id'),
        lastIndex = 0;

    if (lastGroupId) {
        lastIndex = lastGroupId.split('-')[1];
    }

    $.get('/address/get-row?counter=' + lastIndex, function (prodRow) {
        prodContainer.append($(prodRow));
    });
};

// Получение полного адреса
window.buildFullAddress = function () {
    let fullAddress = [];

    if ($('#address-city').val() !== '') {
        fullAddress.push($('#address-city').val());
    }
    if ($('#address-street').val() !== '') {
        fullAddress.push($('#address-street').val());
    }
    if ($('#address-house').val() !== '') {
        fullAddress.push($('#address-house').val());
    }
    if ($('#address-housing').val() !== '') {
        fullAddress.push($('#address-housing').val());
    }
    if ($('#address-flat').val() !== '') {
        fullAddress.push($('#address-flat').val());
    }
    if ($('#address-postcode').val() !== '') {
        fullAddress.push($('#address-postcode').val());
    }

    return fullAddress.join(', ');
};

$(document).ready(function () {
    // Автоподстановка адреса
    new autoComplete({
        selector: '#full_address',
        source: function (term, response) {
            try {
                xhr.abort();
            } catch (e) {
            }
            xhr = $.getJSON('/address/get-by-query', {query: term}, function (data) {
                response(data);
            });
        },
        renderItem: function (item, search) {
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");

            return '<div class="autocomplete-suggestion" ' +
                ' data-region="' + item['data']['regionWithType'] + '"' +
                ' data-city="' + item['data']['cityWithType'] + '"' +
                ' data-street="' + item['data']['streetWithType'] + '"' +
                ' data-house="' + (item['data']['house'] || '') + '"' +
                ' data-flat="' + (item['data']['flat'] || '') + '"' +
                ' data-housing="' + (item['data']['block'] || '') + '"' +
                ' data-postcode="' + (item['data']['postalCode'] || '') + '"' +
                ' data-val="' + item['value'] + '">' + item['value'].replace(re, "<b>$1</b>") + '</div>';
        },
        onSelect: function (e, term, item) {
            var cityInput = $('[name="Address[city]"]');
            var streetInput = $('[name="Address[street]"]');

            cityInput.val(item.getAttribute('data-city'));
            streetInput.val(item.getAttribute('data-street'));

            $('[name="Address[region]"]').val(item.getAttribute('data-region'));
            $('[name="Address[house]"]').val(item.getAttribute('data-house'));
            $('[name="Address[flat]"]').val(item.getAttribute('data-flat'));
            $('[name="Address[housing]"]').val(item.getAttribute('data-housing'));
            $('[name="Address[postcode]"]').val(item.getAttribute('data-postcode'));
        }
    });
});
