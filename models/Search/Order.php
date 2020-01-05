<?php

namespace app\models\search;

use app\models\Helper\Phone;
use app\models\Helper\Status;
use app\models\Repository\Order as Repository;
use app\widgets\Grid\CheckboxColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

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
            [['created_at', 'fio', 'email', 'phone', 'address'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query        = Repository::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_ASC]],
        ]);

        $this->load($params);

        $query->andFilterWhere(['id' => $this->id]);

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
            'attribute' => 'id',
            'contentOptions' => ['style' => 'width:120px;'],
            'label'     => \Yii::t('order', 'ID'),
            'content'   => function ($model) {
                return Html::a($model->id, ['order/view', 'id' => $model->id]);
            }
        ];

        $result['fio'] = [
            'attribute' => 'fio',
            'label'     => \Yii::t('order', 'FIO'),
            'content'   => function ($model) {
                return Html::a($model->customer->fio, ['customer/view', 'id' => $model->customer_id]);
            }
        ];

        $result['status_id'] = [
            'attribute' => 'status_id',
            'label'     => \Yii::t('order', 'Status'),
            'content'   => function ($model) {
                return (new Status($model->status_id))->getStatusName();
            }
        ];

        $result['phone'] = [
            'attribute' => 'phone',
            'label'     => \Yii::t('order', 'Phone'),
            'content'   => function ($model) {
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
            'attribute' => 'total',
            'label'     => \Yii::t('order', 'Total'),
            'content'   => function ($model) {
                return \Yii::$app->formatter->asCurrency($model->total, 'RUB');
            }
        ];

        $result['address'] = [
            'attribute' => 'address',
            'label'     => \Yii::t('order', 'Address'),
            'content'   => function ($model) {
                return $model->address->full_address ?? '---';
            }
        ];

        $result['created_at'] = [
            'attribute' => 'created_at',
            'contentOptions' => ['style' => 'width:240px;'],
            'label'     => \Yii::t('order', 'Created at'),
            'content'   => function ($model) {
                return date('d.m.Y \в H:i', $model->created_at);
            }
        ];
        return $result;
    }
}
