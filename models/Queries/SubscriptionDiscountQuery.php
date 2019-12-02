<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\SubscriptionDiscount]].
 *
 * @see \app\models\SubscriptionDiscount
 */
class SubscriptionDiscountQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\SubscriptionDiscount[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\SubscriptionDiscount|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
