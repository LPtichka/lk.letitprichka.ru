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
                $this->log('franchise-create-success', $franchise->getAttributes());
                return $this->redirect(['franchise/index']);
            } else {
                $this->log('franchise-create-fail', [
                    'name'   => $franchise->name,
                    'errors' => json_encode($franchise->getFirstErrors()),
                ]);
            }
        }

        return $this->renderAjax('/franchise/create', [
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
        $franchise = \app\models\Repository\Franchise::findOne($id);
        if (!$franchise) {
            throw new NotFoundHttpException('Франшиза не найден');
        }

        if (\Yii::$app->request->post()) {
            $this->log('franchise-edit', $franchise->getAttributes());

            $post = \Yii::$app->request->post();
            $franchise->load($post);
            $isValidate = $franchise->validate();
            if ($isValidate && $franchise->save()) {
                $this->log('franchise-edit-success', $franchise->getAttributes());
                \Yii::$app->session->addFlash('success', \Yii::t('franchise', 'Franchise was saved successfully'));
                return $this->redirect(['franchise/index']);
            } else {
                $this->log('franchise-edit-fail', [
                    'name' => $franchise->name,
                    'id'   => $franchise->id,
                ]);
            }
        }

        return $this->renderAjax('/franchise/create', [
            'model' => $franchise,
            'title' => \Yii::t('franchise', 'Franchise update'),
        ]);
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionDelete()
    {
        $franchiseIds = \Yii::$app->request->post('selection');
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $this->log('franchise-delete', $franchiseIds);
        $transaction = \Yii::$app->db->beginTransaction();

        foreach ($franchiseIds as $id) {
            $franchise = \app\models\Repository\Franchise::findOne($id);
            $franchise->status = \app\models\Repository\Franchise::STATUS_DELETED;
            if (!$franchise->save(false)) {
                $transaction->rollBack();
                $this->log('franchise-delete-fail', $franchiseIds);
                return [
                    'status' => false,
                    'title'  => \Yii::t('franchise', 'Franchise was not deleted')
                ];
            }
        }

        $transaction->commit();
        $this->log('franchise-delete-success', $franchiseIds);
        return [
            'status' => true,
            'title'  => \Yii::t('franchise', 'Franchise was successful deleted')
        ];
    }
}
