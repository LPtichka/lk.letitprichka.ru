<?php

namespace app\models\Repository;

use app\models\Queries\ExceptionQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%exception}}".
 *
 * @property int $id
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 */
class Exception extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%exception}}';
    }

    /**
     * @inheritdoc
     * @return ExceptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExceptionQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id'         => \Yii::t('exception', 'ID'),
            'name'       => \Yii::t('exception', 'Name'),
            'updated_at' => \Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'unique', 'message' => \Yii::t('exception', 'This exception has already exists')],
            ['name', 'string'],
            [['name'], 'required'],
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