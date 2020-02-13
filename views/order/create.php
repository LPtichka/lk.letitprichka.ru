<?php

use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/** @var \app\models\Repository\Order $model */

$this->title = $title;

$this->params['breadcrumbs'][] = ['label' => \Yii::t('order', 'Orders'), 'url' => Url::to(['order/index'])];
if ($model->id) {
    $this->params['breadcrumbs'][] = \Yii::t('order', 'Order <span class="num">№ {id}</span>', ['id' => $model->id]);
} else {
    $this->params['breadcrumbs'][] = \Yii::t('order', 'New Order');
}
?>

<?= $this->render('_buttons', [
    'order' => $model,
]); ?>
<div class="row" id="order-container" data-order-id="<?php echo $model->id; ?>">
    <?php $form = ActiveForm::begin(); ?>
    <div class="col-sm-12">

        <div class="row">
            <div class="col-sm-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h2 class="box-title"><?php echo \Yii::t('order', 'Base info'); ?></h2>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <?= $form->field($model, 'customer_id')->widget(Select2::class, [
                                            'data'          => $customers,
                                            'showToggleAll' => false,
                                            'hideSearch'    => false,
                                            'disabled'      => !$model->isEditable(),
                                            'options'       => ['placeholder' => 'Выберите покупателя...'],
                                            'pluginOptions' => [
                                                'closeOnSelect' => true,
                                                'allowClear'    => true
                                            ],
                                            'pluginEvents'  => [
                                                "change" => "function() {
                                                    window.getAddressBlock();
                                                    window.getExceptionBlock();
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
                                                    'disabled'      => !$model->isEditable(),
                                                    'labelOptions'  => ['style' => 'font-size: 12px'],
                                                    'options'       => [
                                                        'data-toggle'   => "collapse",
                                                        'data-target'   => "#collapse-customer",
                                                        'aria-expanded' => "false",
                                                        'aria-controls' => "collapse-customer"
                                                    ],
                                                    'pluginEvents'  => [
                                                        "switchChange.bootstrapSwitch" => "function() {
                                                            if ($('[name=\"Order[isNewCustomer]\"]').is(':checked')) {
                                                                $('#order-customer_id').prop('disabled', true); 
                                                            } else {
                                                                $('#order-customer_id').prop('disabled', false); 
                                                            }
                                                            setTimeout(window.getAddressBlock(), 300);
                                                            $('#collapse-customer').collapse('toggle');
                                                        }"
                                                    ]
                                                ])->label(false); ?>
                                            <label>Новый пользователь</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row collapse" id="collapse-customer">
                                    <div class="col-sm-4">
                                        <?= $form->field($model->customer, 'fio')->textInput([
                                            'class'    => 'form-control input-sm',
                                            'disabled' => !$model->isEditable(),
                                        ]); ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?= $form->field($model->customer, 'phone')->widget(
                                            MaskedInput::class,
                                            [
                                                'mask'          => '+7 (999) 999-99-99',
                                                'clientOptions' => ['onincomplete' => 'function(){$("#user-phone").removeAttr("value").attr("value","");}'],
                                                'options'       => [
                                                    'class'       => 'form-control input-sm',
                                                    'disabled'    => !$model->isEditable(),
                                                    'placeholder' => '+7 (___) ___-__-__',
                                                ]
                                            ]) ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?= $form->field($model->customer, 'email')->textInput([
                                            'class'    => 'form-control input-sm',
                                            'disabled' => !$model->isEditable(),
                                        ]); ?>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <?= $form->field($model, 'payment_type')->dropDownList($payments, [
                                            'class'    => 'form-control input-sm',
                                            'disabled' => !$model->isEditable(),
                                        ]) ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="switch cash-machine">
                                            <?= $form
                                                ->field($model, 'cash_machine')
                                                ->widget(SwitchInput::class, [
                                                    'pluginOptions' => [
                                                        'size'    => 'mini',
                                                        'onText'  => 'Да',
                                                        'offText' => 'Нет',
                                                    ],
                                                    'inlineLabel'   => true,
                                                    'disabled'      => !$model->isEditable(),
                                                    'labelOptions'  => ['style' => 'font-size: 12px'],
                                                    'options'       => [
                                                        'data-toggle'   => "collapse",
                                                        'data-target'   => "#collapse-customer",
                                                        'aria-expanded' => "false",
                                                        'aria-controls' => "collapse-customer"
                                                    ],
                                                    'pluginEvents'  => []
                                                ])->label(false); ?>
                                            <label>Кассовый аппарат</label>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row" id="order-address-block">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label><?= \Yii::t('order', 'Choose address ID'); ?></label>
                                            <?= Html::activeDropDownList($model, 'address_id', $addresses, [
                                                'class'    => 'form-control input-sm',
                                                'disabled' => !$model->isEditable(),
                                            ]); ?>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="switch detailed-address">
                                            <?= SwitchInput::widget([
                                                'name'          => 'address_detailed',
                                                'value'         => false,
                                                'pluginOptions' => [
                                                    'size'    => 'mini',
                                                    'onText'  => 'Да',
                                                    'offText' => 'Нет',
                                                ],
                                                'disabled'      => !$model->isEditable(),
                                                'inlineLabel'   => true,
                                                'labelOptions'  => ['style' => 'font-size: 12px'],
                                                'options'       => [
                                                    'data-toggle'   => "collapse",
                                                    'data-target'   => "#collapse-address",
                                                    'aria-expanded' => "false",
                                                    'aria-controls' => "collapse-address"
                                                ],
                                                'pluginEvents'  => [
                                                    "switchChange.bootstrapSwitch" => "function() { $('#collapse-address').collapse('toggle'); }"
                                                ]
                                            ]);
                                            ?>
                                            <label>Адрес детально</label>
                                        </div>
                                    </div>
                                    <?= $this->render('_address', [
                                        'address' => $model->address,
                                    ]); ?>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?= $form->field($model, 'comment')->textarea([
                                            'rows'     => 3,
                                            'class'    => 'form-control input-sm',
                                            'disabled' => !$model->isEditable(),
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h2 class="box-title"><?php echo \Yii::t('order', 'Order details'); ?></h2>
                    </div>
                    <div class="box-body order-info">
                        <p><?= \Yii::t('order', 'Order create date'); ?>
                            <span><?= $model->created_at ? date('d.m.Y \в H:i', $model->created_at) : '---'; ?></span>
                        </p>
                        <p><?= \Yii::t('order', 'Payment type'); ?> <span><?= $model->payment->name ?? '---'; ?></span>
                        </p>
                        <p><?= \Yii::t('order', 'Order status'); ?>
                            <span><?= $model->getStatusName() ?? '---'; ?></span></p>
                        <p><?= \Yii::t('order', 'Order subscription'); ?>
                            <span><?= $model->getOrderSubscription() ?? '---'; ?></span></p>
                        <p><?= \Yii::t('order', 'Cutlery'); ?> <span><?= $model->cutlery ?? '---'; ?></span></p>
                        <p><?= \Yii::t('order', 'Order subscription dates'); ?>
                            <span><?= $model->getSubscriptionDates() ?? '---'; ?></span></p>
                        <p><?= \Yii::t('order', 'Order total'); ?>
                            <span><?= \Yii::$app->formatter->asCurrency($model->total ?? 0, 'RUB'); ?></span></p>
                    </div>
                </div>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h2 class="box-title"><?php echo \Yii::t('order', 'Order exceptions'); ?></h2>
                    </div>
                    <div class="box-body">
                        <div class="exceptions">
                            <?php foreach ($model->exceptions as $i => $exception): ?>
                                <?= $this->render('_exception', [
                                    'exceptions' => $exceptions,
                                    'exception'  => $exception,
                                    'disabled'   => !$model->isEditable(),
                                    'i'          => $i,
                                ]); ?>
                            <?php endforeach; ?>
                        </div>
                        <hr/>
                        <?php if ($model->isEditable()): ?>
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
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary" id="order-menu-block">
            <div class="box-header with-border">
                <h2 class="box-title"><?php echo \Yii::t('order', 'Menu block'); ?></h2>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-3">
                        <?= $form->field($model, 'subscription_id')->dropDownList(
                            [
                                ''  => \Yii::t('app', 'Choose'),
                                '0' => \Yii::t('order', 'Without subscription'),
                            ] + $subscriptions,
                            [
                                'class' => 'form-control input-sm'
                            ]
                        ) ?>
                    </div>
                    <div class="col-sm-2">
                        <?= $form->field($model, 'cutlery')->textInput([
                            'class' => 'form-control input-sm'
                        ]) ?>
                    </div>
                    <div class="col-sm-2 date-input-wrapper">
                        <?= $form->field($model, 'scheduleFirstDate')->widget(DatePicker::class, [
                            'options'       => [
                                'placeholder' => \Yii::t('order', 'Choose date'),
                                'class'       => 'form-control input-sm',
                            ],
                            'removeButton'  => false,
                            'pluginOptions' => [
                                'autoclose' => true
                            ]
                        ]); ?>
                    </div>
                    <div class="col-sm-2">
                        <?= $form->field($model, 'scheduleInterval')->dropDownList(
                            ['' => \Yii::t('app', 'Choose')] + $intervals,
                            [
                                'class' => 'form-control input-sm'
                            ]
                        ) ?>
                    </div>
                </div>
                <hr/>
                <div class="row subscription-block">
                    <div class="col-sm-2">
                        <?= $form->field($model, 'franchise_id')->dropDownList(
                            (count($franchises) > 1) ? (['' => \Yii::t('app', 'Choose')] + $franchises) : $franchises,
                            [
                                'class' => 'form-control input-sm'
                            ]
                        ) ?>
                    </div>

                    <div class="col-sm-2">
                        <div class="switch detailed-address">
                            <?= $form->field($model, 'without_soup')->widget(SwitchInput::class, [
                                'pluginOptions' => [
                                    'size'    => 'mini',
                                    'onText'  => 'Да',
                                    'offText' => 'Нет',
                                ],
                            ]); ?>
                        </div>
                    </div>


                    <div class="col-sm-2">
                        <?= $form->field($model, 'count')->dropDownList(
                            ['' => \Yii::t('app', 'Choose')] + $subscriptionCounts,
                            [
                                'class' => 'form-control input-sm'
                            ]
                        ) ?>
                    </div>
                </div>
                <hr/>
                <div class="row dish-block">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-3">Название</div>
                            <div class="col-sm-1">Колчество</div>
                            <div class="col-sm-1">Цена</div>
                        </div>
                        <div class="dishes">
                            <?php foreach ($model->schedules as $i => $schedule) : ?>
                                <?php foreach ($schedule->dishes as $j => $dish) : ?>
                                    <?= $this->render('_product', [
                                        'dish' => $dish,
                                        'i'    => $j,
                                    ]) ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 product-buttons">
                            <a href="javascript:void(0)" id="add-dish" class="btn btn-sm btn-primary pull-right">
                                <i class="fa fa-plus"></i><?= \Yii::t('dish', 'Add product') ?>
                            </a>
                        </div>
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
    <?php ActiveForm::end(); ?>
</div>


