<?php

/* @var $order \app\models\Repository\Order */

use yii\widgets\Pjax;

?>

<?php if ($order->id): ?>
    <div class="order-buttons-block">
        <div class="btn-group order-buttons pull-right" role="group">
            <?php foreach ($order::STATUS_MAP[$order->status_id] as $status): ?>
                <?php $statusHelper = new \app\models\Helper\Status($status); ?>
                <?php echo \yii\helpers\Html::a(
                    '<span>' . $statusHelper->getStatusActionName() . '</span>',
                    '#',
                    [
                        'class'            => 'action-with-approve btn ' . ($statusHelper->isGreenFlowStatus() ? 'btn-success' : 'btn-danger'),
                        'data-title'       => \Yii::t('order', 'Do you want change status to ' . $statusHelper->getStatusKey()),
                        'data-text'        => '',
                        'data-request-url' => \yii\helpers\Url::to(['order/set-status', 'orderID' => $order->id, 'statusID' => $status]),
                    ]
                ); ?>
            <?php endforeach; ?>
        </div>
        <div class="pull-right" id="pre-request-modal-buttons">
            <?php Pjax::begin(
                ['id' => 'pre-request-modal-buttons']
            ); ?>
            <?php echo \yii\helpers\Html::a(
                '<span>' . \Yii::t('order', 'Deffer order') . '</span>',
                '#',
                [
                    'class'                => 'action-with-request btn btn-default',
                    'data-pre-request-url' => \yii\helpers\Url::to(['order/deffer-request', 'orderID' => $order->id]),
                    'data-request-url'     => \yii\helpers\Url::to(['order/deffer', 'orderID' => $order->id]),
                ]
            ); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
<?php endif; ?>