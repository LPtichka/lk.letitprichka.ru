<?php

use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var \app\models\Repository\Order $model */

$this->title = $title

?>

<div class="row">
    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h2 class="box-title"><?php echo \Yii::t('order', 'Customer block'); ?></h2>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-8">
                        <?= $form->field($model, 'customer_id')->widget(Select2::class, [
                            'data'          => $customers,
                            'showToggleAll' => false,
                            'hideSearch'    => false,
                            'options'       => ['placeholder' => 'Select status...'],
                            'pluginOptions' => [
                                'closeOnSelect' => true,
                                'allowClear'    => true
                            ],
                            'pluginEvents'  => [
                                "change" => "function() {
                                    window.getAddressBlock();
                                }",
                            ]
                        ]);
                        ?>
                    </div>
                    <div class="col-sm-4">
                        <div class="switch detailed-customer">
                            <?= $form
                                ->field($model, 'isNewCustomer')
                                ->widget(SwitchInput::class, [
                                    'pluginOptions' => [
                                        'size'    => 'mini',
                                        'onText'  => 'Да',
                                        'offText' => 'Нет',
                                    ],
                                    'inlineLabel'   => true,
                                    'labelOptions'  => ['style' => 'font-size: 12px'],
                                    'options'       => [
                                        'data-toggle'   => "collapse",
                                        'data-target'   => "#collapse-customer",
                                        'aria-expanded' => "false",
                                        'aria-controls' => "collapse-customer"
                                    ],
                                    'pluginEvents'  => [
                                        "switchChange.bootstrapSwitch" => "function() {
                                            $('#collapse-customer').collapse('toggle');
                                            if ($('#order-customer_id').attr('disabled')) {
                                                $('#order-customer_id').prop('disabled', false); 
                                            } else {
                                                $('#order-customer_id').prop('disabled', true); 
                                            }
                                        }"
                                    ]
                                ])->label(false); ?>
                            <label>Новый пользователь</label>
                        </div>
                    </div>
                </div>
                <div class="row collapse" id="collapse-customer">
                    <div class="col-sm-4">
                        <?= $form->field($model->customer, 'fio'); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->customer, 'phone'); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->customer, 'email'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h2 class="box-title"><?php echo \Yii::t('order', 'Address block'); ?></h2>
            </div>
            <div class="box-body" id="address-block">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label><?= \Yii::t('order', 'Choose address ID'); ?></label>
                            <?= Html::activeDropDownList($model, 'address_id', [
                                '' => \Yii::t('order', 'New address'),
                            ], [
                                'class' => 'form-control input-sm'
                            ]); ?>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="switch detailed-address">
                            <?= SwitchInput::widget([
                                'name'          => 'status_1',
                                'value'         => true,
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
                            ]);
                            ?>
                            <label>Адрес детально</label>
                        </div>
                    </div>
                </div>
                <?= $this->render('_address', [
                    'address' => $model->address,
                ]); ?>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h2 class="box-title"><?php echo \Yii::t('order', 'Menu block'); ?></h2>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'subscription_id')->dropDownList(
                            ['' => \Yii::t('app', 'Choose')] + $subscriptions
                        ) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'count')->dropDownList(
                            ['' => \Yii::t('app', 'Choose')] + $subscriptionCounts
                        ) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'cutlery') ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'scheduleFirstDate')->widget(DatePicker::class, [
                            'options'       => ['placeholder' => 'Enter birth date ...'],
                            'pluginOptions' => [
                                'autoclose' => true
                            ]
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h2 class="box-title"><?php echo \Yii::t('order', 'Payment block'); ?></h2>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'payment_type')->dropDownList($payments) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'cash_machine')->checkbox() ?>
                    </div>
                </div>
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
                    <?= Html::a(\Yii::t('app', 'Cancel'), ['order/index'], ['class' => 'btn btn-sm btn-default']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h2 class="box-title"><?php echo \Yii::t('order', 'Exceptions'); ?></h2>
            </div>
            <div class="box-body">
                <div class="exceptions">
                    <?php foreach ($model->exceptions as $i => $exception): ?>
                        <?= $this->render('_exception', [
                            'exceptions' => $exceptions,
                            'exception'  => $exception,
                            'i'          => $i,
                        ]); ?>
                    <?php endforeach; ?>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-sm-12 exception-buttons">
                        <a href="javascript:void(0)"
                           id="add-exception"
                           data-href="<?php echo Url::to(['order/add-exception']); ?>"
                           data-block="exceptions"
                           data-row="exception-row"
                           class="btn btn-sm btn-primary pull-right add-row-action"
                        >
                            <i class="fa fa-plus"></i>
                            <?= Yii::t('order', 'Add exception') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h2 class="box-title"><?php echo \Yii::t('order', 'Comment block'); ?></h2>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'comment')->textarea([
                            'rows' => 8
                        ])->label(false) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h2 class="box-title"><?php echo \Yii::t('order', 'Order info'); ?></h2>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>


