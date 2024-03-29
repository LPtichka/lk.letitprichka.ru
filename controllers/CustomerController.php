<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\Helper\ExcelParser;
use app\models\Repository\Address;
use app\models\Repository\CustomerException;
use app\models\Repository\Exception;
use app\models\Search\Customer;
use app\models\Search\PaymentType;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CustomerController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new Customer();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/customer/index', [
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
        $customer = new \app\models\Repository\Customer();

        if ($post = \Yii::$app->request->post()) {
            if ($this->saveCustomer($customer, $post)) {
                return $this->redirect(['customer/index']);
            }
        }

        // Если адреса у пользователя нет нужно засетить пустой адрес
        if (empty($customer->addresses)) {
            $customer->setAddresses([new Address()]);
        }

        $exceptions = ArrayHelper::map(
            Exception::find()->select(['id', 'name'])->where(['status' => Exception::STATUS_ACTIVE])->asArray()->all(),
            'id',
            'name'
        );

        if (empty($customer->exceptions)) {
            $customer->setExceptions([new Exception()]);
        }

        return $this->renderAjax('/customer/create', [
            'title' => \Yii::t('customer', 'Customer create'),
            'exceptions' => $exceptions,
            'model' => $customer,
        ]);
    }

    /**
     * @param \app\models\Repository\Customer $customer
     * @param array $post
     * @return bool
     * @throws \yii\db\Exception
     */
    private function saveCustomer(\app\models\Repository\Customer $customer, array $post): bool
    {
        $this->log('customer-create', []);
        $customer->load($post);
        $isValidate = $customer->validate();

        $transaction = \Yii::$app->db->beginTransaction();

        // Обновим все адреса покупателя, проставив им статус Удален
        Address::updateAll(['status' => Address::STATUS_DELETED], ['customer_id' => $customer->id]);
        CustomerException::deleteAll(['customer_id' => $customer->id]);
        if ($isValidate && $customer->save()) {
            $addresses         = [];
            $exceptions        = [];
            $isAddressesValid  = true;
            $defaultAddressKey = $post['Address']['is_default_address'] ?? null;
            if (!empty($post['Address'])) {
                foreach ($post['Address'] as $key => $address) {
                    if (!is_int($key)) continue;
                    if (empty($address['full_address'])) continue;

                    $tempAddress = null;
                    if (!empty($address['id'])) {
                        $tempAddress = Address::findOne($address['id']);
                    }
                    if (empty($tempAddress)) {
                        $tempAddress = new Address();
                    }

                    $tempAddress->load($address, '');
                    $tempAddress->customer_id = $customer->id;
                    $tempAddress->status      = Address::STATUS_ACTIVE;
                    $tempAddress->prepareAddress($tempAddress, $address['full_address']);

                    if (!$tempAddress->validate()) {
                        $isAddressesValid = false;
                    }
                    $addresses[$key] = $tempAddress;
                }
            }

            $isExceptionsValid  = true;
            if (!empty($post['Exception'])) {
                foreach ($post['Exception'] as $key => $exception) {
                    if (!is_int($key)) continue;
                    if (empty($exception['id'])) continue;

                    $exception = Exception::findOne($exception['id']);
                    $exceptions[$key] = $exception;
                }
            }

            $customer->setAddresses($addresses);
            $customer->setExceptions($exceptions);

            if (!$isAddressesValid || !$isExceptionsValid) {
                $transaction->rollBack();
                \Yii::$app->session->addFlash('danger', \Yii::t('customer', 'Addresses has errors'));
                return false;
            } else {
//                $customer->setAddresses($addresses);
//                $customer->setExceptions($exceptions);

                foreach ($customer->addresses as $key => $address) {
                    if (!$customer->addresses[$key]->save()) {
                        $transaction->rollBack();
                        \Yii::$app->session->addFlash('warning', \Yii::t('customer', 'Addresses can not be saved'));
                        return false;
                    }
                }

                foreach ($customer->exceptions as $key => $exception) {
                    $customerException = new CustomerException();
                    $customerException->customer_id = $customer->id;
                    $customerException->exception_id = $exception->id;
                    if (!$customerException->save()) {
                        $transaction->rollBack();
                        \Yii::$app->session->addFlash('warning', \Yii::t('customer', 'Exception can not be saved'));
                        return false;
                    }
                }

                if ($defaultAddressKey !== null) {
                    $customer->default_address_id = !empty($customer->addresses[$defaultAddressKey])
                        ? $customer->addresses[$defaultAddressKey]->id
                        : $customer->addresses[0]->id;
                    if (!$customer->save()) {
                        $transaction->rollBack();
                        \Yii::$app->session->addFlash('warning', \Yii::t('customer', 'Customer save error'));
                        return false;
                    }
                }

                $transaction->commit();

                \Yii::$app->session->addFlash('success', \Yii::t('customer', 'Customer was saved successfully'));
                $this->log('customer-create-success', ['name' => $customer->fio]);
                return true;
            }
        } else {
            $transaction->rollBack();
            $this->log('customer-create-fail', ['name' => $customer->fio, 'errors' => json_encode($customer->getFirstErrors())]);
            return false;
        }
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionView(int $id)
    {
        $customer = \app\models\Repository\Customer::findOne($id);
        if (!$customer) {
            throw new NotFoundHttpException('Покупатель не найден');
        }

        if ($post = \Yii::$app->request->post()) {
            if ($this->saveCustomer($customer, $post)) {
                return $this->redirect(['customer/index']);
            }
        }

        if (empty($customer->addresses)) {
            $customer->setAddresses([new Address()]);
        }

        $exceptions = ArrayHelper::map(
            Exception::find()->select(['id', 'name'])->where(['status' => Exception::STATUS_ACTIVE])->asArray()->all(),
            'id',
            'name'
        );

        return $this->renderAjax('/customer/create', [
            'model'      => $customer,
            'exceptions' => $exceptions,
            'title'      => \Yii::t('customer', 'Customer update: #') . $customer->id . '<small> ' . $customer->fio . ' </small>',
        ]);
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionDelete()
    {
        $customerIDs = \Yii::$app->request->post('selection');

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $this->log('customer-delete', $customerIDs);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($customerIDs as $id) {
            $customer         = \app\models\Repository\Customer::findOne($id);
            $customer->status = 0;
            if (!$customer->save()) {
                $transaction->rollBack();
                $this->log('customer-delete-fail', ['id' => (string) $id]);
                return [
                    'status' => false,
                    'title'  => \Yii::t('product', 'Customers was not deleted')
                ];
            }
        }

        $transaction->commit();
        $this->log('customer-delete-success', $customerIDs);
        return [
            'status'      => true,
            'title'       => \Yii::t('customer', 'Customer was successful deleted'),
            'description' => \Yii::t('customer', 'Chosen customers was successful deleted'),
        ];
    }

    /**
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function actionExport()
    {
        $customers = (new Customer())->export(\Yii::$app->request->post());

        $excel = new Excel();
        $excel->loadFromTemplate('files/templates/base.xlsx');
        $excel->prepare($customers, Excel::MODEL_CUSTOMER);
        $excel->save('customer.xlsx', 'temp');

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'url' => $excel->getUrl(),
        ];
    }

    /**
     * Импорт товаров из Excel
     *
     * @throws \PHPExcel_Exception
     * @throws \Exception
     */
    public function actionImport()
    {
        $inputFile = $_FILES['xml'];
        $excel     = new Excel();
        $excel->load($inputFile);
        if (!$excel->validate()) {
            throw new \Exception(\Yii::t('file', 'Customer file is not suitable'));
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $parserData  = $excel->parse();
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($parserData as $customerData) {
            $parsedData = (new ExcelParser($customerData, ExcelParser::MODEL_CUSTOMER))->getParsedArray();
            $customer   = (new \app\models\Repository\Customer())->build($parsedData);
            if (!($customer->validate() && $customer->save())) {
                $transaction->rollBack();
                \Yii::$app->session->addFlash('danger', \Yii::t('customer', 'Customer type import was failed'));
                return ['success' => false];
            }

            if (!empty($customer->addresses)) {
                foreach ($customer->addresses as $key => $address) {
                    $address->customer_id = $customer->id;
                    if (!($address->validate() && $address->save())) {
                        $transaction->rollBack();
                        \Yii::$app->session->addFlash('danger', \Yii::t('customer', 'Customer import was failed'));
                        return ['success' => false];
                    }
                    $customer->default_address_id = $address->id;
                    $customer->save();
                }
            }
        }

        $transaction->commit();
        \Yii::$app->session->addFlash('success', \Yii::t('customer', 'Customers was imported successfully'));

        return [
            'success' => true,
        ];
    }

    public function getCustomerByFio(string $fio)
    {
        $customers = \app\models\Repository\Customer::find()->where([
            'LIKE', 'fio', $fio
        ])->asArray()->all();

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            ArrayHelper::map($customers, 'id', 'fio')
        ];
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

        return $this->renderAjax('/customer/_exception', [
            'exception'  => new Exception(),
            'exceptions' => $exceptions,
            'i'          => ++$counter,
        ]);
    }
}
