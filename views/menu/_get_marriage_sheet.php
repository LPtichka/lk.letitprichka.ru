<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var \app\models\Common\MarriageDish[] $ingestions */
Pjax::begin([
    'id'              => 'ingestion-form',
    'formSelector'    => '#ingestion-form form',
    'enablePushState' => false,
]); ?>
<?php if (empty($ingestions)): ?>
    <div class="route-row">
        <h1><?= $title; ?></h1>
        <title><?= $title; ?></title>
        <?php $form = ActiveForm::begin(); ?>
        <div>
            <label><?= \Yii::t('menu', 'Choose date'); ?></label>
            <?php echo DatePicker::widget([
                'name'          => 'date',
                'removeButton'  => false,
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
            <div class="col-sm-2">Дата</div>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-2">Время</div>
                    <div class="col-sm-2">Тип</div>
                    <div class="col-sm-2">Наименование</div>
                    <div class="col-sm-2">Результат</div>
                    <div class="col-sm-2">Разрешение</div>
                    <div class="col-sm-2">Подписи</div>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-sm-2 left-element">
                <?= $date; ?>
            </div>
            <div class="col-sm-10">
                <?php foreach ($ingestions as $ingestion): ?>
                    <div class="row list-element">
                        <div class="col-sm-2"><?php echo $ingestion->getTime(); ?></div>
                        <div class="col-sm-2"><?php echo $ingestion->getType(); ?></div>
                        <div class="col-sm-2"><?php echo $ingestion->getDishName(); ?></div>
                        <div class="col-sm-2"><?php echo $ingestion->getRating(); ?></div>
                        <div class="col-sm-2"><?php echo $ingestion->getResult(); ?></div>
                        <div class="col-sm-2"><?php echo $ingestion->getSignature(); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <hr/>
        <div class="row modal-buttons">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::a(
                        '<i class="fa fa-download"></i> ' . \Yii::t('app', 'Download'),
                        ['menu/save-marriage-sheet'],
                        [
                            'class'     => 'btn btn-sm btn-warning save-marriage-sheet',
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
    body.delegate('.save-marriage-sheet', 'click', function (e) {
        e.preventDefault();
        let date = $(this).data('date');
        $.ajax({
            url: '/menu/save-marriage-sheet',
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