<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var \app\models\Common\Route[] $routes */
Pjax::begin([
    'id'              => 'user-sheet-form',
    'formSelector'    => '#user-sheet-form form',
    'enablePushState' => false,
]); ?>
<?php if (empty($sheet)): ?>
    <div class="user-row">
        <h1><?= $title; ?></h1>
        <title><?= $title; ?></title>
        <?php $form = ActiveForm::begin(); ?>
        <?php if ($id): ?>
            <div class="select-block">
                <div class="form-group">
                    <label><?= \Yii::t('order', 'Choose date'); ?></label>
                    <?php echo Html::dropDownList('schedule_id', '', $dates ?? [], [
                        'class' => 'form-control input-sm'
                    ]); ?>
                </div>
            </div>

        <?php else: ?>
            <div class="row">
                <div class="col-sm-6">
                    <label><?= \Yii::t('order', 'Choose order id'); ?></label>
                    <?php echo Html::textInput('order_id', '', [
                        'id'    => 'input-order-id',
                        'class' => 'form-control input-sm'
                    ]); ?>
                </div>
                <div class="col-sm-6">
                    <div class="select-block">
                        <div class="form-group">
                            <label><?= \Yii::t('order', 'Choose date'); ?></label>
                            <?php echo Html::dropDownList('schedule_id', '', $dates ?? [], [
                                'class' => 'form-control input-sm'
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row modal-buttons">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::button(
                        '<i class="material-icons">done</i> ' . \Yii::t('app', 'Unload'),
                        ['class' => 'btn btn-sm btn-warning get-customer-sheet']
                    ); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group text-right">
                    <?= Html::a(
                        '<span>' . \Yii::t('app', 'Cancel') . '</span>',
                        '#',
                        [
                            'class'        => 'btn btn-sm btn-default',
                            'data-dismiss' => 'modal'
                        ]
                    ) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php endif; ?>
<?php Pjax::end(); ?>
<?php
\Yii::$app->view->registerJs(<<<JS
    body.delegate('.get-customer-sheet', 'click', function (e) {
        e.preventDefault();
        let scheduleId = $('[name="schedule_id"]').val();
        let button = $(this);
        let id = '$id';
        if ($('#input-order-id').length > 0) {
            id = $('#input-order-id').val();
        }
        
        $.ajax({
            url: '/order/get-customer-sheet?id=' + id,
            type: 'POST',
            data: {schedule_id: scheduleId},
            dataType: 'json',
            beforeSend: function() {
                button.addClass('loading');
            },
            complete: function () {
                button.removeClass('loading');
            },
            success: function(data) {
                if (data.success) {
                    button.removeClass('loading');
                    window.location.href = '/' + data.url;
                } else {
                    swal({
                        title: "Ошибка",
                        text: 'Возможно не все блюда назначены заказу в выбранный вами день. Попробуйте проверить содержание дня доставки для заказа.',
                        type: 'error',
                        showCancelButton: true,
                        closeOnConfirm: false,
                    });
                }
                
            }
        });
    });

    body.delegate('#input-order-id', 'blur', function (e) {
        e.preventDefault();
        let id = $(this).val();
        
        $.ajax({
            url: '/order/get-customer-sheet-options?id=' + id,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                let options = '<option>Выберите дату</option>';
                $.each(data.dates, function(index, value) {
                  options += '<option value="'+index+'">'+value+'</option>';
                })
                $('.select-block select').html(options);
            }
        });
    });
JS
);