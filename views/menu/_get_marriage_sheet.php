<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var \app\models\Common\MarriageDish[] $ingestions */
/** @var string $title */

Pjax::begin(
    [
        'id'              => 'ingestion-form',
        'formSelector'    => '#ingestion-form form',
        'enablePushState' => false,
    ]
);?>
<title><?php echo $title; ?></title>
<?php if ($ingestions === null || empty($ingestions)): ?>
    <div class="route-row">
        <?php if ($ingestions !== null):?>
            <div class="alert-danger alert fade in">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fa fa-warning"></i><?= \Yii::t('menu', 'You have not made a menu for this day'); ?>
            </div>
        <br />
        <?php endif;?>
        <?php
        $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-sm-9">
                <label><?= \Yii::t('menu', 'Choose date'); ?></label>
                <?php
                echo DatePicker::widget(
                    [
                        'name'          => 'date',
                        'removeButton'  => false,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format'    => 'dd.mm.yyyy'
                        ]
                    ]
                ); ?>
            </div>
            <div class="col-sm-3">
                <label><?= \Yii::t('menu', 'Choose time'); ?></label>
                <?= Html::textInput(
                    'time',
                    date('H:i', time()),
                    [
                        'class' => 'form-control'
                    ]
                ) ?>
            </div>
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
        <?php
        ActiveForm::end(); ?>
    </div>
<?php
else: ?>
    <div class="route-row">
        <div class="row header-table">
            <div class="col-sm-2">дата приготовления блюда и время бракеража</div>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-3">наименование изделия</div>
                    <div class="col-sm-1">выход</div>
                    <div class="col-sm-2">результат органолептической оценки и степени готовности</div>
                    <div class="col-sm-2">разрешение к реализации</div>
                    <div class="col-sm-2">качество</div>
                    <div class="col-sm-2">подписи членов бракеражной комиссии</div>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-sm-2 left-element">
                <?= $date . " " . $time; ?>
            </div>
            <div class="col-sm-10">
                <?php
                foreach ($ingestions as $ingestion): ?>
                    <div class="row list-element">
                        <div class="col-sm-3"><?php
                            echo $ingestion->getDishName(); ?></div>
                        <div class="col-sm-1"><?php
                            echo $ingestion->getWeight(); ?>&nbsp;г.
                        </div>
                        <div class="col-sm-2"><?php
                            echo $ingestion->getRating(); ?></div>
                        <div class="col-sm-2"><?php
                            echo $ingestion->getResult(); ?></div>
                        <div class="col-sm-2"><?php
                            echo $ingestion->getQuality(); ?></div>
                        <div class="col-sm-2"><?php
                            echo $ingestion->getSignature(); ?></div>
                    </div>
                <?php
                endforeach; ?>
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
                            'data-time' => $time,
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
<?php
endif; ?>
<?php
Pjax::end(); ?>
<?php
\Yii::$app->view->registerJs(
    <<<JS
    body.delegate('.save-marriage-sheet', 'click', function (e) {
        e.preventDefault();
        let button = $(this);
        let date = button.data('date');
        let time = button.data('time');
        
        $.ajax({
            url: '/menu/save-marriage-sheet',
            type: 'POST',
            data: {date: date, time: time},
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