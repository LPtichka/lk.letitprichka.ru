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
<div class="row">
    <?= Alert::widget(['options' => ['style' => 'margin-bottom:20px']]) ?>

    <title><?= Html::encode($this->title) ?></title>
    <h1><?= Html::encode($this->title) ?></h1>
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
                </div>
            </div>
            <div class="col-md-4">
                <label>Исключения</label>

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
                           data-href="<?php echo Url::to(['customer/add-exception']); ?>"
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

        <div class="addresses">
            <hr/>
            <div class="row">
                <div class="col-sm-4 col-print-4"><label>Полный адрес</label></div>
                <div class="col-sm-6 col-print-6"><label>Комментарий</label></div>
                <div class="col-sm-2 col-print-2"><label>Основной</label></div>
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
                        <i class="fa fa-plus"></i>
                        <?= Yii::t('customer', 'Add address') ?>
                    </a>
                    <?= Html::a(\Yii::t('app', 'Cancel'), '#', [
                        'class'        => 'btn btn-sm btn-default',
                        'data-dismiss' => 'modal'
                    ]) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>


