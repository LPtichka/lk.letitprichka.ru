<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\Subscription]].
 *
 * @see \app\models\Subscription
 */
class SubscriptionQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\Subscription[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\Subscription|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
