<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\search\Subscription $searchModel */

$this->title = \Yii::t('subscription', 'Subscriptions');
?>
<?php Pjax::begin([
    'id'              => 'subscription-form',
    'formSelector'    => '#subscription-form form',
    'enablePushState' => false,
]); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a(\Yii::t('subscription', 'Create subscription'), ['subscription/create'], ['class' => 'btn btm-sm btn-warning']) ?>
        </div>
        <div class="pull-right">
            <?= Html::button('<i class="fa fa-upload"></i> ', ['class' => 'btn btm-sm btn-default import']) ?>
            <?= Html::button('<i class="fa fa-download"></i> ', [
                'class'     => 'btn btm-sm btn-default export',
                'data-href' => Url::to(['subscription/export']),
            ]) ?>
            <?= Html::submitButton('<i class="fa fa-times"></i> ', [
                'class'      => 'btn btm-sm btn-danger delete',
                'data-title' => \Yii::t('subscription', 'Do you really want to delete selected subscriptions?'),
                'data-href'  => Url::to(['subscription/delete']),
            ]) ?>
            <div class="hidden"><?= Html::fileInput('import', '', [
                    'data-href' => Url::to(['subscription/import'])
                ]) ?></div>
        </div>
    </div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'tableOptions' => [
            'data-resizable-columns-id' => 'address',
            'class'                     => 'table table-bordered'
        ],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\Subscription())->getSearchColumns($searchModel),
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
<?php Pjax::end(); ?>