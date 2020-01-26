<?php
namespace app\controllers;

use app\components\Dadata;
use app\models\Builder\Suggestions;
use app\models\Helper\Excel;
use app\models\Helper\ExcelParser;
use app\models\Repository\Customer;
use app\models\Search\Address;
use app\models\Search\PaymentType;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class AddressController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new Address();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/address/index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionCreate()
    {
        $address = new \app\models\Repository\Address();

        if (\Yii::$app->request->post()) {
            $this->log('address-create', []);
            $address->load(\Yii::$app->request->post());
            $isValidate = $address->validate();
            if ($isValidate && $address->save()) {
                \Yii::$app->session->addFlash('success', \Yii::t('address', 'Address type was saved successfully'));
                $this->log(
                    'address-create-success',
                    $address->getAttributes()
                );
                return $this->redirect(['address/index']);
            } else {
                $this->log(
                    'payment-create-fail',
                    [
                        'name' => $address->full_address,
                        'post' => \Yii::$app->request->post(),
                        'errors' => json_encode($address->getFirstErrors())
                    ]
                );
            }
        }
        return $this->renderAjax('/address/create', [
            'model' => $address,
            'title' => \Yii::t('address', 'Address create'),
            'customers' => ArrayHelper::map(Customer::find()->asArray()->all(), 'id', 'fio'),
        ]);
    }

    /**
     * @param string $query
     * @return array
     */
    public function actionGetByQuery(string $query)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        /** @var Request $request */
        $request = \Yii::$app->request;
        try {
            $suggestions = (new Dadata())->getSuggestions('address', [
                'query' => $query,
                'limit' => $request->get('limit') ?? 10
            ]);
        } catch (\Exception $e) {
            \Yii::info($e->getMessage());
        }

        return (new Suggestions())->setSuggestions($suggestions['suggestions'] ?? [])->build();
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        $address = \app\models\Repository\Address::findOne($id);
        if (!$address) {
            throw new NotFoundHttpException('Адрес не найден');
        }

        if (\Yii::$app->request->post()) {
            $this->log('address-create', []);
            $address->load(\Yii::$app->request->post());
            $isValidate = $address->validate();
            if ($isValidate && $address->save()) {
                \Yii::$app->session->addFlash('success', \Yii::t('address', 'Address type was saved successfully'));
                $this->log('address-create-success', ['name' => $address->full_address]);
                return $this->redirect(['address/index']);
            } else {
                $this->log('address-create-fail', ['name' => $address->full_address, 'errors' => json_encode($address->getFirstErrors())]);
            }
        }

        return $this->renderAjax('/address/create', [
            'model' => $address,
            'title' => \Yii::t('address', 'Address update'),
            'customers' => ArrayHelper::map(Customer::find()->asArray()->all(), 'id', 'fio'),
        ]);
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionDelete()
    {
        $addressIds = \Yii::$app->request->post('selection');

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $this->log('address-delete', $addressIds);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($addressIds as $id) {
            $isDelete = \app\models\Repository\Address::deleteAll(['id' => $id]);
            if (!$isDelete) {
                $transaction->rollBack();
                $this->log('address-delete-fail', $addressIds);
                return [
                    'status' => false,
                    'title'  => \Yii::t('address', 'Address was not deleted')
                ];
            }
        }

        $transaction->commit();
        $this->log('address-delete-success', $addressIds);
        return [
            'status' => true,
            'title'  => \Yii::t('payment', 'Address was successful deleted')
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
            throw new \Exception(\Yii::t('file', 'Address file is not suitable'));
        }

        $parserData = $excel->parse();

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $transaction                 = \Yii::$app->db->beginTransaction();
        foreach ($parserData as $item) {
            $parsedData = (new ExcelParser($item, ExcelParser::MODEL_ADDRESS))->getParsedArray();
            $address = (new \app\models\Repository\Address())->build($parsedData);

            if (!($address->validate() && $address->save())) {
                $transaction->rollBack();
                $errorMessage = implode(',<br />', $address->getFirstErrors());
                \Yii::$app->session->addFlash('danger', \Yii::t('address', 'Address type import was failed: ' . $errorMessage));
                return [
                    'success' => false,
                ];
            }
        }

        $transaction->commit();
        \Yii::$app->session->addFlash('success', \Yii::t('address', 'Address type was imported successfully'));

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
        $address = (new Address())->export(\Yii::$app->request->post());

        $excel = new Excel();
        $excel->loadFromTemplate('files/templates/base.xlsx');
        $excel->prepare($address, Excel::MODEL_ADDRESS);
        $excel->save('addresses.xlsx', 'temp');

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'url' => $excel->getUrl(),
        ];
    }

    /**
     * @param int $counter
     * @return string
     */
    public function actionGetRow(int $counter)
    {
        return $this->renderAjax('/customer/_address', [
            'address'          => new \app\models\Repository\Address(),
            'i'                => ++$counter,
            'defaultAddressId' => null,
        ]);
    }
}
