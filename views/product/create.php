<?php

use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var \app\models\Search\PaymentType $searchModel */

$this->title = $title
?>
<?php Pjax::begin([
    'id'              => 'product-form',
    'formSelector'    => '#product-form form',
    'enablePushState' => false,
]); ?>
<div class="row product-form">
    <?= Alert::widget(['options' => ['style' => 'margin-bottom:20px']]) ?>

    <title><?= Html::encode($this->title) ?></title>

    <div class="col-md-12">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($model, 'name')->textInput([
                    'class' => 'form-control input-sm',
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'count')->textInput([
                    'class' => 'form-control input-sm',
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'unit')->dropDownList(
                    \app\models\Helper\Unit::UNITS, [
                    'class' => 'form-control input-sm',
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($model, 'exception_id')->dropDownList(
                    (new \app\models\Helper\Arrays($exceptionList))->getSelectOptions(),
                    ['class' => 'form-control input-sm']
                ) ?>
            </div>
        </div>
        <div class="row modal-buttons">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::submitButton('<i class="material-icons">done</i> <span>' . \Yii::t('app', 'Save') . '</span>', ['class' => 'btn btn-sm btn-warning']) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group text-right">
                    <?= Html::a(
                        '<span>' . \Yii::t('app', 'Cancel') . '</span>',
                        '#',
                        ['class' => 'btn btn-sm btn-default', 'data-dismiss' => 'modal']
                    ) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php Pjax::end(); ?>

