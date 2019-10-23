<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\search\PaymentType;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PaymentTypeController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new PaymentType();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/payment/index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionCreate()
    {
        $payment = new \app\models\Repository\PaymentType();

        $logCategory = 'payment-create';
        if (\Yii::$app->request->post()) {
            $this->log('create', $logCategory, []);
            $payment->load(\Yii::$app->request->post());
            $isValidate = $payment->validate();
            if ($isValidate && $payment->save()) {
                \Yii::$app->session->addFlash('success', \Yii::t('payment', 'Payment type was saved successfully'));
                $this->log('create-success', $logCategory, ['name' => $payment->name]);
                return $this->redirect(['payment-type/index']);
            } else {
                $this->log('create-fail', $logCategory, ['name' => $payment->name, 'errors' => json_encode($payment->getFirstErrors())]);
            }
        }
        return $this->render('/payment/create', [
            'model' => $payment,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        $payment = \app\models\Repository\PaymentType::findOne($id);
        if (!$payment) {
            throw new NotFoundHttpException('Тип оплаты не найден');
        }

        $logCategory = 'payment-update';
        if (\Yii::$app->request->post()) {
            $this->log('edit', $logCategory, ['name' => $payment->name]);
            $payment->load(\Yii::$app->request->post());
            $isValidate = $payment->validate();
            if ($isValidate && $payment->save()) {
                $this->log('edit-success', $logCategory, ['name' => $payment->name]);
                \Yii::$app->session->addFlash('success', \Yii::t('payment', 'Payment type was saved successfully'));
                return $this->redirect(['payment-type/index']);
            } else {
                $this->log('edit-fail', $logCategory, ['name' => $payment->name]);
            }
        }
        return $this->render('/payment/create', [
            'model' => $payment,
        ]);
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionDelete()
    {
        $paymentIds = \Yii::$app->request->post('selection');

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $logCategory = 'payment-delete';
        $this->log('delete', $logCategory, $paymentIds);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($paymentIds as $id) {
            $isDelete = \app\models\Repository\PaymentType::deleteAll(['id' => $id]);
            if (!$isDelete) {
                $transaction->rollBack();
                $this->log('delete-fail', $logCategory, $paymentIds);
                return [
                    'status' => false,
                    'title'  => \Yii::t('order', 'Payments was not deleted')
                ];
            }
        }

        $transaction->commit();
        $this->log('delete-success', $logCategory, $paymentIds);
        return [
            'status' => true,
            'title'  => \Yii::t('payment', 'Payment was successful deleted')
        ];
    }

    /**
     * Испорт типов оплат из Excel
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
            throw new \Exception(\Yii::t('file', 'Product file is not suitable'));
        }

        $parserData = $excel->parse();

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $transaction                 = \Yii::$app->db->beginTransaction();
        foreach ($parserData as $payment) {
            $paymentType       = new \app\models\Repository\PaymentType();
            $paymentType->name = $payment[0] ?? null;

            if (!($paymentType->validate() && $paymentType->save())) {
                $transaction->rollBack();
                \Yii::$app->session->addFlash('danger', \Yii::t('payment', 'Payment type import was failed'));
                return [
                    'success' => false,
                ];
            }
        }

        $transaction->commit();
        \Yii::$app->session->addFlash('success', \Yii::t('payment', 'Payment type was imported successfully'));

        return [
            'success' => true,
        ];
    }

    /**
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function actionExport()
    {
        $payments = (new PaymentType())->export(\Yii::$app->request->post());

        $excel = new Excel();
        $excel->loadFromTemplate('files/templates/base.xlsx');
        $excel->prepare($payments, Excel::MODEL_PAYMENT);
        $excel->save('payments.xlsx', 'temp');

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'url' => $excel->getUrl(),
        ];
    }
}
