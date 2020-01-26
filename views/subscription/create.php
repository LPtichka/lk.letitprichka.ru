<?php

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var \app\models\Repository\Address $model */

$this->title = $title;
?>
<?php Pjax::begin([
    'id'              => 'subscription-form',
    'formSelector'    => '#subscription-form form',
    'enablePushState' => false,
]); ?>
<div class="row">
    <?= Alert::widget(['options' => ['style' => 'margin-bottom:20px']]) ?>

    <title><?= Html::encode($this->title) ?></title>
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="col-md-12">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-sm-8">
                <?= $form->field($model, 'name')->textInput([
                    'class' => 'form-control input-sm'
                ]) ?>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'price')->textInput([
                    'class' => 'form-control input-sm'
                ]) ?>
            </div>
        </div>
        <div class="row checkbox-list">
            <div class="col-sm-2">
                <?= $form->field($model, 'has_breakfast')->checkbox() ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'has_dinner')->checkbox() ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'has_lunch')->checkbox() ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'has_supper')->checkbox() ?>
            </div>
        </div>
        <div>
            <hr />
            <div class="row">
                <div class="col-sm-1"><label>#</label></div>
                <div class="col-sm-4"><label><?php echo \Yii::t('subscription', 'Count'); ?></label></div>
                <div class="col-sm-4"><label><?php echo \Yii::t('subscription', 'Discount price'); ?></label></div>
                <div class="col-sm-3"></div>
            </div>
            <hr class="devider" />
            <div class="discounts">
                <?php foreach ($model->discounts as $i => $discount) : ?>
                    <?= $this->render('_discount', [
                        'discount' => $discount,
                        'i'        => $i,
                    ]) ?>
                <?php endforeach; ?>
            </div>
            <hr/>
            <div class="row">
                <div class="col-sm-12 discount-buttons">

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::submitButton('<i class="fa fa-check"></i> ' . \Yii::t('app', 'Save'), ['class' => 'btn btn-sm btn-warning']) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group text-right">
                    <a href="javascript:void(0)"
                       id="add-discount"
                       data-href="<?php echo Url::to(['subscription/add-discount']); ?>"
                       data-block="discounts"
                       data-row="discount-row"
                       class="btn btn-sm btn-primary add-row-action"
                    >
                        <i class="fa fa-plus"></i>
                        <?= Yii::t('subscription', 'Add discount') ?>
                    </a>
                    <?= Html::a(\Yii::t('app', 'Cancel'), ['subscription/index'], ['class' => 'btn btn-sm btn-default']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php Pjax::end(); ?>

