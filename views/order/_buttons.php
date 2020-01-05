<?php

/* @var $order \app\models\Repository\Order */
?>

<div class="btn-group order-buttons" role="group">
    <?php foreach ($order::STATUS_MAP[$order->status_id] as $status): ?>
        <?php $statusHelper = new \app\models\Helper\Status($status); ?>
        <?php echo \yii\helpers\Html::a(
            $statusHelper->getStatusName(),
                ['order/set-status', 'orderID' => $order->id, 'statusID' => $status],
                ['class' => 'btn ' . ($statusHelper->isGreenFlowStatus() ? 'btn-success' : 'btn-danger')]
        );?>
    <?php endforeach; ?>
</div>
