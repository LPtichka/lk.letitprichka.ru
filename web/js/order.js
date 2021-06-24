window.getAddressBlock = function () {
    let customerInput = $('[name="Order[customer_id]"]');
    let customerId = customerInput.val() ? customerInput.val() : 0;
    if (customerInput.is(':disabled')) {
        customerId = 0;
    }
    let addressInput = $('[name="Order[address_id]"]');
    $.ajax({
        url: '/order/get-address?customerId=' + customerId,
        method: 'get',
        beforeSend: function () {
            document.getElementById('order-address-block').classList.add('loading');
        },
        success: function (address) {
            document.getElementById('order-address-block').classList.remove('loading');
            let options = '';
            $.each(address, function (index, value) {
                options += '<option value="' + value.id + '" ' + (value.selected ? ' selected="selected"' : '')
                    + ' data-full_address="' + value.full_address + '"'
                    + ' data-city="' + value.city + '"'
                    + ' data-street="' + value.street + '"'
                    + ' data-house="' + value.house + '"'
                    + ' data-housing="' + value.housing + '"'
                    + ' data-building="' + value.building + '"'
                    + ' data-flat="' + value.flat + '"'
                    + ' data-postcode="' + value.postcode + '"'
                    + ' data-description="' + value.description + '"'
                    + '>'
                    + (value.full_address !== '' ? value.full_address : 'Новый адрес') + '</option>';
                if (value.selected) {
                    $('[name="Address[full_address]"]').val(value.full_address);
                    $('[name="Address[city]"]').val(value.city);
                    $('[name="Address[street]"]').val(value.street);
                    $('[name="Address[house]"]').val(value.house);
                    $('[name="Address[housing]"]').val(value.housing);
                    $('[name="Address[building]"]').val(value.building);
                    $('[name="Address[flat]"]').val(value.flat);
                    $('[name="Address[postcode]"]').val(value.postcode);
                    $('[name="Address[description]"]').val(value.description);
                }
            });

            addressInput.html(options);
            setTimeout(function () {
                if (address.length >= 1) {
                    // $('#collapse-address').collapse('hide');
                    $('[name="address_detailed"]').trigger('click');
                }
            }, 300);
        }
    });
};
window.getExceptionBlock = function () {
    let customerInput = $('[name="Order[customer_id]"]');
    let customerId = customerInput.val() ? customerInput.val() : 0;
    if (customerInput.is(':disabled')) {
        customerId = 0;
    }
    $.ajax({
        url: '/order/get-exception?customerId=' + customerId,
        method: 'get',
        success: function (exceptions) {
            $('.exceptions').html(exceptions)
        }
    });
};

window.userReChoosen = function () {
    let customerInput = $('[name="Order[customer_id]"]');
    let customerId = customerInput.val() ? customerInput.val() : 0;

    if (customerId) {
        $('#collapse-customer').collapse('hide');
    }
};

window.getMenuBlock = function ($orderId) {
    // $.ajax({
    //     url: '/order/get-menu?orderId=' + $orderId,
    //     method: 'get',
    //     beforeSend: function () {
    //         document.getElementById('order-menu-block').classList.add('loading');
    //     },
    //     success: function (html) {
    //         document.getElementById('order-menu-block').classList.remove('loading');
    //         document.getElementById('order-menu-block').innerHTML = html;
    //
    //         setTimeout(function () {
    //             let scriptElements = document.getElementById('order-menu-block').getElementsByTagName('SCRIPT');
    //             for (i = 0; i < scriptElements.length; i ++) {
    //                 let scriptElement = document.createElement('SCRIPT');
    //                 scriptElement.type = 'text/javascript';
    //                 if (!scriptElements[i].src) {
    //                     scriptElement.innerHTML = scriptElements[i].innerHTML;
    //                 } else {
    //                     scriptElement.src = scriptElements[i].src;
    //                 }
    //                 document.head.appendChild(scriptElement);
    //             }
    //         }, 500);
    //     }
    // });
};

let orderId = document.getElementById('order-container').getAttribute('data-order-id');
let dishBlock = $('.dish-block');
let subscriptionBlock = $('.subscription-block');
let noSubscriptionId = 8;
if (orderId) {
    // window.getAddressBlock();
    // window.getExceptionBlock();
    window.getMenuBlock(orderId);
} else {
    dishBlock.hide();
}

body.delegate('.request-dish-to-inventory', 'click', function (e) {
    e.preventDefault();
    let ration = $(this).data('ration');
    let scheduleId = $(this).data('schedule-id');
    let block = $(this);
    $.ajax({
        url: '/order/get-dishes-for-inventory?ration=' + ration,
        method: 'get',
        dataType: 'json',
        success: function (data) {

            let options = '';
            $.each(data.dishes, function (index, value) {
                options += '<option value="' + index + '">' + value + '</option>';
            });

            let dish_request_block = '<div class="row ingestion-row">' +
                '<div class="col-sm-10 ingestion-content">' +
                '<select class="input-sm">' + options + '</select>' +
                '<button class="btn btn-primary add-dish-to-inventory" ' +
                'data-schedule-id="' + scheduleId + '" ' +
                'data-old-dish-id="' + 0 + '" ' +
                'data-ration="' + ration + '">Добавить</button>' +
                '</div>' +
                '</div>';
            block.parent().append(dish_request_block);
        }
    });
});

body.delegate('#order-subscriptioncount', 'change', function (e) {
    e.preventDefault();

    if ($(this).val() === '999') {
        $('#count-block-wrapper').removeClass('hidden');
        $('#order-count').val('');
    } else {
        $('#count-block-wrapper').addClass('hidden');
        $('#order-count').val($(this).val());
    }
});

body.delegate('#order-individual_menu', 'change', function (e) {
    e.preventDefault();

    if ($(this).is(':checked')) {
        $('#comment-block').removeClass('hidden');
    } else {
        $('#comment-block').addClass('hidden');
    }
});

body.delegate('#edit-order-primary-params', 'click', function (e) {
    e.preventDefault();

    $.get('get-edit-primary-block?orderId=' + orderId, function (block) {
        $('.order-info').addClass('hidden-order-info').removeClass('order-info').addClass('hidden');
        $('#ordering-info').append('<div class="box-body order-info">' + block + '</div>');
    });
});

body.delegate('#cancel-primary-order-params', 'click', function (e) {
    e.preventDefault();

    $('#ordering-info .order-info').remove();
    $('.hidden-order-info').addClass('order-info').removeClass('.hidden-order-info').removeClass('hidden');
});

body.delegate('#save-primary-order-params', 'click', function (e) {
    e.preventDefault();

    $.ajax({
        url: 'edit-primary-block?orderId=' + orderId,
        method: 'post',
        data: {
            subscription_id: $('[name="subscription_id"]').val(),
            comment: $('[name="comment"]').val(),
            scheduleFirstDate: $('[name="scheduleFirstDate"]').val(),
            scheduleInterval: $('[name="scheduleInterval"]').val(),
            count: $('[name="count"]').val(),
            without_soup: $('[name="without_soup"]').is(':checked'),
            individual_menu: $('[name="individual_menu"]').is(':checked'),
            cutlery: $('[name="cutlery"]').is(':checked'),
        },
        success: function (html) {
            $('#ordering-info .order-info').remove();
            $('.hidden-order-info').addClass('order-info').removeClass('.hidden-order-info').removeClass('hidden');
            $('.order-info').html(html);

            swal({
                title: 'Внимание.',
                html: true,
                text: 'Через 5 секунд страница будет перезагружена. Либо вы можете обновить страницу прямо сейчас.'
            });

            setTimeout(function () {
                window.location.reload();
            }, 5000);
        }
    });

    $('#ordering-info .order-info').remove();
});

body.delegate('.reload-dish', 'click', function (e) {
    e.preventDefault();
    let ration = $(this).data('ration');
    let scheduleId = $(this).data('schedule-id');
    let oldDishId = $(this).data('dish-id');
    let block = $(this);
    $.ajax({
        url: '/order/get-dishes-for-inventory?ration=' + ration,
        method: 'get',
        dataType: 'json',
        success: function (data) {

            let options = '';
            $.each(data.dishes, function (index, value) {
                options += '<option value="' + index + '">' + value + '</option>';
            });

            let dish_request_block = '<div class="col-sm-10 ingestion-content">' +
                '<select class="input-sm">' + options + '</select>' +
                '<button class="btn btn-primary add-dish-to-inventory" ' +
                'data-schedule-id="' + scheduleId + '" ' +
                'data-old-dish-id="' + oldDishId + '" ' +
                'data-ration="' + ration + '">Добавить</button>' +
                '</div>';
            block.parents('.ingestion-row').html(dish_request_block);
        }
    });
});

body.delegate('.exception-row select', 'change', function (e) {
    e.preventDefault();

    if ($(this).val() == '10') {
        $(this).parents('.exception-row').find('.comment-exception').removeClass('hidden');
    } else {
        $(this).parents('.exception-row').find('.comment-exception').addClass('hidden');
    }
});

body.delegate('.add-dish-to-inventory', 'click', function (e) {
    e.preventDefault();
    let ration = $(this).data('ration');
    let scheduleId = $(this).data('schedule-id');
    let oldDishId = $(this).data('old-dish-id');
    let dishId = $(this).parent().find('select').val();
    let block = $(this).parent();

    $.ajax({
        url: '/order/add-dish-for-inventory?ration=' + ration,
        method: 'post',
        data: {dish_id: dishId, old_dish_id: oldDishId, schedule_id: scheduleId, ration: ration},
        dataType: 'json',
        success: function (data) {
            let html = '';
            if (data.success) {
                html += '<p><a href="' + data.dish.href + '">' + data.dish.name + '</a></p>' +
                    '<p>' + data.dish.description + '</p>';
                block.html(html);
            }
        }
    });
});

body.delegate('[name="Order[address_id]"]', 'change', function () {
    let option = $(this).find('option[value="' + $(this).val() + '"]');

    $('[name="Address[full_address]"]').val(option.data('full_address'));
    $('[name="Address[city]"]').val(option.data('city'));
    $('[name="Address[street]"]').val(option.data('street'));
    $('[name="Address[house]"]').val(option.data('house'));
    $('[name="Address[housing]"]').val(option.data('housing'));
    $('[name="Address[building]"]').val(option.data('building'));
    $('[name="Address[flat]"]').val(option.data('flat'));
    $('[name="Address[postcode]"]').val(option.data('postcode'));
    $('[name="Address[description]"]').val(option.data('description'));

    if ($(this).val() === '' && !$('[name="address_detailed"]').is(':checked')) {
        $('[name="address_detailed"]').trigger('click');
    }

    if ($(this).val() !== '' && $('[name="address_detailed"]').is(':checked')) {
        $('[name="address_detailed"]').trigger('click');
    }

});

body.delegate('[name="Order[subscription_id]"]', 'change', function () {
    if ($(this).val() == noSubscriptionId) {
        subscriptionBlock.hide();
        dishBlock.show();
    } else {
        subscriptionBlock.show();
        dishBlock.hide();
    }
});
