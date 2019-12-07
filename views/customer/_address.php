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
                    + \'<div><span>\' + item.value + \'</span></div>\'
                    + \'<div class="hidden">\' + item.address_id + \'</div></div></div>\')
                .appendTo(ul);
        };
    }');
$openExp   = new JsExpression('function() {}');

$sourceExp = new JsExpression('function(request, response) {
        $.ajax({
            url: "' . Url::to(['address/get-by-query']) . '",
            dataType: "json",
            data: {
                query: request.term, 
                element: $(this.element).attr("id").split("-")[2]
            },
            success: function(data) {
                response(data);
            }
        });
    }');

$fullAddress = Html::getInputId($address, "[$i]full_address");
$city        = Html::getInputId($address, "[$i]city");
$street      = Html::getInputId($address, "[$i]street");
$house       = Html::getInputId($address, "[$i]house");
$housing     = Html::getInputId($address, "[$i]housing");
$building    = Html::getInputId($address, "[$i]building");
$flat        = Html::getInputId($address, "[$i]flat");
$postcode    = Html::getInputId($address, "[$i]postcode");

$selectExp = new JsExpression('function(event, ui) {
    setTimeout(function() {
        $("#' . $fullAddress . '").val(ui.item.value);
        $("#' . $city . '").val(ui.item.data.cityWithType);
        $("#' . $street . '").val(ui.item.data.streetWithType);
        $("#' . $house . '").val(ui.item.data.house);
        $("#' . $housing . '").val(ui.item.data.block);
        $("#' . $flat . '").val(ui.item.data.flat);
        $("#' . $postcode . '").val(ui.item.data.postalCode);
    }, 50);
    window.addAddress();
}');
?>

<div class="address-row" id="group-<?= $i ?>">
    <div class="row">
        <div class="col-sm-4 col-print-4">
            <?= AutoComplete::widget([
                'model'         => $address,
                'attribute'     => "[$i]full_address",
                'options'       => ['class' => 'form-control input-sm address-input', 'autocomplete' => 'off'],
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
        <div class="col-sm-6">
            <?= Html::activeInput('text', $address, "[$i]description", [
                'class' => 'form-control input-sm address-input',
            ]) ?>
        </div>
        <div class="col-sm-1 col-print-1 text-center">
            <?= Html::activeInput('radio', $address, "is_default_address", [
                'class'        => 'form-control input-sm',
                'autocomplete' => false,
                'value'        => $i,
                'checked'      => $address->id === $defaultAddressId
            ]) ?>
        </div>
        <div class="col-sm-1 col-print-1">
            <?= Html::activeInput('hidden', $address, "[$i]id") ?>
            <?= Html::activeInput('hidden', $address, "[$i]city") ?>
            <?= Html::activeInput('hidden', $address, "[$i]street") ?>
            <?= Html::activeInput('hidden', $address, "[$i]house") ?>
            <?= Html::activeInput('hidden', $address, "[$i]housing") ?>
            <?= Html::activeInput('hidden', $address, "[$i]building") ?>
            <?= Html::activeInput('hidden', $address, "[$i]flat") ?>
            <?= Html::activeInput('hidden', $address, "[$i]postcode") ?>
            <button class="btn btn-sm btn-default delete-address pull-right" type="button"><i class="fa fa-trash"></i>
            </button>
        </div>

    </div>
</div>
