<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\MenuDish]].
 *
 * @see \app\models\Menu
 */
class MenuDishQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\MenuDish[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\MenuDish|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
