<?php

namespace app\controllers;

use app\models\Helper\Excel;
use app\models\Helper\ExcelParser;
use app\models\Helper\Unit;
use app\models\Helper\Weight;
use app\models\Repository\Exception;
use app\models\Repository\Menu;
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
        $searchModel = new Product();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/product/index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $product = new \app\models\Repository\Product();

        if (\Yii::$app->request->post()) {
            $this->log('product-create', []);
            $product->load(\Yii::$app->request->post());
            $product->count = (new Unit($product->unit))->convert((float) $product->count);
            $isValidate = $product->validate();

            if ($isValidate && $product->save()) {
                \Yii::$app->session->addFlash('success', \Yii::t('product', 'Product was saved successfully'));
                $this->log('product-create-success', $product->getAttributes());
                return $this->redirect(['product/index']);
            } else {
                $this->log('product-create-fail', [
                    'name'   => $product->name,
                    'post'   => \Yii::$app->request->post(),
                    'errors' => json_encode($product->getFirstErrors()),
                ]);
            }
        }
        return $this->renderAjax('/product/create', [
            'model'         => $product,
            'exceptionList' => ArrayHelper::map((new Exception())->getExceptionList(), 'id', 'name'),
            'title'         => \Yii::t('product', 'Product create'),
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        $product = \app\models\Repository\Product::findOne($id);
        if (!$product) {
            throw new NotFoundHttpException('Продукт не найден');
        }

        if (\Yii::$app->request->post()) {
            $this->log('product-edit', $product->getAttributes());

            $post = \Yii::$app->request->post();
            $product->load($post);
            $product->count = (new Unit($product->unit))->convert($product->count);
            $isValidate = $product->validate();
            if ($isValidate && $product->save()) {
                $this->log('product-edit-success', $product->getAttributes());
                \Yii::$app->session->addFlash('success', \Yii::t('product', 'Product was saved successfully'));
                return $this->redirect(\Yii::$app->request->referrer);
            } else {
                $this->log('product-edit-fail', [
                    'name' => $product->name,
                    'id'   => $product->id,
                    'post' => $post,
                ]);
            }
        }

        if ($product->unit == Unit::UNIT_KG || $product->unit == Unit::UNIT_LITER) {
            $product->count = (new Weight())->setUnit(Weight::UNIT_KG)->convert($product->count, Weight::UNIT_GR);
        }
        return $this->renderAjax('/product/create', [
            'model'         => $product,
            'exceptionList' => ArrayHelper::map((new Exception())->getExceptionList(), 'id', 'name'),
            'title'         => \Yii::t('product', 'Product update'),
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
        $excel = new Excel();
        $excel->load($inputFile);
        if (!$excel->validate()) {
            throw new \Exception(\Yii::t('file', 'Product file is not suitable'));
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $parserData = $excel->parse();
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($parserData as $productData) {
            $parsedData = (new ExcelParser($productData, ExcelParser::MODEL_PRODUCT))->getParsedArray();
            $product = (new \app\models\Repository\Product())->build($parsedData);
            if (!($product->validate() && $product->save())) {
                $transaction->rollBack();
                \Yii::$app->session->addFlash('danger', \Yii::t('product', 'Product import was failed'));
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
    public function actionDelete(): array
    {
        $productIDs = \Yii::$app->request->post('selection');
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $this->log('product-delete', $productIDs);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($productIDs as $id) {
            $product = \app\models\Repository\Product::findOne($id);
            $product->status = \app\models\Repository\Product::STATUS_DISABLED;
            $isUpdated = $product->validate() && $product->save();
            if (!$isUpdated) {
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
            'i'       => ++$counter,
        ]);
    }

    /**
     * @return array
     */
    public function actionSearch()
    {
        $term = \Yii::$app->request->get('term');
        $element = \Yii::$app->request->get('element');

        $products = \app\models\Repository\Product::find()
                                                  ->select(['*', $element . ' as value'])
                                                  ->andFilterWhere(['like', $element, $term])
                                                  ->andFilterWhere(['status', \app\models\Repository\Product::STATUS_ACTIVE])
                                                  ->orderBy(['count' => SORT_DESC])
                                                  ->asArray()
                                                  ->all();

        $result = [];
        foreach ($products as $product) {
            $result[] = [
                'name'       => $product['name'],
                'weight'     => (new Unit($product['unit']))->format($product['count']),
                'product_id' => $product['id'],
                'unit'       => (new Unit($product['unit']))->getLowerUnit(),
            ];
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }

    /**
     * Получение страницы Листа закупок
     *
     * @return string
     */
    public function actionGetProcurementSheet()
    {
        $products = [];
        $menuList = [];
        $success = true;

        $menus = Menu::find()->all();
        foreach ($menus as $menu) {
            $menuList[$menu->id] = sprintf(
                '%s - %s',
                date('d.m.Y', strtotime($menu->menu_start_date)),
                date('d.m.Y', strtotime($menu->menu_end_date))
            );
        }

        if ($post = \Yii::$app->request->post()) {
            $menuId = $post['menu_id'];
            $chosenMenu = Menu::findOne($menuId);
            try {
                $products = $chosenMenu->getProcurementProducts();
            } catch (\LogicException $e) {
                $success = false;
                $error = $e->getMessage();
            }
        }

        return $this->renderAjax('/product/_procurement_sheet', [
            'success'  => $success,
            'error'    => $error ?? null,
            'menus'    => $menuList,
            'menuId'   => \Yii::$app->request->post('menu_id', 0),
            'title'    => \Yii::t('product', 'Procurement sheet'),
            'products' => $products,
        ]);
    }

    /**
     * Получение страницы Склад учет
     *
     * @return string
     */
    public function actionGetWarehouseAccounting()
    {
        return $this->renderAjax('/product/_warehouse_accounting', [
            'title'   => \Yii::t('product', 'Warehouse accounting'),
            'success' => true,
        ]);
    }

    /**
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function actionSaveProcurementSheet()
    {
        if ($menuId = \Yii::$app->request->post('menu_id')) {
            \Yii::$app->response->format = Response::FORMAT_JSON;

            $products = Menu::findOne($menuId)->getProcurementProducts();
            $productList = [];
            foreach ($products as $product) {
                $productList[] = [
                    'id'         => $product->id,
                    'name'       => $product->name,
                    'available'  => $product->count,
                    'need'       => $product->getNeedCount(),
                    'not_enough' => $product->getNotEnoughCount(),
                ];
            }

            $excel = new Excel();
            $excel->loadFromTemplate('files/templates/base.xlsx');
            $excel->prepare($productList, Excel::MODEL_PRODUCT, \Yii::$app->request->post());
            $excel->save('procurement_sheet.xlsx', 'temp');

            \Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'url' => $excel->getUrl()
            ];
        }

        return [];
    }

    /**
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function actionSaveWarehouseAccounting()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $products = \app\models\Repository\Product::find()
                                                  ->andWhere(['>', 'status', \app\models\Repository\Product::STATUS_DISABLED])
                                                  ->all();
        $productList = [];
        foreach ($products as $product) {
            $productList[] = [
                'id'        => $product->id,
                'name'      => $product->name,
                'available' => (new Unit($product->unit))->format($product->count),
            ];
        }

        $excel = new Excel();
        $excel->loadFromTemplate('files/templates/base.xlsx');
        $excel->prepare($productList, Excel::MODEL_PRODUCT, \Yii::$app->request->post());
        $excel->save('warehouse_accounting.xlsx', 'temp');

        \Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'url' => $excel->getUrl()
        ];
    }
}
