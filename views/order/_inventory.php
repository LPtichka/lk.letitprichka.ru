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

        <?php foreach ($types as $key => $type): ?>
            <div class="row inventory-row">
                <div class="col-sm-3 ingestion-name"><?php echo $type;?></div>
                <div class="col-sm-9 ingestion-block">
                    <?php foreach ($dishes as $dish): ?>
                        <?php if ($dish->ingestion_type == $key): ?>
                            <div class="row ingestion-row">
                                <div class="col-sm-12 ingestion-content">
                                    <?php if($dish->dish_id):?>
                                        <p><?php echo Html::a($dish->dish->name, ['dish/view', 'id' => $dish->dish_id]);?></p>
                                        <p><?php echo implode(', ', $dish->dish->getComposition()) . ', ' . $dish->dish->weight . 'г.';?></p>
                                    <?php else:?>
                                        <?php echo \Yii::t('order', 'Not equipped');?>
                                    <?php endif;?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach;?>
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

    <div class="row modal-buttons">
        <div class="col-md-12">
            <div class="form-group text-right">
                <?= Html::a(
                    '<span>' . \Yii::t('app', 'Close') . '</span>',
                    '#',
                    [
                        'class'        => 'btn btn-sm btn-default',
                        'data-dismiss' => 'modal'
                    ]
                ) ?>
            </div>
        </div>
    </div>
</div>