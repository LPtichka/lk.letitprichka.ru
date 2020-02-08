<?php

namespace app\models\Repository;

use app\models\Common\Route;
use app\models\Helper\Status;
use app\models\Queries\OrderQuery;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property int $id
 * @property int $franchise_id
 * @property string $shop_order_number
 * @property bool $without_soup
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
 * @property Subscription $subscription
 * @property Address $address
 * @property PaymentType $payment
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
    public $scheduleInterval;

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
            'subscription_id'   => \Yii::t('order', 'Subscription ID'),
            'franchise_id'      => \Yii::t('order', 'Franchise ID'),
            'count'             => \Yii::t('order', 'Count'),
            'cutlery'           => \Yii::t('order', 'Cutlery'),
            'without_soup'      => \Yii::t('order', 'Without soup'),
            'scheduleFirstDate' => \Yii::t('order', 'Schedule first date'),
            'scheduleInterval'  => \Yii::t('order', 'Schedule interval'),
            'payment_type'      => \Yii::t('order', 'Payment type'),
            'cash_machine'      => \Yii::t('order', 'Cash machine'),
            'comment'           => \Yii::t('order', 'Comment'),
            'customer_id'       => \Yii::t('order', 'Customer Id'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status_id', 'payment_type', 'cutlery', 'count', 'total', 'address_id', 'franchise_id'], 'integer'],
            [['status_id'], 'in', 'range' => self::STATUSES],
            [['cash_machine', 'without_soup'], 'boolean'],
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
     * @param Address $address
     */
    public function setAddress(Address $address): void
    {
        $this->address = $address;
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
    public function getPayment()
    {
        return $this->hasOne(PaymentType::class, ['id' => 'payment_type']);
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
    public function getSubscription()
    {
        return $this->hasOne(Subscription::class, ['id' => 'subscription_id']);
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
        if (!empty($this->subscription_id)) {
            $subscriptionDiscount = SubscriptionDiscount::find()
                ->where(['subscription_id' => $this->subscription_id])
                ->andWhere(['<=', 'count', $this->count])
                ->orderBy(['count' => SORT_DESC])
                ->limit(1)
                ->one();

            $subscription = Subscription::findOne($this->subscription_id);
            if ($this->count == 1) {
                $this->total = $subscription->price;
            } else {
                $this->total = $subscriptionDiscount->price;
            }

        }


        if (empty($data['Order']['customer_id']) || !empty($data['Order']['isNewCustomer'])) {
            $customer = new Customer();
            $customer->load($data);
            $this->setCustomer($customer);
        } else {
            $this->setCustomer(Customer::findOne($data['Order']['customer_id']));
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
                $firstDateTime        = strtotime($scheduleFirstDate);
                $schedule             = new OrderSchedule();
                $i                    = 0;
                $schedule->date       = date('Y-m-d', $firstDateTime + $i * 86400);
                $schedule->address_id = $address->id ?? null;
                $schedule->order_id   = $this->id;
                $schedule->interval   = $data['Order']['scheduleInterval'] ?? null;
                if (empty($this->subscription_id)) {

                    $dishes        = [];
                    $scheduleTotal = 0;
                    foreach ($data['OrderScheduleDish'] as $dishData) {
                        if (empty($dishData['dish_id'])) {
                            continue;
                        }
                        $dish          = new OrderScheduleDish();
                        $dish->count   = (int) $dishData['count'];
                        $dish->price   = (int) $dishData['price'];
                        $dish->dish_id = (int) $dishData['dish_id'];

                        $scheduleTotal += $dish->price * $dish->count;
                        $dishes[]      = $dish;
                    }

                    $schedule->cost = $scheduleTotal;
                    $this->count    = 1;
                    $this->total    = $scheduleTotal;
                    $schedule->setDishes($dishes);
                    $schedules[] = $schedule;
                } else {
                    for ($i = 0; $i < $data['Order']['count']; $i++) {
                        $price = $subscriptionDiscount->price ?? $subscription->price;

                        $schedule->cost = $price / $data['Order']['count'];
                        $schedules[]    = $schedule;
                    }
                }
            }
        } else {
            foreach ($data['OrderSchedule'] as $scheduleId => $schedule) {
                $scheduleData             = OrderSchedule::findOne($scheduleId);
                $scheduleData->address_id = $schedule['address_id'];
                $scheduleData->interval   = $schedule['interval'];
                $scheduleData->comment    = $schedule['comment'];
                $schedules[$scheduleId]   = $scheduleData;
            }
        }

        $this->setSchedules($schedules);

        return true;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
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
        !empty($data['franchise_id']) && $this->franchise_id = $data['franchise_id'];
        !empty($data['options']) && $this->without_soup = in_array('without_soup', $data['options']);

        $paymentType = PaymentType::findOne($this->payment_type);
        if (!$paymentType) {
            return false;
        }

        $this->cash_machine = $paymentType->cash_machine;
        $this->cutlery      = $data['cutlery'] ?? 1;

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
        $schedules         = [];
        if ($scheduleFirstDate !== null) {
            for ($i = 0; $i < $data['schedule']['count']; $i++) {
                $schedule       = new OrderSchedule();
                $schedule->date = date('Y-m-d', $scheduleFirstDate + $i * 86400);
                $schedule->cost = $data['total'] / $data['schedule']['count'];
                $schedules[]    = $schedule;
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
        $event       = new \app\events\OrderCreated();
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
        $this->address_id  = $this->customer->addresses[0]->id;
        if (!$this->save()) {
            $transaction->rollBack();
            return false;
        }
        $event->setOrderId($this->id);

        CustomerException::deleteAll(['customer_id' => $this->customer_id]);

        foreach ($this->exceptions as $exception) {
            $oException               = new OrderException();
            $oException->order_id     = $this->id;
            $oException->exception_id = $exception->id;
            if (!$oException->validate() || !$oException->save()) {
                $transaction->rollBack();
                return false;
            }
            $cException               = new CustomerException();
            $cException->customer_id  = $this->customer_id;
            $cException->exception_id = $exception->id;
            if (!$cException->validate() || !$cException->save()) {
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
            if (empty($this->subscription_id) && !empty($schedule->dishes)) {
                foreach ($schedule->dishes as $dish) {
                    $dish->order_schedule_id = $schedule->id;
                    if (!$dish->validate() || !$dish->save()) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            } else {
                // TODO тут нужно сохранить сразу ORDER_SCHEDULE_DISH
                foreach (OrderSchedule::INGESTION_CONTENT as $key => $ingestion) {
                    if (empty($ingestion)) {
                        $orderScheduleDish = new OrderScheduleDish();

                        $orderScheduleDish->order_schedule_id = $schedule->id;
                        $orderScheduleDish->ingestion_type    = $key;
                        $orderScheduleDish->dish_id           = null;

                        if (!$orderScheduleDish->validate() || !$orderScheduleDish->save()) {
                            $transaction->rollBack();
                            return false;
                        }
                    } else {
                        foreach ($ingestion as $iType) {
                            if ($this->without_soup && $iType == Dish::TYPE_FIRST) {
                                continue;
                            }

                            $orderScheduleDish                    = new OrderScheduleDish();
                            $orderScheduleDish->order_schedule_id = $schedule->id;
                            $orderScheduleDish->ingestion_type    = $key;
                            $orderScheduleDish->dish_id           = null;
                            $orderScheduleDish->type              = $iType;

                            if (!$orderScheduleDish->validate() || !$orderScheduleDish->save()) {
                                $transaction->rollBack();
                                return false;
                            }
                        }
                    }
                }
            }
        }

        $transaction->commit();
        $event->prepareEvent();
        \Yii::$app->trigger(\app\events\OrderCreated::EVENT_ORDER_CREATED, $event);
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

    /**
     * @return array
     */
    public function getExceptionList(): array
    {
        return ArrayHelper::getColumn(
            OrderException::find()->where(['order_id' => $this->id])->asArray()->all(), 'exception_id'
        );
    }

    /**
     * @param string $date
     * @return array
     */
    public function getRoutesForDate(string $date): array
    {
        $time           = strtotime($date);
        $orderSchedules = OrderSchedule::find()->where(['date' => date('Y-m-d', $time)])->all();
        $routes         = [];
        foreach ($orderSchedules as $schedule) {
            $route = new Route(
                $schedule->order->customer->fio,
                $schedule->address->full_address,
                $schedule->order->customer->phone
            );
            $route->setInterval($schedule->interval);
            !empty($schedule->comment) && $route->setComment($schedule->comment);

            if ($schedule->order->isNeedPaymentForDate($schedule->date)) {
                $payment = $schedule->order->total;
                if ($schedule->order->cash_machine) {
                    $payment .= ' + касса';
                }
                $route->setPayment($payment);
            }

            $routes[] = $route;
        }

        return $routes;
    }

    /**
     * @param string $date
     * @return bool
     */
    public function isNeedPaymentForDate(string $date): bool
    {
        $orderSchedule = OrderSchedule::find()->where(['order_id' => $this->id])->orderBy(['date' => SORT_ASC])->one();

        if ($orderSchedule->date != $date) {
            return false;
        }

        if ($orderSchedule->order->payment->type == PaymentType::TYPE_FULL_PAY) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getStatusName(): ?string
    {
        if (empty($this->status_id)) {
            return null;
        }
        return (new Status($this->status_id))->getStatusName();
    }

    /**
     * @return string
     */
    public function getOrderSubscription(): ?string
    {
        return $this->subscription ? $this->subscription->name : '---';
    }

    /**
     * @return string
     */
    public function getSubscriptionDates(): ?string
    {
        if (empty($this->schedules) || empty($this->id)) {
            return null;
        }

        $dates   = [];
        $dates[] = date('d.m.Y', strtotime($this->schedules[0]->date));

        foreach ($this->schedules as $schedule) {
            $dates[1] = date('d.m.Y', strtotime($schedule->date));
        }

        return implode(' - ', $dates);
    }
}