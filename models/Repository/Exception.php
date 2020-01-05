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
 *
 * @property Product[] $products
 */
class Exception extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_DELETED = 0;
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
            'product_count'       => \Yii::t('exception', 'Product count'),
            'updated_at' => \Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['exception_id' => 'id']);
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