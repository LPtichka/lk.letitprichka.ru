<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\search\PaymentType;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PaymentTypeController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new PaymentType();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/payment/types', [
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
            $this->log('create', []);
            $payment->load(\Yii::$app->request->post());
            $isValidate = $payment->validate();
            if ($isValidate && $payment->save()) {
                \Yii::$app->session->addFlash('success', \Yii::t('payment', 'Payment type was saved successfully'));
                $this->log('create-success', ['name' => $payment->name]);
                return $this->redirect(['payment-type/index']);
            } else {
                $this->log('create-fail', ['name' => $payment->name, 'errors' => json_encode($payment->getFirstErrors())]);
            }
        }
        return $this->render('/payment/create', [
            'model' => $payment,
        ]);
    }

    /**
     * @param string $messageType
     * @param array $params
     */
    private function log(string $messageType, array $params = []): void
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;

        switch ($messageType) {
            case 'delete':
                $message = sprintf('Пользователь #%d удаляет следущие типы оплаты: %s', $user->getId(), implode(',', $params));
                break;
            case 'delete-success':
                $message = sprintf('Пользователь #%d удалил следущие типы оплаты: %s', $user->getId(), implode(',', $params));
                break;
            case 'delete-fail':
                $message = sprintf('Пользователь #%d не смог удалить следущие типы оплаты: %s', $user->getId(), implode(',', $params));
                break;
            case 'edit':
                $message = sprintf('Пользователь #%d редактирует тип оплаты "%s"', $user->getId(), $params['name']);
                break;
            case 'edit-success':
                $message = sprintf('Пользователь #%d отредактировал тип оплаты "%s"', $user->getId(), $params['name']);
                break;
            case 'edit-fail':
                $message = sprintf('Пользователь #%d не смог отредактировать тип оплаты "%s"', $user->getId(), $params['name']);
                break;
            case 'create':
                $message = sprintf('Пользователь #%d создает тип оплаты', $user->getId());
                break;
            case 'create-success':
                $message = sprintf('Пользователь #%d создал тип оплаты "%s"', $user->getId(), $params['name']);
                break;
            case 'create-fail':
                $message = sprintf('Пользователь #%d не смогм создать тип оплаты "%s", ошибки: %s', $user->getId(), $params['name'], $params['errors']);
                break;
            default:
                $message = sprintf('Неизвестное действия для пользователя %s', $user->getId());
        }

        \Yii::info($message, 'payment-delete');
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
            $this->log('edit', ['name' => $payment->name]);
            $payment->load(\Yii::$app->request->post());
            $isValidate = $payment->validate();
            if ($isValidate && $payment->save()) {
                $this->log('edit-success', ['name' => $payment->name]);
                \Yii::$app->session->addFlash('success', \Yii::t('payment', 'Payment type was saved successfully'));
                return $this->redirect(['payment-type/index']);
            } else {
                $this->log('edit-fail', ['name' => $payment->name]);
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

        $this->log('delete', $paymentIds);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($paymentIds as $id) {
            $isDelete = \app\models\Repository\PaymentType::deleteAll(['id' => $id]);
            if (!$isDelete) {
                $transaction->rollBack();
                $this->log('delete-fail', $paymentIds);
                return [
                    'status' => false,
                    'title'  => \Yii::t('order', 'Payments was not deleted')
                ];
            }
        }

        $transaction->commit();
        $this->log('delete-success', $paymentIds);
        return [
            'status' => true,
            'title'  => \Yii::t('payment', 'Payment was successful deleted')
        ];
    }

    /**
     * Испорт товаров из Excel
     */
    public function actionImport()
    {
        $inputFile = $_FILES['xml'];
        $excel       = new Excel();
        $excel->load($inputFile);
        if (!$excel->validate()) {
            throw new \Exception(\Yii::t('file', 'Product file is not suitable'));
        }

        $parserData = $excel->parse();

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($parserData as $payment) {
            $paymentType = new \app\models\Repository\PaymentType();
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
        $excel->prepare($payments);
        $excel->save('payments.xlsx', 'temp');

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'url' => $excel->getUrl(),
        ];
    }
}
