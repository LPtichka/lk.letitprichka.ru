<?php

namespace app\models\Repository;

use app\models\Queries\SubscriptionQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%subscription}}".
 *
 * @property int $id
 * @property string $name
 * @property int $price
 * @property boolean $has_breakfast
 * @property boolean $has_dinner
 * @property boolean $has_lunch
 * @property boolean $has_supper
 * @property int $created_at
 * @property int $updated_at
 *
 * @property SubscriptionDiscount[] $discounts
 */
class Subscription extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_DELETED = 0;

    /** @var boolean */
    public $isTest;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subscription}}';
    }

    /**
     * @inheritdoc
     * @return SubscriptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SubscriptionQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('subscription', 'ID'),
            'name' => \Yii::t('subscription', 'Name'),
            'price' => \Yii::t('subscription', 'Price'),
            'has_breakfast' => \Yii::t('subscription', 'Has breakfast'),
            'has_dinner' => \Yii::t('subscription', 'Has dinner'),
            'has_lunch' => \Yii::t('subscription', 'Has lunch'),
            'has_supper' => \Yii::t('subscription', 'Has supper'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['has_breakfast', 'has_dinner', 'has_lunch', 'has_supper'], 'boolean'],
            ['price', 'integer'],
            ['name', 'string'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscounts()
    {
        return $this->hasMany(SubscriptionDiscount::class, ['subscription_id' => 'id']);
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

    /**
     * @param SubscriptionDiscount[] $discounts
     */
    public function setDiscounts(array $discounts): void
    {
        $this->discounts = $discounts;
    }

    /**
     * @param array $post
     * @return bool
     */
    public function build(array $post): bool
    {
        if (!$this->load($post)) {
            return false;
        }

        $discounts = [];
        if (!empty($post['SubscriptionDiscount'])) {
            foreach ($post['SubscriptionDiscount'] as $discount) {
                $discountModel = new SubscriptionDiscount();
                if (!$discountModel->load($discount, '')) {
                    return false;
                }
                $discounts[] = $discountModel;
            }
        }

        $this->setDiscounts($discounts);
        return true;
    }

    /**
     * @return bool
     */
    public function validateAll(): bool
    {
        $isValid = $this->validate();
        $isDiscountValid = true;
        foreach ($this->discounts as $discount) {
            if (!$discount->validate()) {
                $isDiscountValid = false;
            }
        }

        return $isValid && $isDiscountValid;
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function saveAll(): bool
    {
        $transaction = \Yii::$app->db->beginTransaction();
        if (!$isSaved = $this->save()) {
            $transaction->rollBack();
            return false;
        }

        SubscriptionDiscount::deleteAll(['subscription_id' => $this->id]);
        if (!empty($this->discounts)) {
            foreach ($this->discounts as $discount) {
                $discount->subscription_id = $this->id;
                if (!$discount->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
        }

        $transaction->commit();
        return true;
    }

    /**
     * @return array
     */
    public function getCounts(): array
    {
        return [
            1 => \Yii::t('Subscription', 'One day'),
            5 => \Yii::t('Subscription', '5 day'),
            10 => \Yii::t('Subscription', '10 day'),
            20 => \Yii::t('Subscription', '20 day'),
        ];
    }
}