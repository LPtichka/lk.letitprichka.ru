<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\Search\PaymentType $searchModel */

$this->title = \Yii::t('app', 'Reports');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-3 col-md-4 col-sm-4">
        <div class="report-item text-center">

            <div class="text-center">
                <img src="/images/svg/list.svg"  style="margin: 10px 0;" height="60px;" />
            </div>
            <br />
            <p>Получить<br /> бракеражный журнал</p>
            <br />
            <?= Html::a(
                '<span>' . \Yii::t('menu', 'Marriage sheet') . '</span>',
                ['order/get-route-sheet'],
                [
                    'class'       => 'btn btn-sm btn-default',
                    'data-href'   => Url::to(['menu/get-marriage-sheet']),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]) ?>
        </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4">
        <div class="report-item text-center">

            <div class="text-center">
                <img src="/images/svg/route.svg" style="margin: 10px 0;" height="60px;" />
            </div>
            <br />
            <p>Список адресов для заказов<br /> с доставкой на дату</p>
            <br />
                <?= Html::a('<span>Маршрутный лист</span>',
                    ['order/get-route-sheet'],
                    [
                        'class'       => 'btn btn-sm btn-default',
                        'data-href'   => Url::to(['order/get-route-sheet']),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                    ]); ?>
        </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4">
        <div class="report-item text-center">

            <div class="text-center">
                <img src="/images/svg/notepad-1.svg"  style="margin: 10px 0;" height="60px;" />
            </div>
            <br />
            <p>Меню для покупателя<br /> на конкретную дату</p>
            <br />
            <?php echo \yii\helpers\Html::a(
                '<span>' . \Yii::t('menu', 'Customer sheet') . '</span>',
                ['/order/get-customer-sheet', 'id' => 0],
                [
                    'class'       => 'btn btn-default',
                    'data-href'   => \yii\helpers\Url::to(['/order/get-customer-sheet', 'id' => 0]),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]) ?>
        </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4">
        <div class="report-item text-center">

            <div class="text-center">
                <img src="/images/svg/notebook-2.svg"  style="margin: 10px 0;" height="60px;" />
            </div>
            <br />
            <p>Лист закупок<br /> для меню</p>
            <br />
            <?php echo \yii\helpers\Html::a(
                '<span>' . \Yii::t('product', 'Procurement sheet') . '</span>',
                ['product/get-procurement-sheet'],
                [
                    'class'       => 'btn btn-sm btn-default',
                    'data-href'   => Url::to(['product/get-procurement-sheet']),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]) ?>
        </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4">
        <div class="report-item text-center">

            <div class="text-center">
                <img src="/images/svg/notebook-2.svg"  style="margin: 10px 0;" height="60px;" />
            </div>
            <br />
            <p>Актуальные остатки<br /> на складе</p>
            <br />
            <?php echo \yii\helpers\Html::a(
                '<span>' . \Yii::t('product', 'Warehouse accounting') . '</span>',
                ['product/get-warehouse-accounting'],
                [
                    'class'       => 'btn btn-sm btn-default',
                    'data-href'   => Url::to(['product/get-warehouse-accounting']),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]) ?>
        </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4">
        <div class="report-item text-center">

            <div class="text-center">
                <img src="/images/svg/alarm.svg"  style="margin: 10px 0;" height="60px;" />
            </div>
            <br />
            <p>Заказ<br /> для кухни</p>
            <br />
            <?php echo \yii\helpers\Html::a(
                '<span>' . \Yii::t('order', 'Order for kitchen') . '</span>',
                ['order/get-order-for-kitchen'],
                [
                    'class'       => 'btn btn-sm btn-default',
                    'data-href'   => Url::to(['order/get-order-for-kitchen']),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]) ?>
        </div>
    </div>
</div>


