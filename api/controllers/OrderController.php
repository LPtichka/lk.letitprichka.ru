<?php
namespace app\api\controllers;

use app\api\BaseActiveController;
use app\models\Repository\Order;

class OrderController extends BaseActiveController
{
    public $modelClass = 'app\models\Repository\Order';

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class'       => 'yii\rest\IndexAction',
                'modelClass'  => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        $verbs              = parent::verbs();
        $verbs['calculate'] = ['GET'];

        return $verbs;
    }

    /**
     * Создание заказа
     *
     * @return array
     */
    public function actionCreate(): array
    {
        $order = new Order();
        try {
            if ($order->buildFromApi(\Yii::$app->request->post()) && $order->saveAll()) {
                return [
                    'success' => true,
                    'id' => $order->id,
                ];
            }
        } catch (\Exception $e) {
            \Yii::$app->response->setStatusCode(500);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        \Yii::$app->response->setStatusCode(400);
        return [
            'success' => false,
            'errors' => $order->getFirstErrors()
        ];
    }
}