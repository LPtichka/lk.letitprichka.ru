<?php
namespace app\controllers;

use app\models\Repository\SubscriptionDiscount;
use app\models\search\Subscription;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SubscriptionController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new Subscription();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('/subscription/index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $subscription = new \app\models\Repository\Subscription();

        if (\Yii::$app->request->post()) {
            $this->log('subscriptions-create', []);
            if ($subscription->build(\Yii::$app->request->post()) && $subscription->validateAll() && $subscription->saveAll()) {
                \Yii::$app->session->addFlash('success', \Yii::t('subscription', 'Subscription type was saved successfully'));
                $this->log('subscriptions-create-success', ['name' => $subscription->name]);
                return $this->redirect(['subscription/index']);
            } else {
                $this->log('subscriptions-create-fail', ['name' => $subscription->name, 'errors' => json_encode($subscription->getFirstErrors())]);
            }
        }

        if (empty($subscription->discounts)) {
            $subscription->setDiscounts([new SubscriptionDiscount()]);
        }

        return $this->render('/subscription/create', [
            'model' => $subscription,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionView(int $id)
    {
        $subscription = \app\models\Repository\Subscription::findOne($id);
        if (!$subscription) {
            throw new NotFoundHttpException();
        }

        if (\Yii::$app->request->post()) {
            $this->log('subscriptions-update', []);
            if ($subscription->build(\Yii::$app->request->post()) && $subscription->validateAll() && $subscription->saveAll()) {
                \Yii::$app->session->addFlash('success', \Yii::t('subscription', 'Subscription type was saved successfully'));
                $this->log('subscriptions-update-success', ['name' => $subscription->name]);
                return $this->redirect(['subscription/view', 'id' => $subscription->id]);
            } else {
                $this->log('subscriptions-update-fail', ['name' => $subscription->name, 'errors' => json_encode($subscription->getFirstErrors())]);
            }
        }

        if (empty($subscription->discounts)) {
            $subscription->setDiscounts([new SubscriptionDiscount()]);
        }

        return $this->render('/subscription/create', [
            'model' => $subscription,
        ]);
    }

    /**
     * @param int $counter
     * @return string
     */
    public function actionAddDiscount(int $counter)
    {
        return $this->renderAjax('/subscription/_discount', [
            'discount' => new SubscriptionDiscount(),
            'i'        => ++$counter,
        ]);
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionDelete()
    {
        $subscriptionIDs             = \Yii::$app->request->post('selection');
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $this->log('subscription-delete', $subscriptionIDs);
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($subscriptionIDs as $id) {
            $isDelete = SubscriptionDiscount::deleteAll(['subscription_id' => $id])
                && \app\models\Repository\Subscription::deleteAll(['id' => $id]);

            if (empty($isDelete)) {
                $transaction->rollBack();
                $this->log('dish-delete-fail', $subscriptionIDs);
                return [
                    'status' => false,
                    'title'  => \Yii::t('order', 'Dish was not deleted')
                ];
            }
        }

        $transaction->commit();
        $this->log('subscription-delete-success', $subscriptionIDs);
        return [
            'status' => true,
            'title'  => \Yii::t('subscription', 'Subscription was successful deleted')
        ];
    }
}
