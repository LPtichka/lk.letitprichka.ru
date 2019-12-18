<?php

namespace app\models\Repository;

use app\models\Queries\OrderScheduleQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%order_schedule}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int $status
 * @property string $interval
 * @property int $cost
 * @property int $address_id
 * @property string $date
 * @property string $comment
 * @property int $created_at
 * @property int $updated_at
 */
class OrderSchedule extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_DELETED = 0;
    const STATUS_COMPLETED = 10;
    const STATUS_FREEZED = 2;
    const STATUS_DELAYED = 3;

    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_DELETED,
        self::STATUS_COMPLETED,
        self::STATUS_FREEZED,
        self::STATUS_DELAYED,
    ];

    const BASE_INTERVAL = '10:00 - 19:00';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_schedule}}';
    }

    /**
     * @inheritdoc
     * @return OrderScheduleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderScheduleQuery(get_called_class());
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
            [['order_id', 'cost', 'status', 'address_id'], 'integer'],
            [['interval', 'date', 'comment'], 'string'],
            [['address_id'], 'exist', 'targetClass' => Address::class, 'targetAttribute' => 'id', 'message' => 'Указан не существующий адрес'],
            [['status'], 'in', 'range' => self::STATUSES],
            [['status'], 'default', 'value' => self::STATUS_NEW],
            [['interval'], 'default', 'value' => self::BASE_INTERVAL],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
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