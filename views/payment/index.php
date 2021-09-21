<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\Search\PaymentType $searchModel */

$this->title = \Yii::t('payment', 'Payment types');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a(
                '<i class="material-icons">add</i> ' . \Yii::t('payment', 'Create payment'),
                ['payment-type/create'],
                [
                    'class' => 'btn btn-sm btn-warning',
                    'data-href'   => Url::to(['create']),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]
            ); ?>
        </div>
        <div class="pull-right">
            <?= Html::button('<i class="material-icons">cloud_upload</i> ', ['class' => 'btn btn-sm btn-default import']) ?>
            <?= Html::button('<i class="material-icons">cloud_download</i> ', [
                'class'     => 'btn btn-sm btn-default export',
                'data-href' => Url::to(['payment-type/export']),
            ]) ?>
            <?= Html::submitButton('<i class="material-icons">delete</i> ', [
                'class'      => 'btn btn-sm btn-danger delete',
                'data-title' => \Yii::t('payment', 'Do you really want to delete selected payments?'),
                'data-href'  => Url::to(['payment-type/delete']),
            ]) ?>
            <div class="hidden"><?= Html::fileInput('import', '', [
                    'data-href' => Url::to(['payment-type/import'])
                ]) ?></div>
        </div>
    </div>
    <?= GridView::widget([
        'tableOptions' => [
            'data-resizable-columns-id' => 'payment',
            'class'                     => 'table'
        ],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\PaymentType())->getSearchColumns($searchModel),
    ]);
    ?>
</div>
<?php Pjax::end(); ?>


