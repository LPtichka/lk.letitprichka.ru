<?php

namespace app\models\Repository;

use app\models\Common\CustomerSheet;
use app\models\Common\Route;
use app\models\Helper\Date;
use app\models\Helper\Helper;
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
 * @property int $individual_menu
 * @property bool $cash_machine
 * @property string $comment
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Customer $customer
 * @property Franchise $franchise
 * @property Subscription $subscription
 * @property Address $address
 * @property PaymentType $payment
 * @property Exception[] $exceptions
 * @property OrderException[] $orderExceptions
 * @property OrderSchedule[] $schedules
 */
class Order extends \yii\db\ActiveRecord
{
    const SCENARIO_DESKTOP = 'desktop';

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

    const STATUSES_NO_EDITABLE = [
        self::STATUS_CANCELED,
        self::STATUS_PROCESSED,
        self::STATUS_DEFERRED,
        self::STATUS_COMPLETED,
    ];

    const STATUS_MAP = [
        self::STATUS_NEW       => [
            self::STATUS_CANCELED,
            self::STATUS_PROCESSED,
        ],
        self::STATUS_PROCESSED => [
//            self::STATUS_COMPLETED,
//            self::STATUS_CANCELED,
//            self::STATUS_DEFERRED,
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
    public $subscriptionCount;

    private $isUpdated = false;

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
            'individual_menu'   => \Yii::t('order', 'Individual menu'),
            'cash_machine'      => \Yii::t('order', 'Cash machine'),
            'comment'           => \Yii::t('order', 'Comment'),
            'customer_id'       => \Yii::t('order', 'Customer Id'),
            'subscriptionCount' => \Yii::t('order', 'Subscription count'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'status_id',
                    'payment_type',
                    'cutlery',
                    'count',
                    'subscriptionCount',
                    'total',
                    'address_id',
                    'franchise_id',
                    'individual_menu',
                ],
                'integer'
            ],
            [['count', 'franchise_id', 'payment_type', 'subscription_id'], 'required'],
            [['scheduleFirstDate', 'scheduleInterval'], 'required', 'on' => self::SCENARIO_DESKTOP],
            [['status_id'], 'in', 'range' => self::STATUSES],
            [['cutlery'], 'default', 'value' => 1],
            [['cash_machine', 'without_soup'], 'boolean'],
            [['comment', 'shop_order_number'], 'string'],
            [['count'], 'number', 'min' => 1],
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
    public function getFranchise()
    {
        return $this->hasOne(Franchise::class, ['id' => 'franchise_id']);
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
    public function getOrderExceptions()
    {
        return $this->hasMany(OrderException::class, ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getExceptions()
    {
        return $this->hasMany(Exception::class, ['id' => 'exception_id'])->viaTable(
            '{{%order_exception}}',
            ['order_id' => 'id']
        );
    }

    /**
     * Собрать заказ из массива POST
     *
     * @param array $data
     * @return bool
     */
    public function build(array $data): bool
    {
        if ($this->id) {
            $this->isUpdated = true;
        } else {
            $this->load($data);
        }

        $this->scenario = self::SCENARIO_DESKTOP;
        $settings = ArrayHelper::map(Settings::find()->asArray()->all(), 'name', 'value');

        // TODO переделать
        /** @var \app\models\User $user */
        $user = \Yii::$app->user->identity;
        $this->franchise_id = $user->franchise_id ?? 1;
        if (!empty($this->subscription_id) && $this->subscription_id != Subscription::NO_SUBSCRIPTION_ID) {
            $subscriptionDiscount = SubscriptionDiscount::find()
                                                        ->where(['subscription_id' => $this->subscription_id])
                                                        ->andWhere(['count' => $this->count])
                                                        ->orderBy(['count' => SORT_DESC])
                                                        ->limit(1)
                                                        ->one();

            $subscription = Subscription::findOne($this->subscription_id);
            if ($this->count == 1) {
                $this->total = $subscription->price;
            } else {
                if ($subscriptionDiscount) {
                    $this->total = $subscriptionDiscount->price;
                } else {
                    $this->total = $subscription->price * $this->count;
                }
            }

            if ($this->individual_menu) {
                $this->total = $this->total + $settings['individual_menu_price'] * $this->count;
            }
        }

        if ((empty($data['Order']['customer_id']) || !empty($data['Order']['isNewCustomer'])) && !$this->isUpdated) {
            $customer = new Customer();
            $customer->load($data);
            $this->setCustomer($customer);
            $customer->scenario = Customer::SCENARIO_NEW_CUSTOMER;
        } else {
            if (empty($this->customer_id) && !empty($data['Order']['customer_id'])) {
                $this->customer_id = (int)$data['Order']['customer_id'];
            }
            $this->setCustomer(Customer::findOne($this->customer_id));
        }

        if (empty($data['Order']['address_id'])) {
            $address = new Address();
            $address->load($data);
            $this->customer->setAddresses([$address]);
        }

        $exceptions = [];
        if (!empty($data['OrderException'])) {
            foreach ($data['OrderException'] as $exception) {
                if (empty($exception['exception_id'])) {
                    continue;
                }
                $exceptionData = Exception::findOne($exception['exception_id']);
                if ($exception['comment']) {
                    $exceptionData->comment = $exception['comment'];
                }
                $exceptions[] = $exceptionData;
            }
        }
        $this->setExceptions($exceptions);

        $schedules = [];
        if (empty($data['OrderSchedule'])) {
            $scheduleFirstDate = $data['Order']['scheduleFirstDate'] ?? null;

            if ($scheduleFirstDate !== null) {
                $firstDateTime = strtotime($scheduleFirstDate);
                $schedule = new OrderSchedule();
                $i = 0;
                $schedule->date = date('Y-m-d', $firstDateTime + $i * 86400);
                $this->scheduleFirstDate = $schedule->date;
                $schedule->address_id = $address->id ?? null;
                $schedule->order_id = $this->id;
                $schedule->interval = $data['Order']['scheduleInterval'] ?? null;
                $this->scheduleInterval = $schedule->interval;
                if ($this->subscription_id == Subscription::NO_SUBSCRIPTION_ID) {
                    $dishes = [];
                    $scheduleTotal = 0;
                    foreach ($data['OrderScheduleDish'] as $dishData) {
                        if (empty($dishData['dish_id'])) {
                            continue;
                        }
                        $dish = new OrderScheduleDish();
                        $dish->count = (int)$dishData['count'];
                        $dish->price = (int)$dishData['price'];
                        $dish->dish_id = (int)$dishData['dish_id'];

                        $scheduleTotal += $dish->price * $dish->count;
                        $dishes[] = $dish;
                    }

                    $schedule->cost = $scheduleTotal;
                    $this->count = 1;
                    $this->total = $scheduleTotal;
                    $schedule->setDishes($dishes);
                    $schedules[] = $schedule;
                } else {
                    $time = $firstDateTime;
                    for ($i = 0; $i < $data['Order']['count']; $i++) {
                        $orderSchedule = new OrderSchedule();
                        $price = $subscriptionDiscount->price ?? $subscription->price;

                        $orderSchedule->address_id = $schedule->address_id;
                        $orderSchedule->order_id = $schedule->order_id;
                        $orderSchedule->interval = $schedule->interval;

                        $dateObject = new Date($scheduleFirstDate);
                        if (!$dateObject->isWorkDay($time)) {
                            $time = $dateObject->getNextWorkDateTime($time);
                        }
                        $orderSchedule->date = date('Y-m-d', $time);
                        $orderSchedule->cost = $price / $data['Order']['count'];
                        if ($this->individual_menu) {
                            $orderSchedule->cost = $orderSchedule->cost + $settings['individual_menu_price'];
                        }

                        $schedules[] = $orderSchedule;
                        $time = $dateObject->getNextWorkDateTime($time);
                    }
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
     * Собрать заказ из массива POST
     *
     * @param array $data
     * @return bool
     */
    public function reBuildFromParams(array $data): bool
    {
        $settings = ArrayHelper::map(Settings::find()->asArray()->all(), 'name', 'value');

        $this->isUpdated = true;
        $this->subscription_id = $data['subscription_id'];
        $this->comment = $data['comment'];
        $this->count = $data['count'];

        $this->cutlery = $data['cutlery'] == 'true';
        $this->without_soup = $data['without_soup'] == 'true';
        $this->individual_menu = $data['individual_menu'] == 'true';


        if (!empty($data['subscription_id']) && $data['subscription_id'] != Subscription::NO_SUBSCRIPTION_ID) {
            $subscriptionDiscount = SubscriptionDiscount::find()
                                                        ->where(['subscription_id' => $this->subscription_id])
                                                        ->andWhere(['count' => $this->count])
                                                        ->orderBy(['count' => SORT_DESC])
                                                        ->limit(1)
                                                        ->one();

            $subscription = Subscription::findOne($this->subscription_id);
            if ($this->count == 1) {
                $this->total = $subscription->price;
            } else {
                if ($subscriptionDiscount) {
                    $this->total = $subscriptionDiscount->price;
                } else {
                    $this->total = $subscription->price * $this->count;
                }
            }

            if ($this->individual_menu) {
                $this->total = $this->total + $settings['individual_menu_price'] * $this->count;
            }
        }

        if (!empty($data['scheduleFirstDate'])) {
            $scheduleFirstDate = $data['scheduleFirstDate'];
            $firstDateTime = strtotime($scheduleFirstDate);
            $schedule = new OrderSchedule();
            $i = 0;
            $schedule->date = date('Y-m-d', $firstDateTime + $i * 86400);
            $this->scheduleFirstDate = $schedule->date;
            $schedule->address_id = $this->address->id ?? null;
            $schedule->order_id = $this->id;
            $schedule->interval = $data['scheduleInterval'];
            $this->scheduleInterval = $schedule->interval;
            if ($this->subscription_id == Subscription::NO_SUBSCRIPTION_ID) {
                $dishes = [];
                $scheduleTotal = 0;
                $schedule->cost = $scheduleTotal;
                $this->count = 1;
                $this->total = $scheduleTotal;
                $schedule->setDishes($dishes);
                $schedules[] = $schedule;
            } else {
                $time = $firstDateTime;
                for ($i = 0; $i < $this->count; $i++) {
                    $orderSchedule = new OrderSchedule();
                    $price = $subscriptionDiscount->price ?? $subscription->price;

                    $orderSchedule->address_id = $schedule->address_id;
                    $orderSchedule->order_id = $schedule->order_id;
                    $orderSchedule->interval = $schedule->interval;

                    $dateObject = new Date($scheduleFirstDate);
                    if (!$dateObject->isWorkDay($time)) {
                        $time = $dateObject->getNextWorkDateTime($time);
                    }
                    $orderSchedule->date = date('Y-m-d', $time);
                    $orderSchedule->cost = $price / $this->count;
                    if ($this->individual_menu) {
                        $orderSchedule->cost = $orderSchedule->cost + $settings['individual_menu_price'];
                    }

                    $schedules[] = $orderSchedule;
                    $time = $dateObject->getNextWorkDateTime($time);
                }
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
        !empty($data['address']['flat']) && $address->flat = $data['address']['flat'];
        !empty($data['address']['floor']) && $address->floor = $data['address']['floor'];
        !empty($data['address']['porch']) && $address->porch = $data['address']['porch'];
        $address->street = '-';
        $address->house = '-';
        $address->prepareAddress($address, $data['address']['full_address']);

        $addressByParams = (new Address())->getByFullAddress($address->full_address, $customer->id);
        if (!empty($addressByParams)) {
            $address = $addressByParams;
        }

        if (!empty($data['address']['porch']) && $data['address']['porch'] != $address->porch) {
            $address->porch = $data['address']['porch'];
        }
        if (!empty($data['address']['floor']) && $data['address']['floor'] != $address->floor) {
            $address->floor = $data['address']['floor'];
        }
        if (!empty($data['address']['flat']) && $data['address']['flat'] != $address->flat) {
            $address->flat = $data['address']['flat'];
        }

        $customer->setAddresses([$address]);
        $this->setCustomer($customer);

        $exceptions = [];
        foreach ($data['exceptions'] as $exception) {
            if ($exceptionData = Exception::find()->where(['name' => $exception])->one()) {
                $exceptions[] = $exceptionData;
            }
        }
        $this->setExceptions($exceptions);

        $scheduleFirstDate = !empty($data['schedule']['start_date']) ? $data['schedule']['start_date'] : null;
        $schedules = [];

        if ($scheduleFirstDate !== null) {
            $firstDateTime = strtotime($scheduleFirstDate);
            $schedule = new OrderSchedule();
            $i = 0;
            $schedule->date = date('Y-m-d', $firstDateTime + $i * 86400);
            $schedule->address_id = $address->id ?? null;
            $schedule->order_id = $this->id;
            $schedule->interval = $data['schedule']['interval'] ?? null;
            if ($this->subscription_id == Subscription::NO_SUBSCRIPTION_ID) {
                $dishes = [];
                $scheduleTotal = 0;
                foreach ($data['schedule']['dishes'] as $dishData) {
                    if (empty($dishData['dish_id'])) {
                        continue;
                    }
                    $dish = new OrderScheduleDish();
                    $dish->count = (int)$dishData['count'];
                    $dish->price = (int)$dishData['price'];
                    $dish->dish_id = (int)$dishData['dish_id'];

                    $scheduleTotal += $dish->price * $dish->count;
                    $dishes[] = $dish;
                }

                $schedule->cost = $scheduleTotal;
                $this->count = 1;
                $this->total = $scheduleTotal;
                $schedule->setDishes($dishes);
                $schedules[] = $schedule;
            } else {
                $time = $firstDateTime;
                for ($i = 0; $i < $data['schedule']['count']; $i++) {
                    $orderSchedule = new OrderSchedule();
                    $price = $data['total'];

                    $orderSchedule->address_id = $schedule->address_id;
                    $orderSchedule->order_id = $schedule->order_id;
                    $orderSchedule->interval = $schedule->interval;

                    $dateObject = new Date($scheduleFirstDate);
                    if (!$dateObject->isWorkDay($time)) {
                        $time = $dateObject->getNextWorkDateTime($time);
                    }

                    $orderSchedule->date = date('Y-m-d', $time);
                    $orderSchedule->cost = $price / $data['schedule']['count'];
                    $schedules[] = $orderSchedule;

                    $time = $dateObject->getNextWorkDateTime($time);
                }
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

    public function getScheduleFirstDate(): ?string
    {
        foreach ($this->schedules as $schedule) {
            return $schedule->date;
        }

        return null;
    }

    /**
     * Сохранение всех связанных объектов
     *
     * @return bool
     * @throws \yii\db\Exception
     */
    public function saveAll(): bool
    {
        $event = new \app\events\OrderCreated();
        $transaction = \Yii::$app->db->beginTransaction();
        if (!$this->customer->validate() || !$this->customer->save()) {
            \Yii::error(Helper::DEVIDER . json_encode($this->customer->getFirstErrors()));
            $transaction->rollBack();
            return false;
        }
        foreach ($this->customer->addresses as $address) {
            $address->customer_id = $this->customer->id;
            if (!$address->validate() || !$address->save()) {
                \Yii::error(Helper::DEVIDER . json_encode($address->getFirstErrors()));
                $transaction->rollBack();
                return false;
            }
        }
        $this->customer_id = $this->customer->id;
        if (!empty($this->customer->addresses[0]->id)) {
            $this->address_id = $this->customer->addresses[0]->id;
        }

        if (!$this->isUpdated && !$this->validate()) {
            \Yii::error(Helper::DEVIDER . json_encode($this->getFirstErrors()));
            $transaction->rollBack();
            return false;
        }
        if (!$this->save(!$this->isUpdated)) {
            \Yii::error(Helper::DEVIDER . json_encode($this->getFirstErrors()));
            $transaction->rollBack();
            return false;
        }
        $event->setOrderId($this->id);

        CustomerException::deleteAll(['customer_id' => $this->customer_id]);
        OrderException::deleteAll(['order_id' => $this->id]);

        foreach ($this->exceptions as $exception) {
            $oException = new OrderException();
            $oException->order_id = $this->id;
            $oException->exception_id = $exception->id;
            $oException->comment = $exception->comment;
            if (!$oException->validate() || !$oException->save()) {
                \Yii::error(Helper::DEVIDER . json_encode($oException->getFirstErrors()));
                $transaction->rollBack();
                return false;
            }
            $cException = new CustomerException();
            $cException->customer_id = $this->customer_id;
            $cException->exception_id = $exception->id;
            if (!$cException->validate() || !$cException->save()) {
                \Yii::error(Helper::DEVIDER . json_encode($cException->getFirstErrors()));
                $transaction->rollBack();
                return false;
            }
        }

        if ($this->isUpdated) {
            $orderSchedules = OrderSchedule::find()->where(['order_id' => $this->id])->asArray()->all();
            foreach ($orderSchedules as $scheduleItem) {
                OrderScheduleDish::deleteAll(['order_schedule_id' => $scheduleItem['id']]);
                OrderSchedule::deleteAll(['id' => $scheduleItem['id']]);
            }
        }

        foreach ($this->schedules as $schedule) {
            $schedule->order_id = $this->id;
            empty($schedule->address_id) && $schedule->address_id = $this->address_id;
            if (!$schedule->validate() || !$schedule->save()) {
                $transaction->rollBack();
                return false;
            }
            if ($this->subscription_id == Subscription::NO_SUBSCRIPTION_ID && !empty($schedule->dishes)) {
                foreach ($schedule->dishes as $dish) {
                    $dish->order_schedule_id = $schedule->id;
                    if (!$dish->validate() || !$dish->save()) {
                        \Yii::error(Helper::DEVIDER . json_encode($dish->getFirstErrors()));
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
                        $orderScheduleDish->ingestion_type = $key;
                        $orderScheduleDish->dish_id = null;

                        if (!$orderScheduleDish->validate() || !$orderScheduleDish->save()) {
                            \Yii::error(Helper::DEVIDER . json_encode($orderScheduleDish->getFirstErrors()));
                            $transaction->rollBack();
                            return false;
                        }
                    } else {
                        foreach ($ingestion as $iType) {
                            if ($this->without_soup && $iType == Dish::TYPE_FIRST) {
                                continue;
                            }

                            $orderScheduleDish = new OrderScheduleDish();
                            $orderScheduleDish->order_schedule_id = $schedule->id;
                            $orderScheduleDish->ingestion_type = $key;
                            $orderScheduleDish->dish_id = null;
                            $orderScheduleDish->type = $iType;

                            if (!$orderScheduleDish->validate() || !$orderScheduleDish->save()) {
                                \Yii::error(Helper::DEVIDER . json_encode($orderScheduleDish->getFirstErrors()));
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
            OrderException::find()->where(['order_id' => $this->id])->asArray()->all(),
            'exception_id'
        );
    }

    /**
     * @param string $date
     * @return array
     */
    public function getRoutesForDate(string $date): array
    {
        $time = strtotime($date);
        $orderSchedules = OrderSchedule::find()->where(['date' => date('Y-m-d', $time)])->all();
        $routes = [];
        foreach ($orderSchedules as $schedule) {
            $route = new Route(
                $schedule->order->customer->fio,
                $schedule->address->full_address . " " . $schedule->comment,
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
            } else {
                $route->setPayment('нет');
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
     * @param array $dates
     * @return array
     */
    public function getCustomerSheetsByDate(array $dates): array
    {
        $result = [];

        foreach ($dates as $date) {
            $daySchedule = null;
            foreach ($this->schedules as $schedule) {
                if ($schedule->date === $date) {
                    $daySchedule = $schedule;
                    break;
                }
            }

            if ($daySchedule === null) {
                return [];
            }

            $dishes = [];
            $manufacturedAt = 0;
            foreach ($daySchedule->dishes as $scheduleDish) {
                $dishes[] = $scheduleDish->dish;
                if ($scheduleDish->manufactured_at > $manufacturedAt) {
                    $manufacturedAt = $scheduleDish->manufactured_at;
                }
            }

            $dayBalance = OrderSchedule::find()
                                       ->where(['status' => OrderSchedule::EDITABLE_STATUSES])
                                       ->andWhere(['order_id' => $this->id])
                                       ->count();

            $customerSheet = (new CustomerSheet())
                ->setFio($this->customer->fio)
                ->setPhone($this->customer->phone)
                ->setAddress($this->address->full_address)
                ->setFranchise($this->franchise)
                ->setManufacturedAt($manufacturedAt)
                ->setCutlery($this->cutlery)
                ->setSubscriptionId($this->subscription_id)
                ->setSubscriptionName($this->subscription->name)
                ->setSubscriptionDayCount($this->count)
                ->setSubscriptionDayBalance($dayBalance - 1)
                ->setExceptions($this->getExceptionNames())
                ->setDeliveryTime($daySchedule->interval)
                ->setDishes($dishes)
                ->setHasBreakfast($this->subscription->has_breakfast)
                ->setHasDinner($this->subscription->has_dinner)
                ->setHasLunch($this->subscription->has_lunch)
                ->setHasSupper($this->subscription->has_supper);

            $result[] = $customerSheet;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getExceptionNames(): array
    {
        $result = [];
        if (!empty($this->exceptions)) {
            foreach ($this->exceptions as $exception) {
                $result[] = $exception->name;
            }
        }
        return $result;
    }

    /**
     * @return string|null
     */
    public function getScheduleInterval(): ?string
    {
        if (!empty($this->schedules)) {
            foreach ($this->schedules as $schedule) {
                return $schedule->interval;
            }
        }
        return null;
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

        $dates = [];
        $dates[] = date('d.m.Y', strtotime($this->schedules[0]->date));

        foreach ($this->schedules as $schedule) {
            $dates[1] = date('d.m.Y', strtotime($schedule->date));
        }

        return implode(' - ', $dates);
    }

    /**
     * @return bool
     */
    public function isEditable(): bool
    {
        return !in_array($this->status_id, self::STATUSES_NO_EDITABLE);
    }

    public function getErrorMessages(string $model): array
    {
        if ($model == 'address') {
            if (!empty($this->address->getFirstErrors())) {
                return $this->address->getFirstErrors();
            }
            if (isset($this->customer->addresses[0]) && !empty($this->customer->addresses[0]->getFirstErrors())) {
                return $this->customer->addresses[0]->getFirstErrors();
            }
        }

        if ($model == 'customer') {
            if (!empty($this->customer)) {
                return $this->customer->getFirstErrors();
            }
        }

        return [];
    }

    /**
     * @param string|null $date
     * @return bool
     */
    public function isNotEquipped(?string $date = null): bool
    {
        $sql = 'SELECT COUNT(*) as `count` FROM `order_schedule` AS os 
                      LEFT JOIN `order_schedule_dish` AS osd
                        ON os.id = osd.order_schedule_id
                      WHERE os.order_id = "' . $this->id . '" 
                            AND (osd.dish_id IS NULL OR (osd.with_garnish = 1 AND osd.garnish_id is NULL))';

        if ($date) {
            $sql .= ' AND os.date = "' . $date . '"';
        }

        try {
            $result = \Yii::$app->db->createCommand($sql)->queryOne();
        } catch (\yii\db\Exception $e) {
            return false;
        }

        return (bool)$result['count'];
    }
}