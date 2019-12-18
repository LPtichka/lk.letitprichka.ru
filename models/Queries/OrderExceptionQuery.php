<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\OrderException]].
 *
 * @see \app\models\Repository\OrderException
 */
class OrderExceptionQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\OrderException[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\OrderException|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
