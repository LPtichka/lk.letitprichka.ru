<?php

namespace app\models\Repository;

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
 * @property int $brutto_on_1_kg
 * @property int $netto
 * @property int $weight
 * @property int $kkal
 * @property int $fat
 * @property int $proteins
 * @property int $carbohydrates
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
            [['fat', 'proteins', 'kkal', 'carbohydrates', 'dish_id', 'product_id'], 'integer'],
            [['fat', 'weight', 'netto', 'brutto', 'brutto_on_1_kg', 'proteins', 'kkal', 'carbohydrates'], 'required'],
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
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->weight = (new Weight())->convert($this->weight, $this->weightUnitDefault);
        $this->netto = (new Weight())->convert($this->netto, $this->weightUnitDefault);
        $this->brutto = (new Weight())->convert($this->brutto, $this->weightUnitDefault);
        $this->brutto_on_1_kg = (new Weight())->convert($this->brutto_on_1_kg, $this->weightUnitDefault);

        return parent::beforeSave($insert);
    }

    /**
     *
     */
    public function afterFind()
    {
        $this->product && $this->name = $this->product->name;
        $this->weight = (new Weight())->setUnit(Weight::UNIT_KG)->convert($this->weight, Weight::UNIT_GR);
        $this->netto = (new Weight())->setUnit(Weight::UNIT_KG)->convert($this->netto, Weight::UNIT_GR);
        $this->brutto = (new Weight())->setUnit(Weight::UNIT_KG)->convert($this->brutto, Weight::UNIT_GR);
        $this->brutto_on_1_kg = (new Weight())->setUnit(Weight::UNIT_KG)->convert($this->brutto_on_1_kg, Weight::UNIT_GR);
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