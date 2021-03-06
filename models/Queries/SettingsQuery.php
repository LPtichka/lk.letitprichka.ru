<?php

namespace app\models\Queries;

/**
 * This is the ActiveQuery class for [[\app\models\Repository\Settings]].
 *
 * @see \app\models\SubscriptionDiscount
 */
class SettingsQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Repository\Settings[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Repository\Settings|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
