<?php
namespace app\controllers;

use app\models\Helper\Excel;
use app\models\Repository\Franchise;
use app\models\Search\User;
use yii\helpers\ArrayHelper;
use yii\rbac\DbManager;
use yii\rbac\Role;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UserController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new User();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/user/index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionCreate()
    {
        $user = new \app\models\User();

        if (\Yii::$app->request->post()) {
            $this->log('user-create', []);
            $user->load(\Yii::$app->request->post());
            $user->scenario = \app\models\User::SCENARIO_CREATE;
            $isValidate     = $user->validate();

            /** @var DbManager $authManager */
            $authManager = \Yii::$app->authManager;

            $transaction = \Yii::$app->db->beginTransaction();
            if ($isValidate
                && $user->save()
                && ($role = $authManager->getRole($user->getRole()))
                && ($emailSended = $user->sendNewUserEmail())
                && $authManager->assign($role, $user->id)
            ) {
                \Yii::$app->session->addFlash('success', \Yii::t('user', 'User was saved successfully'));
                $this->log('user-create-success', $user->getAttributes());
                $transaction->commit();
                return $this->redirect(['user/index']);
            } else {
                $transaction->rollBack();
                $this->log('user-create-fail', [
                    'name'   => $user->fio,
                    'errors' => json_encode($user->getFirstErrors()),
                ]);
            }
        }

        return $this->renderAjax('/user/create', [
            'model'              => $user,
            'franchises'         => ArrayHelper::map(Franchise::find()->asArray()->all(), 'id', 'name'),
            'canBlockUser'       => \Yii::$app->user->can('/user/block'),
            'canGrantPrivileges' => \Yii::$app->user->can('/user/grant-privilege'),
            'title'              => \Yii::t('user', 'User create'),
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        $user = \app\models\User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        if (\Yii::$app->request->post()) {
            $this->log('user-edit', $user->getAttributes());

            $user->load(\Yii::$app->request->post());
            $isValidate = $user->validate();
            /** @var Role $role */
            if ($isValidate
                && $user->save()
            ) {
                $this->log('user-edit-success', $user->getAttributes());
                \Yii::$app->session->addFlash('success', \Yii::t('user', 'User was saved successfully'));
                return $this->redirect(['user/index']);
            } else {
                $this->log('user-edit-fail', $user->getAttributes());
            }
        }

        return $this->renderAjax('/user/create', [
            'model'              => $user,
            'franchises'         => ArrayHelper::map(Franchise::find()->asArray()->all(), 'id', 'name'),
            'canBlockUser'       => \Yii::$app->user->can('/user/block'),
            'canGrantPrivileges' => \Yii::$app->user->can('/user/grant-privilege'),
            'title'              => \Yii::t('user', 'User update'),
        ]);
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     * @throws ForbiddenHttpException
     */
    public function actionDelete()
    {
        if (!\Yii::$app->user->can('/user/delete')) {
            throw new ForbiddenHttpException(\Yii::t('app', 'Access denided'));
        }

        $userIds                     = \Yii::$app->request->post('selection');
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $this->log('user-delete', $userIds);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($userIds as $id) {
            $user = \app\models\User::findOne($id);
            if (!$user) {
                $message = \Yii::t('user', 'Users #{id} is not exist', ['id' => $id]);
                $this->log('user-delete-fail', [
                    'id'    => (string) $id,
                    'error' => $message,
                ]);
                return [
                    'status'      => false,
                    'title'       => \Yii::t('app', 'Error'),
                    'description' => $message
                ];
            }
            if ($user->status !== \app\models\User::STATUS_INACTIVE) {
                $message = \Yii::t('user', 'Users #{id} is not blocked', ['id' => $id]);
                $this->log('user-delete-fail', [
                    'id'    => (string) $id,
                    'error' => $message,
                ]);
                return [
                    'status'      => false,
                    'title'       => \Yii::t('app', 'Error'),
                    'description' => $message,
                ];
            }

            $isDelete = \app\models\User::deleteAll(['id' => $id]);
            if (!$isDelete) {
                $transaction->rollBack();
                $this->log('user-delete-fail', ['id' => (string) $id]);
                return [
                    'status' => false,
                    'title'  => \Yii::t('user', 'Users was not deleted')
                ];
            }
        }

        $transaction->commit();
        $this->log('user-delete-success', $userIds);
        return [
            'status'      => true,
            'title'       => \Yii::t('product', 'Users was successful deleted'),
            'description' => \Yii::t('product', 'Chosen users was successful deleted'),
        ];
    }

    /**
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function actionExport()
    {
        $payments = (new User())->export(\Yii::$app->request->post());

        $excel = new Excel();
        $excel->loadFromTemplate('files/templates/base.xlsx');
        $excel->prepare($payments, Excel::MODEL_USER);
        $excel->save('users.xlsx', 'temp');

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'url' => $excel->getUrl(),
        ];
    }
}
