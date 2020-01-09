<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\search\Exception $searchModel */
/** @var string $title */

$this->title = $title;
?>
<?php Pjax::begin([
    'id'              => 'franchise-form',
    'formSelector'    => '#franchise-form form',
    'enablePushState' => false,
]); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a(\Yii::t('franchise', 'Create franchise'), ['franchise/create'], ['class' => 'btn btn-sm btn-warning']) ?>
        </div>
        <div class="pull-right">
            <?= Html::submitButton('<i class="fa fa-times"></i> ', [
                'class'      => 'btn btn-sm btn-danger delete',
                'data-title' => \Yii::t('exception', 'Do you really want to delete selected exceptions?'),
                'data-href'  => Url::to(['exception/delete']),
            ]) ?>
        </div>
    </div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'tableOptions' => [
            'data-resizable-columns-id' => 'exception',
            'class'                     => 'table'
        ],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\Franchise())->getSearchColumns($searchModel),
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
<?php Pjax::end(); ?>


