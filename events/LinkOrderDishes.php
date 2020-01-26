<?php
namespace app\events;

use app\models\Repository\Menu;
use app\models\Repository\Order;
use app\models\Repository\OrderSchedule;
use app\models\Repository\OrderScheduleDish;
use yii\base\Event;

class LinkOrderDishes extends Event
{
    const EVENT_MENU_CREATED = 'menu_created';

    /** @var int */
    private $menuID;

    /**
     * @param int $menuID
     */
    public function setMenuID(int $menuID): void
    {
        $this->menuID = $menuID;
    }

    /**
     * @return $this
     */
    public function prepareEvent()
    {
        \Yii::$app->on(LinkOrderDishes::EVENT_MENU_CREATED, function (LinkOrderDishes $event){
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
        $menu = Menu::find()
            ->with(['dishes'])
            ->where(['menu.id' => $this->menuID])
            ->asArray()
            ->one();

        $startDay = $menu['menu_start_date'];
        $endDay   = $menu['menu_end_date'];
        $menu     = (new Menu())->prepareMenuData($menu);

        $orderSchedules = OrderSchedule::find()
            ->with(['dishes'])
            ->where(['>=', 'date', $startDay])
            ->andWhere(['<=', 'date', $endDay])
            ->orderBy(['date' => SORT_ASC])
            ->asArray()
            ->all();

        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($orderSchedules as $schedule) {
            $order = Order::findOne($schedule['order_id']);
            foreach ($schedule['dishes'] as $dish) {
                // Сгенерируем ключ меню
                $key                = sprintf('%s-%d-%d', $schedule['date'], $dish['ingestion_type'], $dish['type'] ?? 0);
                $orderExceptionList = $order->getExceptionList();

                // В меню должен быть элемент с этим ключом
                if (!empty($menu[$key])) {
                    foreach ($menu[$key] as $menuDish) {
                        $dishException = $menuDish['exception_list'];
                        $array         = array_intersect($dishException, $orderExceptionList);
                        // В блюде не должно быть пересекающихся исключений
                        if (empty($array)) {
                            $orderScheduleDish          = OrderScheduleDish::findOne($dish['id']);
                            $orderScheduleDish->dish_id = $menuDish['dish_id'];
                            if (!$orderScheduleDish->save()) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                }
            }
        }
        $transaction->commit();
        return true;
    }
}
