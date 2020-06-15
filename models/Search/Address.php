<?php

namespace app\models\Search;

use app\models\Repository\Address as Repository;
use app\widgets\Grid\CheckboxColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

class Address extends Repository
{
    public $customer_fio;

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
            [['full_address', 'updated_at', 'customer_fio'], 'string'],
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

        $query->leftJoin(['customer'], 'address.customer_id = customer.id');

        $query->andFilterWhere([
            'customer_id' => $this->customer_id,
        ]);

        if (!empty($this->full_address)) {
            $query->andFilterWhere([
                'like', 'full_address', $this->full_address
            ]);
        }

        if (!empty($this->customer_fio)) {
            $query->andFilterWhere([
                'like', 'customer.fio', $this->customer_fio
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
     * @param \app\models\Repository\Address $searchModel
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
            'contentOptions' => ['style' => 'width:60px;'],
            'content'   => function ($model) {
                return Html::a(
                    $model->id,
                    ['address/view', 'id' => $model->id],
                    [
                        'data-href'   => Url::to(['address/view', 'id' => $model->id]),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                    ]
                );
            }
        ];

        $result['customer_id'] = [
            'attribute' => 'customer_id',
            'contentOptions' => ['style' => 'width:60px;'],
            'label'     => \Yii::t('address', 'Customer id'),
        ];

        $result['customer_fio'] = [
            'attribute' => 'customer_fio',
            'label'     => \Yii::t('address', 'Customer FIO'),
            'content'   => function ($model) {
                return !empty($model->customer->fio) ? $model->customer->fio : '---';
            }
        ];

        $result['full_address'] = [
            'attribute' => 'full_address',
            'label'     => \Yii::t('address', 'Full address'),
        ];

        $result['updated_at'] = [
            'attribute' => 'updated_at',
            'contentOptions' => ['style' => 'width:130px;'],
            'label'     => \Yii::t('payment', 'Updated at'),
            'content'   => function ($model) {
                return date('d.m.Y \в H:i', $model->updated_at);
            }
        ];
        return $result;
    }
}
