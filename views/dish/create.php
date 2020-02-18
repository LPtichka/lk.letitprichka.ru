<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var \app\models\Repository\Dish $model */

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('dish', 'Dishes'), 'url' => Url::to(['dish/index'])];
if ($model->id) {
    $this->params['breadcrumbs'][] = \Yii::t('dish', '{name}', ['name' => $model->name]);
} else {
    $this->params['breadcrumbs'][] = \Yii::t('dish', 'New dish');
}
?>
<div style="position: absolute; right: 15px; top: 60px; z-index: 999999;">
    <?= Html::button('<i class="fa fa-download"></i> ', [
        'class'     => 'btn btn-sm btn-default export',
        'data-href' => Url::to(['dish/export', 'id' => $model->id]),
    ]) ?>
</div>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= \Yii::t('dish', 'Primary parameters'); ?></h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'name')->textInput([
                            'class' => 'form-control input-sm'
                        ]) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'type')->dropDownList(
                            (new \app\models\Helper\Arrays($model->getTypes()))->getSelectOptions(),
                            ['class' => 'form-control input-sm']
                        ); ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'weight')->textInput([
                            'class' => 'form-control input-sm',
                            'value' => $model->weight
                                ? (new \app\models\Helper\Weight())
                                    ->setUnit(\app\models\Helper\Weight::UNIT_KG)
                                    ->convert($model->weight, \app\models\Helper\Weight::UNIT_GR)
                                : ''
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 label-block">
                        <div class="row">
                            <div class="col-sm-3">
                                <?= $form->field($model, 'is_breakfast')->checkbox() ?>
                            </div>
                            <div class="col-sm-3">
                                <?= $form->field($model, 'is_dinner')->checkbox() ?>
                            </div>
                            <div class="col-sm-3">
                                <?= $form->field($model, 'is_lunch')->checkbox() ?>
                            </div>
                            <div class="col-sm-3">
                                <?= $form->field($model, 'is_supper')->checkbox() ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-3">
                                <?= $form->field($model, 'kkal')->textInput([
                                    'class' => 'form-control input-sm'
                                ]) ?>
                            </div>
                            <div class="col-sm-3">
                                <?= $form->field($model, 'proteins')->textInput([
                                    'class' => 'form-control input-sm'
                                ]) ?>
                            </div>
                            <div class="col-sm-3">
                                <?= $form->field($model, 'fat')->textInput([
                                    'class' => 'form-control input-sm'
                                ]) ?>
                            </div>

                            <div class="col-sm-3">
                                <?= $form->field($model, 'carbohydrates')->textInput([
                                    'class' => 'form-control input-sm'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= $form->field($model, 'process')->textarea([
                                'rows' => 3
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= $form->field($model, 'storage_condition')->textarea([
                                'rows' => 3
                            ]) ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= \Yii::t('dish', 'Products'); ?></h3>
            </div>
            <div class="box-body">
                <div class="product-form form-group">
                    <div class="">
                        <div class="row">
                            <div class="col-sm-2"><label><?= \Yii::t('product', 'Name');?></label></div>
                            <div class="col-sm-1"><label><?= \Yii::t('product', 'Count / Weight / Liters');?></label></div>
                            <div class="col-sm-1"><label><?= \Yii::t('product', 'Brutto');?></label></div>
                            <div class="col-sm-1"><label><?= \Yii::t('product', 'Netto');?></label></div>
                            <div class="col-sm-1"><label><?= \Yii::t('product', 'Brutto on 1 kg');?></label></div>
                            <div class="col-sm-1"><label><?= \Yii::t('product', 'Kkal');?></label></div>
                            <div class="col-sm-1"><label><?= \Yii::t('product', 'Proteins');?></label></div>
                            <div class="col-sm-1"><label><?= \Yii::t('product', 'Fat');?></label></div>
                            <div class="col-sm-1"><label><?= \Yii::t('product', 'Carbohydrates');?></label></div>
                            <div class="col-sm-1"><label></label></div>
                            <div class="col-sm-1"></div>
                        </div>
                    </div>
                    <hr />
                    <div class="products">
                        <?php foreach ($model->dishProducts as $i => $dishProduct) : ?>
                            <?= $this->render('_product', [
                                'product' => $dishProduct,
                                'i'       => $i,
                            ]) ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 product-buttons">
                            <a href="javascript:void(0)" id="add-product" class="btn btn-sm btn-primary pull-right">
                                <i class="material-icons">add</i><?= \Yii::t('dish', 'Add product') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::submitButton('<i class="material-icons">done</i> ' . \Yii::t('app', 'Save'), ['class' => 'btn btn-sm btn-warning']) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group text-right">
                    <?= Html::a(\Yii::t('app', 'Cancel'), ['dish/index'], ['class' => 'btn btn-sm btn-default']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


