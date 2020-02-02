<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\Search\PaymentType $searchModel */

$this->title = \Yii::t('app', 'Dishes');
?>
<?php Pjax::begin([
    'id'              => 'dish-form',
    'formSelector'    => '#dish-form form',
    'enablePushState' => false,
]); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a(
                '<i class="fa fa-plus"></i> ' . \Yii::t('dish', 'Create dish'),
                ['dish/create'],
                ['class' => 'btn btn-sm btn-warning']
            ) ?>
        </div>
        <div class="pull-right">
            <?= Html::button('<i class="fa fa-upload"></i> ', ['class' => 'btn btn-sm btn-default import']) ?>
            <?= Html::button('<i class="fa fa-download"></i> ', [
                'class'     => 'btn btn-sm btn-default export',
                'data-href' => Url::to(['dish/export']),
            ]) ?>
            <?= Html::submitButton('<i class="fa fa-times"></i> ', [
                'class'      => 'btn btn-sm btn-danger delete',
                'data-title' => \Yii::t('payment', 'Do you really want to delete selected dishes?'),
                'data-href'  => Url::to(['dish/delete']),
            ]) ?>
            <div class="hidden"><?= Html::fileInput('import', '', [
                    'data-href' => Url::to(['dish/import'])
                ]) ?></div>
        </div>
    </div>
    <?= GridView::widget([
        'tableOptions' => [
            'data-resizable-columns-id' => 'dish',
            'class'                     => 'table'
        ],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\Dish())->getSearchColumns($searchModel),
    ]);
    ?>
</div>
<?php Pjax::end(); ?>


