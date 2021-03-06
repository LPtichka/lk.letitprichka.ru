<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\Menu]].
 *
 * @see \app\models\Menu
 */
class MenuQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\Menu[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\Menu|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
