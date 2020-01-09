<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var \app\models\Repository\Exception $model */
/** @var string $title */

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
                        <?= $form->field($model, 'name') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= Html::submitButton('<i class="fa fa-check"></i> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-sm btn-warning']) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group text-right">
                            <?= Html::a(\Yii::t('app', 'Cancel'), ['franchise/index'], ['class' => 'btn btn-sm btn-default']) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>


