<?php

namespace app\models\Search;

use app\models\Helper\Phone;
use app\models\Repository\Customer as Repository;
use kartik\daterange\DateRangePicker;
use yii\data\ActiveDataProvider;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\helpers\Url;

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
            'sort'  => ['defaultOrder' => ['created_at' => SORT_DESC]],
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
                        $result['is_default_address'] = \Yii::t('app', 'Yes');
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
            'label'          => \Yii::t('customer', 'Created at'),
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
            'contentOptions' => ['style' => 'width:120px;'],
            'label'          => \Yii::t('customer', 'ID'),
            'content'        => function ($model){
                return Html::a($model->id,
                    ['customer/view', 'id' => $model->id],
                    [

                        'data-href'   => Url::to(['customer/view', 'id' => $model->id]),
                        'data-toggle' => 'modal',
                        'data-target' => '#modal',
                    ]
                );
            }
        ];

        $result['fio'] = [
            'attribute' => 'fio',
            'label'     => \Yii::t('customer', 'FIO'),
        ];

        $result['phone'] = [
            'attribute' => 'phone',
            'label'     => \Yii::t('customer', 'Phone'),
            'content'   => function ($model){
                return (new Phone($model->phone))->getHumanView();
            }
        ];

        $result['email'] = [
            'attribute' => 'email',
            'label'     => \Yii::t('customer', 'Email'),
        ];

        $result['default_address_id'] = [
            'attribute' => 'default_address_id',
            'label'     => \Yii::t('customer', 'Default address ID'),
            'content'   => function ($model){
                return $model->getDefaultAddress() ? $model->getDefaultAddress()->full_address : '---';
            }
        ];

        return $result;
    }
}
