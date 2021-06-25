<?php

namespace app\models\Repository;

use app\models\Queries\SettingsQuery;
use app\models\Queries\SubscriptionQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%order_dish}}".
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $status
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
     * @return SettingsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SettingsQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'string'],
            [['status'], 'integer'],
            [['name', 'value', 'status'], 'required'],
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
     * @return array
     */
    public function getWorkDays(): array
    {
        $wd = Settings::find()->where(['name' => 'work_days'])->one();
        $workDays = explode(';', $wd->value);
        $result = [];

        foreach ($workDays as $day) {
            $item = explode(':', $day);
            $result[$item[0]] = $item[1];
        }

        return $result;
    }
}