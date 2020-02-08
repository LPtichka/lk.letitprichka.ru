<?php

namespace app\models\Repository;

use app\components\Dadata;
use app\models\Builder\Suggestions;
use app\models\Helper\Phone;
use app\models\Product;
use app\models\Queries\CustomerExceptionQuery;
use app\models\Queries\CustomerQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%customer_exception}}".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $exception_id
 * @property int $created_at
 * @property int $updated_at
 */
class CustomerException extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customer_exception}}';
    }

    /**
     * @inheritdoc
     * @return CustomerExceptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CustomerExceptionQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'exception_id'], 'integer'],
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