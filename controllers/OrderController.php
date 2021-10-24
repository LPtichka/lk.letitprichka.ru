<?php

namespace app\controllers;

use app\events\OrderCompleted;
use app\models\Common\CustomerSheet;
use app\models\Common\Ingestion;
use app\models\Helper\Date;
use app\models\Helper\Excel;
use app\models\Repository\Address;
use app\models\Repository\Customer;
use app\models\Repository\Dish;
use app\models\Repository\Exception;
use app\models\Repository\OrderException;
use app\models\Repository\OrderSchedule;
use app\models\Repository\OrderScheduleDish;
use app\models\Repository\Subscription;
use app\models\Search\Order;
use app\models\Search\PaymentType;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OrderController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $event = new OrderCompleted();
        $event->prepareEvent();
        \Yii::$app->trigger(\app\events\OrderCompleted::EVENT_ORDER_COMPLETED, $event);

        $searchModel = new Order();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render(
            '/order/index',
            [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * @return Response|string
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $order = new \app\models\Repository\Order();

        if (\Yii::$app->request->post()) {
            $this->log('order-create', []);
            if ($order->build(\Yii::$app->request->post()) && $order->saveAll()) {
                \Yii::$app->session->addFlash('success', \Yii::t('order', 'Order was saved successfully'));
                $this->log(
                    'order-create-success',
                    [
                        'name' => $order->id,
                        'id'   => $order->id,
                    ]
                );
                return $this->redirect(['order/index']);
            } else {
                \Yii::$app->session->addFlash('danger', \Yii::t('order', 'Order was not saved successfully'));
                $this->log(
                    'order-create-fail',
                    [
                        'name'   => $order->id,
                        'errors' => json_encode($order->getFirstErrors()),
                    ]
                );
            }
        }

        if (empty($order->customer)) {
            $order->setCustomer(new Customer());
        }

        if (empty($order->address)) {
            if (!empty($order->customer->addresses)) {
                $order->address = $order->customer->addresses[0];
            } else {
                $order->setAddress(new Address());
            }
        }

        if (empty($order->exceptions)) {
            $order->setExceptions([new Exception()]);
        }

        if (empty($order->schedules)) {
            $schedule = new OrderSchedule();
            $schedule->setDishes([new OrderScheduleDish()]);
            $order->setSchedules([$schedule]);
        }


        $paymentTypes = ArrayHelper::map(
            PaymentType::find()->select(['id', 'name'])->where(['status' => PaymentType::STATUS_ACTIVE])->asArray(
            )->all(),
            'id',
            'name'
        );

        $exceptions = ArrayHelper::map(
            Exception::find()->select(['id', 'name'])->where(['status' => Exception::STATUS_ACTIVE])->asArray()->all(),
            'id',
            'name'
        );

        $subscriptions = ArrayHelper::map(
            Subscription::find()->select(['id', 'name'])->where(['status' => Subscription::STATUS_ACTIVE])->asArray(
            )->all(),
            'id',
            'name'
        );

        $subscriptionCounts = (new Subscription())->getCounts();

        return $this->render(
            '/order/create',
            [
                'model'              => $order,
                'payments'           => $paymentTypes,
                'addresses'          => [
                    '' => \Yii::t('order', 'New address'),
                ],
                'exceptions'         => $exceptions,
                'subscriptions'      => $subscriptions,
                'intervals'          => (new OrderSchedule())->getIntervals(),
                'customers'          => ArrayHelper::map(Customer::find()->asArray()->all(), 'id', 'fio'),
                'subscriptionCounts' => $subscriptionCounts,
                'title'              => \Yii::t('order', 'Order create'),
            ]
        );
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws \yii\db\Exception
     */
    public function actionView(int $id)
    {
        $order = \app\models\Repository\Order::findOne($id);

        if (\Yii::$app->request->post()) {
            $this->log('order-update', []);
            if ($order->build(\Yii::$app->request->post()) && $order->saveAll()) {
                \Yii::$app->session->addFlash('success', \Yii::t('order', 'Order was saved successfully'));
                $this->log(
                    'order-update-success',
                    [
                        'name' => $order->id,
                        'id'   => $order->id,
                    ]
                );
                return $this->redirect(['order/view', 'id' => $order->id]);
            } else {
                \Yii::$app->session->addFlash('danger', \Yii::t('order', 'Order was not saved successfully'));
                $this->log(
                    'order-update-fail',
                    [
                        'name'   => $order->id,
                        'errors' => json_encode($order->getFirstErrors()),
                    ]
                );
            }
        }

        if (empty($order->customer)) {
            $order->setCustomer(new Customer());
        }

        if (empty($order->address)) {
            $order->setAddress(new Address());
        }

        $paymentTypes = ArrayHelper::map(
            PaymentType::find()->select(['id', 'name'])->where(['status' => PaymentType::STATUS_ACTIVE])->asArray(
            )->all(),
            'id',
            'name'
        );

        $exceptions = ArrayHelper::map(
            Exception::find()->select(['id', 'name'])->where(['status' => Exception::STATUS_ACTIVE])->asArray()->all(),
            'id',
            'name'
        );

        $subscriptions = ArrayHelper::map(
            Subscription::find()->select(['id', 'name'])->where(['status' => Subscription::STATUS_ACTIVE])->asArray(
            )->all(),
            'id',
            'name'
        );

        $subscriptionCounts = (new Subscription())->getCounts();

        return $this->render(
            '/order/create',
            [
                'model'              => $order,
                'addresses'          => ArrayHelper::map(
                    Address::find()->where(['customer_id' => $order->customer_id])->asArray()->all(),
                    'id',
                    'full_address'
                ),
                'payments'           => $paymentTypes,
                'exceptions'         => $exceptions,
                'subscriptions'      => $subscriptions,
                'customers'          => ArrayHelper::map(Customer::find()->asArray()->all(), 'id', 'fio'),
                'intervals'          => (new OrderSchedule())->getIntervals(),
                'subscriptionCounts' => $subscriptionCounts,
                'title'              => \Yii::t('order', 'Order №') . $order->id,
            ]
        );
    }

    /**
     * @param int $counter
     * @return string
     */
    public function actionAddException(int $counter)
    {
        $exceptions = ArrayHelper::map(
            Exception::find()->select(['id', 'name'])->where(['status' => PaymentType::STATUS_ACTIVE])->asArray()->all(
            ),
            'id',
            'name'
        );

        return $this->renderAjax(
            '/order/_order_exception',
            [
                'exception'  => new OrderException(),
                'exceptions' => $exceptions,
                'disabled'   => false,
                'i'          => ++$counter,
            ]
        );
    }

    /**
     * @param int $customerId
     * @return array
     */
    public function actionGetAddress(int $customerId = 0)
    {
        $addressList = [];

        $customer = Customer::findOne($customerId);
        if ($customer) {
            $addresses = Address::find()
                                ->where(
                                    [
                                        'customer_id' => $customerId,
                                        'status'      => Address::STATUS_ACTIVE
                                    ]
                                )
                                ->asArray()
                                ->all();
            foreach ($addresses as $address) {
                $address['selected'] = $customer->default_address_id == $address['id'];
                $addressList[] = $address;
            }
        }

        $addressList[] = [
            'id'           => '',
            'full_address' => '',
            'city'         => '',
            'street'       => '',
            'house'        => '',
            'housing'      => '',
            'building'     => '',
            'flat'         => '',
            'postcode'     => '',
            'description'  => '',
            'selected'     => empty($addressList),
        ];

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $addressList;
    }

    /**
     * @param int $customerId
     * @return string
     */
    public function actionGetException(int $customerId = 0)
    {
        $exceptionList = '';
        $exceptions = ArrayHelper::map(
            Exception::find()->select(['id', 'name'])->where(['status' => PaymentType::STATUS_ACTIVE])->asArray()->all(
            ),
            'id',
            'name'
        );
        $customer = Customer::findOne($customerId);
        if ($customer) {
            foreach ($customer->exceptions as $key => $exception) {
                $excp = new OrderException();
                $excp->exception_id = $exception->id;

                $exceptionList .= $this->renderPartial(
                    '/order/_order_exception',
                    [
                        'exception'  => $excp,
                        'exceptions' => $exceptions,
                        'disabled'   => false,
                        'i'          => $key,
                    ]
                );
            }
        }

        if (empty($exceptionList)) {
            $exceptionList = $this->renderPartial(
                '/order/_order_exception',
                [
                    'exception'  => new OrderException(),
                    'exceptions' => $exceptions,
                    'disabled'   => false,
                    'i'          => 1,
                ]
            );
        }

        return $exceptionList;
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function actionGetMenu(int $orderId = 0)
    {
        $order = Order::findOne($orderId);
        $intervals = (new OrderSchedule())->getIntervals();
        $addresses = Address::find()
                            ->where(['customer_id' => $order->customer_id, 'status' => Address::STATUS_ACTIVE])
                            ->asArray()
                            ->all();

        return $this->renderAjax(
            '/order/_menu',
            [
                'order'     => $order,
                'intervals' => $intervals,
                'addresses' => ArrayHelper::map($addresses, 'id', 'full_address'),
            ]
        );
    }

    /**
     * @param int $orderID
     * @param int $statusID
     * @return array
     */
    public function actionSetStatus(int $orderID, int $statusID)
    {
        $order = Order::findOne($orderID);

        if ($isSuccess = $order->setStatus($statusID)) {
            $title = \Yii::t('order', 'Order change status successfully');
        } else {
            $title = \Yii::t('order', 'Order was not changed');
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => $isSuccess,
            'title'   => $title
        ];
    }

    /**
     * @param int $orderID
     * @return string
     */
    public function actionDefferRequest(int $orderID)
    {
        $order = Order::findOne($orderID);

        $schedules = OrderSchedule::find()
                                  ->where(['order_id' => $order->id])
                                  ->andWhere(['>=', 'date', date('Y-m-d', time())])
                                  ->asArray()->all();

        $dates = ArrayHelper::map(
            $schedules,
            function ($schedule) {
                return date('d.m.Y', strtotime($schedule['date']));
            },
            function ($schedule) {
                return date('d.m.Y', strtotime($schedule['date']));
            }
        );

        return $this->renderAjax(
            '/order/_request_deffer',
            [
                'order' => $order,
                'dates' => $dates,
            ]
        );
    }

    /**
     * Отложить выполнение заказа с определенной даты
     *
     * @param int $id
     * @return array
     */
    public function actionDeffer(int $id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $newDateFrom = \Yii::$app->request->post('dateTo');
        $oldDateFrom = \Yii::$app->request->post('dateFrom');

        if (!$newDateFrom || !$oldDateFrom) {
            return [
                'success' => false,
                'title'   => \Yii::t('order', 'Required parameters are not filled'),
            ];
        }

        $transaction = \Yii::$app->db->beginTransaction();
        $orderSchedules = OrderSchedule::find()
                                       ->where(['order_id' => $id])
                                       ->andWhere(['>=', 'date', date('Y-m-d', strtotime($oldDateFrom))])
                                       ->orderBy(['date' => SORT_ASC])
                                       ->all();

        $dateObject = new Date($newDateFrom);
        $newDateTimestamp = strtotime($newDateFrom);
        foreach ($orderSchedules as $key => $orderSchedule) {
            $orderSchedule->date = date('Y-m-d', $newDateTimestamp);
            if (!$orderSchedule->save(false)) {
                $transaction->rollBack();
                \Yii::$app->session->addFlash('danger', \Yii::t('order', 'Error schedule saving'));
                return [
                    'success' => false,
                    'title'   => \Yii::t('order', 'Error schedule saving'),
                ];
            }
            $newDateTimestamp = $dateObject->getNextWorkDateTime($newDateTimestamp);
        }

        try {
            $transaction->commit();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->session->addFlash('danger', \Yii::t('order', 'Error schedule saving'));
            return [
                'success' => false,
                'title'   => \Yii::t('order', 'Error saving to database'),
            ];
        }

        \Yii::$app->session->addFlash('success', \Yii::t('order', 'Order schedule saved successfully'));
        return [
            'success' => true,
            'title'   => \Yii::t('order', 'Order schedule saved successfully'),
        ];
    }

    /**
     * @return string
     */
    public function actionGetRouteSheet()
    {
        $orderRoutes = [];

        if (\Yii::$app->request->post()) {
            $date = \Yii::$app->request->post('date');
            $orderRoutes = (new \app\models\Repository\Order())->getRoutesForDate($date);
        }

        return $this->renderAjax(
            '/order/_get_route_sheet',
            [
                'routes' => $orderRoutes,
                'date'   => $date ?? '',
                'title'  => \Yii::t('order', 'Order sheet'),
            ]
        );
    }

    /**
     * @return array
     */
    public function actionSaveRouteSheet()
    {
        $orderRoutes = [];

        if (\Yii::$app->request->post()) {
            $date = \Yii::$app->request->post('date');
            $orderRoutes = (new \app\models\Repository\Order())->getRoutesForDate($date);

            $excel = new Excel();
            $excel->loadFromTemplate('files/templates/base.xlsx');
            $excel->prepare($orderRoutes, Excel::MODEL_ROUTE_SHEET, \Yii::$app->request->post());
            $excel->save('delivery_report.xlsx', 'temp');

            \Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'url' => $excel->getUrl()
            ];
        }
    }

    /**
     * @param int $id
     * @return string|array
     */
    public function actionGetCustomerSheet()
    {
        $userSheet = [];
        $id = \Yii::$app->request->get('id', null);
        if ($post = \Yii::$app->request->post()) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return $this->generateCustomerSheetFile($post, $id);
        }

        $orderId = ArrayHelper::getValue(
            OrderSchedule::find()->where(['order_id' => $id])->asArray()->one(),
            'order_id'
        );
        $dates = ArrayHelper::map(
            OrderSchedule::find()->where(['order_id' => $id])->asArray()->all(),
            'date',
            'date'
        );

        foreach ($dates as $id => $dateValue) {
            $dates[$id] = date('d.m.Y', strtotime($dateValue));
        }

        return $this->renderAjax(
            '/order/_get_customer_sheet',
            [
                'routes' => $userSheet,
                'dates'  => $dates,
                'id'     => $orderId,
                'title'  => \Yii::t('order', 'Customer sheet'),
            ]
        );
    }

    /**
     * @param array $post
     * @param int|null $orderId
     * @return array
     */
    private function generateCustomerSheetFile(array $post, ?int $orderId = null): array
    {
        $query = OrderSchedule::find()->where(['date' => date('Y-m-d', strtotime($post['date']))]);
        if ($orderId) {
            $query->andWhere(['order_id' => $orderId]);
        }
        $dates = $query->all();

        if (!$dates) {
            return [
                'success'      => false,
                'errorMessage' => \Yii::t('order', 'No orders for chousen date'),
            ];
        }

        $customerSheets = (new CustomerSheet())->getAllCustomerSheets($dates);

        try {
            $excel = new Excel();
            $excel->loadFromTemplate('files/templates/base_with_logo.xlsx');
            $excel->prepare($customerSheets, Excel::MODEL_CUSTOMER_SHEET, \Yii::$app->request->post());
            $excel->save('client_report.xlsx', 'temp');
        } catch (\Throwable $e) {
            $schedules = OrderSchedule::find()
                                      ->leftJoin(
                                          'order_schedule_dish',
                                          'order_schedule.id = order_schedule_dish.order_schedule_id'
                                      )
                                      ->where(['order_schedule.date' => date('Y-m-d', strtotime($post['date']))])
                                      ->andWhere(['order_schedule_dish.dish_id' => null])
                                      ->all();

            $order_ids = [];
            foreach ($schedules as $schedule) {
                $order_ids[] = $schedule->order_id;
            }

            \Yii::error($e->getMessage());
            return [
                'success'      => false,
                'errorMessage' => \Yii::t('order', 'Error in this orders') . implode(', ', $order_ids),
            ];
        }

        return [
            'success' => true,
            'url'     => $excel->getUrl(),
        ];
    }

    /**
     * @param int $id
     * @return string|array
     */
    public function actionGetCustomerSheetOptions(int $id)
    {
        $dates = ArrayHelper::map(
            OrderSchedule::find()->where(['order_id' => $id])->asArray()->all(),
            'id',
            'date'
        );

        foreach ($dates as $id => $dateValue) {
            $dates[$id] = date('d.m.Y', strtotime($dateValue));
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'success' => true,
            'dates'   => $dates
        ];
    }

    /**
     * @return array
     */
    public function actionAddDish()
    {
        $orderRoutes = [];

        if (\Yii::$app->request->post()) {
            $date = \Yii::$app->request->post('date');
            $orderRoutes = (new \app\models\Repository\Order())->getRoutesForDate($date);

            $excel = new Excel();
            $excel->loadFromTemplate('files/templates/base.xlsx');
            $excel->prepare($orderRoutes, Excel::MODEL_ROUTE_SHEET, \Yii::$app->request->post());
            $excel->save('route_sheet.xlsx', 'temp');

            \Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'url' => $excel->getUrl()
            ];
        }
    }

    /**
     * @param int $id
     * @param string $date
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionGetDateInventory(int $id, string $date)
    {
        $order = Order::findOne($id);
        if (!$order) {
            throw new NotFoundHttpException('Заказ не найден');
        }

        $dishes = [];
        $scheduleId = 0;
        foreach ($order->schedules as $schedule) {
            if ($schedule->date == $date) {
                $scheduleId = $schedule->id;
                $dishes = $schedule->dishes;
            }
        }

        if (empty($dishes)) {
            throw new NotFoundHttpException('Расписание не найденно');
        }

        $types = [];
        if ($order->subscription_id !== Subscription::NO_SUBSCRIPTION_ID) {
            $ingestion = new Ingestion();
            foreach ($dishes as $dish) {
                if ($dish->ingestion_type == Ingestion::BREAKFAST && $order->subscription->has_breakfast) {
                    $types[$dish->ingestion_type] = $ingestion->getIngestionName($dish->ingestion_type);
                } elseif ($dish->ingestion_type == Ingestion::LUNCH && $order->subscription->has_lunch) {
                    $types[$dish->ingestion_type] = $ingestion->getIngestionName($dish->ingestion_type);
                } elseif ($dish->ingestion_type == Ingestion::DINNER || $dish->ingestion_type == Ingestion::SUPPER) {
                    $types[$dish->ingestion_type] = $ingestion->getIngestionName($dish->ingestion_type);
                }
            }
        }

        return $this->renderAjax(
            '/order/_inventory',
            [
                'date'           => $date,
                'types'          => $types,
                'scheduleId'     => $scheduleId,
                'isSubscription' => $order->subscription_id !== Subscription::NO_SUBSCRIPTION_ID,
                'dishes'         => $dishes,
                'allDishes'      => ArrayHelper::map(
                    Dish::find()->where(['status' => Dish::STATUS_ACTIVE])->asArray()->all(),
                    'id',
                    'name'
                ),
            ]
        );
    }

    /**
     * @param int $ration
     * @return array
     */
    public function actionGetDishesForInventory(int $ration)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $dishes = Dish::find();
        switch ($ration) {
            case Dish::INGESTION_TYPE_BREAKFAST:
                $dishes->andWhere(['is_breakfast' => true]);
                break;
            case Dish::INGESTION_TYPE_DINNER:
                $dishes->andWhere(['is_dinner' => true]);
                break;
            case Dish::INGESTION_TYPE_LUNCH:
                $dishes->andWhere(['is_lunch' => true]);
                break;
            case Dish::INGESTION_TYPE_SUPPER:
                $dishes->andWhere(['is_supper' => true]);
                break;
        }

        return [
            'dishes' => ArrayHelper::map($dishes->asArray()->all(), 'id', 'name')
        ];
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function actionGetEditPrimaryBlock(int $orderId)
    {
        $subscriptions = ArrayHelper::map(
            Subscription::find()->select(['id', 'name'])->where(['status' => Subscription::STATUS_ACTIVE])->asArray(
            )->all(),
            'id',
            'name'
        );

        $paymentTypes = ArrayHelper::map(
            PaymentType::find()->select(['id', 'name'])->where(['status' => PaymentType::STATUS_ACTIVE])->asArray(
            )->all(),
            'id',
            'name'
        );


        $subscriptionCounts = (new Subscription())->getCounts();

        return $this->renderAjax(
            '/order/_primary_block',
            [
                'model'              => \app\models\Repository\Order::findOne($orderId),
                'subscriptions'      => $subscriptions,
                'subscriptionCounts' => $subscriptionCounts,
                'paymentTypes'       => $paymentTypes,
                'intervals'          => (new OrderSchedule())->getIntervals(),
            ]
        );
    }

    /**
     * @param int $orderId
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionEditPrimaryBlock(int $orderId)
    {
        $order = \app\models\Repository\Order::findOne($orderId);

        if ($order->reBuildFromParams(\Yii::$app->request->post()) && $order->saveAll()) {
            return $this->renderAjax(
                '/order/_order_info_block',
                [
                    'model'   => $order,
                    'isError' => false,
                ]
            );
        } else {
            return $this->renderAjax(
                '/order/_order_info_block',
                [
                    'model'   => $order,
                    'isError' => true,
                ]
            );
        }
    }

    /**
     * @return array
     */
    public function actionAddDishForInventory()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $post = \Yii::$app->request->post();
        if (!$post) {
            return [];
        }

        $dishId = $post['dish_id'];
        $scheduleId = $post['schedule_id'];
        $ration = $post['ration'];
        $parentDishId = $post['parent_dish_id'];

        $dish = Dish::findOne($dishId);
        if (!$dish) {
            return [];
        }

        $scheduleDish = new OrderScheduleDish();
        if (!empty($post['old_dish_id'])) {
            $scheduleDish = OrderScheduleDish::find()
                                             ->where(['order_schedule_id' => $scheduleId])
                                             ->andWhere(
                                                 ['dish_id' => empty($parentDishId) ? $post['old_dish_id'] : $parentDishId]
                                             )
                                             ->one();
        } else {
            $oldScheduleDish = OrderScheduleDish::find()
                                                ->where(['order_schedule_id' => $scheduleId])
                                                ->andWhere(['dish_id' => empty($parentDishId) ? null : $parentDishId])
                                                ->andWhere(['ingestion_type' => $ration])
                                                ->one();
            if ($oldScheduleDish) {
                $scheduleDish = $oldScheduleDish;
            }
        }

        if (empty($parentDishId)) {
            $scheduleDish->dish_id = $dish->id;
            $scheduleDish->name = $dish->name;
            $scheduleDish->count = 1;
            $scheduleDish->with_garnish = $dish->with_garnish;
            $scheduleDish->garnish_id = null;
            $scheduleDish->type = $dish->type;
            $scheduleDish->ingestion_type = $ration;
            $scheduleDish->order_schedule_id = $scheduleId;
        } else {
            $scheduleDish->garnish_id = $dish->id;
        }

        if ($scheduleDish->validate() && $scheduleDish->save()) {
            return [
                'success' => true,
                'dish'    => [
                    'href'         => Url::to(['dish/view', 'id' => $dish->id]),
                    'name'         => $dish->name,
                    'dish_id'      => $dish->id,
                    'with_garnish' => (bool)$dish->with_garnish,
                    'description'  => implode(', ', $dish->getComposition()) . ', ' . $dish->weight . 'г.',
                ],
            ];
        }

        return [
            'success' => false
        ];
    }

    /**
     * @return array
     */
    public function actionDeleteDishForInventory()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $post = \Yii::$app->request->post();
        if (!$post) {
            return [];
        }

        $scheduleId = $post['schedule_id'];
        $ration = $post['ration'];
        $dishId = $post['dish_id'];

        $scheduleDish = OrderScheduleDish::find()
                                         ->where(['order_schedule_id' => $scheduleId])
                                         ->andWhere(['ingestion_type' => $ration])
                                         ->andWhere(['dish_id' => $dishId])
                                         ->one();

        if (empty($scheduleDish)) {
            return [
                'message' => "Не удалось найти рацион и блюдо",
                'success' => false,
            ];
        }

        $scheduleDish->dish_id = null;
        $scheduleDish->name = null;
        $scheduleDish->garnish_id = null;
        $scheduleDish->with_garnish = 0;

        if ($scheduleDish->validate() && $scheduleDish->save()) {
            return [
                'success' => true,
            ];
        }

        return [
            'message' => "Ошибка",
            'success' => false,
        ];
    }

    /**
     * @return array
     */
    public function actionDelete()
    {
        $orderIDs = \Yii::$app->request->post('selection');
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $this->log('order-delete', $orderIDs);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($orderIDs as $id) {
            $order = \app\models\Repository\Order::findOne($id);
            $order->status_id = \app\models\Repository\Order::STATUS_ARCHIVED;
            $isUpdated = $order->validate() && $order->save();
            if (!$isUpdated) {
                $transaction->rollBack();
                $this->log('order-delete-fail', ['id' => (string)$id]);
                $this->log('order-delete-fail', ['error' => $order->getFirstErrors()]);
                return [
                    'status' => false,
                    'title'  => \Yii::t('order', 'Order was not deleted')
                ];
            }
        }

        $transaction->commit();
        $this->log('order-delete-success', $orderIDs);
        return [
            'status'      => true,
            'title'       => \Yii::t('order', 'Orders was successful deleted'),
            'description' => \Yii::t('order', 'Chosen orders was successful deleted'),
        ];
    }
}
