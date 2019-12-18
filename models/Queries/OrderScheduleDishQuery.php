<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\OrderScheduleDish]].
 *
 * @see \app\models\Repository\OrderSchedule
 */
class OrderScheduleDishQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\OrderScheduleDish[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\OrderScheduleDish|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
