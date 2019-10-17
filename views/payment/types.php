<?php

use app\widgets\Grid\GridView;
use yii\widgets\Pjax;

/** @var \app\models\search\PaymentType $searchModel */

$this->title = \Yii::t('payment', 'Payment types');
?>

<div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'tableOptions' => [
            'data-resizable-columns-id' => 'product',
            'class'                     => 'table'
        ],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => (new \app\models\Search\PaymentType())->getSearchColumns($searchModel),
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>



