<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\Search\Menu $searchModel */

$this->title                   = \Yii::t('menu', 'Menu');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin([
    'id'              => 'menu-form',
    'formSelector'    => '#menu-form form',
    'enablePushState' => false,
]); ?>
    <div class="box">
        <div class="box-header with-border">
            <div class="pull-left">
                <?= Html::a('<i class="material-icons">add</i> ' . \Yii::t('menu', 'Create menu'),
                    ['menu/create'],
                    ['class' => 'btn btn-sm btn-warning']
                ) ?>
            </div>
            <div class="pull-right">
                <?= Html::a(
                    '<span>' . \Yii::t('menu', 'Marriage sheet') . '</span>',
                    ['order/get-route-sheet'],
                    [
                        'class'       => 'btn btn-sm btn-default',
                        'data-href'   => Url::to(['menu/get-marriage-sheet']),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                    ]) ?>
                <?= Html::submitButton('<i class="material-icons">clear</i>', [
                    'class'      => 'btn btn-sm btn-danger delete',
                    'data-title' => \Yii::t('menu', 'Do you really want to delete selected subscriptions?'),
                    'data-href'  => Url::to(['menu/delete']),
                ]) ?>
            </div>
        </div>
        <?= GridView::widget([
            'tableOptions' => [
                'data-resizable-columns-id' => 'menu',
                'class'                     => 'table'
            ],
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'rowOptions'   => function ($model, $key, $index, $grid){
                $class = $model->isEquipped() ? 'error' : 'success';
                return [
                    'key'   => $key,
                    'index' => $index,
                    'class' => $class
                ];
            },
            'columns'      => (new \app\models\Search\Menu())->getSearchColumns($searchModel),
        ]);
        ?>
    </div>
<?php Pjax::end(); ?>