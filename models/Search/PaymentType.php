<?php

namespace app\models\search;

use app\models\Repository\PaymentType as Repository;
use app\widgets\Grid\CheckboxColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

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
            ['id', 'integer'],
            [['name', 'updated_at'], 'string'],
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
        $query->andFilterWhere(['>', 'status', self::STATUS_DELETED]);

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
        !empty($params['name']) && $paymentsQuery->filterWhere(['like', 'name', urldecode($params['name'])]);

        $payments = $paymentsQuery->asArray()->all();
        foreach ($payments as $payment) {
            yield [
                'id' => $payment['id'],
                'name' => $payment['name'],
                'created_at' => date('d.m.Y \в H:i', $payment['created_at']),
                'updated_at' => date('d.m.Y \в H:i', $payment['updated_at']),
            ];
        }
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
            'label' => \Yii::t('payment', 'ID'),
            'content' => function ($model) {
                return Html::a(
                    $model->id,
                    ['payment-type/view', 'id' => $model->id],
                    [
                        'data-href'   => Url::to(['payment-type/view', 'id' => $model->id]),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                    ]
                );
            }
        ];

        $result['name'] = [
            'attribute' => 'name',
            'label' => \Yii::t('payment', 'Name'),
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
