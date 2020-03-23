<?php
namespace app\events;

use app\models\Repository\MenuDish;
use app\models\Repository\Order;
use app\models\Repository\Subscription;
use yii\base\Event;

class OrderCreated extends Event
{
    const EVENT_ORDER_CREATED = 'order_created';

    /** @var int */
    private $orderId;

    /**
     * @param int $orderId
     */
    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @return $this
     */
    public function prepareEvent()
    {
        \Yii::$app->on(OrderCreated::EVENT_ORDER_CREATED, function (OrderCreated $event){
            $event->linkOrderDishes();
        });

        return $this;
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function linkOrderDishes()
    {
        // TODO добавить логирование
        $order              = Order::findOne($this->orderId);
        $orderExceptionList = $order->getExceptionList();
        if (!$order) {
            return false;
        }

        if ($order->subscription_id != Subscription::NO_SUBSCRIPTION_ID) {
            foreach ($order->schedules as $schedule) {
                $date = $schedule->date;
                $menuDish = MenuDish::find()->where(['date' => $date])->all();
                foreach ($menuDish as $mDish) {
                    $dishException  = $mDish->dish->getExceptionList();
                    $crossException = array_intersect($dishException, $orderExceptionList);

                    if (empty($crossException)) {
                        foreach ($schedule->dishes as $dish) {
                            if (!empty($dish->dish_id)) {
                                continue;
                            }

                            if ($dish->ingestion_type == $mDish->ingestion_type && $dish->type == $mDish->dish_type) {
                                $dish->dish_id = $mDish->dish_id;
                                $dish->save(false);
                            }
                        }
                    }
                }
            }
        }
    }
}
