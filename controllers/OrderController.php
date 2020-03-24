<?php
namespace app\controllers;

use app\models\Common\Ingestion;
use app\models\Helper\Excel;
use app\models\Repository\Address;
use app\models\Repository\Customer;
use app\models\Repository\Dish;
use app\models\Repository\Exception;
use app\models\Repository\Franchise;
use app\models\Repository\OrderSchedule;
use app\models\Repository\OrderScheduleDish;
use app\models\Repository\Subscription;
use app\models\Search\Order;
use app\models\Search\PaymentType;
use app\models\User;
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
        $searchModel  = new Order();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/order/index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $order = new \app\models\Repository\Order();

        if (\Yii::$app->request->post()) {
            $this->log('order-create', []);
            if ($order->build(\Yii::$app->request->post()) && $order->saveAll()) {
                \Yii::$app->session->addFlash('success', \Yii::t('order', 'Order was saved successfully'));
                $this->log('order-create-success', [
                    'name' => $order->id,
                    'id'   => $order->id,
                ]);
                return $this->redirect(['order/index']);
            } else {
                \Yii::$app->session->addFlash('danger', \Yii::t('order', 'Order was not saved successfully'));
                $this->log('order-create-fail', [
                    'name'   => $order->id,
                    'errors' => json_encode($order->getFirstErrors()),
                ]);
            }
        }

        if (empty($order->customer)) {
            $order->setCustomer(new Customer());
        }

        if (empty($order->address)) {
            $order->setAddress(new Address());
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
            PaymentType::find()->select(['id', 'name'])->where(['status' => PaymentType::STATUS_ACTIVE])->asArray()->all(),
            'id',
            'name'
        );

        $exceptions = ArrayHelper::map(
            Exception::find()->select(['id', 'name'])->where(['status' => Exception::STATUS_ACTIVE])->asArray()->all(),
            'id',
            'name'
        );

        $subscriptions = ArrayHelper::map(
            Subscription::find()->select(['id', 'name'])->where(['status' => Subscription::STATUS_ACTIVE])->asArray()->all(),
            'id',
            'name'
        );

        $subscriptionCounts = (new Subscription())->getCounts();
        $franchiseQuery     = Franchise::find()->where(['status' => Franchise::STATUS_ACTIVE]);
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        if (!empty($user->franchise_id)) {
            $franchiseQuery->andWhere(['franchise_id' => $user->franchise_id]);
        }

        return $this->render('/order/create', [
            'model'              => $order,
            'payments'           => $paymentTypes,
            'addresses'          => [
                '' => \Yii::t('order', 'New address'),
            ],
            'exceptions'         => $exceptions,
            'franchises'         => ArrayHelper::map($franchiseQuery->asArray()->all(), 'id', 'name'),
            'subscriptions'      => $subscriptions,
            'intervals'          => (new OrderSchedule())->getIntervals(),
            'customers'          => ArrayHelper::map(Customer::find()->asArray()->all(), 'id', 'fio'),
            'subscriptionCounts' => $subscriptionCounts,
            'title'              => \Yii::t('order', 'Order create'),
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionView(int $id)
    {
        $order = \app\models\Repository\Order::findOne($id);

        if (\Yii::$app->request->post()) {
            $this->log('order-update', []);
            if ($order->build(\Yii::$app->request->post()) && $order->saveAll()) {
                \Yii::$app->session->addFlash('success', \Yii::t('order', 'Order was saved successfully'));
                $this->log('order-update-success', [
                    'name' => $order->id,
                    'id'   => $order->id,
                ]);
                return $this->redirect(['order/view', 'id' => $order->id]);
            } else {
                $this->log('order-update-fail', [
                    'name'   => $order->id,
                    'errors' => json_encode($order->getFirstErrors()),
                ]);
            }
        }

        if (empty($order->customer)) {
            $order->setCustomer(new Customer());
        }

        if (empty($order->address)) {
            $order->setAddress(new Address());
        }

        if (empty($order->exceptions)) {
            $order->setExceptions([new Exception()]);
        }


        $paymentTypes = ArrayHelper::map(
            PaymentType::find()->select(['id', 'name'])->where(['status' => PaymentType::STATUS_ACTIVE])->asArray()->all(),
            'id',
            'name'
        );

        $exceptions = ArrayHelper::map(
            Exception::find()->select(['id', 'name'])->where(['status' => Exception::STATUS_ACTIVE])->asArray()->all(),
            'id',
            'name'
        );

        $subscriptions = ArrayHelper::map(
            Subscription::find()->select(['id', 'name'])->where(['status' => Subscription::STATUS_ACTIVE])->asArray()->all(),
            'id',
            'name'
        );

        $subscriptionCounts = (new Subscription())->getCounts();
        $franchiseQuery     = Franchise::find()->where(['status' => Franchise::STATUS_ACTIVE]);
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        if (!empty($user->franchise_id)) {
            $franchiseQuery->andWhere(['franchise_id' => $user->franchise_id]);
        }

        return $this->render('/order/create', [
            'model'              => $order,
            'addresses'          => ArrayHelper::map(
                Address::find()->where(['customer_id' => $order->customer_id])->asArray()->all(),
                'id',
                'full_address'
            ),
            'payments'           => $paymentTypes,
            'exceptions'         => $exceptions,
            'subscriptions'      => $subscriptions,
            'franchises'         => ArrayHelper::map($franchiseQuery->asArray()->all(), 'id', 'name'),
            'customers'          => ArrayHelper::map(Customer::find()->asArray()->all(), 'id', 'fio'),
            'intervals'          => (new OrderSchedule())->getIntervals(),
            'subscriptionCounts' => $subscriptionCounts,
            'title'              => \Yii::t('order', 'Order №') . $order->id,
        ]);
    }

    /**
     * @param int $counter
     * @return string
     */
    public function actionAddException(int $counter)
    {
        $exceptions = ArrayHelper::map(
            Exception::find()->select(['id', 'name'])->where(['status' => PaymentType::STATUS_ACTIVE])->asArray()->all(),
            'id',
            'name'
        );

        return $this->renderAjax('/order/_order_exception', [
            'exception'  => new Exception(),
            'exceptions' => $exceptions,
            'disabled'   => false,
            'i'          => ++$counter,
        ]);
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
                ->where([
                    'customer_id' => $customerId,
                    'status'      => Address::STATUS_ACTIVE
                ])
                ->asArray()
                ->all();
            foreach ($addresses as $address) {
                $address['selected'] = $customer->default_address_id == $address['id'];
                $addressList[]       = $address;
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
        $exceptions    = ArrayHelper::map(
            Exception::find()->select(['id', 'name'])->where(['status' => PaymentType::STATUS_ACTIVE])->asArray()->all(),
            'id',
            'name'
        );
        $customer      = Customer::findOne($customerId);
        if ($customer) {
            foreach ($customer->exceptions as $key => $exception) {
                $exceptionList .= $this->renderPartial('/order/_order_exception', [
                    'exception'  => $exception,
                    'exceptions' => $exceptions,
                    'disabled'   => false,
                    'i'          => $key,
                ]);
            }
        }

        if (empty($exceptionList)) {
            $exceptionList = $this->renderPartial('/order/_order_exception', [
                'exception'  => new Exception(),
                'exceptions' => $exceptions,
                'disabled'   => false,
                'i'          => 1,
            ]);
        }

        return $exceptionList;
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function actionGetMenu(int $orderId = 0)
    {
        $order     = Order::findOne($orderId);
        $intervals = (new OrderSchedule())->getIntervals();
        $addresses = Address::find()
            ->where(['customer_id' => $order->customer_id, 'status' => Address::STATUS_ACTIVE])
            ->asArray()
            ->all();

        return $this->renderAjax('/order/_menu', [
            'order'     => $order,
            'intervals' => $intervals,
            'addresses' => ArrayHelper::map($addresses, 'id', 'full_address'),
        ]);
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
            function ($schedule){
                return date('d.m.Y', strtotime($schedule['date']));
            },
            function ($schedule){
                return date('d.m.Y', strtotime($schedule['date']));
            }
        );

        return $this->renderAjax('/order/_request_deffer', [
            'order' => $order,
            'dates' => $dates,
        ]);
    }

    /**
     * Отложить выполнение заказа с определенной даты
     *
     * @param int $orderID
     * @return array
     */
    public function actionDeffer(int $orderID)
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

        $transaction    = \Yii::$app->db->beginTransaction();
        $orderSchedules = OrderSchedule::find()
            ->where(['order_id' => $orderID])
            ->andWhere(['>=', 'date', date('Y-m-d', strtotime($oldDateFrom))])
            ->orderBy(['date' => SORT_ASC])
            ->all();

        $newDateTimestamp = strtotime($newDateFrom);
        foreach ($orderSchedules as $key => $orderSchedule) {
            $orderSchedule->date = date('Y-m-d', $newDateTimestamp + ($key * 86400));
            if (!$orderSchedule->save(false)) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'title'   => \Yii::t('order', 'Error schedule saving'),
                ];
            }
        }

        try {
            $transaction->commit();
        } catch (\yii\db\Exception $e) {
            return [
                'success' => false,
                'title'   => \Yii::t('order', 'Error saving to database'),
            ];
        }

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
            $date        = \Yii::$app->request->post('date');
            $orderRoutes = (new \app\models\Repository\Order())->getRoutesForDate($date);
        }

        return $this->renderAjax('/order/_get_route_sheet', [
            'routes' => $orderRoutes,
            'date'   => $date ?? '',
            'title'  => \Yii::t('order', 'Order sheet'),
        ]);
    }

    /**
     * @return array
     */
    public function actionSaveRouteSheet()
    {
        $orderRoutes = [];

        if (\Yii::$app->request->post()) {
            $date        = \Yii::$app->request->post('date');
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
     * @return string|array
     */
    public function actionGetCustomerSheet(int $id)
    {
        $userSheet = [];
        if ($post = \Yii::$app->request->post()) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return $this->generateCustomerSheetFile($post);
        }

        $orderId = ArrayHelper::getValue(
            OrderSchedule::find()->where(['order_id' => $id])->asArray()->one(),
            'order_id'
        );
        $dates   = ArrayHelper::map(
            OrderSchedule::find()->where(['order_id' => $id])->asArray()->all(),
            'id',
            'date'
        );
        foreach ($dates as $id => $dateValue) {
            $dates[$id] = date('d.m.Y', strtotime($dateValue));
        }

        return $this->renderAjax('/order/_get_user_sheet', [
            'routes' => $userSheet,
            'dates'  => $dates,
            'id'     => $orderId,
            'title'  => \Yii::t('order', 'Customer sheet'),
        ]);
    }

    /**
     * @param array $post
     * @return array
     */
    private function generateCustomerSheetFile(array $post): array
    {
        $date = OrderSchedule::find()
            ->where(['id' => $post['schedule_id']])
            ->one();

        if (!$date) {
            return ['success' => false];
        }

        $customerSheet = $date->order->getCustomerSheetsByDate([$date->date]);

        try {
            $excel = new Excel();
            $excel->loadFromTemplate('files/templates/base.xlsx');
            $excel->prepare($customerSheet, Excel::MODEL_CUSTOMER_SHEET, \Yii::$app->request->post());
            $excel->save('customer_sheet.xlsx', 'temp');
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            return ['success' => false];
        }

        return [
            'success' => true,
            'url'     => $excel->getUrl(),
        ];
    }

    /**
     * @return array
     */
    public function actionAddDish()
    {
        $orderRoutes = [];

        if (\Yii::$app->request->post()) {
            $date        = \Yii::$app->request->post('date');
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

        $types     = [];
        if ($order->subscription_id !== Subscription::NO_SUBSCRIPTION_ID) {
            $ingestion = new Ingestion();
            foreach ($dishes as $dish) {
                $types[$dish->ingestion_type] = $ingestion->getIngestionName($dish->ingestion_type);
            }
        }

        return $this->renderAjax('/order/_inventory', [
            'date'           => $date,
            'types'          => $types,
            'scheduleId'          => $scheduleId,
            'isSubscription' => $order->subscription_id !== Subscription::NO_SUBSCRIPTION_ID,
            'dishes'         => $dishes,
        ]);
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

        $dish = Dish::findOne($dishId);
        if (!$dish) {
            return [];
        }

        $scheduleDish = new OrderScheduleDish();
        if (!empty($post['old_dish_id'])) {
            $scheduleDish = OrderScheduleDish::find()
                ->where(['order_schedule_id' => $scheduleId])
                ->andWhere(['dish_id' => $post['old_dish_id']])
                ->one();
        }

        $scheduleDish->dish_id = $dish->id;
        $scheduleDish->order_schedule_id = $scheduleId;
        $scheduleDish->name = $dish->name;
        $scheduleDish->count = 1;
        $scheduleDish->type = $dish->type;
        $scheduleDish->ingestion_type = $ration;

        if ($scheduleDish->validate() && $scheduleDish->save()) {
            return [
                'success' => true,
                'dish' => [
                    'href' => Url::to(['dish/view', 'id' => $dish->id]),
                    'name' => $dish->name,
                    'description' => implode(', ', $dish->getComposition()) . ', ' . $dish->weight . 'г.',
                ],
            ];
        }

        return [
            'success' => false
        ];
    }
}
