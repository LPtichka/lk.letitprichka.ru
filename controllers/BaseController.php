<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\Helper\ExcelParser;
use app\models\search\PaymentType;
use app\models\search\Product;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BaseController extends Controller
{
    /**
     * @param string $messageType
     * @param array $params
     */
    protected function log(string $messageType, array $params = []): void
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;

        $params['user_id'] = $user->getId();
        $params['user_email'] = $user->getEmail();

        $message = json_encode($params);

        \Yii::info($message, $messageType);
    }
}

