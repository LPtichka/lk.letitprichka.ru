<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\Search\PaymentType;
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

        if (\Yii::$app->request->post()) {
            $this->log('payment-create', []);
            $payment->load(\Yii::$app->request->post());
            $isValidate = $payment->validate();
            if ($isValidate && $payment->save()) {
                \Yii::$app->session->addFlash('success', \Yii::t('payment', 'Payment type was saved successfully'));
                $this->log('payment-create-success', $payment->getAttributes());
                return $this->redirect(['payment-type/index']);
            } else {
                $this->log(
                    'payment-create-fail',
                    [
                        'name'   => $payment->name,
                        'post'   => json_encode(\Yii::$app->request->post()),
                        'errors' => json_encode($payment->getFirstErrors())
                    ]
                );
            }
        }
        return $this->renderAjax('/payment/create', [
            'model' => $payment,
            'title' => \Yii::t('payment', 'Payment create'),
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

        if (\Yii::$app->request->post()) {
            $this->log('payment-edit',
                [
                    $payment->getAttributes()
                ]
            );
            $payment->load(\Yii::$app->request->post());
            $isValidate = $payment->validate();
            if ($isValidate && $payment->save()) {
                $this->log(
                    'payment-edit-success',
                    $payment->getAttributes()
                );
                \Yii::$app->session->addFlash('success', \Yii::t('payment', 'Payment type was saved successfully'));
                return $this->redirect(['payment-type/index']);
            } else {
                $this->log(
                    'payment-edit-fail',
                    [
                        $payment->getAttributes(),
                        'post' => \Yii::$app->request->post()
                    ]
                );
            }
        }
        return $this->renderAjax('/payment/create', [
            'model' => $payment,
            'title' => \Yii::t('payment', 'Payment update'),
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

        $this->log('payment-delete', $paymentIds);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($paymentIds as $id) {
            $payment = \app\models\Repository\PaymentType::findOne($id);
            $payment->status = \app\models\Repository\PaymentType::STATUS_DELETED;
            if (!$payment->save(false)) {
                $transaction->rollBack();
                $this->log('payment-delete-fail', $paymentIds);
                return [
                    'status' => false,
                    'title'  => \Yii::t('order', 'Payments was not deleted')
                ];
            }
        }

        $transaction->commit();
        $this->log('payment-delete-success', $paymentIds);
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
