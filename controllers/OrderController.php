<?php
namespace app\controllers;

use app\models\Repository\Address;
use app\models\Repository\Customer;
use app\models\Repository\Exception;
use app\models\Repository\Franchise;
use app\models\Repository\OrderSchedule;
use app\models\Repository\Subscription;
use app\models\search\Order;
use app\models\search\PaymentType;
use app\models\User;
use yii\helpers\ArrayHelper;
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
     */
    public function actionCreate()
    {
        $order = new \app\models\Repository\Order();

        if (\Yii::$app->request->post()) {
            $this->log('order-create', []);
            if ($order->build(\Yii::$app->request->post()) && $order->saveAll()) {
                \Yii::$app->session->addFlash('success', \Yii::t('product', 'Order was saved successfully'));
                $this->log('order-create-success', [
                    'name' => $order->id,
                    'id'   => $order->id,
                ]);
                return $this->redirect(['order/index']);
            } else {
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
            'payments'           => $paymentTypes,
            'exceptions'         => $exceptions,
            'subscriptions'      => $subscriptions,
            'franchises'         => ArrayHelper::map($franchiseQuery->asArray()->all(), 'id', 'name'),
            'customers'          => ArrayHelper::map(Customer::find()->asArray()->all(), 'id', 'fio'),
            'intervals'          => (new OrderSchedule())->getIntervals(),
            'subscriptionCounts' => $subscriptionCounts,
            'title'              => \Yii::t('order', 'Order create'),
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

        return $this->renderAjax('/order/_exception', [
            'exception'  => new Exception(),
            'exceptions' => $exceptions,
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
            function ($schedule) {
                return date('d.m.Y', strtotime($schedule['date']));
            },
            function ($schedule) {
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
}
