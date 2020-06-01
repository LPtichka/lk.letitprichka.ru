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
                    (new \app\models\Helper\Arrays($breakfasts))->getSelectOptions('Выберите блюдо на завтрак'),
                    [
                        'class' => 'form-control input-sm dish-for-menu',
                        'data-ingestion-date' => $date,
                        'data-ingestion-type' => 'breakfast',
                        'data-ingestion-number' => $i,
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
            <h5><?= \Yii::t('menu', 'Dinner'); ?></h5>
            <div class="clearfix"></div>
        </div>
        <div class="ingestion-wrapper">
            <div class="ingestion" data-ingestion-id="<?php echo $i; ?>">
                <?php echo \yii\helpers\Html::dropDownList(
                    'dish[' . $date . '][dinner][first][' . $i . ']',
                    $menu->getDishIDByParams($i, $date, 'dinner', \app\models\Repository\Dish::TYPE_FIRST),
                    (new \app\models\Helper\Arrays($firstDishesDinner))->getSelectOptions('Выберите блюдо на первое - обед'),
                    [
                        'class' => 'form-control input-sm dish-for-menu',
                        'data-ingestion-date' => $date,
                        'data-ingestion-type' => 'dinner',
                        'data-ingestion-number' => $i,
                    ]
                ); ?>
                <?php echo \yii\helpers\Html::dropDownList(
                    'dish[' . $date . '][dinner][second][' . $i . ']',
                    $menu->getDishIDByParams($i, $date, 'dinner', \app\models\Repository\Dish::TYPE_SECOND),
                    (new \app\models\Helper\Arrays($secondDishesDinner))->getSelectOptions('Выберите блюдо на второе - обед'),
                    [
                        'class' => 'form-control input-sm dish-for-menu',
                        'data-ingestion-date' => $date,
                        'data-ingestion-type' => 'dinner',
                        'data-ingestion-number' => $i,
                    ]
                ); ?>

                <?php if($menu->isGarnishNeeded($i, $date, 'dinner', \app\models\Repository\Dish::TYPE_SECOND)):?>
                    <?php echo \yii\helpers\Html::dropDownList(
                        'dish[' . $date . '][dinner][garnish][' . $i . ']',
                        $menu->getDishIDByParams($i, $date, 'dinner', \app\models\Repository\Dish::TYPE_GARNISH),
                        (new \app\models\Helper\Arrays($garnishDishes))->getSelectOptions('Выберите блюдо на гарнир'),
                        [
                            'class' => 'form-control input-sm dish-for-menu dish-garnish',
                            'data-ingestion-date' => $date,
                            'data-ingestion-type' => 'dinner',
                            'data-ingestion-number' => $i,
                        ]
                    ); ?>
                <?php endif;?>
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
                    (new \app\models\Helper\Arrays($lunches))->getSelectOptions('Выберите блюдо на ланч'),
                    [
                        'class' => 'form-control input-sm dish-for-menu',
                        'data-ingestion-date' => $date,
                        'data-ingestion-type' => 'lunch',
                        'data-ingestion-number' => $i,
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
                    'dish[' . $date . '][supper][second][' . $i . ']',
                    $menu->getDishIDByParams($i, $date, 'supper', \app\models\Repository\Dish::TYPE_SECOND),
                    (new \app\models\Helper\Arrays($suppers))->getSelectOptions('Выберите блюдо на второе - ужин'),
                    [
                        'class' => 'form-control input-sm dish-for-menu',
                        'data-ingestion-date' => $date,
                        'data-ingestion-type' => 'supper',
                        'data-ingestion-number' => $i,
                    ]
                ); ?>
                <?php if($menu->isGarnishNeeded($i, $date, 'supper', \app\models\Repository\Dish::TYPE_SECOND)):?>
                    <?php echo \yii\helpers\Html::dropDownList(
                        'dish[' . $date . '][supper][garnish][' . $i . ']',
                        $menu->getDishIDByParams($i, $date, 'supper', \app\models\Repository\Dish::TYPE_GARNISH),
                        (new \app\models\Helper\Arrays($garnishDishes))->getSelectOptions('Выберите блюдо на гарнир'),
                        [
                            'class' => 'form-control input-sm dish-for-menu dish-garnish',
                            'data-ingestion-date' => $date,
                            'data-ingestion-type' => 'supper',
                            'data-ingestion-number' => $i,
                        ]
                    ); ?>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>