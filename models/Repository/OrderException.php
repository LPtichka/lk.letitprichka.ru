<?php

namespace app\models\Repository;

use app\models\Queries\OrderScheduleQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%order_exception}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int $exception_id
 * @property string $comment
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Exception $exception
 */
class OrderException extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_exception}}';
    }

    /**
     * @inheritdoc
     * @return OrderScheduleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderScheduleQuery(get_called_class());
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
            [['order_id', 'exception_id'], 'integer'],
            [['comment'], 'string'],
            [['order_id'], 'exist', 'targetClass' => Order::class, 'targetAttribute' => 'id', 'message' => 'Указан не существующий заказ'],
            [['exception_id'], 'exist', 'targetClass' => Exception::class, 'targetAttribute' => 'id', 'message' => 'Указан не существующее исключение'],
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getException()
    {
        return $this->hasOne(Exception::class, ['id' => 'exception_id']);
    }
}