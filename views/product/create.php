<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var \app\models\search\PaymentType $searchModel */

$this->title = $title

?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border"></div>
            <div class="box-body">
                <?php $form = ActiveForm::begin(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'name') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'count') ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'weight') ?>
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
                            <?= Html::a(\Yii::t('app', 'Cancel'), ['product/index'], ['class' => 'btn btm-sm btn-default']) ?>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>


