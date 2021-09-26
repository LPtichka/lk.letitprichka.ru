<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\Repository\Order $searchModel */

$this->title = \Yii::t('order', 'Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
Pjax::begin([
                'id'           => 'order-form',
                'formSelector' => '#order-form form',
                'timeout'      => 2000,
            ]); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a(
                '<i class="material-icons">add</i> ' . \Yii::t('order', 'Create order'),
                ['order/create'],
                ['class' => 'btn btn-sm btn-warning']
            ) ?>
        </div>
        <div class="pull-right">
            <?= Html::a(
                '<span>Маршрутный лист</span>',
                ['order/get-route-sheet'],
                [
                    'class'       => 'btn btn-sm btn-default',
                    'data-href'   => Url::to(['order/get-route-sheet']),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]
            ); ?>
            <?= Html::button('<i class="material-icons">cloud_upload</i>', ['class' => 'btn btn-sm btn-default import']
            ) ?>
            <?= Html::button('<i class="material-icons">cloud_download</i>', [
                'class'     => 'btn btn-sm btn-default export',
                'data-href' => Url::to(['product/export']),
            ]) ?>
            <?= Html::submitButton('<i class="material-icons">delete_forever</i>', [
                'class'      => 'btn btn-sm btn-danger delete',
                'data-title' => \Yii::t('order', 'Do you really want to delete selected products?'),
                'data-href'  => Url::to(['product/delete']),
            ]); ?>
            <div class="hidden">
                <?= Html::fileInput('import', '', [
                    'data-href' => Url::to(['product/import'])
                ]) ?>
            </div>
        </div>
    </div>
    <?php
    Pjax::begin(); ?>
    <?php
    echo GridView::widget([
                              'tableOptions' => [
                                  'data-resizable-columns-id' => 'order',
                                  'class'                     => 'table'
                              ],
                              'dataProvider' => $dataProvider,
                              'rowOptions'   => function ($model, $key, $index, $grid) {
                                  $class = $model->isNotEquipped() ? 'error' : 'success';
                                  return [
                                      'key'   => $key,
                                      'index' => $index,
                                      'class' => $class
                                  ];
                              },
                              'filterModel'  => $searchModel,
                              'columns'      => (new \app\models\Search\Order())->getSearchColumns($searchModel),
                          ]);
    ?>
    <?php
    Pjax::end(); ?>
</div>
<?php
Pjax::end(); ?>


