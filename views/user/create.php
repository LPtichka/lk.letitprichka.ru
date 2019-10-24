<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = $title;
?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border"></div>
            <div class="box-body">
                <?php $form = ActiveForm::begin(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'fio') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'email') ?>
                    </div>
                    <div class="col-md-6">
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
                <div class="row">
                    <?php if ($canBlockUser):?>
                    <div class="col-md-6">
                        <?= $form->field($model, 'status')->dropDownList($model->getStatuses()) ?>
                    </div>
                    <?php endif;?>
                    <?php if ($canGrantPrivileges):?>
                    <div class="col-md-6">
                        <?= $form->field($model, 'role')->dropDownList($model->getAllowedRoles()) ?>
                    </div>
                    <?php endif;?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= Html::submitButton('<i class="fa fa-check"></i> ' . Yii::t('app', 'Save'), ['class' => 'btn btm-sm btn-warning']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group text-right">
                            <?= Html::a(\Yii::t('app', 'Cancel'), ['product/index'], ['class' => 'btn btm-sm btn-default']) ?>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>


