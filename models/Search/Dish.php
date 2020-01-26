<?php

namespace app\models\Search;

use app\models\Helper\Arrays;
use app\models\Helper\Weight;
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

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['type' => $this->type]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return iterable
     */
    public function export($params): iterable
    {
        $dishQuery = self::find();

        !empty($params['id']) && $dishQuery->filterWhere(['id' => $params['id']]);
        !empty($params['name']) && $dishQuery->filterWhere(['like', 'name', urldecode($params['name'])]);

        return $dishQuery->all();
//        foreach ($dishes as $dish) {
//            yield [
//                'id' => $dish['id'],
//                'name' => $dish['name'],
//                'created_at' => date('d.m.Y \в H:i', $dish['created_at']),
//                'updated_at' => date('d.m.Y \в H:i', $dish['updated_at']),
//            ];
//        }
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
            'content' => function ($model) {
                return (new Weight())->setUnit(Weight::UNIT_KG)->convert($model->weight, Weight::UNIT_GR);
            }
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
