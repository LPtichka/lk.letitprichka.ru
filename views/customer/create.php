<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/** @var \app\models\search\Customer $searchModel */
/** @var \app\models\Repository\Customer $model */

$this->title = \Yii::t('customer', 'Customer create');
?>
<div class="row">
    <div class="col-md-8">
        <?php $form = ActiveForm::begin(); ?>
        <div class="box box-primary">
            <div class="box-header with-border"></div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'fio') ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'email') ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'phone')->widget(
                            MaskedInput::class,
                            [
                                'mask'          => '+7 (999) 999-99-99',
                                'clientOptions' => ['onincomplete' => 'function(){$("#user-phone").removeAttr("value").attr("value","");}'],
                                'options'       => [
                                    'class'       => 'form-control',
                                    'placeholder' => '+7 (___) ___-__-__',
                                ]
                            ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h5><?php echo \Yii::t('customer', 'Addresses'); ?></h5>
            </div>
            <div class="box-body">
                <div class="addresses">
                    <div class="row">
                        <div class="col-sm-8 col-print-8">Полный адрес</div>
                        <div class="col-sm-3 col-print-3">Адрес по умолчанию</div>
                        <div class="col-sm-1 col-print-1"></div>
                    </div>
                    <?php foreach ($model->addresses as $i => $address) : ?>
                        <?= $this->render('_address', [
                            'address'          => $address,
                            'i'                => $i,
                            'defaultAddressId' => $model->default_address_id,
                        ]) ?>
                    <?php endforeach; ?>
                </div>
                <div class="row">
                    <div class="col-sm-12 product-buttons">
                        <a href="javascript:void(0)" id="add-address"
                           class="btn btn-sm btn-primary pull-right">
                            <i class="fa fa-plus"></i>
                            <?= Yii::t('customer', 'Add address') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::submitButton('<i class="fa fa-check"></i> ' . Yii::t('app', 'Save'), ['class' => 'btn btm-sm btn-warning']) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group text-right">
                    <?= Html::a(\Yii::t('app', 'Cancel'), ['customer/index'], ['class' => 'btn btm-sm btn-default']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>


