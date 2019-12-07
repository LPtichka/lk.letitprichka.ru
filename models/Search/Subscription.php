<?php

namespace app\models\search;

use app\models\Repository\Subscription as Repository;
use kartik\daterange\DateRangePicker;
use yii\data\ActiveDataProvider;
use app\widgets\Grid\CheckboxColumn;
use yii\helpers\Html;

class Subscription extends Repository
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
            [['id', 'price'], 'integer'],
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
            'sort'  => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

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
     * @param $params
     * @return iterable
     */
    public function export($params): iterable
    {
        $addressQuery = self::find();

        !empty($params['id']) && $addressQuery->filterWhere(['id' => $params['id']]);
        !empty($params['name']) && $addressQuery->filterWhere(['like', 'name', urldecode($params['name'])]);

        $addresses = $addressQuery->asArray()->all();
        foreach ($addresses as $address) {
            yield [
                'id'         => $address['id'],
                'name'       => $address['name'],
                'created_at' => date('d.m.Y \в H:i', $address['created_at']),
                'updated_at' => date('d.m.Y \в H:i', $address['updated_at']),
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

        $result['created_at'] = [
            'attribute' => 'created_at',
            'label'     => \Yii::t('subscription', 'Created at'),
            'contentOptions' => ['style' => 'width:200px;'],
            'filter' => Html::tag('div',
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
            'content'   => function ($model) {
                return date('d.m.Y \в H:i', $model->updated_at);
            }
        ];

        $result['id'] = [
            'attribute'      => 'id',
            'label'          => \Yii::t('subscription', 'ID'),
            'contentOptions' => ['style' => 'width:120px;'],
            'content'        => function ($model) {
                return Html::a($model->id, ['subscription/view', 'id' => $model->id]);
            }
        ];

        $result['name'] = [
            'attribute' => 'name',
            'label'     => \Yii::t('subscription', 'Name'),
        ];

        $result['price'] = [
            'attribute' => 'price',
            'label'     => \Yii::t('subscription', 'Price'),
            'contentOptions' => ['style' => 'width:200px;'],
            'content'   => function ($model) {
                return \Yii::$app->formatter->asCurrency($model->price, 'RUB');
            }
        ];

        return $result;
    }
}
