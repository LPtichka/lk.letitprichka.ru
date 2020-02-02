<?php

namespace app\models\Search;

use app\models\Repository\Exception as Repository;
use app\widgets\Grid\CheckboxColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

class Exception extends Repository
{
    /** @var int */
    public $product_count;

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
            [['id', 'product_count'], 'integer'],
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
        $query->andFilterWhere(['status' => self::STATUS_ACTIVE]);

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
        !empty($params['name']) && $paymentsQuery->filterWhere(['like', 'name', urldecode($params['name'])]);

        $payments = $paymentsQuery->asArray()->all();
        foreach ($payments as $payment) {
            yield [
                'ID' => $payment['id'],
                'Name' => $payment['name'],
                'Created at' => date('d.m.Y \в H:i', $payment['created_at']),
                'Updated at' => date('d.m.Y \в H:i', $payment['updated_at']),
            ];
        }
    }

    /**
     * Список полей для поиска
     * @param Exception $searchModel
     * @return array
     */
    public function getSearchColumns(Exception $searchModel)
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
            'label' => \Yii::t('exception', 'ID'),
            'contentOptions' => ['style' => 'width:120px;'],
            'content' => function ($model) {
                return Html::a(
                    $model->id,
                    [
                        'exception/view',
                        'id' => $model->id,
                    ],
                    [
                        'data-href'   => Url::to(['exception/view', 'id' => $model->id]),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                    ]
                );
            }
        ];

        $result['name'] = [
            'attribute' => 'name',
            'label' => \Yii::t('exception', 'Name'),
        ];

        $result['product_count'] = [
            'attribute' => 'product_count',
            'contentOptions' => ['style' => 'width:240px;'],
            'label' => \Yii::t('exception', 'Product count'),
            'content' => function ($model) {
                return count($model->products);
            }
        ];

        $result['updated_at'] = [
            'attribute' => 'updated_at',
            'label' => \Yii::t('payment', 'Updated at'),
            'contentOptions' => ['style' => 'width:240px;'],
            'content' => function($model) {
                return date('d.m.Y \в H:i', $model->updated_at);
            }
        ];
        return $result;
    }
}
