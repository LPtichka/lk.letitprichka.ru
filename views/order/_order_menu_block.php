<?php

use kartik\date\DatePicker;

/** @var \app\models\Repository\Order $model */

?>
<div class="box box-primary" id="order-menu-block">
    <div class="box-header with-border">
        <h2 class="box-title"><?php echo \Yii::t('order', 'Menu block'); ?></h2>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-3 col-sm-12">
                <div class="row">
                    <div class="col-sm-12 select-block">
                        <?= $form->field($model, 'subscription_id')->dropDownList(
                            [
                                '' => \Yii::t('app', 'Choose'),
                            ] + $subscriptions,
                            [
                                'class' => 'form-control input-sm'
                            ]
                        ) ?>
                    </div>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'without_soup')->checkbox(); ?>
                    </div>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'cutlery')->textInput([
                            'class' => 'form-control input-sm'
                        ]) ?>
                    </div>
                    <div class="col-sm-12 date-input-wrapper">
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
                    <div class="col-sm-12 select-block">
                        <?= $form->field($model, 'scheduleInterval')->dropDownList(
                            ['' => \Yii::t('app', 'Choose')] + $intervals,
                            [
                                'class' => 'form-control input-sm'
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-9 col-sm-12">
                <div class="row subscription-block">
                    <div class="col-sm-6 select-block">
                        <?= $form->field($model, 'franchise_id')->dropDownList(
                            (count($franchises) > 1) ? (['' => \Yii::t('app', 'Choose')] + $franchises) : $franchises,
                            [
                                'class' => 'form-control input-sm'
                            ]
                        ) ?>
                    </div>

                    <div class="col-sm-3 select-block">
                        <?= $form->field($model, 'count')->dropDownList(
                            ['' => \Yii::t('app', 'Choose')] + $subscriptionCounts,
                            [
                                'class' => 'form-control input-sm'
                            ]
                        ) ?>
                    </div>

                </div>
                <div class="row dish-block">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6"><label>Название</label></div>
                            <div class="col-sm-2"><label>Колчество</label></div>
                            <div class="col-sm-2"><label>Цена</label></div>
                        </div>
                        <div class="dishes">
                            <?php foreach ($model->schedules as $i => $schedule) : ?>
                                <?php foreach ($schedule->dishes as $j => $dish) : ?>
                                    <?= $this->render('_order_product', [
                                        'dish' => $dish,
                                        'i'    => $j,
                                    ]) ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-sm-12 product-buttons">
                        <a href="javascript:void(0)" id="add-dish" class="btn btn-sm btn-primary">
                            <i class="material-icons">add</i><?= \Yii::t('dish', 'Add product') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>