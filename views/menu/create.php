<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var \app\models\Repository\Menu $model */

$this->title = \Yii::t('menu', 'Menu create');
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
                        <div class="col-sm-6">
                            <?= DatePicker::widget([
                                'name'          => 'menu_start_date',
                                'value'         => $model->menu_start_date,
                                'type'          => DatePicker::TYPE_RANGE,
                                'name2'         => 'menu_end_date',
                                'disabled'      => !empty($model->id),
                                'value2'        => $model->menu_end_date,
                                'pluginOptions' => [
                                    'autoclose'     => true,
                                    'datesDisabled' => $disabledDays,
                                    'format'        => 'yyyy-mm-dd',
                                    'startDate'     => date('Y-m-d', time())
                                ],
                                'pluginEvents'  => [
                                    "changeDate" => "function(e) {
                                    window.getMenuBlocks();
                                }",
                                ]
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo \Yii::t('menu', 'Menu composition'); ?></h3>
                </div>
                <div class="box-body" id="menu-composition">
                    <div class="row">

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= Html::submitButton('<i class="fa fa-check"></i> ' . \Yii::t('app', 'Save'), ['class' => 'btn btn-sm btn-warning']) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group text-right">
                        <?= Html::a(\Yii::t('app', 'Cancel'), ['menu/index'], ['class' => 'btn btn-sm btn-default']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

<?php
if ($model->id) {
    \Yii::$app->view->registerJs(<<<JS
        window.getMenuBlocks("$model->id");
JS
    );
}




