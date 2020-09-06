<?php

/* @var $order \app\models\Repository\Order */
/* @var $menu \app\models\Repository\Menu */

$dateObject = (new \app\models\Helper\Date($date));

use app\widgets\Html; ?>

<div class="row menu-row">
    <div class="col-sm-12">
        <strong>Меню
            на <?php echo $dateObject->getWeekdayName(); ?> <?php echo $dateObject->getFormattedDate(); ?></strong>
    </div>
    <hr class="col-sm-12" />
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
            <?php $count = $menu->id ? $menu->getIngestionCountForDay('breakfast') : 1; ?>
            <?php for ($i = 0; $i < $count; $i++): ?>
                <?php if ($menu->hasIngestion('breakfast', $i) || !$menu->id): ?>
                    <div class="ingestion" data-ingestion-id="<?php echo $i; ?>">
                        <?= Html::a('<i class="material-icons">delete_forever</i>', '#', [
                            'class' => 'btn btn-sm btn-default delete-ingestion',
                        ]); ?>
                        <?php echo \yii\helpers\Html::dropDownList(
                            'dish[' . $date . '][breakfast][' . $i . ']',
                            $menu->getDishIDByParams($i, $date, 'breakfast', $i, $chosenDishes['breakfast'] ?? []),
                            (new \app\models\Helper\Arrays($breakfasts))->getSelectOptions('Выберите блюдо на завтрак'),
                            [
                                'class'                 => 'form-control input-sm dish-for-menu',
                                'data-ingestion-date'   => $date,
                                'data-ingestion-type'   => 'breakfast',
                                'data-ingestion-number' => $i,
                            ]
                        ); ?>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
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
            <?php $count = $menu->id ? $menu->getIngestionCountForDay('dinner') : 1; ?>
            <?php for ($i = 0; $i < $count; $i++): ?>
                <?php if ($menu->hasIngestion('dinner', $i) || !$menu->id): ?>
                    <div class="ingestion" data-ingestion-id="<?php echo $i; ?>">
                        <?= Html::a('<i class="material-icons">delete_forever</i>', '#', [
                            'class' => 'btn btn-sm btn-default delete-ingestion',
                        ]); ?>
                        <?php echo \yii\helpers\Html::dropDownList(
                            'dish[' . $date . '][dinner][first][' . $i . ']',
                            $menu->getDishIDByParams($i, $date, 'dinner', \app\models\Repository\Dish::TYPE_FIRST, $chosenDishes['dinner'] ?? []),
                            (new \app\models\Helper\Arrays($firstDishesDinner))->getSelectOptions('Выберите блюдо на первое - обед'),
                            [
                                'class'                 => 'form-control input-sm dish-for-menu',
                                'data-ingestion-date'   => $date,
                                'data-ingestion-type'   => 'dinner',
                                'data-ingestion-number' => $i,
                            ]
                        ); ?>
                        <?php echo \yii\helpers\Html::dropDownList(
                            'dish[' . $date . '][dinner][second][' . $i . ']',
                            $menu->getDishIDByParams($i, $date, 'dinner', \app\models\Repository\Dish::TYPE_SECOND, $chosenDishes['dinner'] ?? []),
                            (new \app\models\Helper\Arrays($secondDishesDinner))->getSelectOptions('Выберите блюдо на второе - обед'),
                            [
                                'class'                 => 'form-control input-sm dish-for-menu',
                                'data-ingestion-date'   => $date,
                                'data-ingestion-type'   => 'dinner',
                                'data-ingestion-number' => $i,
                            ]
                        ); ?>

                        <?php if ($menu->isGarnishNeeded($i, $date, 'dinner', \app\models\Repository\Dish::TYPE_SECOND, $chosenDishes['dinner'] ?? [])): ?>
                            <?php echo \yii\helpers\Html::dropDownList(
                                'dish[' . $date . '][dinner][garnish][' . $i . ']',
                                $menu->getDishIDByParams($i, $date, 'dinner', \app\models\Repository\Dish::TYPE_GARNISH, $chosenDishes['dinner'] ?? []),
                                (new \app\models\Helper\Arrays($garnishDishes))->getSelectOptions('Выберите блюдо на гарнир'),
                                [
                                    'class'                 => 'form-control input-sm dish-for-menu dish-garnish',
                                    'data-ingestion-date'   => $date,
                                    'data-ingestion-type'   => 'dinner',
                                    'data-ingestion-number' => $i,
                                ]
                            ); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
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
            <?php $count = $menu->id ? $menu->getIngestionCountForDay('lunch') : 1; ?>
            <?php for ($i = 0; $i < $count; $i++): ?>
                <?php if ($menu->hasIngestion('lunch', $i) || !$menu->id): ?>
                    <div class="ingestion" id="ingestion_lunch_<?php echo $i; ?>">
                        <?= Html::a('<i class="material-icons">delete_forever</i>', '#', [
                            'class' => 'btn btn-sm btn-default delete-ingestion',
                        ]); ?>
                        <?php echo \yii\helpers\Html::dropDownList(
                            'dish[' . $date . '][lunch][' . $i . ']',
                            $menu->getDishIDByParams($i, $date, 'lunch', $i, $chosenDishes['lunch'] ?? []),
                            (new \app\models\Helper\Arrays($lunches))->getSelectOptions('Выберите блюдо на перекус'),
                            [
                                'class'                 => 'form-control input-sm dish-for-menu',
                                'data-ingestion-date'   => $date,
                                'data-ingestion-type'   => 'lunch',
                                'data-ingestion-number' => $i,
                            ]
                        ); ?>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
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
            <?php $count = $menu->id ? $menu->getIngestionCountForDay('supper') : 1; ?>
            <?php for ($i = 0; $i < $count; $i++): ?>
                <?php if ($menu->hasIngestion('supper', $i) || !$menu->id): ?>
                    <div class="ingestion" id="ingestion_supper_<?php echo $i; ?>">
                        <?= Html::a('<i class="material-icons">delete_forever</i>', '#', [
                            'class' => 'btn btn-sm btn-default delete-ingestion',
                        ]); ?>
                        <?php echo \yii\helpers\Html::dropDownList(
                            'dish[' . $date . '][supper][second][' . $i . ']',
                            $menu->getDishIDByParams($i, $date, 'supper', \app\models\Repository\Dish::TYPE_SECOND, $chosenDishes['supper'] ?? []),
                            (new \app\models\Helper\Arrays($suppers))->getSelectOptions('Выберите блюдо на второе - ужин'),
                            [
                                'class'                 => 'form-control input-sm dish-for-menu',
                                'data-ingestion-date'   => $date,
                                'data-ingestion-type'   => 'supper',
                                'data-ingestion-number' => $i,
                            ]
                        ); ?>
                        <?php if ($menu->isGarnishNeeded($i, $date, 'supper', \app\models\Repository\Dish::TYPE_SECOND, $chosenDishes['supper'] ?? [])): ?>
                            <?php echo \yii\helpers\Html::dropDownList(
                                'dish[' . $date . '][supper][garnish][' . $i . ']',
                                $menu->getDishIDByParams($i, $date, 'supper', \app\models\Repository\Dish::TYPE_GARNISH, $chosenDishes['supper'] ?? []),
                                (new \app\models\Helper\Arrays($garnishDishes))->getSelectOptions('Выберите блюдо на гарнир'),
                                [
                                    'class'                 => 'form-control input-sm dish-for-menu dish-garnish',
                                    'data-ingestion-date'   => $date,
                                    'data-ingestion-type'   => 'supper',
                                    'data-ingestion-number' => $i,
                                ]
                            ); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    </div>
</div>