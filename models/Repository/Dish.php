<?php

namespace app\models\Repository;

use app\models\Helper\Weight;
use app\models\Queries\DishQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%dish}}".
 *
 * @property int $id
 * @property int $type
 * @property string $name
 * @property boolean $is_breakfast
 * @property boolean $is_dinner
 * @property boolean $is_lunch
 * @property boolean $is_supper
 * @property boolean $with_garnish
 * @property int $weight
 * @property string $process
 * @property int $kkal
 * @property int $fat
 * @property int $proteins
 * @property int $carbohydrates
 * @property string $storage_condition
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Exception $exception
 * @property DishProduct[] $dishProducts
 */
class Dish extends \yii\db\ActiveRecord
{
    const TYPE_FIRST = 1;
    const TYPE_SECOND = 2;
    const TYPE_DESERT = 3;
    const TYPE_SALAD = 4;
    const TYPE_GARNISH = 5;

    const INGESTION_TYPE_BREAKFAST = 1;
    const INGESTION_TYPE_DINNER = 2;
    const INGESTION_TYPE_LUNCH = 3;
    const INGESTION_TYPE_SUPPER = 4;

    const INGESTION_TYPE_BREAKFAST_NAME = 'breakfast';
    const INGESTION_TYPE_DINNER_NAME = 'dinner';
    const INGESTION_TYPE_LUNCH_NAME = 'lunch';
    const INGESTION_TYPE_SUPPER_NAME = 'supper';

    private $weightUnitDefault = Weight::UNIT_KG;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dish}}';
    }

    /**
     * @inheritdoc
     * @return DishQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DishQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'name'              => \Yii::t('dish', 'Name'),
            'is_breakfast'      => \Yii::t('dish', 'Breakfast'),
            'is_dinner'         => \Yii::t('dish', 'Dinner'),
            'is_lunch'          => \Yii::t('dish', 'Lunch'),
            'is_supper'         => \Yii::t('dish', 'Supper'),
            'with_garnish'      => \Yii::t('dish', 'With garnish'),
            'fat'               => \Yii::t('dish', 'Fat'),
            'kkal'              => \Yii::t('dish', 'Kkal'),
            'type'              => \Yii::t('dish', 'Type'),
            'weight'            => \Yii::t('dish', 'Weight'),
            'proteins'          => \Yii::t('dish', 'Proteins'),
            'carbohydrates'     => \Yii::t('dish', 'Carbohydrates'),
            'storage_condition' => \Yii::t('dish', 'Storage condition'),
            'process'           => \Yii::t('dish', 'Process'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_breakfast', 'is_dinner', 'is_lunch', 'is_supper', 'with_garnish'], 'boolean'],
            [['storage_condition', 'process', 'name'], 'string'],
            [['type'], 'integer'],
            [['fat', 'weight', 'proteins', 'kkal', 'carbohydrates'], 'number'],
            [['name', 'process', 'fat', 'weight', 'proteins', 'kkal', 'carbohydrates'], 'required'],
            [['with_garnish'], 'validateGarnish'],
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
     * @param $attribute
     * @param $params
     */
    public function validateGarnish($attribute, $params)
    {
        if (!empty($this->$attribute) && $this->type == self::TYPE_GARNISH) {
            $this->addError($attribute, \Yii::t('dish', 'The type of dish cannot be a side dish and have an option with a side dish'));
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDishProducts()
    {
        return $this->hasMany(DishProduct::class, ['dish_id' => 'id']);
    }

    /**
     * Получение типов блюд
     *
     * @return array
     */
    public function getTypes(): array
    {
        return [
            self::TYPE_FIRST  => \Yii::t('dish', 'First course'),
            self::TYPE_SECOND => \Yii::t('dish', 'Second course'),
//            self::TYPE_SALAD   => \Yii::t('dish', 'Salad'),
//            self::TYPE_DESERT  => \Yii::t('dish', 'Dessert'),
            self::TYPE_GARNISH => \Yii::t('dish', 'Garnish'),
        ];
    }

    public function getKkalFoodValue(): float
    {
        $value = 0;
        foreach ($this->dishProducts as $product) {
            $value += ($product->kkal / 100) * (new Weight())->convert($product->netto, Weight::UNIT_KG);
        }
        return $value;
    }

    public function getFatFoodValue(): float
    {
        $value = 0;
        foreach ($this->dishProducts as $product) {
            $value += ($product->fat / 100) * (new Weight())->convert($product->netto, Weight::UNIT_KG);
        }
        return $value;
    }

    public function getProteinsFoodValue(): float
    {
        $value = 0;
        foreach ($this->dishProducts as $product) {
            $value += ($product->proteins / 100) * (new Weight())->convert($product->netto, Weight::UNIT_KG);
        }
        return $value;
    }

    public function getCarbohydratesFoodValue(): float
    {
        $value = 0;
        foreach ($this->dishProducts as $product) {
            $value += ($product->carbohydrates / 100) * (new Weight())->convert($product->netto, Weight::UNIT_KG);
        }
        return $value;
    }

    /**
     * Получение типа блюда по его названию
     *
     * @param string $title
     * @return int|null
     */
    public function getTypeByTitle(string $title): ?int
    {
        $title = mb_strtolower(trim($title));
        switch ($title) {
            case 'первое':
                return self::TYPE_FIRST;
            case 'второе':
                return self::TYPE_SECOND;
            case 'гарнир':
                return self::TYPE_GARNISH;
            case 'десерт':
                return self::TYPE_DESERT;
            case 'салат':
                return self::TYPE_SALAD;
            default:
                return null;
        }
    }

    /**
     * @param array $data
     * @param string $weightUnit
     * @return Dish
     */
    public function build(array $data, $weightUnit = Weight::UNIT_KG): Dish
    {
        $dish = new Dish();
        $dish->setWeightUnitDefault($weightUnit);

        $dish->name              = $data['name'];
        $dish->storage_condition = $data['storage_conditions'];
        $dish->process           = $data['process'];
        $dish->kkal              = $data['kkal'];
        $dish->proteins          = $data['proteins'];
        $dish->fat               = $data['fat'];
        $dish->carbohydrates     = $data['carbohydrates'];
        $dish->is_breakfast      = $data['is_breakfast'];
        $dish->is_dinner         = $data['is_dinner'];
        $dish->is_lunch          = $data['is_lunch'];
        $dish->is_supper         = $data['is_supper'];
        $dish->type              = $data['type'];
        $dish->with_garnish      = $data['with_garnish'];
        $dish->weight            = $data['weight'];

        $products = [];
        foreach ($data['products'] as $product) {
            $dishProduct = new DishProduct();
            $dishProduct->setWeightUnitDefault($weightUnit);

            if (!empty($product['name'])) {
                $productRepository = Product::find()->where(['name' => trim($product['name'])])->one();
            }

            $dishProduct->product_id     = $productRepository->id ?? null;
            $dishProduct->name           = $product['name'];
            $dishProduct->brutto         = $product['brutto'];
            $dishProduct->netto          = $product['netto'];
            $dishProduct->weight         = $product['weight'];
            $dishProduct->brutto_on_1_kg = $product['weight_on_1_kg'];
            $dishProduct->kkal           = $product['kkal'];
            $dishProduct->proteins       = $product['proteins'];
            $dishProduct->fat            = $product['fat'];
            $dishProduct->carbohydrates  = $product['carbohydrates'];

            $products[] = $dishProduct;
        }

        $dish->setDishProducts($products);
        return $dish;
    }

    /**
     * @param string $weightUnitDefault
     */
    public function setWeightUnitDefault(string $weightUnitDefault): void
    {
        $this->weightUnitDefault = $weightUnitDefault;
    }

    /**
     * @param DishProduct[] $dishProducts
     */
    public function setDishProducts(array $dishProducts): void
    {
        $this->dishProducts = $dishProducts;
    }

    /**
     * @param string $name
     * @return int|null
     */
    public function getIngestionTypeByName(string $name): ?int
    {
        switch ($name) {
            case self::INGESTION_TYPE_BREAKFAST_NAME:
                return self::INGESTION_TYPE_BREAKFAST;
            case self::INGESTION_TYPE_DINNER_NAME:
                return self::INGESTION_TYPE_DINNER;
            case self::INGESTION_TYPE_LUNCH_NAME:
                return self::INGESTION_TYPE_LUNCH;
            case self::INGESTION_TYPE_SUPPER_NAME:
                return self::INGESTION_TYPE_SUPPER;
            default:
                return null;
        }
    }

    /**
     * @param string $name
     * @return int|null
     */
    public function getDishTypeByName(string $name): ?int
    {
        switch ($name) {
            case 'first':
                return self::TYPE_FIRST;
            case 'second':
                return self::TYPE_SECOND;
            case 'garnish':
                return self::TYPE_GARNISH;
            default:
                return null;
        }
    }

    /**
     * @return array
     */
    public function getIngestionTypes(): array
    {
        return [
            self::INGESTION_TYPE_BREAKFAST,
            self::INGESTION_TYPE_DINNER,
            self::INGESTION_TYPE_LUNCH,
            self::INGESTION_TYPE_SUPPER,
        ];
    }

    /**
     * Все возможные рационы блюд
     *
     * @return array
     */
    public function getIngestions(): array
    {
        return [
            self::INGESTION_TYPE_BREAKFAST => \Yii::t('dish', self::INGESTION_TYPE_BREAKFAST_NAME),
            self::INGESTION_TYPE_DINNER    => \Yii::t('dish', self::INGESTION_TYPE_DINNER_NAME),
            self::INGESTION_TYPE_LUNCH     => \Yii::t('dish', self::INGESTION_TYPE_LUNCH_NAME),
            self::INGESTION_TYPE_SUPPER    => \Yii::t('dish', self::INGESTION_TYPE_SUPPER_NAME),
        ];
    }

    /**
     * Список рационов в который входит блюдо
     *
     * @return array
     */
    public function getIngestionList(): array
    {
        $ingestions = [];

        if ($this->is_lunch) {
            $ingestions[] = \Yii::t('dish', self::INGESTION_TYPE_LUNCH_NAME);
        }
        if ($this->is_breakfast) {
            $ingestions[] = \Yii::t('dish', self::INGESTION_TYPE_BREAKFAST_NAME);
        }
        if ($this->is_dinner) {
            $ingestions[] = \Yii::t('dish', self::INGESTION_TYPE_DINNER_NAME);
        }
        if ($this->is_supper) {
            $ingestions[] = \Yii::t('dish', self::INGESTION_TYPE_SUPPER_NAME);
        }

        return $ingestions;
    }

    /**
     * Получить состав блюда
     *
     * @return array
     */
    public function getComposition(): array
    {
        $result = [];
        foreach ($this->dishProducts as $product) {
            $result[] = $product->name;
        }
        return $result;
    }

    /**
     * Список ID исключений входящий в состав блюда
     * @return array
     */
    public function getExceptionList(): array
    {
        $query = (new Query())
            ->select('product.exception_id')
            ->from('dish_product')
            ->leftJoin('product', 'dish_product.product_id = product.id')
            ->where(['dish_id' => $this->id])
            ->all();

        return ArrayHelper::getColumn($query, 'exception_id');
    }
}