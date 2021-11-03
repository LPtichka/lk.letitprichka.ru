<?php

use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\widgets\Pjax;

/** @var \app\models\Search\Customer $searchModel */
/** @var \app\models\Repository\Customer $model */

$this->title                   = $title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Customers'), 'url' => Url::to(['customer/index'])];
if ($model->id) {
    $this->params['breadcrumbs'][] = \Yii::t('app', 'Customer № {id}', ['id' => $model->id]);
} else {
    $this->params['breadcrumbs'][] = \Yii::t('app', 'New Customer');
}
?>
<?php Pjax::begin([
    'id'              => 'customer-form',
    'formSelector'    => '#customer-form form',
    'enablePushState' => false,
]); ?>
<div class="row customer-form">
    <?= Alert::widget(['options' => ['style' => 'margin-bottom:20px']]) ?>

    <title><?= Html::encode($this->title) ?></title>
    <div class="col-md-12">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'fio')->textInput([
                            'class' => 'form-control input-sm'
                        ]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'email')->textInput([
                            'class' => 'form-control input-sm'
                        ]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'phone')->widget(
                            MaskedInput::class,
                            [
                                'mask'          => '+7 (999) 999-99-99',
                                'clientOptions' => ['onincomplete' => 'function(){$("#user-phone").removeAttr("value").attr("value","");}'],
                                'options'       => [
                                    'class'       => 'form-control input-sm',
                                    'placeholder' => '+7 (___) ___-__-__',
                                ]
                            ]) ?>
                    </div>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'comment')->textarea([
                            'class' => 'form-control input-sm'
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <label>Исключения</label>

                <div class="exceptions" data-empty-text="<?php echo \Yii::t('order', 'No exceptions'); ?>">
                    <?php if (empty($model->exceptions)): ?>
                        <p class="empty-text"><?php echo \Yii::t('order', 'No exceptions'); ?></p>
                    <?php else: ?>
                        <?php foreach ($model->exceptions as $i => $exception): ?>
                            <?= $this->render('_exception', [
                                'exceptions' => $exceptions,
                                'exception'  => $exception,
                                'i'          => $i + 1,
                            ]); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
                <hr/>
                <div class="row">
                    <div class="col-sm-12 exception-buttons">
                        <a href="javascript:void(0)"
                           id="add-exception"
                           data-href="<?php echo Url::to(['customer/add-exception']); ?>"
                           data-block="exceptions"
                           data-row="exception-row"
                           class="btn btn-sm btn-primary pull-right add-row-action"
                        >
                            <i class="material-icons">add</i>
                            <?= Yii::t('order', 'Add exception') ?>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <div class="addresses">
            <hr/>
            <div class="row">
                <div class="col-sm-5 col-print-5"><label><?php echo \Yii::t('customer', 'Full address'); ?></label>
                </div>
                <div class="col-sm-5 col-print-5">
                    <label><?php echo \Yii::t('customer', 'Address additional info'); ?></label></div>
                <div class="col-sm-2 col-print-2"><label><?php echo \Yii::t('customer', 'Main address'); ?></label>
                </div>
            </div>
            <hr class="devider"/>
            <?php foreach ($model->addresses as $i => $address) : ?>
                <?= $this->render('_address', [
                    'address'          => $address,
                    'i'                => $i,
                    'defaultAddressId' => $model->default_address_id,
                ]) ?>
            <?php endforeach; ?>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::submitButton('<i class="material-icons">done</i> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-sm btn-warning']) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group text-right">
                    <a href="javascript:void(0)" id="add-address"
                       class="btn btn-sm btn-primary mr-15">
                        <i class="material-icons">add</i>
                        <span><?= Yii::t('customer', 'Add address') ?></span>
                    </a>
                    <?= Html::a('<span>' . \Yii::t('app', 'Cancel') . '</span>', '#', [
                        'class'        => 'btn btn-sm btn-default',
                        'data-dismiss' => 'modal'
                    ]) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php Pjax::end(); ?>


