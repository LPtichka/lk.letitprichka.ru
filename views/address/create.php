<?php

use app\widgets\Alert;
use kartik\switchinput\SwitchInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var \app\models\Repository\Address $model */

$this->title = \Yii::t('address', 'Address create');
?>

<?php Pjax::begin([
    'id'              => 'address-form',
    'formSelector'    => '#address-form form',
    'enablePushState' => false,
]); ?>
    <div class="row">
        <?= Alert::widget(['options' => ['style' => 'margin-bottom:20px']]) ?>

        <title><?= Html::encode($this->title) ?></title>
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="col-md-12">
            <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'customer_id')->dropDownList($customers, [
                        'disabled' => $model->customer_id,
                        'class'    => 'form-control input-sm',
                    ]) ?>
                </div>
                <div class="col-sm-8">
                    <?= $form->field($model, 'full_address')->textInput([
                        'id'    => 'full_address',
                        'class' => 'form-control input-sm',
                    ]) ?>
                </div>
                <div class="col-sm-4">
                    <div class="switch detailed-address">
                        <?= $form
                            ->field($model, 'address_detailed')
                            ->widget(SwitchInput::class, [
                                'pluginOptions' => [
                                    'size'    => 'mini',
                                    'onText'  => 'Вкл',
                                    'offText' => 'Выкл',
                                ],
                                'inlineLabel'   => true,
                                'labelOptions'  => ['style' => 'font-size: 12px'],
                                'options'       => [
                                    'data-toggle'   => "collapse",
                                    'data-target'   => "#collapseExample",
                                    'aria-expanded' => "false",
                                    'aria-controls' => "collapseExample"
                                ],
                                'pluginEvents'  => [
                                    "switchChange.bootstrapSwitch" => "function() { $('#collapse-address').collapse('toggle'); }"
                                ]
                            ])->label(false); ?>
                        <label>Адрес детально</label>
                    </div>
                </div>
            </div>
            <div class="row collapse" id="collapse-address">
                <div class="col-sm-4">
                    <?= $form->field($model, 'city')->textInput([
                        'class' => 'form-control input-sm',
                    ]) ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'street')->textInput([
                        'class' => 'form-control input-sm',
                    ]) ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'house')->textInput([
                        'class' => 'form-control input-sm',
                    ]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'housing')->textInput([
                        'class' => 'form-control input-sm',
                    ]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'building')->textInput([
                        'class' => 'form-control input-sm',
                    ]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'flat')->textInput([
                        'class' => 'form-control input-sm',
                    ]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'postcode')->textInput([
                        'class' => 'form-control input-sm',
                    ]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'description')->textarea([
                        'class' => 'form-control input-sm',
                    ]) ?>
                </div>
            </div>
            <div class="row modal-buttons">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= Html::submitButton('<i class="fa fa-check"></i> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-sm btn-warning']) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group text-right">
                        <?= Html::a(\Yii::t('app', 'Cancel'), ['address/index'], ['class' => 'btn btn-sm btn-default']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php Pjax::end(); ?>
<?php
\Yii::$app->view->registerJs(<<<JS
    $(document).ready(function () {
        // Автоподстановка адреса
        new autoComplete({
            selector: '#full_address',
            source: function (term, response) {
                try {
                    xhr.abort();
                } catch (e) {
                }
                xhr = $.getJSON('/address/get-by-query', {query: term}, function (data) {
                    response(data);
                });
            },
            renderItem: function (item, search) {
                search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                let re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
    
                return '<div class="autocomplete-suggestion" ' +
                    ' data-region="' + item['data']['regionWithType'] + '"' +
                    ' data-city="' + item['data']['cityWithType'] + '"' +
                    ' data-street="' + item['data']['streetWithType'] + '"' +
                    ' data-house="' + (item['data']['house'] || '') + '"' +
                    ' data-flat="' + (item['data']['flat'] || '') + '"' +
                    ' data-housing="' + (item['data']['block'] || '') + '"' +
                    ' data-postcode="' + (item['data']['postalCode'] || '') + '"' +
                    ' data-val="' + item['value'] + '">' + item['value'].replace(re, "<b>$1</b>") + '</div>';
            },
            onSelect: function (e, term, item) {
                let cityInput = $('[name="Address[city]"]');
                let streetInput = $('[name="Address[street]"]');
    
                cityInput.val(item.getAttribute('data-city'));
                streetInput.val(item.getAttribute('data-street'));
    
                $('[name="Address[region]"]').val(item.getAttribute('data-region'));
                $('[name="Address[house]"]').val(item.getAttribute('data-house'));
                $('[name="Address[flat]"]').val(item.getAttribute('data-flat'));
                $('[name="Address[housing]"]').val(item.getAttribute('data-housing'));
                $('[name="Address[postcode]"]').val(item.getAttribute('data-postcode'));
            }
        });
    });
JS
);

