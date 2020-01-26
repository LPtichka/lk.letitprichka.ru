<?php

namespace app\models\search;

use app\models\Repository\Franchise as Repository;
use app\widgets\Grid\CheckboxColumn;
use kartik\daterange\DateRangePicker;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

class Franchise extends Repository
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
            [['id'], 'integer'],
            [['name'], 'string'],
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

        /** @var \app\models\User $user */
        $user = \Yii::$app->user->identity;
        if (!empty($user->franchise_id)) {
            $query->andWhere(['id' => $user->franchise_id]);
        }

        $query->andWhere(['>', 'status', Repository::STATUS_DELETED]);

        $this->load($params);

        if (!empty($params['created_at'])) {
            $dates = explode('-', $params['created_at']);
            $query->andFilterWhere([
                '>', 'created_at', strtotime(trim($dates[0]))
            ]);
            $query->andFilterWhere([
                '<', 'created_at', strtotime(trim($dates[1])) + 86400
            ]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        if (!empty($this->name)) {
            $query->andFilterWhere([
                'like', 'name', $this->name
            ]);
        }

        return $dataProvider;
    }

    /**
     * Список полей для поиска
     *
     * @param Repository $searchModel
     * @return array
     * @throws \Exception
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
            'attribute'      => 'created_at',
            'label'          => \Yii::t('franchise', 'Created at'),
            'contentOptions' => ['style' => 'width:200px;'],
            'filter'         => Html::tag('div',
                DateRangePicker::widget([
                    'name'          => 'created_at',
                    'value'         => $searchModel->created_at,
                    'convertFormat' => false,
                    'useWithAddon'  => false,
                    'options'       => ['class' => 'form-control input-sm', 'id' => 'order-create-date'],
                    'pluginOptions' => [
                        'locale' => [
                            'format'    => 'DD.MM.YYYY',
                            'separator' => ' - ',
                        ]
                    ]
                ])
            ),
            'content'        => function ($model){
                return date('d.m.Y \в H:i', $model->updated_at);
            }
        ];

        $result['id'] = [
            'attribute'      => 'id',
            'label'          => \Yii::t('franchise', 'ID'),
            'contentOptions' => ['style' => 'width:120px;'],
            'content'        => function ($model){
                return Html::a(
                    $model->id,
                    ['franchise/view', 'id' => $model->id],
                    [
                        'data-href'   => Url::to(['franchise/view', 'id' => $model->id]),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                    ]
                );
            }
        ];

        $result['name'] = [
            'attribute' => 'name',
            'label'     => \Yii::t('franchise', 'Name'),
        ];

        return $result;
    }
}
