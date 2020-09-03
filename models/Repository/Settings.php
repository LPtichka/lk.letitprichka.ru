<?php

namespace app\models\Repository;

use app\models\Queries\OrderDishQuery;
use app\models\Queries\OrderScheduleQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%order_dish}}".
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $franchise_id
 * @property int $created_at
 * @property int $updated_at
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%settings}}';
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
            [['name', 'value'], 'string'],
            [['name', 'value'], 'required'],
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