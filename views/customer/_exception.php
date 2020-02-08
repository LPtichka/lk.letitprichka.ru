<?php

use app\widgets\Html;

/* @var $this yii\web\View */
/* @var $exception app\models\Repository\Exception */
/* @var $i integer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="exception-row" id="group-exc-<?= $i ?>">
    <div class="row">
        <div class="col-sm-1 text-center">
            <span class="counter"><?= $i + 1 ?></span>
        </div>
        <div class="col-sm-8">
            <?= Html::activeDropDownList(
                $exception,
                "[$i]id",
                ['' => \Yii::t('app', 'Choose')] + $exceptions,
                ['class' => 'form-control input-sm']
            ) ?>
        </div>
        <div class="col-sm-2" style="padding-right: 0">
            <button
                    class="btn btn-sm btn-default delete-row-action pull-right"
                    type="button"
                    data-row="exception-row"
            >
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </div>
</div>
