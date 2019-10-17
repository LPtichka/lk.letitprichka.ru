<?php
namespace app\models\Search;

use yii\data\ActiveDataProvider;

interface SearchModelInterface
{
    /**
     * @param $params array
     * @return ActiveDataProvider
     */
    public function search($params);
}