<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\search\PaymentType $searchModel */

$this->title = \Yii::t('address', 'Addresses');
?>
<?php Pjax::begin(); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a(
                '<i class="fa fa-plus"></i> ' . \Yii::t('address', 'Create address'),
                ['address/create'],
                [
                    'class' => 'btn btn-sm btn-warning',
                    'data-href'   => Url::to(['create']),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]
            ) ?>
        </div>
        <div class="pull-right">
            <?= Html::button('<i class="fa fa-upload"></i> ', ['class' => 'btn btn-sm btn-default import']) ?>
            <?= Html::button('<i class="fa fa-download"></i> ', [
                'class'     => 'btn btn-sm btn-default export',
                'data-href' => Url::to(['address/export']),
            ]) ?>
            <?= Html::submitButton('<i class="fa fa-times"></i> ', [
                'class'      => 'btn btn-sm btn-danger delete',
                'data-title' => \Yii::t('address', 'Do you really want to delete selected addresses?'),
                'data-href'  => Url::to(['address/delete']),
            ]) ?>
            <div class="hidden"><?= Html::fileInput('import', '', [
                    'data-href' => Url::to(['address/import'])
                ]) ?></div>
        </div>
    </div>
    <?= GridView::widget([
        'tableOptions' => [
            'data-resizable-columns-id' => 'address',
            'class'                     => 'table table-bordered'
        ],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\Address())->getSearchColumns($searchModel),
    ]);
    ?>
</div>
<?php Pjax::end(); ?>


