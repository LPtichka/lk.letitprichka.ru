<?php declare(strict_types=1);
namespace app\models\Common;

use app\models\Helper\Phone;
use app\models\Repository\Dish;
use app\models\Repository\OrderSchedule;
use app\models\Repository\OrderScheduleDish;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class OrderForKitchen - Заказ для кухни
 *
 * @package app\models\Common
 */
class OrderForKitchen extends Model
{
    /** @var string */
    private $date;

    /**
     * @param string $fio
     * @param string $address
     * @param string $phone
     * @param array $config
     */
    public function __construct(string $date, array $config = [])
    {
        $this->date = $date;

        parent::__construct($config);
    }

    public function getOrderForKitchen(): array
    {
        $order = [];
        $result = [];
        $orderSchedules = OrderSchedule::find()->where(['date' => $this->date])->all();
        foreach ($orderSchedules as $orderSchedule) {
            foreach ($orderSchedule->dishes as $scheduleDish) {
                if (!$scheduleDish->dish_id) {
                    continue;
                }
                $key = sprintf('%d-%d-%d-%d',
                               $scheduleDish->ingestion_type,
                               (int) $scheduleDish->type,
                               $scheduleDish->dish_id,
                               (int)$scheduleDish->garnish_id
                );

                if (!$orderSchedule->order->subscription->has_breakfast && $scheduleDish->ingestion_type == Dish::INGESTION_TYPE_BREAKFAST) {
                    continue;
                } elseif (!$orderSchedule->order->subscription->has_lunch && $scheduleDish->ingestion_type == Dish::INGESTION_TYPE_LUNCH) {
                    continue;
                } elseif (!$orderSchedule->order->subscription->has_supper && $scheduleDish->ingestion_type == Dish::INGESTION_TYPE_SUPPER) {
                    continue;
                }

                if (empty($order[$key])) {
                    // Если в результате еще нет такого ключа то значит создаем
                    $type = sprintf('%d-%d',
                                    $scheduleDish->ingestion_type,
                                    (int) $scheduleDish->type
                    );
                    $name = $scheduleDish->dish->name;
                    if ($scheduleDish->with_garnish) {
                        $name = $name . " + " . $scheduleDish->garnish->name;
                    }
                    $order[$key] = [
                        'type' => $type,
                        'dish' => $name,
                        'count' => 1,
                        'comment' => ''
                    ];
                } else {
                    // Если есть ключ то нужно просто инкрементить значение
                    $order[$key]['count']++;
                }
            }
        }

        if (!empty($order)) {
            $breakfastCount = 1;
            $breakfasts = [];
            foreach ($order as $item) {
                if ($item['type'] == '1-0') {
                    $item['type'] = 'Завтрак' . ($breakfastCount > 1 ? $breakfastCount : '');
                    $breakfasts[] = $item;
                    $breakfastCount++;
                }
            }
            ArrayHelper::multisort($breakfasts, ['count', 'dish'], [SORT_DESC, SORT_ASC]);
            if ($breakfastCount > 1) {
                $breakfasts[] = [
                    'type' => '',
                    'name' => '',
                    'count' => '',
                    'comment' => '',
                ];
            }
            $result = array_merge($result, $breakfasts);

            $soupCount = 1;
            $soups = [];
            foreach ($order as $item) {
                if ($item['type'] == '2-1') {
                    $item['type'] = 'Суп' . ($soupCount > 1 ? $soupCount : '');
                    $soups[] = $item;
                    $soupCount++;
                }
            }
            ArrayHelper::multisort($soups, ['count', 'dish'], [SORT_DESC, SORT_ASC]);
            if ($soupCount > 1) {
                $soups[] = [
                    'type' => '',
                    'name' => '',
                    'count' => '',
                    'comment' => '',
                ];
            }
            $result = array_merge($result, $soups);

            $dinnerCount = 1;
            $dinners = [];
            foreach ($order as $item) {
                if ($item['type'] == '2-2') {
                    $item['type'] = 'Обед' . ($dinnerCount > 1 ? $dinnerCount : '');
                    $dinners[] = $item;
                    $dinnerCount++;
                }
            }
            ArrayHelper::multisort($dinners, ['count', 'dish'], [SORT_DESC, SORT_ASC]);
            if ($dinnerCount > 1) {
                $dinners[] = [
                    'type' => '',
                    'name' => '',
                    'count' => '',
                    'comment' => '',
                ];
            }
            $result = array_merge($result, $dinners);

            $lunchs = [];
            $lunchCount = 1;
            foreach ($order as $item) {
                if ($item['type'] == '3-0') {
                    $item['type'] = 'Перекус' . ($lunchCount > 1 ? $lunchCount : '');
                    $lunchs[] = $item;
                    $lunchCount++;
                }
            }
            ArrayHelper::multisort($lunchs, ['count', 'dish'], [SORT_DESC, SORT_ASC]);
            if ($lunchCount > 1) {
                $lunchs[] = [
                    'type' => '',
                    'name' => '',
                    'count' => '',
                    'comment' => '',
                ];
            }
            $result = array_merge($result, $lunchs);

            $suppers = [];
            $supperCount = 1;
            foreach ($order as $item) {
                if ($item['type'] == '4-2') {
                    $item['type'] = 'Ужин' . ($supperCount > 1 ? $supperCount : '');
                    $suppers[] = $item;
                    $supperCount++;
                }
            }
            ArrayHelper::multisort($suppers, ['count', 'dish'], [SORT_DESC, SORT_ASC]);
            $result = array_merge($result, $suppers);
        }
        return $result;
    }
}