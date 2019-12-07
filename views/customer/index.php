<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\search\PaymentType $searchModel */

$this->title = \Yii::t('customer', 'Customers');
?>
<?php Pjax::begin([
    'id'              => 'customer-form',
    'formSelector'    => '#customer-form form',
    'enablePushState' => false,
]); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a(\Yii::t('customer', 'Create customer'), ['customer/create'], ['class' => 'btn btn-sm btn-warning']) ?>
        </div>
        <div class="pull-right">
            <?= Html::button('<i class="fa fa-upload"></i> ', ['class' => 'btn btn-sm btn-default import']) ?>
            <?= Html::button('<i class="fa fa-download"></i> ', [
                'class'     => 'btn btn-sm btn-default export',
                'data-href' => Url::to(['customer/export']),
            ]) ?>
            <?= Html::submitButton('<i class="fa fa-times"></i> ', [
                'class'      => 'btn btn-sm btn-danger delete',
                'data-title' => \Yii::t('customer', 'Do you really want to delete selected customers?'),
                'data-href'  => Url::to(['customer/delete']),
            ]) ?>
            <div class="hidden"><?= Html::fileInput('import', '', [
                    'data-href' => Url::to(['customer/import'])
                ]) ?></div>
        </div>
    </div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'tableOptions' => [
            'data-resizable-columns-id' => 'customer',
            'class'                     => 'table'
        ],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\Customer())->getSearchColumns($searchModel),
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
<?php Pjax::end(); ?>


