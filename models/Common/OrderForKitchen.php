<?php declare(strict_types=1);
namespace app\models\Common;

use app\models\Helper\Phone;
use app\models\Repository\Dish;
use app\models\Repository\OrderSchedule;
use yii\base\Model;

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
            foreach ($order as $item) {
                if ($item['type'] == '1-0') {
                    $item['type'] = 'Завтрак' . ($breakfastCount > 1 ? $breakfastCount : '');
                    $result[] = $item;
                    $breakfastCount++;
                }
            }
            if ($breakfastCount > 1) {
                $result[] = [
                    'type' => '',
                    'name' => '',
                    'count' => '',
                    'comment' => '',
                ];
            }
            $soupCount = 1;
            foreach ($order as $item) {
                if ($item['type'] == '2-1') {
                    $item['type'] = 'Суп' . ($soupCount > 1 ? $soupCount : '');
                    $result[] = $item;
                    $soupCount++;
                }
            }
            if ($soupCount > 1) {
                $result[] = [
                    'type' => '',
                    'name' => '',
                    'count' => '',
                    'comment' => '',
                ];
            }
            $dinnerCount = 1;
            foreach ($order as $item) {
                if ($item['type'] == '2-2') {
                    $item['type'] = 'Обед' . ($dinnerCount > 1 ? $dinnerCount : '');
                    $result[] = $item;
                    $dinnerCount++;
                }
            }
            if ($dinnerCount > 1) {
                $result[] = [
                    'type' => '',
                    'name' => '',
                    'count' => '',
                    'comment' => '',
                ];
            }
            $lunchCount = 1;
            foreach ($order as $item) {
                if ($item['type'] == '3-0') {
                    $item['type'] = 'Перекус' . ($lunchCount > 1 ? $lunchCount : '');
                    $result[] = $item;
                    $lunchCount++;
                }
            }
            if ($lunchCount > 1) {
                $result[] = [
                    'type' => '',
                    'name' => '',
                    'count' => '',
                    'comment' => '',
                ];
            }
            $supperCount = 1;
            foreach ($order as $item) {
                if ($item['type'] == '4-2') {
                    $item['type'] = 'Ужин' . ($supperCount > 1 ? $supperCount : '');
                    $result[] = $item;
                    $supperCount++;
                }
            }
        }
        return $result;
    }
}