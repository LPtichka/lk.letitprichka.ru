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
        <div>
            <label><?= \Yii::t('order', 'Choose date'); ?></label>
            <?php echo Html::dropDownList('schedule_id', '', $dates, [
                'class' => 'form-control input-sm'
            ]); ?>
        </div>
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
        <?php ActiveForm::end(); ?>
    </div>
<?php endif; ?>
<?php Pjax::end(); ?>
<?php
\Yii::$app->view->registerJs(<<<JS
    body.delegate('.get-customer-sheet', 'click', function (e) {
        e.preventDefault();
        let scheduleId = $('[name="schedule_id"]').val();
        $.ajax({
            url: '/order/get-customer-sheet?id=$id',
            type: 'POST',
            data: {schedule_id: scheduleId},
            dataType: 'json',
            success: function(data) {
                window.location.href = data.url;
            }
        });
    });
JS
);