<?php

/* @var $order \app\models\Repository\Order */
/* @var $dates array */

use kartik\date\DatePicker;

?>

<div>
    <title><?php echo \Yii::t('order', 'Defer the implementation of the order'); ?></title>
    <h1><?php echo \Yii::t('order', 'Defer the implementation of the order'); ?></h1>

    <p><?php echo \Yii::t('order', 'Select from which date you want to transfer the order to which date. This means that the order will begin to be executed from a new date.'); ?></p>
    <form class="row">
        <div class="col-sm-4 select-block no-label">
            <div class="form-group">
                <?php echo \yii\helpers\Html::dropDownList('dateFrom', '', $dates, [
                    'class' => 'form-control input-sm'
                ]); ?>
            </div>
        </div>
        <div class="col-sm-4 date-input-wrapper">
            <?php echo DatePicker::widget([
                'name'          => 'dateTo',
                'value'         => date('d.m.Y', time() + 86400),
                'removeButton'  => false,
                'options' => [
                    'id'            => 'modal-date-picker',
                    'class'         => 'form-control input-sm',
                ],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format'    => 'dd.mm.yyyy',
                    'startDate' => date('d.m.Y', time() + 86400),
                ]
            ]); ?>
        </div>
    </form>
</div>
