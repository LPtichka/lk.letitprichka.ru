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
 * @property float $cost
 * @property int $address_id
 * @property string $date
 * @property string $pickup_date
 * @property string $comment
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Order $order
 * @property Address $address
 * @property OrderScheduleDish[] $dishes
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

    const STATUS_NAMES = [
        self::STATUS_NEW => 'new',
        self::STATUS_DELETED => 'deleted',
        self::STATUS_COMPLETED => 'completed',
        self::STATUS_FREEZED => 'freezed',
        self::STATUS_DELAYED => 'delayed',
    ];

    const EDITABLE_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_FREEZED,
        self::STATUS_DELAYED,
    ];

    const BASE_INTERVAL = '08:00 - 10:00';

    const INTERVALS = [
        '08:00 - 10:00' => '08:00 - 10:00',
        '09:00 - 11:00' => '09:00 - 11:00',
        '10:00 - 12:00' => '10:00 - 12:00',
    ];

    const INGESTION_CONTENT = [
        Dish::INGESTION_TYPE_BREAKFAST => [],
        Dish::INGESTION_TYPE_DINNER => [
            Dish::TYPE_FIRST,
            Dish::TYPE_SECOND,
        ],
        Dish::INGESTION_TYPE_LUNCH => [],
        Dish::INGESTION_TYPE_SUPPER => [
            Dish::TYPE_SECOND,
        ],
    ];

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
            [['order_id', 'status', 'address_id'], 'integer'],
            [['cost'], 'number'],
            [['interval', 'date', 'pickup_date', 'comment'], 'string'],
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

    /**
     * @return array
     */
    public function getIntervals(): array
    {
        return self::INTERVALS;
    }

    /**
     * @return string
     */
    public function getStatusKey(): string
    {
        return self::STATUS_NAMES[$this->status];
    }

    /**
     * @return bool
     */
    public function isEditable(): bool
    {
        return in_array($this->status, self::EDITABLE_STATUSES);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(Address::class, ['id' => 'address_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDishes()
    {
        return $this->hasMany(OrderScheduleDish::class, ['order_schedule_id' => 'id']);
    }

    /**
     * @param OrderScheduleDish[] $orderScheduleDishes
     */
    public function setDishes(array $orderScheduleDishes)
    {
        $this->dishes = $orderScheduleDishes;
    }
}