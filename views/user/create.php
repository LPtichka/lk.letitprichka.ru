<?php

use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\widgets\Pjax;

$this->title = $title;
?>
<?php Pjax::begin([
    'id'              => 'user-form',
    'formSelector'    => '#user-form form',
    'enablePushState' => false,
]); ?>
<div class="row user-form">
    <?= Alert::widget(['options' => ['style' => 'margin-bottom:20px']]) ?>

    <title><?= Html::encode($this->title) ?></title>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <div class="col-sm-12">
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
            <?php if ($canBlockUser): ?>
                <div class="col-md-6">
                    <?= $form->field($model, 'status')->dropDownList($model->getStatuses()) ?>
                </div>
            <?php endif; ?>
            <?php if ($canGrantPrivileges): ?>
                <div class="col-md-6">
                    <?= $form->field($model, 'role')->dropDownList($model->getAllowedRoles()) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="row modal-buttons">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::submitButton('<i class="material-icons">done</i> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-sm btn-warning']) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group text-right">
                    <?= Html::a(\Yii::t('app', 'Cancel'), '#', [
                        'class'        => 'btn btn-sm btn-default',
                        'data-dismiss' => 'modal'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php Pjax::end(); ?>

