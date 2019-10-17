<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\PaymentType]].
 *
 * @see \app\models\PaymentType
 */
class PaymentTypeQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\PaymentType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\PaymentType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
