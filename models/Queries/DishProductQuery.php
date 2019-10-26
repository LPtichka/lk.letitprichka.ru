<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\DishProduct]].
 *
 * @see \app\models\DishProduct
 */
class DishProductQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\DishProduct[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\DishProduct|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
