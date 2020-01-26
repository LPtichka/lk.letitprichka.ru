<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\Search\PaymentType $searchModel */

$this->title = \Yii::t('product', 'Products');
?>
<?php Pjax::begin(); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a(
                '<i class="fa fa-plus"></i> ' . \Yii::t('product', 'Create product'),
                ['product/create'],
                [
                    'class'       => 'btn btn-sm btn-warning',
                    'data-href'   => Url::to(['create']),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]) ?>
        </div>
        <div class="pull-right">
            <?= Html::button('<i class="fa fa-upload"></i> ', ['class' => 'btn btn-sm btn-default import']) ?>
            <?= Html::button('<i class="fa fa-download"></i> ', [
                'class'     => 'btn btn-sm btn-default export',
                'data-href' => Url::to(['product/export']),
            ]) ?>
            <?= Html::submitButton('<i class="fa fa-times"></i> ', [
                'class'      => 'btn btn-sm btn-danger delete',
                'data-title' => \Yii::t('product', 'Do you really want to delete selected products?'),
                'data-href'  => Url::to(['product/delete']),
            ]); ?>
            <div class="hidden"><?= Html::fileInput('import', '', [
                    'data-href' => Url::to(['product/import'])
                ]) ?></div>
        </div>
    </div>
    <?= GridView::widget([
        'tableOptions' => [
            'data-resizable-columns-id' => 'product',
            'class'                     => 'table'
        ],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\Product())->getSearchColumns($searchModel),
    ]);
    ?>
</div>
<?php Pjax::end(); ?>


