<?php

namespace app\rbac;

use yii\base\Controller;
use yii\web\ForbiddenHttpException;

class AccessControl extends \yii2mod\rbac\filters\AccessControl
{
    /**
     * @param $action
     * @return bool
     */
    public function beforeAction($action): bool
    {
        $controller = $action->controller;
        $controller->on(ControllerAccessEvent::AFTER_FIND_MODEL, [$this, 'checkAccess']);
        $controller->on(ControllerAccessEvent::AFTER_CHECK_ACCESS, [$this, 'checkAccess']);
        return parent::beforeAction($action);
    }

    /**
     * @param ControllerAccessEvent $event
     * @return bool
     * @throws ForbiddenHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function checkAccess(ControllerAccessEvent $event)
    {
        $action          = $event->action;
        $model           = $event->model;
        $params          = $this->params;
        $params['model'] = $model;

        if (self::can($action->getUniqueId(), $params)) {
            return true;
        }

        throw new ForbiddenHttpException(\Yii::t('app', 'You are not allowed to access this page'));
    }

    /**
     * @param $action
     * @param array $params
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function can($action, $params = [])
    {
        if (\Yii::$app->user->can('/' . ltrim($action, '/'), $params)) {
            return true;
        }

        /** @var Controller $controller */
        list($controller) = \Yii::$app->createController($action);

        do {
            if (\Yii::$app->user->can('/' . ltrim($controller->getUniqueId() . '/*', '/'), $params)) {
                return true;
            }
            $controller = $controller->module;
        } while ($controller !== null);

        return false;
    }
}