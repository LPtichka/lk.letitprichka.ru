<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\Search\PaymentType;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ReportController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('/report/index', []);
    }
}
