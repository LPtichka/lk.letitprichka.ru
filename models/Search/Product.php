<?php

namespace app\models\Search;

use app\models\Helper\Arrays;
use app\models\Helper\Unit;
use app\models\Helper\Weight;
use app\models\Repository\Product as Repository;
use app\widgets\Grid\CheckboxColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

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
            [['id', 'count', 'exception_id'], 'integer'],
            [['weight'], 'number'],
            [['name', 'updated_at'], 'string'],
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
        $query->andFilterWhere(['count' => $this->count]);
        $query->andFilterWhere(['exception_id' => $this->exception_id]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return iterable
     */
    public function export($params): iterable
    {
        $paymentsQuery = self::find()->select(
            'product.id as product_id, 
            product.name as product_name, 
            product.count as product_count, 
            product.unit as product_unit, 
            product.created_at as product_created_at, 
            product.updated_at as product_updated_at,
            exception.name as exception_name'
        )->leftJoin('exception', 'product.exception_id = exception.id');

        !empty($params['id']) && $paymentsQuery->filterWhere(['product.id' => $params['id']]);
        !empty($params['count']) && $paymentsQuery->filterWhere(['product.count' => (int) $params['count']]);
        !empty($params['weight']) && $paymentsQuery->filterWhere(['product.weight' => ((float) $params['count']) * 1000]);
        !empty($params['name']) && $paymentsQuery->filterWhere(['like', 'product.name', urldecode($params['name'])]);
        !empty($params['exception_id']) && $paymentsQuery->filterWhere(['product.exception_id' => (int) $params['exception_id']]);

        $products = $paymentsQuery->asArray()->all();
        foreach ($products as $product) {
            yield [
                'id'           => $product['product_id'],
                'name'         => $product['product_name'],
                'count'        => (new Unit($product['product_unit']))->format($product['product_count']),
                'Exception ID' => $product['exception_name'] ?? '',
                'created_at'   => date('d.m.Y \в H:i', $product['product_created_at']),
                'updated_at'   => date('d.m.Y \в H:i', $product['product_updated_at']),
            ];
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
            'attribute'      => 'id',
            'contentOptions' => ['style' => 'width:120px;'],
            'label'          => \Yii::t('product', 'ID'),
            'content'        => function ($model){
                return Html::a(
                    $model->id,
                    ['product/view', 'id' => $model->id],
                    [
                        'data-href'   => Url::to(['product/view', 'id' => $model->id]),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                    ]
                );
            }
        ];

        $result['name'] = [
            'attribute' => 'name',
            'label'     => \Yii::t('product', 'Name'),
        ];

        $result['count'] = [
            'attribute' => 'count',
            'label'     => \Yii::t('product', 'Remains'),
            'content'   => function ($model){
                return (new Unit($model->unit))->format($model->count);
            }
        ];

        $result['exception_id'] = [
            'attribute' => 'exception_id',
            'label'     => \Yii::t('product', 'Exception ID'),
            'filter'    => Html::tag('div', Html::dropDownList(
                'exception_id',
                $searchModel->exception_id,
                (new Arrays($searchModel->getExceptionList()))->getSelectOptions(),
                ['class' => 'form-control']
            ),
                ['class' => 'select_wrapper']
            ),
            'content'   => function ($model){
                return $model->exception->name ?? '';
            }
        ];

        $result['updated_at'] = [
            'attribute' => 'updated_at',
            'label'     => \Yii::t('payment', 'Updated at'),
            'content'   => function ($model){
                return date('d.m.Y \в H:i', $model->updated_at);
            }
        ];
        return $result;
    }
}
