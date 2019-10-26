<?php

use app\widgets\AutoComplete;
use app\widgets\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $product app\models\Repository\DishProduct */
/* @var $disabledEdit boolean */
/* @var $i integer */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$createExp = new JsExpression('function () {
        $(this).data("ui-autocomplete")._renderItem = function (ul, item) {
            var ul_item = $("<li>");
            return ul_item
                .append(\'<div>\'
                    + \'<div><span>\' + item.name + \'</span></div>\'
                    + \'<div class="hidden">\' + item.product_id + \'</div></div></div>\')
                .appendTo(ul);
        };
    }');
$openExp   = new JsExpression('function() {}');

$sourceExp = new JsExpression('function(request, response) {
        $.ajax({
            url: "' . Url::to(['product/search']) . '",
            dataType: "json",
            data: {
                term: request.term, 
                element: $(this.element).attr("id").split("-")[2]
            },
            success: function(data) {
                response(data);
            }
        });
    }');

$nameId    = Html::getInputId($product, "[$i]name");
$productId = Html::getInputId($product, "[$i]product_id");

$selectExp = new JsExpression('function(event, ui) {
            $("#' . $productId . '").val(ui.item.product_id);
            setTimeout(function() {
                $("#' . $nameId . '").val(ui.item.name);
            }, 50);
            window.addProduct();
    }');
?>

<div class="product-row" id="group-<?= $i ?>">
    <div class="row">
        <div class="col-sm-6 col-print-6">
            <label><?= \Yii::t('product', 'Name');?></label>
            <?= AutoComplete::widget([
                'model'         => $product,
                'attribute'     => "[$i]name",
                'options'       => ['class' => 'form-control input-sm', 'autocomplete' => 'off'],
                'clientOptions' => [
                    'source'   => $sourceExp,
                    'appendTo' => "#group-$i",
                    'create'   => $createExp,
                    'open'     => $openExp,
                    'select'   => $selectExp,
                    'position' => ['in' => "#group-$i"],
                ],
            ]) ?>
        </div>
        <div class="col-sm-2 col-print-2">
            <label><?= \Yii::t('product', 'Weight');?></label>
            <?= Html::activeInput('text', $product, "[$i]weight", ['class' => 'form-control input-sm', 'autocomplete' => false]) ?>
        </div>
        <div class="col-sm-2 col-print-2">
            <label><?= \Yii::t('product', 'Brutto');?></label>
            <?= Html::activeInput('text', $product, "[$i]brutto", ['class' => 'form-control input-sm', 'autocomplete' => false]) ?>
        </div>
        <div class="col-sm-2 col-print-2">
            <label><?= \Yii::t('product', 'Netto');?></label>
            <?= Html::activeInput('text', $product, "[$i]netto", ['class' => 'form-control input-sm', 'autocomplete' => false]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2 col-print-2">
            <label><?= \Yii::t('product', 'Kkal');?></label>
            <?= Html::activeInput('text', $product, "[$i]kkal", ['class' => 'form-control input-sm', 'autocomplete' => false]) ?>
        </div>
        <div class="col-sm-2 col-print-2">
            <label><?= \Yii::t('product', 'Proteins');?></label>
            <?= Html::activeInput('text', $product, "[$i]proteins", ['class' => 'form-control input-sm', 'autocomplete' => false]) ?>
        </div>
        <div class="col-sm-2 col-print-2">
            <label><?= \Yii::t('product', 'Fat');?></label>
            <?= Html::activeInput('text', $product, "[$i]fat", ['class' => 'form-control input-sm', 'autocomplete' => false]) ?>
        </div>
        <div class="col-sm-2 col-print-2">
            <label><?= \Yii::t('product', 'Carbohydrates');?></label>
            <?= Html::activeInput('text', $product, "[$i]carbohydrates", ['class' => 'form-control input-sm', 'autocomplete' => false]) ?>
        </div>
        <div class="col-sm-2 col-print-2">
            <label><?= \Yii::t('product', 'Count');?></label>
            <?= Html::activeInput('text', $product, "[$i]count", ['class' => 'form-control input-sm', 'autocomplete' => false]) ?>
        </div>
        <div class="col-sm-2 col-print-2">
            <?= Html::activeInput('hidden', $product, "[$i]product_id") ?>
            <button class="btn btn-sm btn-default delete-product pull-right" type="button"><i class="fa fa-trash"></i></button>
        </div>
    </div>
</div>
