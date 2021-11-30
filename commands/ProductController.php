<?php

namespace app\commands;

use app\models\Repository\Dish;
use app\models\Repository\Menu;
use app\models\Repository\Order;
use app\models\Repository\OrderSchedule;
use yii\base\Controller;

class ProductController extends Controller
{
    /**
     * Списание остатков меню
     *
     * @throws \Exception
     */
    public function actionWriteOffBalance()
    {
        $yesterday = time() - 86400;
        $date = date('Y-m-d', $yesterday);

        \Yii::info("Начинаем процесс обновления остатков", 'write-off-product');
        $menu = Menu::find()
                    ->where(['menu_end_date' => $date])
                    ->andWhere(['status' => Menu::STATUS_ACTIVE])
                    ->one();

        if (!$menu) {
            \Yii::info("Нет подходящего меню на конечную дату " . $date, 'write-off-product');
            return;
        }

        \Yii::info("Нашли меню с датами: " . $menu->menu_start_date . "-" . $menu->menu_end_date, 'write-off-product');
        $schedules = OrderSchedule::find()
                                  ->where(['>=', 'date', $menu->menu_start_date])
                                  ->andWhere(['<=', 'date', $menu->menu_end_date])
                                  ->all();

        \Yii::info("Нашли " . count($schedules) . "расписаний блюд", 'write-off-product');
        $products = [];
        foreach ($schedules as $schedule) {
            // Если заказ заархивирован просто пропускаем его
            if ($schedule->order->status_id == Order::STATUS_ARCHIVED) {
                continue;
            }

            foreach ($schedule->dishes as $scheduleDish) {
                if (!$schedule->order->subscription->has_breakfast && $scheduleDish->ingestion_type == Dish::INGESTION_TYPE_BREAKFAST) {
                    continue;
                } elseif (!$schedule->order->subscription->has_lunch && $scheduleDish->ingestion_type == Dish::INGESTION_TYPE_LUNCH) {
                    continue;
                } elseif (!$schedule->order->subscription->has_supper && $scheduleDish->ingestion_type == Dish::INGESTION_TYPE_SUPPER) {
                    continue;
                }

                if (empty($scheduleDish->dish)) {
                    // Тут нечего не делаем раз нет назначенного блюда то и списывать нечего
                    // throw new \LogicException('Имеются не назначенные блюда в меню для заказа '.$schedule->order->id.'.');
                } else {
                    foreach ($scheduleDish->dish->dishProducts as $dishProduct) {
                        if (empty($products[$dishProduct->product_id])) {
                            $product = $dishProduct->product;
                            $product->setNeedCount($dishProduct->weight);
                            $products[$dishProduct->product_id] = $product;
                        } else {
                            $products[$dishProduct->product_id]->setNeedCount($dishProduct->weight);
                        }
                    }
                }
            }
        }

        if (!empty($products)) {
            $transaction = \Yii::$app->db->beginTransaction();
            foreach ($products as $product) {
                \Yii::info("Продукт: " . $product->name . " Было на складе: " . $product->count, 'write-off-product');
                $product->count = $product->count - $product->getNeedCount();
                if (!$product->save(false)) {
                    $transaction->rollBack();
                    \Yii::info("Не удалось обновить остатки", 'write-off-product');
                }
                \Yii::info("Продукт: " . $product->name . " Стало на складе: " . $product->count, 'write-off-product');
            }
            $transaction->commit();

            \Yii::info("Все остатки обновлены", 'write-off-product');
        } else {
            \Yii::info("Нет подходящих продуктов", 'write-off-product');
        }
    }
}