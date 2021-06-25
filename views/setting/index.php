<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var \app\models\Repository\Settings[] $settings */

$this->title = \Yii::t('app', 'Settings');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Settings'), 'url' => Url::to(['setting/index'])];
?>

<div class="row">
    <div class="col-md-12">
        <?php
        $form = ActiveForm::begin(); ?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php
                    echo \Yii::t('menu', 'Base information'); ?></h3>
            </div>
            <div class="box-body">
                <div class="row">

                </div>
            </div>
            <div class="box-body" id="menu-composition">
                <?php
                foreach ($settings as $setting): ?>
                    <div class="row <?php
                    if (!$setting->status): ?> hidden <?php
                    endif; ?>">
                        <div class="col-sm-3"><?php
                            echo \Yii::t('settings', $setting->name); ?></div>
                        <div class="col-sm-9">
                            <?= Html::activeInput(
                                'text',
                                $setting,
                                "[$setting->name]value",
                                ['class' => 'form-control input-sm', 'autocomplete' => false]
                            ) ?>
                        </div>
                        <div class="col-sm-12">
                            <hr/>
                        </div>
                    </div>
                <?php
                endforeach; ?>

                <div class="row">
                    <div class="col-sm-3"><?php
                        echo \Yii::t('settings', 'Work days'); ?></div>
                    <div class="col-sm-9">
                        <span>
                            <?php
                            echo \Yii::t('settings', 'Monday'); ?>
                            <?= Html::checkbox(
                                "monday",
                                (bool)$workDays['monday'], [
                                        'class' => 'work-days'
                                ]
                            ) ?>
                        </span>
                        &nbsp;|&nbsp;
                        <span>
                            <?php
                            echo \Yii::t('settings', 'Tuesday'); ?>
                            <?= Html::checkbox(
                                "tuesday",
                                (bool)$workDays['tuesday'], [
                                    'class' => 'work-days'
                                ]
                            ) ?>
                        </span>
                        &nbsp;|&nbsp;
                        <span>
                            <?php
                            echo \Yii::t('settings', 'Wednesday'); ?>
                            <?= Html::checkbox(
                                "wednesday",
                                (bool)$workDays['wednesday'], [
                                    'class' => 'work-days'
                                ]
                            ) ?>
                        </span>
                        &nbsp;|&nbsp;
                        <span>
                            <?php
                            echo \Yii::t('settings', 'Thursday'); ?>
                            <?= Html::checkbox(
                                "thursday",
                                (bool)$workDays['thursday'], [
                                    'class' => 'work-days'
                                ]
                            ) ?>
                        </span>
                        &nbsp;|&nbsp;
                        <span>
                            <?php
                            echo \Yii::t('settings', 'Friday'); ?>
                            <?= Html::checkbox(
                                "friday",
                                (bool)$workDays['friday'], [
                                    'class' => 'work-days'
                                ]
                            ) ?>
                        </span>
                        &nbsp;|&nbsp;
                        <span>
                            <?php
                            echo \Yii::t('settings', 'Saturday'); ?>
                            <?= Html::checkbox(
                                "saturday",
                                (bool)$workDays['saturday'], [
                                    'class' => 'work-days'
                                ]
                            ) ?>
                        </span>
                        &nbsp;|&nbsp;
                        <span>
                            <?php
                            echo \Yii::t('settings', 'Sunday'); ?>
                            <?= Html::checkbox(
                                "sunday",
                                (bool)$workDays['sunday'], [
                                    'class' => 'work-days'
                                ]
                            ) ?>
                        </span>
                    </div>
                    <div class="col-sm-12">
                        <hr/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::submitButton(
                        '<i class="material-icons">done</i> ' . \Yii::t('app', 'Save'),
                        ['class' => 'btn btn-sm btn-warning']
                    ) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group text-right">
                    <?= Html::a(\Yii::t('app', 'Cancel'), ['site/index'], ['class' => 'btn btn-sm btn-default']) ?>
                </div>
            </div>
        </div>
        <?php
        ActiveForm::end(); ?>
    </div>
</div>




