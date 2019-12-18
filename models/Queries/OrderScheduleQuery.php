<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\OrderSchedule]].
 *
 * @see \app\models\Repository\OrderSchedule
 */
class OrderScheduleQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\OrderSchedule[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\OrderSchedule|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
