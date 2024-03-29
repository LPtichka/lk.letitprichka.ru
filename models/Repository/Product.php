<?php

namespace app\models\Repository;

use app\models\Helper\Unit;
use app\models\Queries\ProductQuery;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%payment_type}}".
 *
 * @property int $id
 * @property int $status
 * @property string $name
 * @property int $count
 * @property string $unit
 * @property int $exception_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Exception $exception
 */
class Product extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_DISABLED = 0;

    /** @var int */
    private $needCount = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id'           => \Yii::t('product', 'ID'),
            'name'         => \Yii::t('product', 'Name'),
            'count'        => \Yii::t('app', 'Count'),
            'unit'         => \Yii::t('product', 'Unit'),
            'weight'       => \Yii::t('app', 'Weight'),
            'updated_at'   => \Yii::t('app', 'Updated at'),
            'exception_id' => \Yii::t('product', 'Exception ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'unique', 'message' => \Yii::t('product', 'This product has already exists')],
            [['count'], 'number'],
            [['name', 'unit'], 'string'],
            [['unit'], 'in', 'range' => [Unit::UNIT_COUNT, Unit::UNIT_KG, Unit::UNIT_LITER]],
            [['exception_id', 'status'], 'integer'],
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

        $unit           = new Unit($data['unit']);
        $product->name  = trim($data['name']);
        $product->count = (float) $product->count + $unit->convert((float) $data['count']);
        $product->unit  = $unit->getMainUnit();

        return $product;
    }

    /**
     * @inheritdoc
     * @return ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function getExceptionList(): array
    {
        return ArrayHelper::map(Exception::find()->asArray()->all(), 'id', 'name');
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (empty($this->count)) {
            $this->count = 0;
        }
//        if ($this->unit == Unit::UNIT_KG || $this->unit == Unit::UNIT_LITER) {
//            $this->count = (new Weight())->convert((float) $this->count, Weight::UNIT_KG);
//        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @return int
     */
    public function getNeedCount(): int
    {
        return $this->needCount;
    }

    /**
     * @param int $count
     */
    public function setNeedCount(int $count)
    {
        $this->needCount = $this->needCount + $count;
    }

    /**
     * @return int
     */
    public function getNotEnoughCount(): int
    {
        $count = $this->count - $this->needCount;
        if ($count < 0) {
            return $count * -1;
        }

        return 0;
    }
}