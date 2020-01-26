<?php
namespace app\api;

use app\models\Search\SearchModelInterface;
use app\rbac\ControllerAccessEvent;
use yii\rest\ActiveController;

class BaseActiveController extends ActiveController
{
    public $modelClass;
    public $searchModelClass;
    public $serializer = [
        'class'              => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
        'preserveKeys'       => false,
    ];

    /**
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        if ($this->searchModelClass !== null) {
            $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        }
        return $actions;
    }

    /**
     * @return \yii\data\ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        /** @var SearchModelInterface $searchModel */
        $searchModel = new $this->searchModelClass();
        return $searchModel->search(\Yii::$app->request->queryParams);
    }

    /**
     * @param string $action
     * @param null $model
     * @param array $params
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        $event = new ControllerAccessEvent([
            'action' => $this->action,
            'model'  => $model,
        ]);

        \Yii::$app->controller->trigger(ControllerAccessEvent::AFTER_CHECK_ACCESS, $event);
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'create' => ['POST'],
            'update' => ['POST'],
            'view'   => ['GET'],
            'index'  => ['GET'],
        ];
    }
}