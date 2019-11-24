<?php

namespace app\models\search;

use app\models\Repository\Address as Repository;
use yii\data\ActiveDataProvider;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;

class Address extends Repository
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
            [['id', 'customer_id'], 'integer'],
            ['full_address', 'string'],
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

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere([
            'customer_id' => $this->customer_id,
        ]);

        if (!empty($this->full_address)) {
            $query->andFilterWhere([
                'like', 'full_address', $this->full_address
            ]);
        }

        return $dataProvider;
    }

    /**
     * @param $params
     * @return iterable
     */
    public function export($params): iterable
    {
        $addressQuery = self::find();

        !empty($params['id']) && $addressQuery->filterWhere(['id' => $params['id']]);
        !empty($params['customer_id']) && $addressQuery->filterWhere(['customer_id' => $params['customer_id']]);
        !empty($params['full_address']) && $addressQuery->filterWhere(['like', 'full_address', urldecode($params['full_address'])]);

        $addresses = $addressQuery->asArray()->all();
        foreach ($addresses as $address) {
            yield [
                'id'           => $address['id'],
                'customer_id'  => $address['customer_id'],
                'city'         => $address['city'],
                'street'       => $address['street'],
                'house'        => $address['house'],
                'housing'      => $address['housing'],
                'flat'         => $address['flat'],
                'full_address' => $address['full_address'],
                'created_at'   => date('d.m.Y \в H:i', $address['created_at']),
                'updated_at'   => date('d.m.Y \в H:i', $address['updated_at']),
            ];
        }
    }

    /**
     * Список полей для поиска
     *
     * @param Address $searchModel
     * @return array
     */
    public function getSearchColumns(\app\models\Repository\Address $searchModel)
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
            'label'     => \Yii::t('address', 'ID'),
            'content'   => function ($model) {
                return Html::a($model->id, ['address/view', 'id' => $model->id]);
            }
        ];

        $result['customer_id'] = [
            'attribute' => 'customer_id',
            'label'     => \Yii::t('address', 'Customer id'),
        ];

        $result['full_address'] = [
            'attribute' => 'full_address',
            'label'     => \Yii::t('address', 'Full address'),
        ];

        $result['updated_at'] = [
            'attribute' => 'updated_at',
            'label'     => \Yii::t('payment', 'Updated at'),
            'content'   => function ($model) {
                return date('d.m.Y \в H:i', $model->updated_at);
            }
        ];
        return $result;
    }
}
