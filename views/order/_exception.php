<?php

use app\widgets\Html;

/* @var $this yii\web\View */
/* @var $exception app\models\Repository\Exception */
/* @var $i integer */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="exception-row" id="group-exc-<?= $i ?>">
    <div class="row">
        <div class="col-sm-1 col-print-1 text-center">
            <span class="counter"><?= $i + 1 ?></span>
        </div>
        <div class="col-sm-<?php echo $disabled ? '11': '9';?>">
            <?= Html::activeDropDownList(
                $exception,
                "[$i]id",
                ['' => \Yii::t('app', 'Choose')] + $exceptions,
                [
                    'class'    => 'form-control input-sm',
                    'disabled' => $disabled,
                ]
            ) ?>
        </div>
        <?php if (!$disabled): ?>
            <div class="col-sm-2 col-print-2">
                <button
                        class="btn btn-sm btn-default delete-row-action pull-right"
                        type="button"
                        data-row="exception-row"
                >
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>
