<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\CustomerException]].
 *
 * @see \app\models\Customer
 */
class CustomerExceptionQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\CustomerException[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\CustomerException|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
