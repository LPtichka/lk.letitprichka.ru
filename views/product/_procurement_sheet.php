<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var \app\models\Common\MarriageDish[] $ingestions */
/** @var array $menus */
/** @var int $menuId */
/** @var \app\models\Repository\Product[] $products */

Pjax::begin([
                'id'              => 'procurement-form',
                'formSelector'    => '#procurement-form form',
                'enablePushState' => false,
            ]); ?>
<?php
if (empty($products) && $success): ?>
    <div class="route-row">
        <title><?= $title; ?></title>
        <?php
        $form = ActiveForm::begin(); ?>
        <div>
            <label><?= \Yii::t('menu', 'Choose date'); ?></label>
            <?= DatePicker::widget(
                [
                    'name'          => 'menu_start_date',
                    'value'         => date('Y-m-d', time()),
                    'type'          => DatePicker::TYPE_RANGE,
                    'separator'     => ' - ',
                    'name2'         => 'menu_end_date',
                    'disabled'      => false,
                    'value2'        => date('Y-m-d', time()),
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format'    => 'yyyy-mm-dd',
                    ],
                ]
            ); ?>
        </div>
        <div class="row modal-buttons">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::submitButton(
                        '<i class="material-icons">done</i> <span>' . \Yii::t('app', 'Unload') . '</span>',
                        ['class' => 'btn btn-sm btn-warning']
                    ) ?>
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
        <?php
        ActiveForm::end(); ?>
    </div>
<?php
else: ?>
    <div class="route-row">
        <div class="row header-table">
            <div class="col-sm-2"><?php
                echo \Yii::t('product', 'ID'); ?></div>
            <div class="col-sm-4"><?php
                echo \Yii::t('product', 'Name'); ?></div>
            <div class="col-sm-2"><?php
                echo \Yii::t('product', 'Available'); ?></div>
            <div class="col-sm-2"><?php
                echo \Yii::t('product', 'Required amount'); ?></div>
            <div class="col-sm-2"><?php
                echo \Yii::t('product', 'Not enough count'); ?></div>
        </div>
        <hr/>
        <?php
        if ($success): ?>
            <?php
            foreach ($products as $product): ?>
                <div class="row list-element">
                    <div class="col-sm-2"><?php
                        echo $product->id; ?></div>
                    <div class="col-sm-4"><?php
                        echo $product->name; ?></div>
                    <div class="col-sm-2"><?php
                        echo (new \app\models\Helper\Unit($product->unit))->format($product->count); ?></div>
                    <div class="col-sm-2"><?php
                        echo (new \app\models\Helper\Unit($product->unit))->format($product->getNeedCount()); ?></div>
                    <div class="col-sm-2"><?php
                        echo (new \app\models\Helper\Unit($product->unit))->format(
                            $product->getNotEnoughCount()
                        ); ?></div>
                </div>
            <?php
            endforeach; ?>
        <?php
        else: ?>
            <div class="row list-element">
                <div class="col-sm-12"><p class="text-center text-danger">Ошибка: <?php
                        echo $error; ?></p></div>
            </div>
        <?php
        endif; ?>
        <hr/>
        <div class="row modal-buttons">
            <div class="col-md-6">
                <?php
                if ($success): ?>
                    <div class="form-group">
                        <?= Html::a(
                            '<i class="material-icons">done</i> <span>' . \Yii::t('app', 'Download') . '</span>',
                            ['product/save-procurement-sheet'],
                            [
                                'class'           => 'btn btn-sm btn-warning save-procurement-sheet',
                                'data-menu-start' => $menuStart,
                                'data-menu-end'   => $menuEnd,
                            ]
                        ) ?>
                    </div>
                <?php
                endif; ?>
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
    </div>
<?php
endif; ?>
<?php
Pjax::end(); ?>
<?php
\Yii::$app->view->registerJs(
    <<<JS
    body.delegate('.save-procurement-sheet', 'click', function (e) {
        e.preventDefault();
        let start = $(this).data('menu-start');
        let end = $(this).data('menu-end');
        let button = $(this);
        $.ajax({
            url: '/product/save-procurement-sheet',
            type: 'POST',
            data: {start: start, end: end},
            dataType: 'json',
            beforeSend: function () {
               button.addClass('loading'); 
            },
            success: function(data) {
                button.removeClass('loading');
                window.location.href = data.url;
            }
        });
    });
JS
);