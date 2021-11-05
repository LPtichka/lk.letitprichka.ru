<?php
namespace app\events;

use app\models\Repository\Dish;
use app\models\Repository\Menu;
use app\models\Repository\Order;
use app\models\Repository\OrderSchedule;
use app\models\Repository\OrderScheduleDish;
use app\models\Repository\Subscription;
use yii\base\Event;

class MenuCreated extends Event
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
        \Yii::$app->on(MenuCreated::EVENT_MENU_CREATED, function (MenuCreated $event){
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
        $this->log('menu-create', ["======================================================"]);
        // TODO добавить логирование
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
            if ($order->status_id == Order::STATUS_ARCHIVED) {
                continue;
            }
            if ($order->subscription_id != Subscription::NO_SUBSCRIPTION_ID) {
                foreach ($schedule['dishes'] as $dish) {
                    // Сгенерируем ключ меню
                    $key                = sprintf('%s-%d-%d', $schedule['date'], $dish['ingestion_type'], $dish['type'] ?? 0);
                    $garnishKey         = sprintf('%s-%d-%d', $schedule['date'], $dish['ingestion_type'], Dish::TYPE_GARNISH);
                    $orderExceptionList = $order->getExceptionList();
                    $isSettedInSchedule = false;
                    // В меню должен быть элемент с этим ключом
                    if (!empty($menu[$key])) {
                        // Применим только регулярное меню

                        foreach ($menu[$key] as $menuDish) {
                            // Если меню не регулярное просто пропустим его
                            if (!$menuDish['is_main']) {
                                continue;
                            }

                            try {
                                $isSettedInSchedule = $this->setDishInSchedule(
                                    $menu,
                                    $menuDish,
                                    $dish,
                                    $orderExceptionList,
                                    $garnishKey
                                );
                            } catch (\Exception $e) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        // Применим остальное
                        foreach ($menu[$key] as $menuDish) {
                            // Если меню регулярное и уже назначено просто пропустим его
                            if ($menuDish['is_main'] || $isSettedInSchedule) {
                                continue;
                            }

                            try {
                                $isSettedInSchedule = $this->setDishInSchedule(
                                    $menu,
                                    $menuDish,
                                    $dish,
                                    $orderExceptionList,
                                    $garnishKey
                                );
                            } catch (\Exception $e) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                }
            }
        }
        $transaction->commit();
        $this->log('menu-create', ["======================================================"]);
        return true;
    }

    /**
     * @throws \Exception
     */
    private function setDishInSchedule(
        array $menu,
        array $menuDish,
        array $dish,
        array $orderExceptionList,
        string $garnishKey
    ): bool {
        $array = [];
        $dishException = $menuDish['exception_list'];
        $array         = array_intersect($dishException, $orderExceptionList);

        $this->log('menu-create', [
            $dishException,
            $orderExceptionList,
            $array,
            "Тут пересечение исключений",
        ]);

        // В блюде не должно быть пересекающихся исключений
        if (empty($array)) {
            $orderScheduleDish               = OrderScheduleDish::findOne($dish['id']);
            $orderScheduleDish->dish_id      = $menuDish['dish_id'];
            $orderScheduleDish->name         = $menuDish['name'];
            $orderScheduleDish->with_garnish = $menuDish['with_garnish'];

            if ($menuDish['with_garnish']) {
                $this->log('menu-create', [
                    $menuDish,
                    "Это блюдо идет с гарниром",
                ]);
                if (!empty($menu[$garnishKey])) {
                    foreach ($menu[$garnishKey] as $menuGarnish) {
                        $garnishException = $menuGarnish['exception_list'];
                        $gArray           = array_intersect($garnishException, $orderExceptionList);
                        if (empty($gArray)) {
                            $orderScheduleDish->garnish_id = $menuGarnish['dish_id'];
                            break;
                        }
                    }
                }
            }

            if (!$orderScheduleDish->save()) {
                throw new \Exception("Not saved Schedule dish");
            }
            return true;
        }

        return false;
    }

    /**
     * @param string $messageType
     * @param array $params
     */
    protected function log(string $messageType, array $params = []): void
    {
        \Yii::info($params, $messageType);
    }
}
