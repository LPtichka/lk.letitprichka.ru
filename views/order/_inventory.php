<?php

use app\widgets\Html;

/* @var $dishes \app\models\Repository\OrderScheduleDish[] */
/* @var $isSubscription bool */
/* @var $types array */
/* @var $scheduleId int */

$this->title = \Yii::t('order', 'Order inventory');

?>

<div class="inventory-row">
    <title><?= Html::encode($this->title) ?></title>
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($isSubscription): ?>

        <?php foreach ($types as $key => $type): ?>
            <div class="row inventory-row">
                <div class="col-sm-3 ingestion-name"><?php echo $type; ?></div>
                <div class="col-sm-9 ingestion-block">
                    <?php echo Html::a('+', '#', [
                        'class'            => 'request-dish-to-inventory',
                        'data-ration'      => $key,
                        'data-schedule-id' => $scheduleId,
                    ]); ?>
                    <?php foreach ($dishes as $dish): ?>
                        <?php if ($dish->ingestion_type == $key): ?>
                            <div class="row ingestion-row">
                                <?php if ($dish->dish_id): ?>
                                    <?php if ($dish->with_garnish): ?>
                                        <div class="col-sm-5 ingestion-content">
                                            <p>
                                                <?php echo Html::a($dish->dish->name, ['dish/view', 'id' => $dish->dish_id]); ?>
                                                &nbsp;<a href="#"
                                                   class="reload-dish"
                                                   data-ration="<?= $key; ?>"
                                                   data-dish-id="<?= $dish->dish_id; ?>"
                                                   data-schedule-id="<?= $scheduleId; ?>"
                                                ><i class="material-icons">cached</i></a><a href="#"
                                                   class="delete-dish"
                                                   data-ration="<?= $key; ?>"
                                                   data-dish-id="<?= $dish->dish_id; ?>"
                                                   data-schedule-id="<?= $scheduleId; ?>"
                                                ><i class="material-icons">delete</i></a>
                                            </p>
                                            <p><?php echo implode(', ', $dish->dish->getComposition()) . ', ' . $dish->dish->weight . 'г.'; ?></p>
                                        </div>
                                        <div class="col-sm-5 ingestion-content">
                                            <?php if ($dish->garnish_id): ?>
                                                <p>
                                                    <?php echo Html::a($dish->garnish->name, ['dish/view', 'id' => $dish->garnish_id]); ?>
                                                    &nbsp;<a href="#"
                                                       class="reload-dish"
                                                       data-ration="<?= $key; ?>"
                                                       data-dish-id="<?= $dish->garnish_id; ?>"
                                                       data-schedule-id="<?= $scheduleId; ?>"
                                                    ><i class="material-icons">cached</i></a><a href="#"
                                                       class="delete-dish"
                                                       data-ration="<?= $key; ?>"
                                                       data-dish-id="<?= $dish->garnish_id; ?>"
                                                       data-schedule-id="<?= $scheduleId; ?>"
                                                    ><i class="material-icons">delete</i></a>
                                                </p>
                                                <p><?php echo implode(', ', $dish->garnish->getComposition()) . ', ' . $dish->garnish->weight . 'г.'; ?></p>
                                            <?php else: ?>
                                                <p>Еще не назначено</p>
                                                <a href="#"
                                                   class="reload-dish"
                                                   data-ration="<?= $key; ?>"
                                                   data-dish-id="<?= $dish->dish_id; ?>"
                                                   data-schedule-id="<?= $scheduleId; ?>"
                                                ><i class="material-icons">cached</i></a>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-sm-10 ingestion-content">
                                            <p>
                                                <?php echo Html::a($dish->dish->name, ['dish/view', 'id' => $dish->dish_id]); ?>
                                                &nbsp;<a href="#"
                                                   class="reload-dish"
                                                   data-ration="<?= $key; ?>"
                                                   data-dish-id="<?= $dish->dish_id; ?>"
                                                   data-schedule-id="<?= $scheduleId; ?>"
                                                ><i class="material-icons">cached</i></a><a href="#"
                                                   class="delete-dish"
                                                   data-ration="<?= $key; ?>"
                                                   data-dish-id="<?= $dish->dish_id; ?>"
                                                   data-schedule-id="<?= $scheduleId; ?>"
                                                ><i class="material-icons">delete</i></a>
                                            </p>
                                            <p><?php echo implode(', ', $dish->dish->getComposition()) . ', ' . $dish->dish->weight . 'г.'; ?></p>
                                        </div>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <div class="col-sm-10 ingestion-content">
                                        <?php echo \Yii::t('order', 'Not equipped'); ?>
                                        <a href="#"
                                           class="reload-dish"
                                           data-ration="<?= $key; ?>"
                                           data-dish-id="<?= $dish->dish_id; ?>"
                                           data-schedule-id="<?= $scheduleId; ?>"
                                        ><i class="material-icons">cached</i></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="row">
            <div class="col-sm-4"><label><?= \Yii::t('dish', 'Name'); ?></label></div>
            <div class="col-sm-2"><label><?= \Yii::t('dish', 'Quantity'); ?></label></div>
            <div class="col-sm-2"><label><?= \Yii::t('dish', 'Price'); ?></label></div>
            <div class="col-sm-2"><label><?= \Yii::t('dish', 'Total'); ?></label></div>
        </div>
        <hr/>
        <?php foreach ($dishes as $dish): ?>
            <div class="row">
                <div class="col-sm-4"><?= $dish->name ?? $dish->dish->name; ?></div>
                <div class="col-sm-2"><?= $dish->count; ?> шт.</div>
                <div class="col-sm-2"><?= \Yii::$app->formatter->asCurrency($dish->price ?? 0, 'RUB'); ?></div>
                <div class="col-sm-2"><?= \Yii::$app->formatter->asCurrency($dish->price * $dish->count ?? 0, 'RUB'); ?></div>
            </div>
            <hr/>
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