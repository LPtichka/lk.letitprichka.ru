<?php

namespace app\models\Repository;

use app\models\Helper\Phone;
use app\models\Queries\FranchiseQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%franchise}}".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property string $phone
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Exception $exception
 */
class Franchise extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_DELETED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%franchise}}';
    }

    /**
     * @inheritdoc
     * @return FranchiseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FranchiseQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'exception_id' => \Yii::t('franchise', 'Exception ID'),
            'id'           => \Yii::t('franchise', 'ID'),
            'name'         => \Yii::t('franchise', 'Name'),
            'phone'        => \Yii::t('franchise', 'Phone'),
            'updated_at'   => \Yii::t('franchise', 'Updated at'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone'], 'filter', 'filter' => function (){
                return '+7' . (new Phone((string) $this->phone))->getClearPhone();
            }],
            [['name'], 'required'],
            [['name'], 'string', 'min' => 10],
            [['phone'], 'string'],
            [['name'], 'unique', 'message' => \Yii::t('product', 'This product has already exists')],

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