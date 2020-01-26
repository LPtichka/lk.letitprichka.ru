<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/** @var \app\models\Search\Customer $searchModel */
/** @var \app\models\Repository\Customer $model */

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Customers'), 'url' => Url::to(['customer/index'])];
if ($model->id) {
    $this->params['breadcrumbs'][] = \Yii::t('app', 'Customer № {id}', ['id' => $model->id]);
} else {
    $this->params['breadcrumbs'][] = \Yii::t('app', 'New Customer');
}
?>
<div class="row">
    <div class="col-md-8">
        <?php $form = ActiveForm::begin(); ?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo \Yii::t('customer', 'Base information'); ?></h3>
            </div>
            <div class="box-body">
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
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo \Yii::t('customer', 'Addresses'); ?></h3>
            </div>
            <div class="box-body">
                <div class="addresses">
                    <div class="row">
                        <div class="col-sm-4 col-print-4"><label>Полный адрес</label></div>
                        <div class="col-sm-6 col-print-6"><label>Комментарий</label></div>
                        <div class="col-sm-2 col-print-2"><label>Основной</label></div>
                    </div>
                    <?php foreach ($model->addresses as $i => $address) : ?>
                        <?= $this->render('_address', [
                            'address'          => $address,
                            'i'                => $i,
                            'defaultAddressId' => $model->default_address_id,
                        ]) ?>
                    <?php endforeach; ?>
                </div>
                <hr />
                <div class="row">
                    <div class="col-sm-12 product-buttons">
                        <a href="javascript:void(0)" id="add-address"
                           class="btn btn-sm btn-primary pull-right">
                            <i class="fa fa-plus"></i>
                            <?= Yii::t('customer', 'Add address') ?>
                        </a>
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
                    <?= Html::a(\Yii::t('app', 'Cancel'), ['customer/index'], ['class' => 'btn btn-sm btn-default']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>


