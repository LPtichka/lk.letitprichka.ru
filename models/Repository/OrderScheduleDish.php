<?php

namespace app\models\Repository;

use app\models\Queries\OrderScheduleDishQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%order_schedule_dish}}".
 *
 * @property int $id
 * @property int $order_schedule_id
 * @property int $dish_id
 * @property int $ingestion_type
 * @property int $type
 * @property int $manufactured_at
 * @property string $storage_condition
 * @property int $created_at
 * @property int $updated_at
 */
class OrderScheduleDish extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_schedule_dish}}';
    }

    /**
     * @inheritdoc
     * @return OrderScheduleDishQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderScheduleDishQuery(get_called_class());
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
            ['ingestion_type', 'in', 'range' => (new Dish())->getIngestionTypes()],
            ['dish_id', 'exist', 'targetClass' => Dish::class, 'targetAttribute' => 'id', 'message' => 'Указан не существующий ID блюда'],
            ['order_schedule_id', 'exist', 'targetClass' => OrderSchedule::class, 'targetAttribute' => 'id', 'message' => 'Указан не существующий ID расписания'],
            ['storage_condition', 'string'],
            ['manufactured_at', 'integer'],
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