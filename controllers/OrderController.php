<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\Helper\ExcelParser;
use app\models\Helper\Weight;
use app\models\Repository\Address;
use app\models\Repository\Customer;
use app\models\Repository\Exception;
use app\models\Repository\Subscription;
use app\models\search\Order;
use app\models\search\PaymentType;
use app\models\Search\Product;
use yii\helpers\ArrayHelper;
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

        return $this->render('/order/create', [
            'model' => $order,
            'payments' => $paymentTypes,
            'exceptions' => $exceptions,
            'subscriptions' => $subscriptions,
            'customers' => ArrayHelper::map(Customer::find()->asArray()->all(), 'id', 'fio'),
            'subscriptionCounts' => $subscriptionCounts,
            'title' => \Yii::t('order', 'Order create'),
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
            'exception' => new Exception(),
            'exceptions' => $exceptions,
            'i'        => ++$counter,
        ]);
    }

    /**
     * @param int $customerId
     * @return array
     */
    public function actionGetAddress(int $customerId = 0)
    {
        $addressList = [];

        $customer = \app\models\Repository\Customer::findOne($customerId);
        if ($customer) {
            $addresses = Address::find()
                ->where([
                    'customer_id' => $customerId,
                    'status' => Address::STATUS_ACTIVE
                ])
                ->asArray()
                ->all();
            foreach ($addresses as $address) {
                $address['selected'] = $customer->default_address_id == $address['id'];
                $addressList[] = $address;
            }
        }

        $addressList[] = [
            'id' => '',
            'full_address' => '',
            'city' => '',
            'street' => '',
            'house' => '',
            'housing' => '',
            'building' => '',
            'flat' => '',
            'postcode' => '',
            'description' => '',
            'selected' => empty($addressList),
        ];

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $addressList;
    }
}
