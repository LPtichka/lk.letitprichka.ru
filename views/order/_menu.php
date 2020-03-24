<?php

use app\widgets\Html;
use yii\helpers\Url;

/* @var $order \app\models\Repository\Order */
/* @var $addresses array */

?>

<div class="box-header with-border">
    <h2 class="box-title"><?php echo \Yii::t('order', 'Menu block'); ?></h2>
</div>
<div class="box-body order-menu-block-body">
    <div class="row">
        <div class="col-sm-2"><label><?php echo \Yii::t('order', 'Delivery date');?></label></div>
        <div class="col-sm-2"><label><?php echo \Yii::t('order', 'Delivery address');?></label></div>
        <div class="col-sm-2"><label><?php echo \Yii::t('order', 'Delivery interval');?></label></div>
        <div class="col-sm-6"><label><?php echo \Yii::t('order', 'Comment');?></label></div>
    </div>
    <hr />
    <?php foreach ($order->schedules as $schedule): ?>
        <div class="row <?php echo $schedule->getStatusKey();?> ">
            <div class="col-sm-2"><span><?php echo date('d.m.Y', strtotime($schedule->date));?></span></div>
            <div class="col-sm-2">
                <?= Html::activeDropDownList(
                        $schedule,
                        "[$schedule->id]address_id",
                        ['' => \Yii::t('app', 'Choose')] + $addresses,
                        ['class' => 'form-control input-sm', 'disabled' => !$schedule->isEditable()]
                ) ?>
            </div>
            <div class="col-sm-2">
                <?= Html::activeDropDownList(
                        $schedule,
                        "[$schedule->id]interval",
                        ['' => \Yii::t('app', 'Choose')] + $intervals,
                        ['class' => 'form-control input-sm', 'disabled' => !$schedule->isEditable()]
                ) ?>
            </div>
            <div class="col-sm-4">
                <?= Html::activeInput(
                        'text',
                        $schedule,
                        "[$schedule->id]comment",
                        ['class' => 'form-control input-sm', 'disabled' => !$schedule->isEditable()]
                ) ?>
            </div>
            <div class="col-sm-2">
                <?= Html::a(\Yii::t('order', 'Get order enventory'), '#', [
                    'data-href'   => Url::to(['order/get-date-inventory', 'id' => $order->id, 'date' => $schedule->date]),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]) ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>