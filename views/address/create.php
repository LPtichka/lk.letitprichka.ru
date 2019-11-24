<?php

use kartik\switchinput\SwitchInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var \app\models\Repository\Address $model */

$this->title = \Yii::t('address', 'Address create');
?>

<div class="row">
    <div class="col-md-8">
        <?php $form = ActiveForm::begin(); ?>
        <div class="box box-primary">
            <div class="box-header with-border"></div>
            <div class="box-body">

                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'customer_id')->dropDownList($customers, [
                            'disabled' => $model->customer_id
                        ]) ?>
                    </div>
                    <div class="col-sm-8">
                        <?= $form->field($model, 'full_address')->textInput([
                            'id' => 'full_address'
                        ]) ?>
                    </div>
                    <div class="col-sm-4">
                        <div class="switch detailed-address">
                            <?= $form
                                ->field($model, 'address_detailed')
                                ->widget(SwitchInput::class, [
                                    'pluginOptions' => [
                                        'size'    => 'mini',
                                        'onText'  => 'Вкл',
                                        'offText' => 'Выкл',
                                    ],
                                    'inlineLabel'   => true,
                                    'labelOptions'  => ['style' => 'font-size: 12px'],
                                    'options'       => [
                                        'data-toggle'   => "collapse",
                                        'data-target'   => "#collapseExample",
                                        'aria-expanded' => "false",
                                        'aria-controls' => "collapseExample"
                                    ],
                                    'pluginEvents'  => [
                                        "switchChange.bootstrapSwitch" => "function() { $('#collapse-address').collapse('toggle'); }"
                                    ]
                                ])->label(false); ?>
                            <label>Адрес детально</label>
                        </div>
                    </div>
                </div>
                <div class="row collapse" id="collapse-address">
                    <div class="col-sm-4">
                        <?= $form->field($model, 'city') ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'street') ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'house') ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'housing') ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'building') ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'flat') ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'postcode') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'description')->textarea() ?>
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
                    <?= Html::a(\Yii::t('app', 'Cancel'), ['payment-type/index'], ['class' => 'btn btm-sm btn-default']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>


