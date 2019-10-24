<?php

namespace app\models\Repository;

use app\models\Queries\UserQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%payment_type}}".
 *
 * @property int $id
 * @property string $email
 * @property string $fio
 * @property string $phone
 * @property string $access_token
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class User extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_DISABLED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id'         => \Yii::t('user', 'ID'),
            'email'      => \Yii::t('user', 'Email'),
            'fio'        => \Yii::t('user', 'Fio'),
            'phone'      => \Yii::t('user', 'Phone'),
            'updated_at' => \Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'unique', 'message' => \Yii::t('user', 'This email has already exists')],
            ['email', 'email'],
            [['phone', 'fio'], 'string'],
            [['email', 'phone', 'fio'], 'required'],
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
     * @return string
     */
    public function getStatus(): string
    {
        return $this->getStatuses()[$this->status];
    }

    /**
     * @return array
     */
    public function getStatuses(): array
    {
        return [
            '' => \Yii::t('app', 'Choose'),
            self::STATUS_ACTIVE => \Yii::t('app', 'Active'),
            self::STATUS_DISABLED => \Yii::t('app', 'Disabled'),
        ];
    }
}