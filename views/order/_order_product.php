<?php

use app\widgets\AutoComplete;
use app\widgets\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $dish app\models\Repository\OrderScheduleDish */
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
            url: "' . Url::to(['dish/search']) . '",
            dataType: "json",
            data: {
                term: request.term, 
                element: $(this.element).attr("id").split("-")[2]
            },
            success: function(data) {
            console.log(data);
                response(data);
            }
        });
    }');

$nameId    = Html::getInputId($dish, "[$i]name");
$productId = Html::getInputId($dish, "[$i]dish_id");

$selectExp = new JsExpression('function(event, ui) {
            $("#' . $productId . '").val(ui.item.product_id);
            setTimeout(function() {
                $("#' . $nameId . '").val(ui.item.name);
            }, 50);
            window.addDish();
    }');
?>

<div class="product-row" id="group-<?= $i ?>">
    <div class="row">
        <div class="col-sm-6">
            <?= AutoComplete::widget([
                'model'         => $dish,
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
        <div class="col-sm-2">
            <?= Html::activeInput('text', $dish, "[$i]count", ['class' => 'form-control input-sm', 'autocomplete' => false]) ?>
        </div>
        <div class="col-sm-2">
            <?= Html::activeInput('text', $dish, "[$i]price", ['class' => 'form-control input-sm', 'autocomplete' => false]) ?>
        </div>
        <div class="col-sm-2">
            <?= Html::activeInput('hidden', $dish, "[$i]dish_id") ?>
            <button class="btn btn-sm btn-default delete-product pull-right" type="button"><i class="fa fa-trash"></i></button>
        </div>
    </div>
</div>
