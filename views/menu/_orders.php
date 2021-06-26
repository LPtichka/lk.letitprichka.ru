<?php

/* @var $order \app\models\Repository\Order */

use app\widgets\Grid\GridView;

?>
<div>
    <h4>Заказы входящие в это меню</h4>
    <?php
    echo GridView::widget(
        [
            'tableOptions' => [
                'data-resizable-columns-id' => 'order',
                'class'                     => 'table'
            ],
            'dataProvider' => $dataProvider,
            'rowOptions'   => function ($model, $key, $index, $grid) {
                $class = $model->isNotEquipped() ? 'error' : 'success';
                return [
                    'key'   => $key,
                    'index' => $index,
                    'class' => $class
                ];
            },
            'filterModel'  => $searchModel,
            'columns'      => (new \app\models\Search\Order())->getSearchColumns($searchModel),
        ]
    );
    ?>
</div>