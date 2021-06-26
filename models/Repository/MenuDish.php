<?php

namespace app\models\Repository;

use app\models\Common\Ingestion;
use app\models\Common\MarriageDish;
use app\models\Helper\Weight;
use app\models\Queries\MenuDishQuery;
use app\models\Queries\MenuQuery;
use app\models\Queries\OrderQuery;
use app\models\Queries\ProductQuery;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%menu_dish}}".
 *
 * @property int $id
 * @property string $date
 * @property int $menu_id
 * @property int $dish_id
 * @property int $dish_type
 * @property int $ingestion_type
 * @property int $ingestion
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Dish $dish
 */
class MenuDish extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu_dish}}';
    }

    /**
     * @inheritdoc
     * @return MenuDishQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuDishQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dish_id', 'menu_id', 'ingestion', 'ingestion_type', 'dish_type'], 'integer'],
            [['date'], 'string'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDish()
    {
        return $this->hasOne(Dish::class, ['id' => 'dish_id']);
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
     * @param string $date
     * @return array
     */
    public function getMarriageForDate(string $date): array
    {
        $result = [];
        $time = strtotime($date);
        $dishes = MenuDish::find()->where(['date' => date('Y-m-d', $time)])->orderBy(['ingestion_type' => SORT_ASC])->all();
        $time = date("H:i", time());
        foreach ($dishes as $dish) {
            if (!empty($dish->dish_type)) {
                $ingestionName = (new Ingestion())->getIngestionName($dish->ingestion_type, $dish->dish_type);
            } else {
                $ingestionName = (new Ingestion())->getIngestionName($dish->ingestion_type);
            }

            $marriageDish = new MarriageDish($time, $ingestionName, $dish->dish->name);
            $marriageDish->setWeight($dish->dish->weight);
            $marriageDish->setResult('проба снята, разрешено к выдаче');
            $result[] = $marriageDish;
        }

        return $result;
    }
}