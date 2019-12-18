<?php

namespace app\models\search;

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
            [['id', 'total'], 'integer'],
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
            'label'     => \Yii::t('product', 'ID'),
            'content'   => function ($model) {
                return Html::a($model->id, ['product/view', 'id' => $model->id]);
            }
        ];

        $result['fio'] = [
            'attribute' => 'fio',
            'label'     => \Yii::t('order', 'FIO'),
            'content'   => function ($model) {
                return $model->customer->fio ?? '---';
            }
        ];

        $result['phone'] = [
            'attribute' => 'phone',
            'label'     => \Yii::t('order', 'Phone'),
            'content'   => function ($model) {
                return $model->customer->phone ?? '---';
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
