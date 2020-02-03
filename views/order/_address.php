<?php

use app\widgets\Html;

/* @var $address \app\models\Repository\Address */
?>

<div class="collapse" id="collapse-address">
    <div class="col-sm-12">
        <div class="form-group">
            <label><?= \Yii::t('order', 'Full address'); ?></label>
            <?= Html::activeInput('text', $address, "full_address", [
                'class' => 'form-control input-sm',
                'id'    => 'full_address',
            ]) ?>
        </div>

    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label><?= \Yii::t('order', 'City'); ?></label>
            <?= Html::activeInput('text', $address, "city", ['class' => 'form-control input-sm']) ?>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label><?= \Yii::t('order', 'Street'); ?></label>
            <?= Html::activeInput('text', $address, "street", ['class' => 'form-control input-sm']) ?>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label><?= \Yii::t('order', 'House'); ?></label>
            <?= Html::activeInput('text', $address, "house", ['class' => 'form-control input-sm']) ?>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <label><?= \Yii::t('order', 'Housing'); ?></label>
            <?= Html::activeInput('text', $address, "housing", ['class' => 'form-control input-sm']) ?>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <label><?= \Yii::t('order', 'Building'); ?></label>
            <?= Html::activeInput('text', $address, "building", ['class' => 'form-control input-sm']) ?>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <label><?= \Yii::t('order', 'Flat'); ?></label>
            <?= Html::activeInput('text', $address, "flat", ['class' => 'form-control input-sm']) ?>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <label><?= \Yii::t('order', 'Postcode'); ?></label>
            <?= Html::activeInput('text', $address, "postcode", ['class' => 'form-control input-sm']) ?>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
            <label><?= \Yii::t('order', 'Comment'); ?></label>
            <?= Html::activeTextarea($address, "description", [
                'class' => 'form-control input-sm',
                'rows'  => 3,
            ]) ?>
        </div>
    </div>
</div>

