<?php

/* @var $order \app\models\Repository\Order */
/* @var $menu \app\models\Repository\Menu */

$dateObject = (new \app\models\Helper\Date($date));
?>
<div><strong>Меню
        на <?php echo $dateObject->getWeekdayName(); ?> <?php echo $dateObject->getFormattedDate(); ?></strong></div>

<div class="row menu-row">
    <div class="col-sm-3">
        <div class="ingestion-header">
            <div class="pull-right">
                <?php echo \yii\helpers\Html::button(\Yii::t('menu', 'Add'), [
                    'class' => 'btn btn-default btm-sm pull-right add-menu-ingestion'
                ]); ?>
            </div>
            <h5><?= \Yii::t('menu', 'Breakfast'); ?></h5>
            <div class="clearfix"></div>
        </div>
        <div class="ingestion-wrapper">
            <div class="ingestion" data-ingestion-id="<?php echo $i; ?>">
                <?php echo \yii\helpers\Html::dropDownList(
                    'dish[' . $date . '][breakfast][' . $i . ']',
                    $menu->getDishIDByParams($i, $date, 'breakfast', $i),
                    (new \app\models\Helper\Arrays($breakfasts))->getSelectOptions(),
                    [
                        'class' => 'form-control input-sm'
                    ]
                ); ?>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="ingestion-header">
            <div class="pull-right">
                <?php echo \yii\helpers\Html::button(\Yii::t('menu', 'Add'), [
                    'class' => 'btn btn-default btm-sm pull-right add-menu-ingestion'
                ]); ?>
            </div>
            <h5><?= \Yii::t('menu', 'Supper'); ?></h5>
            <div class="clearfix"></div>
        </div>
        <div class="ingestion-wrapper">
            <div class="ingestion" data-ingestion-id="<?php echo $i; ?>">
                <?php echo \yii\helpers\Html::dropDownList(
                    'dish[' . $date . '][dinner][first][' . $i . ']',
                    $menu->getDishIDByParams($i, $date, 'dinner', \app\models\Repository\Dish::TYPE_FIRST),
                    (new \app\models\Helper\Arrays($firstDishesDinner))->getSelectOptions(),
                    [
                        'class' => 'form-control input-sm'
                    ]
                ); ?>
                <?php echo \yii\helpers\Html::dropDownList(
                    'dish[' . $date . '][dinner][second][' . $i . ']',
                    $menu->getDishIDByParams($i, $date, 'dinner', \app\models\Repository\Dish::TYPE_SECOND),
                    (new \app\models\Helper\Arrays($secondDishesDinner))->getSelectOptions(),
                    [
                        'class' => 'form-control input-sm'
                    ]
                ); ?>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="ingestion-header">
            <div class="pull-right">
                <?php echo \yii\helpers\Html::button(\Yii::t('menu', 'Add'), [
                    'class' => 'btn btn-default btm-sm pull-right add-menu-ingestion'
                ]); ?>
            </div>
            <h5><?= \Yii::t('menu', 'Lunch'); ?></h5>
            <div class="clearfix"></div>
        </div>
        <div class="ingestion-wrapper">
            <div class="ingestion" id="ingestion_lunch_<?php echo $i; ?>">
                <?php echo \yii\helpers\Html::dropDownList(
                    'dish[' . $date . '][lunch][' . $i . ']',
                    $menu->getDishIDByParams($i, $date, 'lunch', $i),
                    (new \app\models\Helper\Arrays($lunches))->getSelectOptions(),
                    [
                        'class' => 'form-control input-sm'
                    ]
                ); ?>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="ingestion-header">
            <div class="pull-right">
                <?php echo \yii\helpers\Html::button(\Yii::t('menu', 'Add'), [
                    'class' => 'btn btn-default btm-sm pull-right add-menu-ingestion'
                ]); ?>
            </div>
            <h5><?= \Yii::t('menu', 'Supper'); ?></h5>
            <div class="clearfix"></div>
        </div>
        <div class="ingestion-wrapper">
            <div class="ingestion" id="ingestion_supper_<?php echo $i; ?>">
                <?php echo \yii\helpers\Html::dropDownList(
                    'dish[' . $date . '][supper][' . $i . ']',
                    $menu->getDishIDByParams($i, $date, 'supper', \app\models\Repository\Dish::TYPE_FIRST),
                    (new \app\models\Helper\Arrays($suppers))->getSelectOptions(),
                    [
                        'class' => 'form-control input-sm'
                    ]
                ); ?>
            </div>
        </div>
    </div>
</div>