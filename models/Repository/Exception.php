<?php

namespace app\models\Repository;

use app\models\Queries\ExceptionQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%exception}}".
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property int $with_comment
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Product[] $products
 */
class Exception extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_DELETED = 0;

    public $comment;

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
            'with_comment'       => \Yii::t('exception', 'With comment'),
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
            ['name', 'string', 'min' => 5],
            ['with_comment', 'integer'],
            ['comment', 'string'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
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

    /**
     * @return array
     */
    public function getExceptionList(): array
    {
        return Exception::find()->where(['status' => self::STATUS_ACTIVE])->asArray()->all();
    }
}