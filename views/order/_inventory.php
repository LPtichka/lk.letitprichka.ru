<?php

use app\widgets\Html;

/* @var $dishes \app\models\Repository\OrderScheduleDish[] */
/* @var $isSubscription bool */
/* @var $types array */

$this->title = \Yii::t('order', 'Order inventory');

?>

<div class="inventory-row">
    <title><?= Html::encode($this->title) ?></title>
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($isSubscription): ?>
        <div class="row">
            <?php foreach ($types as $key => $type): ?>
                <div class="col-sm-3">
                    <span class="text-capitalize"><strong><?= $type; ?></strong></span>
                </div>
            <?php endforeach; ?>
        </div>
        <hr/>
        <div class="row">
            <?php foreach ($types as $key => $type): ?>
                <div class="col-sm-3">
                    <?php foreach ($dishes as $dish): ?>
                        <?php if ($dish->ingestion_type == $key): ?>
                            <div><?= $dish->dish_id
                                    ? Html::a($dish->dish->name, ['dish/view', 'id' => $dish->dish_id])
                                    : \Yii::t('order', 'Not equipped'); ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-sm-4"><?= \Yii::t('order', 'Name');?></div>
            <div class="col-sm-2"><?= \Yii::t('order', 'Quantity');?></div>
            <div class="col-sm-2"><?= \Yii::t('order', 'Price');?></div>
            <div class="col-sm-2"><?= \Yii::t('order', 'Total');?></div>
        </div>
        <hr/>
        <?php foreach ($dishes as $dish): ?>
            <div class="row">
                <div class="col-sm-4"><?= $dish->name ?? $dish->dish->name; ?></div>
                <div class="col-sm-2"><?= $dish->count; ?></div>
                <div class="col-sm-2"><?= $dish->price; ?></div>
                <div class="col-sm-2"><?= $dish->price * $dish->count; ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <br />
    <br />
</div>