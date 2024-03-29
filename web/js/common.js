let body = $('body');
let redColor, colorDanger = "#D9534F";
let attentionTitle = "Внимание";
let noSelectionText = "Вы не выбрали ни один элемент?";
let approveButton = "Подтвердить";
let cancelButton = "Отменить";
let closeButton = "Закрыть";

body.delegate('[name="import"]', 'change', function () {
    let url = $(this).attr('data-href');
    window.parseXML(url);
});

body.delegate('#add-product', 'click', function () {
    window.addProduct();
});

body.delegate('#add-dish', 'click', function () {
    window.addDish();
});

body.delegate('#add-address', 'click', function () {
    window.addAddress();
});

body.delegate('.import', 'click', function (e) {
    e.preventDefault();
    $('[name="import"]').trigger('click');
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

body.delegate('.add-row-action', 'click', function () {
    let container = $('.' + $(this).data('block')),
        lastGroupId = container.find('[class*=' + $(this).data('row') + ']:last').prop('id'),
        lastIndex = 0;

    if (lastGroupId) {
        lastIndex = lastGroupId.split('-')[2];
    }

    $.get($(this).data('href') + '?counter=' + lastIndex, function (row) {
        container.find('.empty-text').remove();
        container.append($(row));
    });
});

body.delegate('.delete-row-action', 'click', function () {
    let row = $(this).parents('[class*=' + $(this).data('row') + ']');
    let block = row.parent();
    row.remove();
    if ($('[class*=' + $(this).data('row') + ']').length == 0) {
        block.html('<p class="empty-text">' + block.data('empty-text') + '</p>');
    }
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

body.delegate('.work-days', 'change', function () {
    let value = '';

    $('.work-days').each(function () {
        if ($(this).is(':checked')) {
            value += $(this).attr('name') + ':1;'
        } else {
            value += $(this).attr('name') + ':0;'
        }
    });

    value = value.substring(0, value.length - 1)

    $('#settings-work_days-value').val(value);
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
                        setTimeout(function () {
                            swal({
                                title: data.title,
                                text: data.description,
                                confirmButtonText: closeButton,
                            }, function () {
                                location.reload();
                            });
                        }, 300)
                    } else {
                        setTimeout(function () {
                            swal({
                                title: data.title,
                                text: data.description,
                            }, function () {
                                location.reload();
                            });
                        }, 300)
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

body.delegate('.action-with-approve', 'click', function (e) {
    let title = $(this).data('title');
    let text = $(this).data('text');
    let url = $(this).data('request-url');
    swal({
        title: title,
        text: text,
        html: true,
        confirmButtonText: 'Подтвердить',
        cancelButtonText: 'Отменить',
        confirmButtonColor: "#5CB85C",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function () {
        $.ajax({
            url: url,
            dataType: 'json',
            type: 'POST',
            success: function (data) {
                if (data.status) {
                    swal({
                        title: data.title
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
    e.preventDefault();
});

body.delegate('.delete-ingestion', 'click', function (e) {
    $(this).parent().remove();
    e.preventDefault();
});

body.delegate('.action-with-request', 'click', function (e) {
    let title = $(this).data('title');
    let text = $(this).data('text');
    let preRequestUrl = $(this).data('pre-request-url');
    let requestUrl = $(this).data('request-url');

    $('#pre-request-modal').modal('show');
    $.ajax({
        url: preRequestUrl,
        dataType: 'html',
        type: 'POST',
        success: function (data) {
            let modal = $('#pre-request-modal'),
                page = $(data),
                header = page.find('h1').text(),
                title = page.find('title').text();

            page.find('h1').remove();
            modal.find('.modal-title').remove();
            modal.find('.modal-header').append('<h5 class="modal-title lead">' + header + '</h5>');
            modal.find('.modal-body').html(page);

            if (page.find('title').length > 0) {
                $('head').find('title').html(title);
                page.find('title').remove();
            }

            body.delegate('#modal-make-request', 'click', function (e) {
                $.ajax({
                    url: requestUrl,
                    dataType: 'json',
                    data: modal.find('form').serialize(),
                    type: 'POST',
                    success: function (data) {
                        $('#pre-request-modal').modal('hide');
                        swal({
                            title: data.title
                        }, function () {
                            location.reload();
                        });
                    }
                });
                e.preventDefault();
            });
        },
        error: function (data) {
            $('#pre-request-modal').find('.modal-body').html('<h2 class="text-center">' + data.responseText + '</h2><br/>');
            console.log(data);
        }
    });
    e.preventDefault();
});

body.delegate('.add-menu-ingestion', 'click', function (e) {
    let wrapper = $(this).parent().parent().parent().find('.ingestion-wrapper');
    let lastIngestion = wrapper.children().last();
    let lastIngestionID = parseInt(lastIngestion.data('ingestion-id')) + 1;
    let ingestionClone = lastIngestion.clone();

    console.log(lastIngestionID);
    console.log('=====');

    ingestionClone.find('option:selected').prop('selected', false);
    ingestionClone.find('select').each(function () {
        let newName = $(this).prop('name').replace('[' + (lastIngestionID - 1) + ']', '[' + lastIngestionID + ']');
        $(this).prop('name', newName);
        $(this).attr('data-ingestion-number', lastIngestionID.toString());
    });
    ingestionClone.find('input[type="hidden"]').each(function () {
        let newName = $(this).prop('name').replace('[' + (lastIngestionID - 1) + ']', '[' + lastIngestionID + ']');
        $(this).prop('name', newName);
        $(this).attr('value', "0");
    });
    ingestionClone.find('a.delete-ingestion').each(function () {
        $(this).removeClass('hidden');
    });

    ingestionClone.attr('data-ingestion-id', lastIngestionID.toString());

    wrapper.append(ingestionClone);

    e.preventDefault();
});

body.delegate('.dish-for-menu', 'change', function (e) {
    let dishId = $(this).val();

    let ingestionType = $(this).data('ingestion-type');
    let ingestionNumber = $(this).data('ingestion-number');
    let ingestionDate = $(this).data('ingestion-date');

    let ingestion = $(this).parent();

    $.ajax({
        url: '/menu/get-menu-additionals?dishId=' + dishId,
        data: {
            dishId: dishId,
            ingestionType: ingestionType,
            ingestionNumber: ingestionNumber,
            ingestionDate: ingestionDate
        },
        type: 'GET',
        success: function (json) {
            if (json.dishType == 2) {
                ingestion.find('.dish-garnish').remove();

                if (json.isNeedAddGarnish) {
                    let dropdown = '<select class="form-control input-sm dish-for-menu dish-garnish" name="dish[' + json.ingestionDate + '][' + json.ingestionType + '][garnish][' + json.ingestionNumber + ']">';
                    dropdown += '<option>Выберите блюдо на гарнир</option>';

                    $.each(json.dishes, function (index, value) {
                        dropdown += '<option value="' + value.id + '">' + value.name + '</option>';
                    });

                    dropdown += '</select>';
                    ingestion.append(dropdown);
                }
            }

        }
    });
});

window.changePriceBlockForDish = function (row) {
    row.find('.change-no-subscription-dish').removeClass('hidden');
    var dishId = row.find('select').val();
    var count = row.find('[name="count"]').val();

    $.ajax({
        url: "/dish/"+dishId+"/get-info",
        type: 'GET',
        success: function (data) {
            console.log(data);
            row.find('[name="price"]').val(data.price);
            row.find('[name="total"]').val(data.price * count);
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

window.defferOrder = function (orderId, dateFrom, dateTo) {
    swal({
        confirmButtonColor: redColor,
        title: 'Внимание',
        html: true,
        confirmButtonText: approveButton,
        cancelButtonText: cancelButton,
        showCancelButton: true,
        text: 'Вы действительно хотите перенести дату доставки. Обратите внимание что все даты после измененной сместятся соответственно.'
    }, function (isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: '/order/' + orderId + '/deffer',
                data: {dateTo: dateTo, dateFrom: dateFrom},
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    window.location.reload();
                },
                error: function (data) {
                    console.log(data)
                }
            });
        } else {
            window.location.reload();
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
            var paramValue = typeof (a[1]) === 'undefined' ? true : a[1];

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
window.addDish = function () {
    let prodContainer = $('.dishes'),
        lastGroupId = prodContainer.find('[class*=dish-row]:last').prop('id'),
        lastIndex = 0;

    if (lastGroupId) {
        lastIndex = lastGroupId.split('-')[1];
    }

    $.get('/dish/get-row?counter=' + lastIndex, function (prodRow) {
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

window.getMenuBlocks = function (menuID = 0) {
    let startDate = $('[name="menu_start_date"]').val();
    let endDate = $('[name="menu_end_date"]').val();

    $.ajax({
        url: '/menu/get-day-blocks',
        data: {menuStartDate: startDate, menuEndDate: endDate, menuID: menuID},
        type: 'POST',
        success: function (html) {
            $('#menu-composition').html(html);
        }
    });
};

window.getMenuOrders = function (menuID = 0) {
    let startDate = $('[name="menu_start_date"]').val();
    let endDate = $('[name="menu_end_date"]').val();

    $.ajax({
        url: '/menu/get-orders',
        data: {menuStartDate: startDate, menuEndDate: endDate, menuID: menuID},
        type: 'POST',
        success: function (html) {
            $('#menu-orders').html(html);
        }
    });
};

window.getMenuBlocksForCreate = function (post = '') {
    let startDate = $('[name="menu_start_date"]').val();
    let endDate = $('[name="menu_end_date"]').val();

    $.ajax({
        url: '/menu/get-day-blocks',
        data: {menuStartDate: startDate, menuEndDate: endDate, data: post},
        type: 'POST',
        success: function (html) {
            $('#menu-composition').html(html);
        }
    });
};

$(document).on('pjax:beforeSend', function () {
    if ($('.modal.in').length > 0) {
        $('.modal.in').find('.modal-content').addClass('pjax-loading');
    } else {
        $('.container-fluid').addClass('pjax-loading');
    }
});

$(document).on('pjax:complete', function () {
    $('[data-toggle="tooltip"]').tooltip();
    $('.pjax-loading').removeClass('pjax-loading');
    // $(".grid-view .table").resizableColumns({
    //     store: window.store
    // });
});

body.on('click', 'tr a[data-toggle=modal], a[data-toggle=modal]', function (e) {
    e.stopPropagation();
    let link = $(this),
        obj = $($(this).data('target'));

    obj.find('.modal-body').html('<div class="text-center"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></div>');
    obj.find('.modal-header').find('.modal-title').remove();
    obj.modal('show');

    $.get(link.data('href'), function (data) {
        let page = $(data),
            header = page.find('title').text(),
            title = page.find('title').text();

        page.find('h1').remove();
        obj.find('.modal-header').append('<h5 class="modal-title lead">' + header + '</h5>');
        obj.find('.modal-body').html(page);
        if (page.find('title').length > 0) {
            $('head').find('title').html(title);
            page.find('title').remove();
        }
    });
    return false;
});

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

            let block = '';
            if (item['data']['blockType']) {
                block = item['data']['blockType'] + '. ' + item['data']['block'];
            }
            return '<div class="autocomplete-suggestion" ' +
                ' data-region="' + item['data']['regionWithType'] + '"' +
                ' data-city="' + item['data']['cityWithType'] + '"' +
                ' data-street="' + item['data']['streetWithType'] + '"' +
                ' data-house="' + (item['data']['house'] || '') + '"' +
                ' data-flat="' + (item['data']['flat'] || '') + '"' +
                ' data-housing="' + block + '"' +
                ' data-postcode="' + (item['data']['postalCode'] || '') + '"' +
                ' data-val="' + item['value'] + '">' + item['value'].replace(re, "<b>$1</b>") + '</div>';
        },
        onSelect: function (e, term, item) {
            var cityInput = $('[name="Address[city]"]');
            var streetInput = $('[name="Address[street]"]');

            cityInput.val(item.getAttribute('data-city'));
            streetInput.val(item.getAttribute('data-street'));

            $('[name="Address[region]"]').val(item.getAttribute('data-region'));
            let house = item.getAttribute('data-house');
            if (item.getAttribute('data-housing')) {
                house += ', ' + item.getAttribute('data-housing');
            }
            $('[name="Address[house]"]').val(house);
            $('[name="Address[flat]"]').val(item.getAttribute('data-flat'));
            // $('[name="Address[housing]"]').val(item.getAttribute('data-housing'));
            // $('[name="Address[postcode]"]').val(item.getAttribute('data-postcode'));
        }
    });
});
