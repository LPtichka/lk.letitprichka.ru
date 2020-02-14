<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use app\widgets\Alert;

/** @var \app\models\Repository\Exception $model */
/** @var string $title */

$this->title = $title;
?>

<?php Pjax::begin([
    'id'              => 'franchise-form',
    'formSelector'    => '#franchise-form form',
    'enablePushState' => false,
]); ?>
<div class="row franchise-form">
    <?= Alert::widget(['options' => ['style' => 'margin-bottom:20px']]) ?>

    <title><?= Html::encode($this->title) ?></title>
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="col-sm-12">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($model, 'name')->textInput([
                    'class' => 'form-control input-sm'
                ]); ?>
            </div>
        </div>
        <div class="row modal-buttons">
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
                    <?= Html::a(
                        \Yii::t('app', 'Cancel'),
                        ['franchise/index'],
                        ['class' => 'btn btn-sm btn-default']
                    ); ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php Pjax::end(); ?>
