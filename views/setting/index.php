<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title                   = \Yii::t('app', 'Settings');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Settings'), 'url' => Url::to(['setting/index'])];
?>

<div class="row">
    <div class="col-md-12">
        <?php $form = ActiveForm::begin(); ?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo \Yii::t('menu', 'Base information'); ?></h3>
            </div>
            <div class="box-body">
                <div class="row">

                </div>
            </div>
            <div class="box-body" id="menu-composition">
                <?php foreach ($settings as $setting):?>
                <div class="row">
                    <div class="col-sm-3"><?php echo \Yii::t('settings', $setting->name);?></div>
                    <div class="col-sm-9">
                        <?= Html::activeInput('text', $setting, "[$setting->name]value", ['class' => 'form-control input-sm', 'autocomplete' => false]) ?>
                    </div>
                </div>
                <hr />
                <?php endforeach;?>
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
                    <?= Html::a(\Yii::t('app', 'Cancel'), ['site/index'], ['class' => 'btn btn-sm btn-default']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>




