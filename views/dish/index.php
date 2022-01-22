<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\Search\PaymentType $searchModel */

$this->title = \Yii::t('dish', 'Dishes');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin([
    'id'              => 'dish-form',
    'formSelector'    => '#dish-form form',
]); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a(
                '<i class="material-icons">add</i> ' . \Yii::t('dish', 'Create dish'),
                ['dish/create'],
                ['class' => 'btn btn-sm btn-warning']
            ) ?>
        </div>
        <div class="pull-right">
            <?= Html::button('<i class="material-icons">cloud_upload</i>', ['class' => 'btn btn-sm btn-default import']) ?>
            <?= Html::button('<i class="material-icons">cloud_download</i>', [
                'class'     => 'btn btn-sm btn-default export',
                'data-href' => Url::to(['dish/export']),
            ]) ?>
            <?= Html::submitButton('<i class="material-icons">delete</i>', [
                'class'      => 'btn btn-sm btn-danger delete',
                'data-title' => \Yii::t('dish', 'Do you really want to delete selected dishes?'),
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
        'rowOptions'   => function ($model, $key, $index, $grid){
            $class = $model->hasDeletedProducts() ? 'error' : 'success';
            return [
                'key'   => $key,
                'index' => $index,
                'class' => $class
            ];
        },
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\Dish())->getSearchColumns($searchModel),
    ]);
    ?>
</div>
<?php Pjax::end(); ?>


