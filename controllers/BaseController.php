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
     * @param string $category
     * @param array $params
     */
    protected function log(string $messageType, string $category, array $params = []): void
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;

        switch ($messageType) {
            case 'delete':
                $message = sprintf('Пользователь #%d удаляет следущие типы оплаты: %s', $user->getId(), implode(',', $params));
                break;
            case 'delete-success':
                $message = sprintf('Пользователь #%d удалил следущие типы оплаты: %s', $user->getId(), implode(',', $params));
                break;
            case 'delete-fail':
                $message = sprintf('Пользователь #%d не смог удалить следущие типы оплаты: %s', $user->getId(), implode(',', $params));
                break;
            case 'edit':
                $message = sprintf('Пользователь #%d редактирует тип оплаты "%s"', $user->getId(), $params['name']);
                break;
            case 'edit-success':
                $message = sprintf('Пользователь #%d отредактировал тип оплаты "%s"', $user->getId(), $params['name']);
                break;
            case 'edit-fail':
                $message = sprintf('Пользователь #%d не смог отредактировать тип оплаты "%s"', $user->getId(), $params['name']);
                break;
            case 'create':
                $message = sprintf('Пользователь #%d создает тип оплаты', $user->getId());
                break;
            case 'create-success':
                $message = sprintf('Пользователь #%d создал тип оплаты "%s"', $user->getId(), $params['name']);
                break;
            case 'create-fail':
                $message = sprintf('Пользователь #%d не смогм создать тип оплаты "%s", ошибки: %s', $user->getId(), $params['name'], $params['errors']);
                break;

            case 'product-create':
                $message = sprintf('Пользователь #%d создает продукт', $user->getId());
                break;
            case 'product-create-success':
                $message = sprintf('Пользователь #%d создал продукт "%s"', $user->getId(), $params['name']);
                break;
            case 'product-create-fail':
                $message = sprintf('Пользователь #%d не смог создать продукт "%s", ошибки: %s', $user->getId(), $params['name'], $params['errors']);
                break;
            case 'product-edit':
                $message = sprintf('Пользователь #%d редактирует продукт "%s"', $user->getId(), $params['name']);
                break;
            case 'product-edit-success':
                $message = sprintf('Пользователь #%d отредактировал продукт "%s"', $user->getId(), $params['name']);
                break;
            case 'product-edit-fail':
                $message = sprintf('Пользователь #%d не смог отредактировать продукт "%s"', $user->getId(), $params['name']);
                break;
            case 'product-delete':
                $message = sprintf('Пользователь #%d удаляет следущие продукты: %s', $user->getId(), implode(',', $params));
                break;
            case 'product-delete-success':
                $message = sprintf('Пользователь #%d удалил следущие продукты: %s', $user->getId(), implode(',', $params));
                break;
            case 'product-delete-fail':
                $message = sprintf('Пользователь #%d не смог удалить следущие продукты: %s', $user->getId(), $params['id']);
                break;
            default:
                $message = sprintf('Неизвестное действия для пользователя %s', $user->getId());
        }

        \Yii::info($message, $category);
    }
}

