<?php
namespace app\api;

use yii\filters\auth\CompositeAuth;
use yii\web\Request;
use yii\web\Response;
use yii\web\User;

class Module extends \yii\base\Module
{
    public function init()
    {
        /** @var Request $request */
        $request = \Yii::$app->request;
        $authenticator = new CompositeAuth();

        $authenticator->authMethods = [
            \yii\filters\auth\HttpBasicAuth::class,
            \yii\filters\auth\QueryParamAuth::class,
        ];

        /** @var User $user */
        $user = \Yii::$app->user;

        /** @var Response $response */
        $response = \Yii::$app->response;

        $authenticator->authenticate($user, $request, $response);
        parent::init();
    }
}