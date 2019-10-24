<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\Exception]].
 *
 * @see \app\models\Product
 */
class ExceptionQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\Exception[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\Exception|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
