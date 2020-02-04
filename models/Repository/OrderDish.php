<?php

namespace app\models\Repository;

use app\models\Queries\OrderDishQuery;
use app\models\Queries\OrderScheduleQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%order_dish}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int $dish_id
 * @property int $price
 * @property int $count
 * @property int $created_at
 * @property int $updated_at
 */
class OrderDish extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_dish}}';
    }

    /**
     * @inheritdoc
     * @return OrderDishQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderDishQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'dish_id', 'count', 'price'], 'integer'],
            [['order_id'], 'exist', 'targetClass' => Order::class, 'targetAttribute' => 'id', 'message' => 'Указан не существующий заказ'],
            [['dish_id'], 'exist', 'targetClass' => Dish::class, 'targetAttribute' => 'id', 'message' => 'Указан не существующее товар'],
        ];
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
}