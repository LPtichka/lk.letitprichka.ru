<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\Helper\ExcelParser;
use app\models\Helper\Weight;
use app\models\Repository\Exception;
use app\models\search\Franchise;
use app\models\Search\Product;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class FranchiseController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new Franchise();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/franchise/index', [
            'searchModel'  => $searchModel,
            'title' => \Yii::t('franchise', 'Franchises'),
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Создание франшизы
     *
     * @return string
     */
    public function actionCreate()
    {
        $franchise = new \app\models\Repository\Franchise();

        if (\Yii::$app->request->post()) {
            $this->log('franchise-create', []);
            $franchise->load(\Yii::$app->request->post());
            $isValidate = $franchise->validate();

            if ($isValidate && $franchise->save()) {
                \Yii::$app->session->addFlash('success', \Yii::t('franchise', 'Franchise was saved successfully'));
                $this->log('franchise-create-success', [
                    'name' => $franchise->name,
                    'id'   => $franchise->id,
                ]);
                return $this->redirect(['franchise/index']);
            } else {
                $this->log('franchise-create-fail', [
                    'name'   => $franchise->name,
                    'errors' => json_encode($franchise->getFirstErrors()),
                ]);
            }
        }

        return $this->render('/franchise/create', [
            'model' => $franchise,
            'title' => \Yii::t('franchise', 'Franchise create'),
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
}
