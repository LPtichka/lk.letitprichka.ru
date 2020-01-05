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
 * @property string $shop_order_number
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
        return [
            'subscription_id' => \Yii::t('order', 'Subscription ID'),
        ];
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
            [['comment', 'shop_order_number'], 'string'],
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
        $this->total = $subscriptionDiscount->price;

        if (empty($data['Order']['customer_id']) || !empty($data['Order']['isNewCustomer'])) {
            $customer = new Customer();
            $customer->load($data);
            $this->setCustomer($customer);
        }

        if (empty($data['Order']['address_id'])) {
            $address = new Address();
            $address->load($data);
            $this->customer->setAddresses([$address]);
        }

        $exceptions = [];
        foreach ($data['Exception'] as $exception) {
            if (empty($exception['id'])) {
                continue;
            }
            $exceptions[] = Exception::findOne($exception['id']);
        }
        $this->setExceptions($exceptions);

        $schedules = [];
        if (empty($data['OrderSchedule'])) {
            $scheduleFirstDate = $data['Order']['scheduleFirstDate'] ?? null;
            if ($scheduleFirstDate !== null) {
                $firstDateTime = strtotime($scheduleFirstDate);
                for ($i = 0; $i < $data['Order']['count']; $i++) {
                    $schedule = new OrderSchedule();

                    $schedule->date = date('Y-m-d', $firstDateTime + $i * 86400);
                    $schedule->cost = $subscriptionDiscount->price / $data['Order']['count'];
                    $schedule->address_id = $address->id ?? null;
                    $schedule->order_id = $this->id;

                    $schedules[] = $schedule;
                }
            }
        } else {
            foreach ($data['OrderSchedule'] as $scheduleId => $schedule) {
                $scheduleData = OrderSchedule::findOne($scheduleId);
                $scheduleData->address_id = $schedule['address_id'];
                $scheduleData->interval = $schedule['interval'];
                $scheduleData->comment = $schedule['comment'];
                $schedules[$scheduleId] = $scheduleData;
            }
        }

        $this->setSchedules($schedules);

        return true;
    }


    /**
     * Собрать заказ из массива данные в API
     *
     * @param array $data
     * @return bool
     */
    public function buildFromApi(array $data): bool
    {
        !empty($data['shop_order_number']) && $this->shop_order_number = $data['shop_order_number'];
        !empty($data['schedule']['subscription_id']) && $this->subscription_id = $data['schedule']['subscription_id'];
        !empty($data['total']) && $this->total = $data['total'];
        !empty($data['schedule']['count']) && $this->count = $data['schedule']['count'];
        !empty($data['payment_type']) && $this->payment_type = $data['payment_type'];
        !empty($data['comment']) && $this->comment = $data['comment'];

        $paymentType = PaymentType::findOne($this->payment_type);
        if (!$paymentType) {
            return false;
        }

        $this->cash_machine = $paymentType->cash_machine;
        $this->cutlery = $data['cutlery'] ?? 1;

        $customer = (new Customer())->getByParams($data['customer']);
        if (empty($customer->id)) {
            !empty($data['customer']['fio']) && $customer->fio = $data['customer']['fio'];
            !empty($data['customer']['email']) && $customer->email = $data['customer']['email'];
            !empty($data['customer']['phone']) && $customer->phone = $data['customer']['phone'];
        }

        $address = new Address();
        !empty($data['address']['city']) && $address->city = $data['address']['city'];
        !empty($data['address']['full_address']) && $address->full_address = $data['address']['full_address'];
        !empty($data['address']['description']) && $address->description = $data['address']['description'];
        $address->prepareAddress($address, $data['address']['full_address']);

        $addressByParams = (new Address())->getByFullAddress($address->full_address, $customer->id);
        if (!empty($addressByParams)) {
            $address = $addressByParams;
        }

        $customer->setAddresses([$address]);
        $this->setCustomer($customer);

        $exceptions = [];
        foreach ($data['exceptions'] as $exception) {
            if ($exceptionData = Exception::find()->where(['name' => $exception])->one())
            $exceptions[] = $exceptionData;
        }
        $this->setExceptions($exceptions);

        $scheduleFirstDate = !empty($data['schedule']['start_date']) ? strtotime($data['schedule']['start_date']) : null;
        $schedules = [];
        if ($scheduleFirstDate !== null) {
            for ($i = 0; $i < $data['schedule']['count']; $i++) {
                $schedule = new OrderSchedule();
                $schedule->date = date('Y-m-d', $scheduleFirstDate + $i * 86400);
                $schedule->cost = $data['total'] / $data['schedule']['count'];
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
            empty($schedule->address_id) && $schedule->address_id = $this->address_id;
            if (!$schedule->validate() || !$schedule->save()) {
                $transaction->rollBack();
                return false;
            }
        }

        $transaction->commit();
        return true;
    }

    /**
     * @param int $statusID
     * @return bool
     */
    public function setStatus(int $statusID): bool
    {
        if (!in_array($statusID, self::STATUSES)) {
            return false;
        }

        $this->status_id = $statusID;
        return $this->save(false);
    }
}