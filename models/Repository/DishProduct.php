<?php

namespace app\models\Repository;

use app\models\Helper\Unit;
use app\models\Helper\Weight;
use app\models\Queries\DishProductQuery;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%dish}}".
 *
 * @property int $id
 * @property string $name
 * @property int $product_id
 * @property int $dish_id
 * @property int $brutto
 * @property int $netto
 * @property int $weight
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Exception $exception
 * @property Product $product
 */
class DishProduct extends \yii\db\ActiveRecord
{
    /** @var string */
    public $name;

    private $weightUnitDefault = Weight::UNIT_KG;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dish_product}}';
    }

    /**
     * @inheritdoc
     * @return DishProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DishProductQuery(get_called_class());
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
            [['name'], 'string'],
            [['weight', 'netto', 'brutto'], 'number'],
            [['dish_id', 'product_id'], 'integer'],
            [['weight', 'netto', 'brutto'], 'required'],
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
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDish()
    {
        return $this->hasOne(Dish::class, ['id' => 'dish_id']);
    }

    /**
     * @return string|null
     */
    public function getUnit(): ?string
    {
        if (empty($this->product)) {
            return null;
        }

        switch ($this->product->unit) {
            case Unit::UNIT_KG:
                return Unit::UNIT_GR;
            case Unit::UNIT_LITER:
                return Unit::UNIT_MILLI_LITER;
            default:
                return $this->product->unit;
        }
    }

    /**
     *
     */
    public function afterFind()
    {
        $this->product && $this->name = $this->product->name;
        parent::afterFind();
    }

    /**
     * @param string $weightUnitDefault
     */
    public function setWeightUnitDefault(string $weightUnitDefault): void
    {
        $this->weightUnitDefault = $weightUnitDefault;
    }
}