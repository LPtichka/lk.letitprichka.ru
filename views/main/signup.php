<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \app\models\forms\SignupForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Signup');
?>
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Admin</b>LTE</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <div class="signup-form">
            <p><?= Yii::t('app', 'Please fill out the following fields to signup:') ?></p>
            <?php
            $form = ActiveForm::begin([
                'id'                     => 'signup-form',
                'layout'                 => 'horizontal',
                'enableClientValidation' => false,
                'errorCssClass'          => false,
                'fieldConfig'            => [
                    'template'     => "<div class=\"col-sm-12\">{input}</div>\n",
                    'labelOptions' => ['class' => 'col-lg-3 control-label'],
                ],
            ]);
            ?>
            <?php
            if ($model->hasErrors()) {
                $errors = [];
                foreach ($model->errors as $errorsField) {
                    foreach ($errorsField as $error) {
                        $errors[] = $error;
                    }
                }
                echo Html::tag('div', implode('<br/>', $errors), ['class' => 'error-summary']);
            }
            ?>
            <?= $form->field($model, 'fio')->textInput(['autofocus' => true, 'placeholder' => $model->getAttributeLabel('fio')]) ?>
            <?= $form->field($model, 'email')->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

            <div class="form-group">
                <div class="col-sm-12">
                    <?= Html::submitButton(\Yii::t('app', 'Signup'), ['class' => 'btn btn-warning', 'name' => 'signup-button']) ?>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <a class="ml15 mt8"
                   href="<?= Url::to(['/main/login']) ?>"><?= \Yii::t('app', 'Alredy registered?') ?></a>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>