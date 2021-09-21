<?php

namespace app\models\Search;

use app\models\Helper\Arrays;
use app\models\Helper\Weight;
use app\models\Repository\Dish as Repository;
use app\widgets\Grid\CheckboxColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

class Dish extends Repository
{
    /** @var string */
    public $ingestion;

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
            [['name', 'updated_at', 'ingestion'], 'string'],
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

        switch ($this->ingestion) {
            case self::INGESTION_TYPE_BREAKFAST:
                $query->andFilterWhere(['is_breakfast' => true]);
                break;
            case self::INGESTION_TYPE_DINNER:
                $query->andFilterWhere(['is_dinner' => true]);
                break;
            case self::INGESTION_TYPE_LUNCH:
                $query->andFilterWhere(['is_lunch' => true]);
                break;
            case self::INGESTION_TYPE_SUPPER:
                $query->andFilterWhere(['is_supper' => true]);
                break;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['status' => \app\models\Repository\Dish::STATUS_ACTIVE]);
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
            'headerOptions' => [
                'width' => '80px',
            ],
            'content' => function ($model) {
                return Html::a($model->id, ['dish/view', 'id' => $model->id]);
            }
        ];

        $result['name'] = [
            'attribute' => 'name',
            'label' => \Yii::t('dish', 'Name'),
        ];

        $result['ingestion'] = [
            'attribute' => 'ingestion',
            'label' => \Yii::t('dish', 'Ingestion'),
            'filter'    => Html::tag('div', Html::dropDownList(
                'ingestion',
                $searchModel->ingestion,
                (new Arrays($searchModel->getIngestions()))->getSelectOptions(),
                ['class' => 'form-control']
            ),
                ['class' => 'select_wrapper']
            ),
            'content' => function($model) {
                $ingestionList = $model->getIngestionList();
                return !empty($ingestionList) ? implode(', ', $ingestionList) : '---';
            }
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
                return $model->getTypes()[$model->type] ?? '---';
            }
        ];

        $result['weight'] = [
            'attribute' => 'weight',
            'label' => \Yii::t('dish', 'Weight'),
            'headerOptions' => [
                'width' => '80px',
            ],
            'content' => function ($model) {
                return (new Weight())->setUnit(Weight::UNIT_GR)->convert($model->weight, Weight::UNIT_GR);
            }
        ];

        $result['kkal'] = [
            'attribute' => 'kkal',
            'headerOptions' => [
                'width' => '80px',
            ],
            'label' => \Yii::t('dish', 'KKal'),
        ];

        $result['fat'] = [
            'attribute' => 'fat',
            'headerOptions' => [
                'width' => '80px',
            ],
            'label' => \Yii::t('dish', 'Fat'),
        ];

        $result['proteins'] = [
            'attribute' => 'proteins',
            'headerOptions' => [
                'width' => '80px',
            ],
            'label' => \Yii::t('dish', 'Proteins'),
        ];

        $result['carbohydrates'] = [
            'attribute' => 'carbohydrates',
            'headerOptions' => [
                'width' => '80px',
            ],
            'label' => \Yii::t('dish', 'Carbohydrates'),
        ];

        $result['updated_at'] = [
            'attribute' => 'updated_at',
            'headerOptions' => [
                'width' => '160px',
            ],
            'label' => \Yii::t('dish', 'Updated at'),
            'content' => function($model) {
                return date('d.m.Y \в H:i', $model->updated_at);
            }
        ];
        return $result;
    }
}
