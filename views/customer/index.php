<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\Search\PaymentType $searchModel */

$this->title = \Yii::t('customer', 'Customers');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin([
    'enablePushState' => false,
    'enableReplaceState' => true,
]); ?>
<div class="box">
    <div class="box-header with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> ' . \Yii::t('customer', 'Create customer'),
                ['customer/create'],
                [
                    'class'       => 'btn btn-sm btn-warning',
                    'data-href'   => Url::to(['create']),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]
            ) ?>
        </div>
        <div class="pull-right">
            <?= Html::button('<i class="material-icons">cloud_upload</i>', ['class' => 'btn btn-sm btn-default import']) ?>
            <?= Html::button('<i class="material-icons">cloud_download</i>', [
                'class'     => 'btn btn-sm btn-default export',
                'data-href' => Url::to(['customer/export']),
            ]) ?>
            <?= Html::submitButton('<i class="material-icons">clear</i>', [
                'class'      => 'btn btn-sm btn-danger delete',
                'data-title' => \Yii::t('customer', 'Do you really want to delete selected customers?'),
                'data-href'  => Url::to(['customer/delete']),
            ]) ?>
            <div class="hidden"><?= Html::fileInput('import', '', [
                    'data-href' => Url::to(['customer/import'])
                ]) ?></div>
        </div>
    </div>
    <?= GridView::widget([
        'tableOptions' => [
            'data-resizable-columns-id' => 'customer',
            'class'                     => 'table'
        ],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\Customer())->getSearchColumns($searchModel),
    ]);
    ?>
</div>
<?php Pjax::end(); ?>


