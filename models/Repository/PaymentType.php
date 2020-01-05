<?php

namespace app\models\Repository;

use app\models\Queries\PaymentTypeQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%payment_type}}".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property boolean $cash_machine
 * @property int $created_at
 * @property int $updated_at
 */
class PaymentType extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_DELETED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payment_type}}';
    }

    /**
     * @inheritdoc
     * @return PaymentTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentTypeQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('payment', 'ID'),
            'name' => \Yii::t('payment', 'Name'),
            'updated_at' => \Yii::t('payment', 'Updated at'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'unique', 'message' => \Yii::t('payment', 'This payment has already exists')],
            ['name', 'string'],
            ['cash_machine', 'boolean'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['cash_machine', 'default', 'value' => 0],
            ['name', 'required'],
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