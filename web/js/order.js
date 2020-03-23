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
            if (address.length === 1 && !$('[name="address_detailed"]').is(':checked')) {
                $('[name="address_detailed"]').trigger('click');
            }
            addressInput.html(options);
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

window.getMenuBlock = function ($orderId) {
    $.ajax({
        url: '/order/get-menu?orderId=' + $orderId,
        method: 'get',
        beforeSend: function () {
            document.getElementById('order-menu-block').classList.add('loading');
        },
        success: function (html) {
            document.getElementById('order-menu-block').classList.remove('loading');
            document.getElementById('order-menu-block').innerHTML = html;
        }
    });
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

$('body').delegate('[name="Order[address_id]"]', 'change', function () {
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
});

$('body').delegate('[name="Order[subscription_id]"]', 'change', function () {
    if ($(this).val() == noSubscriptionId) {
        subscriptionBlock.hide();
        dishBlock.show();
    } else {
        subscriptionBlock.show();
        dishBlock.hide();
    }
});
