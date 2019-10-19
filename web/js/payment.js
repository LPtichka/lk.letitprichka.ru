let body = $('body');

body.delegate('.delete', 'click', function (e) {
    e.preventDefault();

    if ($('[name="selection[]"]:checked').length > 0) {
        swal({
            confirmButtonColor: "#d9534f",
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
            confirmButtonColor: "#d9534f",
            title: "Внимание",
            html: true,
            text: 'Вы не выбрали ни один элемент?'
        });
    }
});
