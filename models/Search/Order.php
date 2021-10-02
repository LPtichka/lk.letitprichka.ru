<?php

namespace app\models\Search;

use app\models\Helper\Phone;
use app\models\Helper\Status;
use app\models\Repository\Order as Repository;
use app\widgets\Grid\CheckboxColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

class Order extends Repository
{
    public $fio;
    public $email;
    public $phone;

    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'total', 'status_id'], 'integer'],
            [['created_at', 'fio', 'email', 'phone', 'address', 'subscription_id'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Repository::find()->joinWith(['customer']);
        $dataProvider = new ActiveDataProvider([
                                                   'query' => $query,
                                                   'sort'  => ['defaultOrder' => ['id' => SORT_DESC]],
                                               ]);

        /** @var \app\models\User $user */
        $user = \Yii::$app->user->identity;
        if (!empty($user->franchise_id)) {
            $query->andWhere(['franchise_id' => $user->franchise_id]);
        }

        $this->load($params);
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['>', 'status_id', \app\models\Repository\Order::STATUS_ARCHIVED]);
        if (!empty($this->email)) {
            $query->andFilterWhere(['LIKE', 'customer.email', '%' . $this->email . '%', false]);
        }
        if (!empty($this->fio)) {
            $query->andFilterWhere(['LIKE', 'customer.fio', '%' . $this->fio . '%', false]);
        }
        if (!empty($this->phone)) {
            $query->andFilterWhere(['LIKE', 'customer.phone', '%' . $this->phone . '%', false]);
        }

        return $dataProvider;
    }

    /**
     * @param $params
     * @return iterable
     */
    public function export($params): iterable
    {
    }

    /**
     * Список полей для поиска
     *
     * @param Repository $searchModel
     * @return array
     */
    public function getSearchColumns(Repository $searchModel)
    {
        $result[] = [
            'class'         => CheckboxColumn::class,
            'headerOptions' => [
                'width'                    => '40px',
                'data-resizable-column-id' => 'checker'
            ],
        ];

        $result['id'] = [
            'attribute'      => 'id',
            'contentOptions' => ['style' => 'width:60px;'],
            'label'          => \Yii::t('order', 'ID'),
            'content'        => function ($model) {
                return Html::a($model->id, ['order/view', 'id' => $model->id]);
            }
        ];

        $result['fio'] = [
            'attribute' => 'fio',
            'label'     => \Yii::t('order', 'FIO'),
            'content'   => function ($model) {
                return Html::a($model->customer->fio, ['customer/view', 'id' => $model->customer_id], [
                    'data-href'   => Url::to(['customer/view', 'id' => $model->customer_id]),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal',
                ]);
            }
        ];

        $result['status_id'] = [
            'attribute'      => 'status_id',
            'label'          => \Yii::t('order', 'Status'),
            'contentOptions' => ['style' => 'width:100px;'],
            'content'        => function ($model) {
                return (new Status($model->status_id))->getStatusName();
            }
        ];

        $result['phone'] = [
            'attribute'      => 'phone',
            'label'          => \Yii::t('order', 'Phone'),
            'contentOptions' => ['style' => 'width:125px; min-width:125px'],
            'content'        => function ($model) {
                return !empty($model->customer->phone) ? (new Phone($model->customer->phone))->getHumanView() : '---';
            }
        ];

        $result['email'] = [
            'attribute' => 'email',
            'label'     => \Yii::t('order', 'Email'),
            'content'   => function ($model) {
                return $model->customer->email ?? '---';
            }
        ];

        $result['total'] = [
            'attribute'      => 'total',
            'label'          => \Yii::t('order', 'Total'),
            'contentOptions' => ['style' => 'width:100px;'],
            'content'        => function ($model) {
                return \Yii::$app->formatter->asCurrency($model->total, 'RUB');
            }
        ];

        $result['subscription_id'] = [
            'attribute' => 'subscription_id',
            'label'     => \Yii::t('order', 'Subscription'),
            'content'   => function ($model) {
                /** @var \app\models\Repository\Order $model */
                return $model->subscription->name . ($model->without_soup ? ' б/супа' : '') . ', ' . $model->count . 'д.';
            }
        ];

//        $result['address'] = [
//            'attribute' => 'address',
//            'label'     => \Yii::t('order', 'Address'),
//            'content'   => function ($model){
//                return $model->address->full_address ?? '---';
//            }
//        ];

        $result['created_at'] = [
            'attribute'      => 'created_at',
            'contentOptions' => ['style' => 'width:130px;'],
            'label'          => \Yii::t('order', 'Created at'),
            'content'        => function ($model) {
                return date('d.m.Y \в H:i', $model->created_at);
            }
        ];
        return $result;
    }
}
