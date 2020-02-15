<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\Repository\User $searchModel */

$this->title = \Yii::t('user', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a('<i class="material-icons">add</i> ' . \Yii::t('user', 'Create user'),
                ['user/create'],
                [
                    'class'       => 'btn btn-sm btn-warning',
                    'data-href'   => Url::to(['create']),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]
            ) ?>
        </div>
        <div class="pull-right">
            <?= Html::button('<i class="fa fa-download"></i> ', [
                'class'     => 'btn btn-sm btn-default export',
                'data-href' => Url::to(['user/export']),
            ]) ?>
            <?= Html::submitButton('<i class="fa fa-times"></i> ', [
                'class'      => 'btn btn-sm btn-danger delete',
                'data-title' => \Yii::t('user', 'Do you really want to delete selected users?'),
                'data-href'  => Url::to(['user/delete']),
            ]); ?>
        </div>
    </div>
    <?= GridView::widget([
        'tableOptions' => [
            'data-resizable-columns-id' => 'user',
            'class'                     => 'table'
        ],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\User())->getSearchColumns($searchModel),
    ]);
    ?>
</div>
<?php Pjax::end(); ?>


