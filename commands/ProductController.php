<?php

namespace app\commands;

use app\models\Repository\Dish;
use app\models\Repository\Menu;
use app\models\Repository\Order;
use app\models\Repository\OrderSchedule;
use Helper\Unit;
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
//        $date = "2022-01-14";
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
        $i = 0;
        foreach ($schedules as $schedule) {
            // Если заказ заархивирован просто пропускаем его
            if ($schedule->order->status_id == Order::STATUS_ARCHIVED) {
                continue;
            }

            echo "----------------------------------------------------------- \n";
            echo "Обработали заказа ".$schedule->order_id." дату: ".$schedule->date." \n";

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
                    echo "----------------------------------------------------------- \n";
                    echo "Обработали блюдо ".$scheduleDish->dish->name."  \n";
                    $i++;
                    foreach ($scheduleDish->dish->dishProducts as $dishProduct) {
                        echo "Обработали продукт ".$dishProduct->name."  \n";
                        if (empty($products[$dishProduct->product_id])) {
                            $product = $dishProduct->product;
                            // Списываем брутто: Должен списываться брутто грязный вес, От куриного филе взяли грязный вес и запекли
                            // Кабачки взяли грязный вес, почистили и приготовили
                            $product->setNeedCount($dishProduct->brutto);
                            $products[$dishProduct->product_id] = $product;
                        } else {
                            $products[$dishProduct->product_id]->setNeedCount($dishProduct->brutto);
                        }
                    }
                    if ($scheduleDish->with_garnish && $scheduleDish->garnish_id) {
                        echo "----------------------------------------------------------- \n";
                        echo "Обработали блюдо ".$scheduleDish->garnish->name."  \n";
                        foreach ($scheduleDish->garnish->dishProducts as $dishProduct) {
                            echo "Обработали продукт ".$dishProduct->name."  \n";
                            if (empty($products[$dishProduct->product_id])) {
                                $product = $dishProduct->product;
                                // Списываем брутто: Должен списываться брутто грязный вес, От куриного филе взяли грязный вес и запекли
                                // Кабачки взяли грязный вес, почистили и приготовили
                                $product->setNeedCount($dishProduct->brutto);
                                $products[$dishProduct->product_id] = $product;
                            } else {
                                $products[$dishProduct->product_id]->setNeedCount($dishProduct->brutto);
                            }
                        }
                    }
                }
            }
        }

        echo "Всего блюд задействованно: ".$i."\n";

        if (!empty($products)) {
            $transaction = \Yii::$app->db->beginTransaction();
            foreach ($products as $product) {
//                \Yii::info("Продукт: " . $product->name . " Было на складе: " . $product->count, 'write-off-product');
                $product->count = $product->count - $product->getNeedCount();
                if (!$product->save(false)) {
                    $transaction->rollBack();
                    \Yii::info("Не удалось обновить остатки", 'write-off-product');
                }
                $formattedCounts = (new \app\models\Helper\Unit($product->unit))->format($product->getNeedCount());
                \Yii::info("Продукт: " . $product->name . " Списали: " . $formattedCounts, 'write-off-product');
//                \Yii::info("Продукт: " . $product->name . " Стало на складе: " . $product->count, 'write-off-product');
            }
            $transaction->commit();

            \Yii::info("Все остатки обновлены", 'write-off-product');
        } else {
            \Yii::info("Нет подходящих продуктов", 'write-off-product');
        }
    }
}