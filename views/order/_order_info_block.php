<?php

use app\models\Repository\Order;

/** @var Order $model */
?>
<p>
    <?= \Yii::t('order', 'Order create date'); ?>
    <span><?= $model->created_at ? date('d.m.Y \в H:i', $model->created_at) : '---'; ?></span>
</p>
<p>
    <?= \Yii::t('order', 'Order status'); ?>
    <span><?= $model->getStatusName() ?? '---'; ?></span>
</p>
<p><?= \Yii::t('order', 'Payment type'); ?>
    <span><?= $model->payment->name ?? '---'; ?></span>
</p>

<p><?= \Yii::t('order', 'Order subscription'); ?>
    <span><?= $model->getOrderSubscription() ?? '---'; ?></span></p>
<p><?= \Yii::t('order', 'Without soup'); ?>
    <span><?= $model->without_soup !== null ? ($model->without_soup ? 'Да' : 'Нет') : '---'; ?></span></p>
<p><?= \Yii::t('order', 'Cutlery'); ?>
    <span><?= $model->id ? ($model->cutlery ? 'Да' : 'Нет') : '---'; ?></span></p>
<p><?= \Yii::t('order', 'Individual menu'); ?>
    <span><?= $model->id ? ($model->individual_menu ? 'Да' : 'Нет') : '---'; ?></span></p>
<?php
if ($model->comment): ?>
    <p><?= \Yii::t('order', 'Comment'); ?> <span><?= $model->comment; ?></span></p>
<?php
endif; ?>
<p><?= \Yii::t('order', 'Order subscription dates'); ?>
    <span><?= $model->getSubscriptionDates() ?? '---'; ?></span></p>
<p><?= \Yii::t('order', 'Order total'); ?>
    <span><?= \Yii::$app->formatter->asCurrency($model->total ?? 0, 'RUB'); ?></span></p>