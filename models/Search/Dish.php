<?php

namespace app\models\search;

use app\models\Helper\Arrays;
use app\models\Repository\Dish as Repository;
use yii\data\ActiveDataProvider;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;

class Dish extends Repository
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
            [['id', 'fat', 'weight', 'proteins', 'kkal', 'type', 'carbohydrates'], 'integer'],
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

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

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
                'id' => $payment['id'],
                'name' => $payment['name'],
                'created_at' => date('d.m.Y \в H:i', $payment['created_at']),
                'updated_at' => date('d.m.Y \в H:i', $payment['updated_at']),
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
            'class' => CheckboxColumn::class,
            'headerOptions' => [
                'width' => '40px',
                'data-resizable-column-id' => 'checker'
            ],
        ];

        $result['id'] = [
            'attribute' => 'id',
            'label' => \Yii::t('dish', 'ID'),
            'content' => function ($model) {
                return Html::a($model->id, ['dish/view', 'id' => $model->id]);
            }
        ];

        $result['name'] = [
            'attribute' => 'name',
            'label' => \Yii::t('dish', 'Name'),
        ];

        $result['type'] = [
            'attribute' => 'type',
            'label' => \Yii::t('dish', 'Type'),
            'filter'    => Html::tag('div', Html::dropDownList(
                'type',
                $searchModel->type,
                (new Arrays($searchModel->getTypes()))->getSelectOptions(),
                ['class' => 'form-control']
            ),
                ['class' => 'select_wrapper']
            ),
            'content' => function($model) {
                return $model->getTypes()[$model->type];
            }
        ];

        $result['weight'] = [
            'attribute' => 'weight',
            'label' => \Yii::t('dish', 'Weight'),
        ];


        $result['kkal'] = [
            'attribute' => 'kkal',
            'label' => \Yii::t('dish', 'KKal'),
        ];

        $result['fat'] = [
            'attribute' => 'fat',
            'label' => \Yii::t('dish', 'Fat'),
        ];

        $result['proteins'] = [
            'attribute' => 'proteins',
            'label' => \Yii::t('dish', 'Proteins'),
        ];

        $result['carbohydrates'] = [
            'attribute' => 'carbohydrates',
            'label' => \Yii::t('dish', 'Carbohydrates'),
        ];

        $result['updated_at'] = [
            'attribute' => 'updated_at',
            'label' => \Yii::t('dish', 'Updated at'),
            'content' => function($model) {
                return date('d.m.Y \в H:i', $model->updated_at);
            }
        ];
        return $result;
    }
}
