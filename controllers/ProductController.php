<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\Helper\ExcelParser;
use app\models\Helper\Weight;
use app\models\Repository\Exception;
use app\models\Search\Product;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ProductController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new Product();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/product/index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionCreate()
    {
        $product = new \app\models\Repository\Product();

        if (\Yii::$app->request->post()) {
            $this->log('product-create', []);
            $product->load(\Yii::$app->request->post());
            $isValidate = $product->validate();

            if ($isValidate && $product->save()) {
                \Yii::$app->session->addFlash('success', \Yii::t('product', 'Product was saved successfully'));
                $this->log('product-create-success', [
                    'name' => $product->name,
                    'id'   => $product->id,
                ]);
                return $this->redirect(['product/index']);
            } else {
                $this->log('product-create-fail', [
                    'name'   => $product->name,
                    'errors' => json_encode($product->getFirstErrors()),
                ]);
            }
        }
        return $this->render('/product/create', [
            'model' => $product,
            'exceptionList' => ArrayHelper::map(Exception::find()->asArray()->all(), 'id', 'name'),
            'title' => \Yii::t('product', 'Product create'),
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        $product = \app\models\Repository\Product::findOne($id);
        if (!$product) {
            throw new NotFoundHttpException('Продукт не найден');
        }

        if (\Yii::$app->request->post()) {
            $this->log('product-edit', [
                'name' => $product->name,
                'id'   => $product->id,
            ]);

            $post = \Yii::$app->request->post();
            !empty($post['Product']['weight']) && $post['Product']['weight'] = (new Weight())->convert((float)$post['Product']['weight'], Weight::UNIT_KG);

            $product->load($post);
            $isValidate = $product->validate();
            if ($isValidate && $product->save()) {
                $this->log('product-edit-success', [
                    'name' => $product->name,
                    'id'   => $product->id,
                ]);
                \Yii::$app->session->addFlash('success', \Yii::t('product', 'Product was saved successfully'));
                return $this->redirect(['product/index']);
            } else {
                $this->log('product-edit-fail', [
                    'name' => $product->name,
                    'id'   => $product->id,
                ]);
            }
        }

        $product->weight = (new Weight())->setUnit(Weight::UNIT_KG)->convert($product->weight, Weight::UNIT_GR);
        return $this->render('/product/create', [
            'model' => $product,
            'exceptionList' => ArrayHelper::map(Exception::find()->asArray()->all(), 'id', 'name'),
            'title' => \Yii::t('product', 'Product update'),
        ]);
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
            throw new \Exception(\Yii::t('file', 'Product file is not suitable'));
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $parserData  = $excel->parse();
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($parserData as $productData) {
            $parsedData = (new ExcelParser($productData, ExcelParser::MODEL_PRODUCT))->getParsedArray();
            $product    = (new \app\models\Repository\Product())->build($parsedData);
            if (!($product->validate() && $product->save())) {
                $transaction->rollBack();
                \Yii::$app->session->addFlash('danger', \Yii::t('product', 'Payment type import was failed'));
                return ['success' => false,];
            }
        }

        $transaction->commit();
        \Yii::$app->session->addFlash('success', \Yii::t('product', 'Products was imported successfully'));

        return [
            'success' => true,
        ];
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionDelete()
    {
        $productIDs = \Yii::$app->request->post('selection');
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $this->log('product-delete', $productIDs);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($productIDs as $id) {
            $isDelete = \app\models\Repository\Product::deleteAll(['id' => $id]);
            if (!$isDelete) {
                $transaction->rollBack();
                $this->log('product-delete-fail', ['id' => (string)$id]);
                return [
                    'status' => false,
                    'title'  => \Yii::t('product', 'Products was not deleted')
                ];
            }
        }

        $transaction->commit();
        $this->log('product-delete-success', $productIDs);
        return [
            'status'      => true,
            'title'       => \Yii::t('product', 'Products was successful deleted'),
            'description' => \Yii::t('product', 'Chosen products was successful deleted'),
        ];
    }

    /**
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function actionExport()
    {
        $payments = (new Product())->export(\Yii::$app->request->post());

        $excel = new Excel();
        $excel->loadFromTemplate('files/templates/base.xlsx');
        $excel->prepare($payments, Excel::MODEL_PRODUCT);
        $excel->save('products.xlsx', 'temp');

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
        return $this->renderAjax('/dish/_product', [
            'product' => new \app\models\Repository\DishProduct(),
            'i'            => ++$counter,
        ]);
    }

    /**
     * @return array
     */
    public function actionSearch()
    {
        $term    = \Yii::$app->request->get('term');
        $element = \Yii::$app->request->get('element');

        $products = \app\models\Repository\Product::find()
            ->select(['*', $element . ' as value'])
            ->andFilterWhere(['like', $element, $term])
            ->orderBy(['count'   => SORT_DESC])
            ->asArray()
            ->all();

        $result = [];
        foreach ($products as $product) {
            $result[] = [
                'name' => $product['name'],
                'weight' => $product['weight'],
                'count' => $product['count'],
                'product_id' => $product['id'],
            ];
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
}
