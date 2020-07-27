<?php

namespace app\models\Search;

use app\models\Repository\Menu as Repository;
use app\widgets\Grid\CheckboxColumn;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

class Menu extends Repository
{
    public $is_equipped;

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
            [['id'], 'integer'],
            [['menu_start_date', 'menu_end_date', 'created_at'], 'string'],
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
            'sort'  => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        $query->andFilterWhere(['status' => \app\models\Repository\Menu::STATUS_ACTIVE]);

        $this->load($params);
        if (!empty($params['is_equipped'])) {
            $this->is_equipped = $params['is_equipped'];
        }

        return $dataProvider;
    }

    /**
     * @param $params
     * @return iterable
     */
    public function export($params): iterable
    {
        // TODO
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

        $result['created_at'] = [
            'attribute' => 'created_at',
            'label'     => \Yii::t('menu', 'Created at'),
            'content'   => function ($model){
                return date('d.m.Y \в H:i', $model->created_at);
            }
        ];

        $result['id'] = [
            'attribute' => 'id',
            'label'     => \Yii::t('menu', 'ID'),
            'content'   => function ($model){
                return Html::a($model->id, ['menu/view', 'id' => $model->id]);
            }
        ];

        $result['menu_start_date'] = [
            'attribute' => 'menu_start_date',
            'label'     => \Yii::t('menu', 'Start day'),
            'content'   => function ($model){
                return date('d.m.Y', strtotime($model->menu_start_date));
            },
        ];

        $result['menu_end_date'] = [
            'attribute' => 'menu_end_date',
            'label'     => \Yii::t('menu', 'End day'),
            'content'   => function ($model){
                return date('d.m.Y', strtotime($model->menu_end_date));
            },
        ];

        $result['is_equipped'] = [
            'attribute' => 'is_equipped',
            'label'     => \Yii::t('menu', 'Is equipped'),
            'content'   => function ($model){
                return $model->isEquipped() ? \Yii::t('menu', 'No equipped') : \Yii::t('menu', 'Equipped');
            },
            'filter'    => Html::tag('div', Html::dropDownList(
                'is_equipped',
                $searchModel->is_equipped,
                [
                    ''    => '',
                    'yes' => \Yii::t('app', 'Yes'),
                    'no'  => \Yii::t('app', 'No'),
                ],
                ['class' => 'form-control']
            ),
                ['class' => 'select_wrapper']
            ),
        ];


        return $result;
    }
}
