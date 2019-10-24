<?php

namespace app\models\search;

use app\models\Helper\Weight;
use app\models\Repository\User as Repository;
use yii\data\ActiveDataProvider;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;

class User extends Repository
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
            [['id', 'status'], 'integer'],
            [['fio', 'phone', 'email'], 'string'],
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
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['like', 'fio', $this->fio]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return iterable
     */
    public function export($params): iterable
    {
        $usersQuery = self::find();

        !empty($params['id']) && $usersQuery->filterWhere(['id' => $params['id']]);
        !empty($params['status']) && $usersQuery->filterWhere(['status' => $params['status']]);
        !empty($params['fio']) && $usersQuery->filterWhere(['like', 'fio', urldecode($params['fio'])]);
        !empty($params['phone']) && $usersQuery->filterWhere(['like', 'phone', urldecode($params['phone'])]);
        !empty($params['email']) && $usersQuery->filterWhere(['like', 'email', urldecode($params['email'])]);

        $users = $usersQuery->asArray()->all();
        foreach ($users as $user) {
            yield [
                'ID'         => $user['id'],
                'Fio'       => $user['fio'],
                'Email'      => $user['email'],
                'Phone'      => $user['phone'],
                'Status'      => $user['status'],
                'Created at' => date('d.m.Y \в H:i', $user['created_at']),
                'Updated at' => date('d.m.Y \в H:i', $user['updated_at']),
            ];
        }
    }

    /**
     * Список полей для поиска
     *
     * @param User $searchModel
     * @return array
     */
    public function getSearchColumns(User $searchModel)
    {
        $result[] = [
            'class'         => CheckboxColumn::class,
            'headerOptions' => [
                'width'                    => '40px',
                'data-resizable-column-id' => 'checker'
            ],
        ];

        $result['id'] = [
            'attribute' => 'id',
            'label'     => \Yii::t('user', 'ID'),
            'content'   => function ($model) {
                return Html::a($model->id, ['user/view', 'id' => $model->id]);
            }
        ];

        $result['fio'] = [
            'attribute' => 'fio',
            'label'     => \Yii::t('user', 'Fio'),
        ];

        $result['phone'] = [
            'attribute' => 'phone',
            'label'     => \Yii::t('user', 'Phone'),
        ];

        $result['email'] = [
            'attribute' => 'email',
            'label'     => \Yii::t('user', 'Email'),
        ];

        $result['status'] = [
            'attribute' => 'status',
            'label'     => \Yii::t('user', 'Status'),
            'filter'    => Html::tag('div', Html::dropDownList(
                    'status',
                    $searchModel->status,
                    $searchModel->getStatuses(),
                    ['class' => 'form-control']
                ),
                ['class' => 'select_wrapper']
            ),
            'content'   => function ($model) {
                return $model->getStatus();
            }
        ];

        $result['created_at'] = [
            'attribute' => 'created_at',
            'label'     => \Yii::t('user', 'Created at'),
            'content'   => function ($model) {
                return date('d.m.Y \в H:i', $model->created_at);
            }
        ];
        return $result;
    }
}
