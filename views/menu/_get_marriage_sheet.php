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
                    <?= Html::submitButton('<i class="fa fa-check"></i> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-sm btn-warning']) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group text-right">
                    <?= Html::a(\Yii::t('app', 'Cancel'), ['menu/index'], ['class' => 'btn btn-sm btn-default']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php else: ?>
    <div class="route-row">
        <div class="row">
            <div class="col-sm-2">дата приготовления блюда</div>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-2">время бракеража</div>
                    <div class="col-sm-2">тип</div>
                    <div class="col-sm-2">наименование изделия</div>
                    <div class="col-sm-2">результат органолептической оценки и степени готовности</div>
                    <div class="col-sm-2">разрешение к реализации</div>
                    <div class="col-sm-2">подписи членов бракеражной комиссии</div>
                </div>
            </div>

        </div>
        <hr/>
        <div class="row">
            <div class="col-sm-2">
                <?= $date;?>
            </div>
            <div class="col-sm-10">
                <div class="row">
                    <?php foreach ($ingestions as $ingestion): ?>
                        <div class="row">
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
        </div>

        <hr/>
        <div class="row modal-buttons">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::a(
                        '<i class="fa fa-check"></i> ' . \Yii::t('app', 'Save'),
                        ['menu/save-marriage-sheet'],
                        [
                            'class' => 'btn btn-sm btn-warning save-marriage-sheet',
                            'data-date' => $date,
                        ]
                    ) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group text-right">
                    <?= Html::a(\Yii::t('app', 'Cancel'), ['menu/index'], ['class' => 'btn btn-sm btn-default']) ?>
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