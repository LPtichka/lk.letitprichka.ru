<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\search\Menu $searchModel */

$this->title = \Yii::t('subscription', 'Menu');
?>
<?php Pjax::begin([
    'id'              => 'menu-form',
    'formSelector'    => '#menu-form form',
    'enablePushState' => false,
]); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a(\Yii::t('subscription', 'Create menu'), ['menu/create'], ['class' => 'btn btn-sm btn-warning']) ?>
        </div>
        <div class="pull-right">
            <?= Html::submitButton('<i class="fa fa-times"></i> ', [
                'class'      => 'btn btn-sm btn-danger delete',
                'data-title' => \Yii::t('menu', 'Do you really want to delete selected subscriptions?'),
                'data-href'  => Url::to(['menu/delete']),
            ]) ?>
        </div>
    </div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'tableOptions' => [
            'data-resizable-columns-id' => 'menu',
            'class'                     => 'table'
        ],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\Menu())->getSearchColumns($searchModel),
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
<?php Pjax::end(); ?>