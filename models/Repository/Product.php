<?php

namespace app\models\Repository;

use app\models\Helper\Weight;
use app\models\Queries\PaymentTypeQuery;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%payment_type}}".
 *
 * @property int $id
 * @property string $name
 * @property int $count
 * @property int $weight
 * @property int $exception_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Exception $exception
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product}}';
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
            'id' => \Yii::t('product', 'ID'),
            'name' => \Yii::t('product', 'Name'),
            'count' => \Yii::t('app', 'Count'),
            'weight' => \Yii::t('app', 'Weight'),
            'updated_at' => \Yii::t('app', 'Updated at'),
            'exception_id' => \Yii::t('product', 'Exception ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'unique', 'message' => \Yii::t('product', 'This product has already exists')],
            ['name', 'string'],
            [['count', 'weight', 'exception_id'], 'integer'],
            ['exception_id', 'exist', 'targetClass' => Exception::class, 'targetAttribute' => 'id'],
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
     * @return \yii\db\ActiveQuery
     */
    public function getException()
    {
        return $this->hasOne(Exception::class, ['id' => 'exception_id']);
    }

    /**
     * @param array $data
     * @return Product
     */
    public function build(array $data): Product
    {
        if (!empty($data['id'])) {
             $product = Product::findOne((int) $data['id']);
        }

        if (!empty($data['name'])) {
             $product = Product::find()->where(['name' => trim($data['name'])])->one();
        }

        if (empty($product)) {
            $product = new Product();
        }

        $product->name = trim($data['name']);
        $product->count = (int) $product->count + (int) ($data['count'] ?? 0);
        $product->weight = (int) $product->weight + (new Weight())->convert((float) ($data['weight'] ?? 0), Weight::UNIT_KG);

        return $product;
    }

    /**
     * @return array
     */
    public function getExceptionList(): array
    {
        return ArrayHelper::map(Exception::find()->asArray()->all(), 'id', 'name');
    }
}