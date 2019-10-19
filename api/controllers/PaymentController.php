<?php
namespace app\api\controllers;

use app\api\BaseActiveController;

class PaymentController extends BaseActiveController
{
    public $modelClass = 'app\models\OrderDelivery';

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
            ],
            'view'  => [
                'class'       => 'yii\rest\ViewAction',
                'modelClass'  => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
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
}