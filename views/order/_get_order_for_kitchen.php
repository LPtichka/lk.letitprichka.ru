<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var \app\models\Common\Route[] $routes */
Pjax::begin([
    'id'              => 'user-sheet-form',
    'formSelector'    => '#user-sheet-form form',
    'enablePushState' => false,
]); ?>
    <title><?php echo $title; ?></title>
    <div class="route-row">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-sm-12">
                <label><?= \Yii::t('menu', 'Choose date'); ?></label>
                <?php
                echo DatePicker::widget(
                    [
                        'name'          => 'date',
                        'removeButton'  => false,
                        'options'       => [
                            'autocomplete' => 'off'
                        ],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format'    => 'dd.mm.yyyy'
                        ]
                    ]
                ); ?>
            </div>
        </div>
        <div class="row modal-buttons">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::button(
                        '<i class="material-icons">done</i> ' . \Yii::t('app', 'Save'),
                        ['class' => 'btn btn-sm btn-warning save-order-to-kitchen-sheet']
                    ) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group text-right">
                    <?= Html::a(
                        \Yii::t('app', 'Cancel'),
                        '#',
                        [
                            'class'        => 'btn btn-sm btn-default',
                            'data-dismiss' => 'modal'
                        ]
                    ) ?>
                </div>
            </div>
        </div>
        <?php
        ActiveForm::end(); ?>
    </div>
<?php Pjax::end(); ?>
<?php
\Yii::$app->view->registerJs(
    <<<JS
    body.delegate('.save-order-to-kitchen-sheet', 'click', function (e) {
        e.preventDefault();
        let button = $(this);
        let date = $('[name="date"]').val();
        
        $.ajax({
            url: '/order/save-order-for-kitchen',
            type: 'POST',
            data: {date: date},
            dataType: 'json',
            beforeSend: function() {
                button.button('loading');
            },
            complete: function() {
                button.button('reset');
            },
            success: function(data) {
                window.location.href = data.url;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                swal({
                    title: "Ошибка",
                    text: thrownError,
                    type: 'error',
                    showCancelButton: true,
                    closeOnConfirm: false,
                });
            }
        });
    });
JS
);
