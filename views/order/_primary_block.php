<?php

use app\models\Repository\Order;
use kartik\date\DatePicker;
use yii\helpers\Html;

/** @var Order $model */
/** @var array $subscriptions */

$subscription_id = Html::getInputId($model, "subscription_id");
?>

<div><?= \Yii::t('order', 'Order subscription'); ?>
    <span class="select-block">
        <div class="form-group">
            <?= Html::dropDownList(
                'subscription_id',
                $model->subscription_id,
                [
                    '' => \Yii::t('app', 'Choose'),
                ] + $subscriptions,
                [
                    'class' => 'form-control input-sm'
                ]
            ); ?>
        </div>
    </span>
</div>
<br/>
<div><?= \Yii::t('order', 'Cutlery'); ?>
    <span>
        <?= Html::checkbox(
            'cutlery',
            $model->cutlery,
            [
                'value' => 1
            ]
        );
        ?>
    </span>
</div>
<br/>
<div><?= \Yii::t('order', 'Individual menu'); ?>
    <span>
        <?= Html::checkbox(
            'individual_menu',
            $model->individual_menu,
            [
                'value' => 1
            ]
        );
        ?>
    </span>
</div>
<br/>
<div><?= \Yii::t('order', 'Without soup'); ?>
    <span>
        <?= Html::checkbox(
            'without_soup',
            $model->without_soup,
            [
                'value' => 1
            ]
        );
        ?>
    </span>
</div>
<br/>
<div><?= \Yii::t('order', 'Comment'); ?>
    <span>
    <?= Html::textInput(
        'comment',
        $model->comment,
        [
            'class' => 'form-control input-sm',
            'style' => 'width: 100%;'
        ]
    ); ?>
    </span>
</div>
<br/>
<div><?= \Yii::t('order', 'Order subscription dates'); ?>
    <span>
        <?= DatePicker::widget(
            [
                'name'          => 'scheduleFirstDate',
                'value'         => date('d.m.Y', strtotime($model->getScheduleFirstDate())),
                'options'       => [
                    'placeholder' => \Yii::t('order', 'Choose date'),
                    'class'       => 'form-control input-sm',
                ],
                'removeButton'  => false,
                'pluginOptions' => [
                    'autoclose' => true
                ]
            ]
        ); ?>
    </span>
</div>
<br/>
<div><?= \Yii::t('order', 'Schedule interval'); ?>
    <span>
        <?= Html::dropDownList(
            'scheduleInterval',
            $model->getScheduleInterval(),
            ['' => \Yii::t('app', 'Choose')] + $intervals,
            [
                'class' => 'subscription-schedule-interval-select form-control input-sm',
                'id'    => 'order-scheduleinterval',
            ]
        ); ?>
    </span>
</div>
<br/>
<div>
    <?= \Yii::t('order', 'Subscription count'); ?>
    <span class="select-block">
        <div class="form-group">
            <?= Html::dropDownList(
                'subscriptionCount',
                $model->count,
                ['' => \Yii::t('app', 'Choose')] + $subscriptionCounts,
                [
                    'class' => 'subscription-count-select form-control input-sm',
                    'id'    => 'order-subscriptioncount',
                ]
            ); ?>
        </div>
    </span>
</div>
<br/>
<div class="hidden" id="count-block-wrapper">
    <?= \Yii::t('order', 'Count'); ?>
    <span class="">
    <?= Html::textInput(
        'count',
        $model->count,
        [
            'class' => 'form-control input-sm',
            'style' => 'width: 100%;',
            'id' => 'order-count',
        ]
    ); ?>
    </span>
</div>
<br/>
<div>
    <span class="">
    <?= Html::textInput(
        'total',
        $model->total,
        [
            'class' => 'form-control input-sm',
            'style' => 'width: 100%;',
        ]
    ); ?>
    </span>
    <?= \Yii::t('order', 'Total<br /> cost'); ?>

</div>
<br/>
<div class="text-right">
    <a href="#" class="btn btn-default" id="cancel-primary-order-params"><?php echo \Yii::t('app', 'Cancel');?></a>
    <a href="#" class="btn btn-primary" id="save-primary-order-params"><?php echo \Yii::t('app', 'Save');?></a>
</div>