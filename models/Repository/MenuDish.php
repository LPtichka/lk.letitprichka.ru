<?php

namespace app\models\Repository;

use app\models\Common\Ingestion;
use app\models\Common\MarriageDish;
use app\models\Queries\MenuDishQuery;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%menu_dish}}".
 *
 * @property int $id
 * @property int $is_main
 * @property string $date
 * @property int $menu_id
 * @property int $dish_id
 * @property int $dish_type
 * @property int $ingestion_type
 * @property int $ingestion
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Dish $dish
 */
class MenuDish extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu_dish}}';
    }

    /**
     * @inheritdoc
     * @return MenuDishQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuDishQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dish_id', 'menu_id', 'ingestion', 'ingestion_type', 'dish_type', 'is_main'], 'integer'],
            [['date'], 'string'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDish()
    {
        return $this->hasOne(Dish::class, ['id' => 'dish_id']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @param string $date
     * @return array
     */
    public function getMarriageForDate(string $date): array
    {
        $result = [];
        $time = strtotime($date);
        $orderSchedules = OrderSchedule::find()
                                       ->where(['order_schedule.date' => date('Y-m-d', $time)])
                                       ->leftJoin(
                                           'order_schedule_dish',
                                           ['order_schedule.id' => 'order_schedule_dish.order_schedule_id']
                                       )
                                       ->orderBy(['order_schedule_dish.ingestion_type' => SORT_ASC])
                                       ->all();

        $time = date("H:i", time());
        $temp = [];
        foreach ($orderSchedules as $orderSchedule) {
            foreach ($orderSchedule->dishes as $orderScheduleDish) {
                if ($orderScheduleDish->dish_id == null) {
                    continue;
                }

                if (in_array($orderScheduleDish->dish_id, $temp)) {
                    continue;
                }

                if (!empty($orderScheduleDish->type)) {
                    $ingestionName = (new Ingestion())->getIngestionName($orderScheduleDish->ingestion_type, $orderScheduleDish->type);
                } else {
                    $ingestionName = (new Ingestion())->getIngestionName($orderScheduleDish->ingestion_type);
                }

                $marriageDish = new MarriageDish($time, $ingestionName, $orderScheduleDish->dish->name);
                $marriageDish->setWeight($orderScheduleDish->dish->weight);
                $marriageDish->setResult('проба снята, разрешено к выдаче');
                $temp[] = $orderScheduleDish->dish_id;
                $result[] = $marriageDish;

                if ($orderScheduleDish->with_garnish && $orderScheduleDish->garnish_id && !in_array($orderScheduleDish->garnish_id, $temp)) {
                    $marriageDish = new MarriageDish($time, $ingestionName, $orderScheduleDish->garnish->name);
                    $marriageDish->setWeight($orderScheduleDish->garnish->weight);
                    $marriageDish->setResult('проба снята, разрешено к выдаче');
                    $temp[] = $orderScheduleDish->garnish_id;
                    $result[] = $marriageDish;
                }
            }

        }

        $res = [];
        foreach ($result as $item) {
            if ($item->getType() == 'завтрак') {
                $res[] = $item;
            }
        }
        foreach ($result as $item) {
            if ($item->getType() == 'обед первое') {
                $res[] = $item;
            }
        }
        foreach ($result as $item) {
            if ($item->getType() == 'обед второе') {
                $res[] = $item;
            }
        }
        foreach ($result as $item) {
            if ($item->getType() == 'перекус') {
                $res[] = $item;
            }
        }
        foreach ($result as $item) {
            if ($item->getType() == 'ужин второе') {
                $res[] = $item;
            }
        }

        return $res;
    }
}