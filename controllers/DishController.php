<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\Helper\ExcelParser;
use app\models\Helper\Weight;
use app\models\Repository\DishProduct;
use app\models\Search\Dish;
use yii\db\IntegrityException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DishController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new Dish();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/dish/index', [
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
        $dish = new \app\models\Repository\Dish();

        if (\Yii::$app->request->post()) {
            $this->log('dish-create', []);
            $post = \Yii::$app->request->post();
            $dish->load($post);

            $dishProducts = [];
            foreach ($post['DishProduct'] as $dishProduct) {
                if (empty($dishProduct['product_id'])) {
                    continue;
                }
                $product = new DishProduct();
                $product->load($dishProduct, '');
                $dishProducts[] = $product;
            }
            $dish->setDishProducts($dishProducts);

            $isValidate  = $dish->validate();
            $transaction = \Yii::$app->db->beginTransaction();
            if ($isValidate && $dish->save()) {
                $isProductSaved = true;
                foreach ($dish->dishProducts as $product) {
                    $product->dish_id = $dish->id;
                    if ($product->validate() && $product->save()) {
                        $this->log('dish-product-create-success', ['name' => $dish->name]);
                    } else {
                        \Yii::$app->session->addFlash('danger', \Yii::t('dish', 'Dish product has error'));
                        $isProductSaved = false;
                        $transaction->rollBack();
                        break;
                    }
                }
                if ($isProductSaved) {
                    \Yii::$app->session->addFlash('success', \Yii::t('dish', 'Dish type was saved successfully'));
                    $transaction->commit();
                    $this->log('dish-create-success', ['name' => $dish->name]);
                    return $this->redirect(['dish/index']);
                }
            } else {
                $transaction->rollBack();
                $this->log('dish-create-fail', ['name' => $dish->name, 'errors' => json_encode($dish->getFirstErrors())]);
            }
        }

        if (empty($dish->dishProducts)) {
            $dish->setDishProducts([new DishProduct()]);
        }
        return $this->render('/dish/create', [
            'model' => $dish,
            'title' => \Yii::t('dish', 'Dish create'),
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionView(int $id)
    {
        $dish = \app\models\Repository\Dish::findOne($id);
        if (!$dish) {
            throw new NotFoundHttpException('Блюдо не найдено');
        }

        if (\Yii::$app->request->post()) {
            $this->log('dish-update', []);
            $post = \Yii::$app->request->post();
            $dish->load($post);

            $dishProducts = [];
            if (!empty($post['DishProduct'])) {
                foreach ($post['DishProduct'] as $dishProduct) {
                    if (empty($dishProduct['product_id'])) {
                        continue;
                    }
                    $product = new DishProduct();
                    $product->load($dishProduct, '');
                    $dishProducts[] = $product;
                }
            }
            if (empty($dishProducts)) {
                \Yii::$app->session->addFlash('danger', \Yii::t('dish', 'Dish products is empty'));
                if (empty($dish->dishProducts)) {
                    $dish->setDishProducts([new DishProduct()]);
                }
                return $this->render('/dish/create', [
                    'model' => $dish,
                    'title' => \Yii::t('dish', 'Dish update'),
                ]);
            }
            $dish->setDishProducts($dishProducts);

            $isValidate  = $dish->validate();
            $transaction = \Yii::$app->db->beginTransaction();
            if ($isValidate && $dish->save()) {
                $isProductSaved = true;
                DishProduct::deleteAll(['dish_id' => $dish->id]);
                if (!empty($dish->dishProducts)) {
                    foreach ($dish->dishProducts as $product) {
                        $product->dish_id = $dish->id;
                        if ($product->validate() && $product->save()) {
                            $this->log('dish-product-update-success', ['name' => $dish->name]);
                        } else {
                            \Yii::$app->session->addFlash('danger', sprintf(
                                '%s <br /> %s',
                                \Yii::t('dish', \Yii::t('dish', 'Product was not saved')),
                                implode('<br />', $product->getFirstErrors())));
                            $isProductSaved = false;
                            $transaction->rollBack();
                            break;
                        }
                    }
                }
                if ($isProductSaved) {
                    \Yii::$app->session->addFlash('success', \Yii::t('dish', 'Dish was saved successfully'));
                    $transaction->commit();
                    $this->log('dish-update-success', ['name' => $dish->name]);
                    return $this->redirect(['dish/view', 'id' => $dish->id]);
                }
            } else {
                \Yii::$app->session->addFlash('danger', sprintf(
                    '%s <br /> %s',
                    \Yii::t('dish', 'Dish was not saved successfully'),
                    implode('<br />', $dish->getFirstErrors())));
                $transaction->rollBack();
                $this->log('dish-update-fail', ['name' => $dish->name, 'errors' => json_encode($dish->getFirstErrors())]);
            }
        }

        if (empty($dish->dishProducts)) {
            $dish->setDishProducts([new DishProduct()]);
        }
        return $this->render('/dish/create', [
            'model' => $dish,
            'title' => \Yii::t('dish', 'Dish update'),
        ]);
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionDelete()
    {
        $dishIds = \Yii::$app->request->post('selection');

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $this->log('dish-delete', $dishIds);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($dishIds as $id) {
            $errorMessage = \Yii::t('order', 'Dish was not deleted');
            try {
                $isDelete = DishProduct::deleteAll(['dish_id' => $id])
                    && \app\models\Repository\Dish::deleteAll(['id' => $id]);
            } catch (IntegrityException $e) {
                $this->log('dish-delete-fail', [$e->getMessage()]);
                $errorMessage = \Yii::t('dish', 'You can not delete dish before you dont delete products');
            }

            if (empty($isDelete)) {
                $transaction->rollBack();
                $this->log('dish-delete-fail', $dishIds);
                return [
                    'status' => false,
                    'title'  => $errorMessage
                ];
            }
        }

        $transaction->commit();
        $this->log('dish-delete-success', $dishIds);
        return [
            'status' => true,
            'title'  => \Yii::t('dish', 'Dish was successful deleted')
        ];
    }

    /**
     * Испорт типов оплат из Excel
     *
     * @throws \Exception
     */
    public function actionImport()
    {
        $inputFile = $_FILES['xml'];
        $excel     = new Excel();
        $excel->load($inputFile);
        if (!$excel->validate()) {
            throw new \Exception(\Yii::t('file', 'Dish file is not suitable'));
        }

        $parserData = $excel->parse();
        $data       = (new ExcelParser(array_merge([$excel->getHeaderRow()], $parserData), ExcelParser::MODEL_DISH))->getParsedArray();
        $dish       = (new \app\models\Repository\Dish())->build($data, Weight::UNIT_GR);

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $transaction                 = \Yii::$app->db->beginTransaction();
        if ($dish->validate() && $dish->save()) {
            foreach ($dish->dishProducts as $dishProduct) {
                if (empty($dishProduct->product_id)) {
                    \Yii::$app->session->addFlash('danger', \Yii::t('dish', 'Dish product is unknown'));
                    $transaction->rollBack();
                    return [
                        'success' => false,
                    ];
                }
                $dishProduct->dish_id = $dish->id;
                if (!($dishProduct->validate() && $dishProduct->save())) {
                    $errorMessages = implode(', <br />', $dishProduct->getFirstErrors());
                    \Yii::$app->session->addFlash('danger', \Yii::t('dish', 'Dish product saving error:') . $errorMessages);
                    $transaction->rollBack();
                    return [
                        'success' => false,
                    ];
                }
            }
        } else {
            $transaction->rollBack();
            $errorMessages = implode(', <br />', $dish->getFirstErrors());
            \Yii::$app->session->addFlash('danger', \Yii::t('dish', 'Dish saving error:') . $errorMessages);
            return [
                'success' => false,
            ];
        }

        $transaction->commit();
        \Yii::$app->session->addFlash('success', \Yii::t('address', 'Dish was imported successfully'));

        return [
            'success' => true,
        ];
    }

    /**
     * @param int|null $id
     * @return array
     */
    public function actionExport(int $id = null)
    {
        if ($id) {
            $dishes = [\app\models\Repository\Dish::findOne($id)];
        } else {
            $dishes = (new Dish())->export(\Yii::$app->request->post());
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $excel = new Excel();
            $excel->loadFromTemplate('files/templates/base.xlsx');
            $excel->prepare($dishes, Excel::MODEL_DISH);
            $excel->save('dish.xlsx', 'temp');
            return [
                'success' => true,
                'url'     => $excel->getUrl(),
            ];
        } catch (\Exception $e) {
            \Yii::info($e->getMessage());
        }

        return [
            'success' => false,
        ];
    }

    /**
     * @param int|null $id
     * @return array
     */
    public function actionSearch()
    {
        $term    = \Yii::$app->request->get('term');
        $element = \Yii::$app->request->get('element');

        $dishes = \app\models\Repository\Dish::find()
            ->select(['*', $element . ' as value'])
            ->andFilterWhere(['like', $element, $term])
            ->asArray()
            ->all();

        $result = [];
        foreach ($dishes as $product) {
            $result[] = [
                'name'       => $product['name'],
                'product_id' => $product['id'],
            ];
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }


    /**
     * @param int $counter
     * @return string
     */
    public function actionGetRow(int $counter)
    {
        return $this->renderAjax('/order/_order_product', [
            'dish' => new \app\models\Repository\OrderScheduleDish(),
            'i'    => ++$counter,
        ]);
    }
}
