<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\OrderDish]].
 *
 * @see \app\models\Repository\OrderDish
 */
class OrderDishQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\OrderDish[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\OrderDish|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
