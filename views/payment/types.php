<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\search\PaymentType $searchModel */

$this->title = \Yii::t('payment', 'Payment types');
?>
<?php Pjax::begin([
    'id'              => 'payment-form',
    'formSelector'    => '#payment-form form',
    'enablePushState' => false,
]); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a(\Yii::t('payment', 'Create payment'), ['payment-type/create'], ['class' => 'btn btm-sm btn-warning']) ?>
        </div>
        <div class="pull-right">
            <?= Html::submitButton('<i class="fa fa-times"></i> ', ['class' => 'btn btm-sm btn-danger delete']) ?>
        </div>
    </div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'tableOptions' => [
            'data-resizable-columns-id' => 'product',
            'class'                     => 'table table-bordered'
        ],
        'rowOptions'   => function ($model) {
            return [
                'onclick' => 'location.href="'
                    . Url::to(['payment-type/view', 'id' => $model->id]) . '";'
            ];
        },
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\PaymentType())->getSearchColumns($searchModel),
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
<?php Pjax::end(); ?>


