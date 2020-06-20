<?php
namespace app\events;

use app\models\Repository\Dish;
use app\models\Repository\MenuDish;
use app\models\Repository\Order;
use app\models\Repository\Subscription;
use yii\base\Event;

class OrderCompleted extends Event
{
    const EVENT_ORDER_COMPLETED = 'order_completed';

    /**
     * @return $this
     */
    public function prepareEvent()
    {
        \Yii::$app->on(OrderCompleted::EVENT_ORDER_COMPLETED, function (OrderCompleted $event){
            $event->updateOrderStatuses();
        });

        return $this;
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function updateOrderStatuses()
    {
        $orders = \app\models\Repository\Order::find()->select(['order.id', 'MAX(order_schedule.date) as date'])
            ->leftJoin('order_schedule', 'order.id = order_schedule.order_id')
            ->where(['order.status_id' => Order::STATUS_PROCESSED])
            ->groupBy('order.id')
            ->asArray()
            ->all();

        $nowDate = time();
        foreach ($orders as $order) {
            if (strtotime($order['date']) < $nowDate) {
                $orderData = Order::findOne($order['id']);
                $orderData->status_id = Order::STATUS_COMPLETED;
                if ($orderData->save(false)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
}
