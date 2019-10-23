<?php

namespace app\models\search;

use app\models\Helper\Weight;
use app\models\Repository\Product as Repository;
use yii\data\ActiveDataProvider;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;

class Product extends Repository
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
            [['id', 'count', 'weight'], 'integer'],
            ['name', 'string'],
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

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['count' => $this->count]);
        $query->andFilterWhere(['weight' => $this->weight]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return iterable
     */
    public function export($params): iterable
    {
        $paymentsQuery = self::find();

        !empty($params['id']) && $paymentsQuery->filterWhere(['id' => $params['id']]);
        !empty($params['count']) && $paymentsQuery->filterWhere(['count' => (int) $params['count']]);
        !empty($params['weight']) && $paymentsQuery->filterWhere(['weight' => ((float) $params['count']) * 1000]);
        !empty($params['name']) && $paymentsQuery->filterWhere(['like', 'name', urldecode($params['name'])]);

        $payments = $paymentsQuery->asArray()->all();
        foreach ($payments as $payment) {
            yield [
                'id' => $payment['id'],
                'name' => $payment['name'],
                'count' => $payment['count'],
                'weight' => (new Weight())->format($payment['weight'], Weight::UNIT_KG),
                'created_at' => date('d.m.Y \в H:i', $payment['created_at']),
                'updated_at' => date('d.m.Y \в H:i', $payment['updated_at']),
            ];
        }
    }

    /**
     * Список полей для поиска
     * @param \app\models\Repository\Product $searchModel
     * @return array
     */
    public function getSearchColumns(\app\models\Repository\Product $searchModel)
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
            'label' => \Yii::t('product', 'ID'),
            'content' => function ($model) {
                return Html::a($model->id, ['product/view', 'id' => $model->id]);
            }
        ];

        $result['name'] = [
            'attribute' => 'name',
            'label' => \Yii::t('product', 'Name'),
        ];

        $result['count'] = [
            'attribute' => 'count',
            'label' => \Yii::t('app', 'Count'),
        ];

        $result['weight'] = [
            'attribute' => 'weight',
            'label' => \Yii::t('app', 'Weight'),
            'content' => function($model) {
                return (new Weight())->format($model->weight, Weight::UNIT_KG);
            }
        ];

        $result['updated_at'] = [
            'attribute' => 'updated_at',
            'label' => \Yii::t('payment', 'Updated at'),
            'content' => function($model) {
                return date('d.m.Y \в H:i', $model->updated_at);
            }
        ];
        return $result;
    }
}
