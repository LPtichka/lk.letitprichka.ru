<?php

use app\widgets\Html;

/* @var $this yii\web\View */
/* @var $discount app\models\Repository\SubscriptionDiscount */
/* @var $i integer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="discount-row" id="group-discount-<?= $i ?>">
    <div class="row">
        <div class="col-sm-1 col-print-1">
            <span class="counter"><?= $i + 1 ?></span>
        </div>
        <div class="col-sm-4 col-p-rint-4">
            <?= Html::activeInput('text', $discount, "[$i]count", ['class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-4 col-print-4">
            <?= Html::activeInput('text', $discount, "[$i]price", ['class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-3 col-print-3">
            <?= Html::activeInput('hidden', $discount, "[$i]id") ?>
            <button
                    class="btn btn-sm btn-default delete-row-action pull-right"
                    type="button"
                    data-row="discount-row"
            >
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </div>
</div>
