<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\Search\Subscription $searchModel */

$this->title = \Yii::t('subscription', 'Subscriptions');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(); ?>
    <div class="box">
        <div class="box-header with-border">
            <div class="pull-left">
                <?= Html::a(
                    '<i class="material-icons">add</i> ' . \Yii::t('subscription', 'Create subscription'),
                    ['subscription/create'],
                    [
                        'class'       => 'btn btn-sm btn-warning',
                        'data-href'   => Url::to(['create']),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                    ]
                ) ?>
            </div>
            <div class="pull-right">
                <?= Html::submitButton('<i class="material-icons">delete</i> ', [
                    'class'      => 'btn btn-sm btn-danger delete',
                    'data-title' => \Yii::t('subscription', 'Do you really want to delete selected subscriptions?'),
                    'data-href'  => Url::to(['subscription/delete']),
                ]) ?>
            </div>
        </div>
        <?= GridView::widget([
            'tableOptions' => [
                'data-resizable-columns-id' => 'subscription',
                'class'                     => 'table'
            ],
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => (new \app\models\Search\Subscription())->getSearchColumns($searchModel),
        ]);
        ?>
    </div>
<?php Pjax::end(); ?>