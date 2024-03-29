<?php

use app\widgets\Html;

/* @var $this yii\web\View */
/* @var $exception app\models\Repository\OrderException */
/* @var $i integer */
/* @var $form yii\widgets\ActiveForm */
/* @var $disabled bool */
/* @var $exceptions array */

?>

<div class="exception-row" id="group-exc-<?= $i ?>">
    <div class="row">
        <div class="col-sm-10 select-block no-label">
            <div class="form-group">
                <?= Html::activeDropDownList(
                    $exception,
                    "[$i]exception_id",
                    ['' => \Yii::t('app', 'Choose')] + $exceptions,
                    [
                        'class'             => 'form-control input-sm',
                    ]
                ) ?>
            </div>

            <div class="comment-exception form-group <?php if (!empty($exception) && (!$exception->exception || !$exception->exception->with_comment)): ?> hidden <?php endif; ?>">
                <?= Html::textarea(
                    "OrderException[$i][comment]",
                    $exception->comment ?? '',
                    [
                        'class'    => 'form-control input-sm',
                        'disabled' => $disabled,
                    ]
                ) ?>
            </div>

        </div>

            <div class="col-sm-2 col-print-2">
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
