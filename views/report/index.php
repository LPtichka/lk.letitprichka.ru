<?php

use app\widgets\Grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var \app\models\Search\PaymentType $searchModel */

$this->title = \Yii::t('report', 'Reports');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-2">
        <div class="report-item text-center">

            <div class="text-center">
                <img src="/images/marriage.svg" height="90px;" />
            </div>
            <h4><?= Html::a(
                '<span>' . \Yii::t('menu', 'Marriage sheet') . '</span>',
                ['order/get-route-sheet'],
                [
                    'class'       => 'btn btn-sm btn-default',
                    'data-href'   => Url::to(['menu/get-marriage-sheet']),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]) ?></h4>
            <p>Получить бракеражный журнал</p>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="report-item text-center">

            <div class="text-center">
                <img src="/images/truck.svg" height="90px;" />
            </div>
            <h4><?= Html::a('<span>Маршрутный лист</span>',
                    ['order/get-route-sheet'],
                    [
                        'class'       => 'btn btn-sm btn-default',
                        'data-href'   => Url::to(['order/get-route-sheet']),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                    ]); ?></h4>
            <p>Список адресов для заказов с доставкой на дату</p>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="report-item text-center">

            <div class="text-center">
                <img src="/images/customer.svg" height="90px;" />
            </div>
            <h4><?php echo \yii\helpers\Html::a(
                    '<span>' . \Yii::t('menu', 'Customer sheet') . '</span>',
                    ['/order/get-customer-sheet', 'id' => 0],
                    [
                        'class'       => 'btn btn-default',
                        'data-href'   => \yii\helpers\Url::to(['/order/get-customer-sheet', 'id' => 0]),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                    ]) ?>
            </h4>
            <p>Меню для покупателя на конкретную дату</p>
        </div>
    </div>
</div>


