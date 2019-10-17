<?php
namespace app\controllers;

use app\models\search\PaymentType;
use Yii;
use yii\web\Controller;

class PaymentTypeController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new PaymentType();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('/payment/types', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
