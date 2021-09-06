<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var \app\models\Common\MarriageDish[] $ingestions */
/** @var array $menus */
/** @var int $menuId */
/** @var \app\models\Repository\Product[] $products */

Pjax::begin([
    'id'              => 'warehouse-accounting-form',
    'formSelector'    => '#warehouse-accounting-form form',
    'enablePushState' => false,
]); ?>
    <div class="route-row">
        <h1><?= $title; ?></h1>
        <title><?= $title; ?></title>
        <?php $form = ActiveForm::begin(); ?>
        <div class="row modal-buttons">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::a(
                        '<i class="material-icons">done</i> <span>' . \Yii::t('app', 'Download') . '</span>',
                        ['product/save-warehouse-accounting'],
                        [
                            'class'        => 'btn btn-sm btn-warning save-warehouse-accounting',
                            'data-menu-id' => $menuId,
                        ]
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
        <?php ActiveForm::end(); ?>
    </div>
<?php Pjax::end(); ?>
<?php
\Yii::$app->view->registerJs(<<<JS
    body.delegate('.save-warehouse-accounting', 'click', function (e) {
        e.preventDefault();
        let button = $(this);
        $.ajax({
            url: '/product/save-warehouse-accounting',
            type: 'POST',
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