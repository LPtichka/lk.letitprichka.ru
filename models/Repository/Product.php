<?php

namespace app\models\Repository;

use app\models\Helper\Weight;
use app\models\Queries\PaymentTypeQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%payment_type}}".
 *
 * @property int $id
 * @property string $name
 * @property int $count
 * @property int $weight
 * @property int $created_at
 * @property int $updated_at
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
            [['count', 'weight'], 'integer'],
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
        !empty($data['count']) && $product->count = $product->count + (int) $data['count'];
        !empty($data['weight']) && $product->weight = $product->weight + (new Weight())->convert((float) $data['weight'], Weight::UNIT_KG);

        return $product;
    }
}