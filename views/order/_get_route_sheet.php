<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var \app\models\Common\Route[] $routes */
Pjax::begin([
    'id'              => 'payment-form',
    'formSelector'    => '#payment-form form',
    'enablePushState' => false,
]); ?>
<?php if (empty($routes)): ?>
    <div class="route-row">
        <title><?= $title; ?></title>
        <?php $form = ActiveForm::begin(); ?>
        <div>
            <label><?= \Yii::t('order', 'Choose date'); ?></label>
            <?php echo DatePicker::widget([
                'name'          => 'date',
                'removeButton'  => false,
                'options'       => [
                    'autocomplete' => 'off'
                ],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format'    => 'dd.mm.yyyy'
                ]
            ]); ?>
        </div>
        <div class="row modal-buttons">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::submitButton(
                        '<i class="material-icons">done</i> ' . \Yii::t('app', 'Unload'),
                        ['class' => 'btn btn-sm btn-warning']
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
        <?php ActiveForm::end(); ?>
    </div>
<?php else: ?>
    <div class="route-row">
        <div class="row header-table">
            <div class="col-sm-2"><?php echo \Yii::t('order', 'Client'); ?></div>
            <div class="col-sm-4"><?php echo \Yii::t('order', 'Address'); ?></div>
            <div class="col-sm-2"><?php echo \Yii::t('order', 'Interval'); ?></div>
            <div class="col-sm-2"><?php echo \Yii::t('order', 'Phone'); ?></div>
            <div class="col-sm-2"><?php echo \Yii::t('order', 'Payment'); ?></div>
        </div>
        <hr/>
        <?php foreach ($routes as $route): ?>
            <div class="row list-element">
                <div class="col-sm-2"><?php echo $route->getFio(); ?></div>
                <div class="col-sm-4"><?php echo $route->getAddress(); ?></div>
                <div class="col-sm-2"><?php echo $route->getInterval(); ?></div>
                <div class="col-sm-2"><?php echo $route->getPhone(); ?></div>
                <div class="col-sm-2"><?php echo $route->getPayment(); ?></div>
            </div>
        <?php endforeach; ?>
        <hr/>
        <div class="row modal-buttons">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::a(
                        '<i class="material-icons">done</i> ' . \Yii::t('app', 'Download'),
                        ['order/save-route-sheet'],
                        [
                            'class'     => 'btn btn-sm btn-warning save-route-sheet',
                            'data-date' => $date,
                        ]
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
    </div>
<?php endif; ?>
<?php Pjax::end(); ?>
<?php
\Yii::$app->view->registerJs(<<<JS
    body.delegate('.save-route-sheet', 'click', function (e) {
        e.preventDefault();
        let date = $(this).data('date');
        $.ajax({
            url: '/order/save-route-sheet',
            type: 'POST',
            data: {date: date},
            dataType: 'json',
            success: function(data) {
                window.location.href = data.url;
            }
        });
    });
JS
);