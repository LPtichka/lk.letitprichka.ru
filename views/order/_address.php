<?php

use app\widgets\Html;

/* @var $address \app\models\Repository\Address */
/* @var $order \app\models\Repository\Order */
$errors = $order->getErrorMessages('address');
?>

<div class="collapse" id="collapse-address">
    <div class="col-sm-12">
        <div class="form-group <?php if (!empty($errors) && !empty($errors['full_address'])): ?> has-error<?php endif;?>">
            <label><?= \Yii::t('address', 'Full address'); ?></label>
            <?= Html::activeInput('text', $address, "full_address", [
                'class' => 'form-control input-sm',
                'id'    => 'full_address',
            ]) ?>
        </div>

    </div>
    <div class="col-sm-2">
        <div class="form-group <?php if (!empty($errors) && !empty($errors['city'])): ?> has-error<?php endif;?>">
            <label><?= \Yii::t('address', 'City'); ?></label>
            <?= Html::activeInput('text', $address, "city", ['class' => 'form-control input-sm']) ?>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group <?php if (!empty($errors) && !empty($errors['street'])): ?> has-error<?php endif;?>">
            <label><?= \Yii::t('address', 'Street'); ?></label>
            <?= Html::activeInput('text', $address, "street", ['class' => 'form-control input-sm']) ?>
        </div>
    </div>
    <div class="col-sm-7">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group <?php if (!empty($errors) && !empty($errors['house'])): ?> has-error<?php endif;?>">
                    <label><?= \Yii::t('address', 'House'); ?></label>
                    <?= Html::activeInput('text', $address, "house", ['class' => 'form-control input-sm']) ?>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group <?php if (!empty($errors) && !empty($errors['flat'])): ?> has-error<?php endif;?>">
                    <label><?= \Yii::t('address', 'Flat'); ?></label>
                    <?= Html::activeInput('text', $address, "flat", ['class' => 'form-control input-sm']) ?>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group <?php if (!empty($errors) && !empty($errors['floor'])): ?> has-error<?php endif;?>">
                    <label><?= \Yii::t('address', 'Floor'); ?></label>
                    <?= Html::activeInput('text', $address, "floor", ['class' => 'form-control input-sm']) ?>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group <?php if (!empty($errors) && !empty($errors['porch'])): ?> has-error<?php endif;?>">
                    <label><?= \Yii::t('address', 'Porch'); ?></label>
                    <?= Html::activeInput('text', $address, "porch", ['class' => 'form-control input-sm']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <label><?= \Yii::t('address', 'Comment'); ?></label>
            <?= Html::activeTextarea($address, "description", [
                'class' => 'form-control input-sm',
                'rows'  => 3,
            ]) ?>
        </div>
    </div>
</div>

