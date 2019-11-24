<?php

namespace app\models\search;

use app\models\Repository\Customer as Repository;
use yii\data\ActiveDataProvider;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;

class Customer extends Repository
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
            [['id', 'default_address_id'], 'integer'],
            [['fio', 'email', 'phone'], 'string'],
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
        $query->andFilterWhere(['>', 'status', 0]);

        !empty($this->fio) && $query->andFilterWhere(['like', 'fio', $this->fio]);
        !empty($this->email) && $query->andFilterWhere(['like', 'email', $this->email]);
        !empty($this->phone) && $query->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return iterable
     */
    public function export($params): iterable
    {
        $customerQuery = self::find();
        $customerQuery->andFilterWhere(['>', 'status', 0]);

        !empty($params['id']) && $customerQuery->filterWhere(['id' => $params['id']]);
        !empty($params['fio']) && $customerQuery->filterWhere(['like', 'fio', urldecode($params['fio'])]);
        !empty($params['phone']) && $customerQuery->filterWhere(['like', 'phone', urldecode($params['phone'])]);
        !empty($params['email']) && $customerQuery->filterWhere(['like', 'email', urldecode($params['email'])]);

        $customers = $customerQuery
            ->joinWith(['addresses'])
            ->asArray()
            ->all();

        foreach ($customers as $customer) {
            $result = [
                'id'                 => $customer['id'],
                'fio'                => $customer['fio'],
                'email'              => $customer['email'],
                'phone'              => $customer['phone'],
                'full_address'       => '',
                'description'        => '',
                'is_default_address' => \Yii::t('app', 'No'),
                'created_at'         => date('d.m.Y \в H:i', $customer['created_at']),
                'updated_at'         => date('d.m.Y \в H:i', $customer['updated_at']),
            ];

            if (!empty($customer['addresses'])) {
                foreach ($customer['addresses'] as $address) {
                    $result['full_address'] = $address['full_address'];
                    $result['description']  = $address['description'];
                    if ($customer['default_address_id'] === $address['id']) {
                        $result['is_default_address']  = \Yii::t('app', 'Yes');
                    }
                    yield $result;
                }
            } else {
                yield $result;
            }
        }
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
            'label'     => \Yii::t('customer', 'ID'),
            'content'   => function ($model) {
                return Html::a($model->id, ['customer/view', 'id' => $model->id]);
            }
        ];

        $result['fio'] = [
            'attribute' => 'fio',
            'label'     => \Yii::t('customer', 'Fio'),
        ];

        $result['phone'] = [
            'attribute' => 'phone',
            'label'     => \Yii::t('customer', 'Phone'),
        ];

        $result['email'] = [
            'attribute' => 'email',
            'label'     => \Yii::t('customer', 'Email'),
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
