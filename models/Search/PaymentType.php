<?php

namespace app\models\search;

use app\models\Repository\PaymentType as Repository;
use yii\data\ActiveDataProvider;
use yii\grid\CheckboxColumn;

class PaymentType extends Repository
{
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
            [['id'], 'integer'],
            [['name'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Repository::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_ASC]],
        ]);

        $this->load($params);

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    /**
     * Список полей для поиска
     * @param PaymentType $searchModel
     * @return array
     */
    public function getSearchColumns(PaymentType $searchModel)
    {
        $result[] = [
            'class' => CheckboxColumn::class,
            'headerOptions' => [
                'width' => '40px',
                'data-resizable-column-id' => 'checker'
            ],
        ];

        $result['id'] = [
            'attribute' => 'id',
            'label' => 'id',
        ];

        $result['name'] = [
            'attribute' => 'name',
            'label' => 'name',
        ];

        $result['updated_at'] = [
            'attribute' => 'updated_at',
            'label' => 'updated_at',
            'content' => function($model) {
                return date('d.m.Y \в H:i', $model->updated_at);
            }
        ];
        return $result;
    }
}
