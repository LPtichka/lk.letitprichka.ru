<?php

namespace app\models\Repository;

use app\models\Helper\Weight;
use app\models\Queries\OrderQuery;
use app\models\Queries\ProductQuery;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property int $id
 * @property int $status_id
 * @property int $payment_type
 * @property int $customer_id
 * @property int $subscription_id
 * @property int $address_id
 * @property int $count
 * @property int $total
 * @property int $cutlery
 * @property bool $cash_machine
 * @property string $comment
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Customer $customer
 * @property Address $address
 * @property Exception[] $exceptions
 * @property OrderSchedule[] $schedules
 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_PROCESSED = 2;
    const STATUS_CANCELED = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_DEFERRED = 5;

    const STATUSES = [
        self::STATUS_CANCELED,
        self::STATUS_PROCESSED,
        self::STATUS_DEFERRED,
        self::STATUS_COMPLETED,
        self::STATUS_NEW,
    ];

    const STATUS_MAP = [
        self::STATUS_NEW       => [
            self::STATUS_CANCELED,
            self::STATUS_PROCESSED,
        ],
        self::STATUS_PROCESSED => [
            self::STATUS_COMPLETED,
            self::STATUS_CANCELED,
            self::STATUS_DEFERRED,
        ],
        self::STATUS_DEFERRED  => [
            self::STATUS_COMPLETED,
            self::STATUS_PROCESSED,
            self::STATUS_CANCELED,
        ],
    ];

    public $isNewCustomer = false;
    public $scheduleFirstDate;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     * @return OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderQuery(get_called_class());
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
            [['status_id', 'payment_type', 'cutlery', 'count', 'total', 'address_id'], 'integer'],
            [['status_id'], 'in', 'range' => self::STATUSES],
            [['cash_machine'], 'boolean'],
            [['comment'], 'string'],
            [['status_id'], 'default', 'value' => self::STATUS_NEW],
            ['payment_type', 'exist', 'targetClass' => PaymentType::class, 'targetAttribute' => 'id'],
            ['subscription_id', 'exist', 'targetClass' => Subscription::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * Получить возможные переводы в статусы
     *
     * @return array
     */
    public function getAvailableStatusTransfer(): array
    {
        return self::STATUS_MAP[$this->status] ?? [];
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
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }
    /**
     * @param Address $address
     */
    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    /**
     * @param Exception[] $exceptions
     */
    public function setExceptions(array $exceptions): void
    {
        $this->exceptions = $exceptions;
    }

    /**
     * @param OrderSchedule[] $schedules
     */
    public function setSchedules(array $schedules): void
    {
        $this->schedules = $schedules;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
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
    public function getSchedules()
    {
        return $this->hasMany(OrderSchedule::class, ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getExceptions()
    {
        return $this->hasMany(Exception::class, ['id' => 'exception_id'])->viaTable('{{%order_exception}}', ['order_id' => 'id']);
    }

    /**
     * Собрать заказ из массива POST
     *
     * @param array $data
     * @return bool
     */
    public function build(array $data): bool
    {
        $this->load($data);
        $subscriptionDiscount = SubscriptionDiscount::find()
            ->where(['subscription_id' => $this->subscription_id])
            ->andWhere(['<=', 'count', $this->count])
            ->orderBy(['count' => SORT_DESC])
            ->limit(1)
            ->one();
        $this->total = $subscriptionDiscount->price * $this->count;

        $customer = new Customer();
        $customer->load($data);
        $this->setCustomer($customer);

        $address = new Address();
        $address->load($data);
        $customer->setAddresses([$address]);

        $exceptions = [];
        foreach ($data['Exception'] as $exception) {
            $exceptions[] = Exception::findOne($exception['id']);
        }
        $this->setExceptions($exceptions);

        $scheduleFirstDate = $data['Order']['scheduleFirstDate'] ?? null;
        $schedules = [];
        if ($scheduleFirstDate !== null) {
            $firstDateTime = strtotime($scheduleFirstDate);
            for ($i = 0; $i < $data['Order']['count']; $i++) {
                $schedule = new OrderSchedule();

                $schedule->date = date('Y-m-d', $firstDateTime + $i * 86400);
                $schedule->cost = $subscriptionDiscount->price;
                $schedule->address_id = $address->id;
                $schedule->order_id = $this->id;

                $schedules[] = $schedule;
            }
        }
        $this->setSchedules($schedules);

        return true;
    }

    /**
     * Валидация всех связанных объектов
     *
     * @return bool
     */
    public function validateAll(): bool
    {
        return false;
    }

    /**
     * Сохранение всех связанных объектов
     *
     * @return bool
     * @throws \yii\db\Exception
     */
    public function saveAll(): bool
    {
        $transaction = \Yii::$app->db->beginTransaction();
        if (!$this->customer->validate() || !$this->customer->save()) {
            $transaction->rollBack();
            return false;
        }
        foreach ($this->customer->addresses as $address) {
            $address->customer_id = $this->customer->id;
            if (!$address->validate() || !$address->save()) {
                $transaction->rollBack();
                return false;
            }
        }
        $this->customer_id = $this->customer->id;
        $this->address_id = $this->customer->addresses[0]->id;
        if (!$this->save()) {
            $transaction->rollBack();
            return false;
        }

        foreach ($this->exceptions as $exception) {
            $oException = new OrderException();
            $oException->order_id = $this->id;
            $oException->exception_id = $exception->id;
            if (!$oException->validate() || !$oException->save()) {
                $transaction->rollBack();
                return false;
            }
        }

        foreach ($this->schedules as $schedule) {
            $schedule->order_id = $this->id;
            $schedule->address_id = $this->address_id;
            if (!$schedule->validate() || !$schedule->save()) {
                $transaction->rollBack();
                return false;
            }
        }

        $transaction->commit();
        return true;
    }
}