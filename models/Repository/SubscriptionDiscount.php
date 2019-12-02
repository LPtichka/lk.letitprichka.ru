<?php

namespace app\models\Repository;

use app\models\Queries\SubscriptionDiscountQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%subscription_discount}}".
 *
 * @property int $id
 * @property int $subscription_id
 * @property int $count
 * @property int $price
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Exception $exception
 */
class SubscriptionDiscount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subscription_discount}}';
    }

    /**
     * @inheritdoc
     * @return SubscriptionDiscountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SubscriptionDiscountQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('subscription', 'ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price', 'count', 'subscription_id'], 'integer'],
            ['subscription_id', 'exist', 'targetClass' => Subscription::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }
}