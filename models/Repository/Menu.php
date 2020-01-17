<?php

namespace app\models\Repository;

use app\models\Helper\Weight;
use app\models\Queries\MenuQuery;
use app\models\Queries\OrderQuery;
use app\models\Queries\ProductQuery;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property int $id
 * @property string $menu_start_date
 * @property string $menu_end_date
 * @property int $created_at
 * @property int $updated_at
 *
 * @property MenuDish[] $dishes
 */
class Menu extends \yii\db\ActiveRecord
{
    /** @var int */
    public $dayCount = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     * @return MenuQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MenuQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'subscription_id' => \Yii::t('order', 'Subscription ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_start_date', 'menu_end_date'], 'string'],
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
     * @param array $dishes
     */
    public function setDishes(array $dishes): void
    {
        $this->dishes = $dishes;
    }

    /**
     * @return string[]
     */
    public function getDisabledDays(): array
    {
        $startDate = date('Y-m-d', time() - 86400 * 50);
        $menu = Menu::find()->where(['>', 'menu_start_date', $startDate])->asArray()->all();

        $result = [];
        foreach ($menu as $menuItem) {
            $startMenuTimestamp = strtotime($menuItem['menu_start_date']);
            $i = 0;
            do {
                $date = date('Y-m-d', $startMenuTimestamp + 86400 * $i);
                $result[] = $date;
                $i++;
            } while ($date < $menuItem['menu_end_date']);
        }

        return $result;
    }

    /**
     * @param array $data
     * @return Menu
     */
    public function build(array $data): Menu
    {
        !empty($data['menu_start_date']) && $this->menu_start_date = date('Y-m-d', strtotime($data['menu_start_date']));
        !empty($data['menu_end_date']) && $this->menu_end_date = date('Y-m-d', strtotime($data['menu_end_date']));

        $dishList = [];
        foreach ($data['dish'] as $date => $dayMenu) {
            foreach ($dayMenu as $ingestionType => $ingestion) {
                if ($ingestionType == 'dinner' || $ingestionType == 'supper') {
                    foreach ($ingestion as $type => $dishes) {
                        foreach ($dishes as $ingestionId => $dishId) {
                            if ($dishId) {
                                $menuDish = new MenuDish();

                                $menuDish->dish_id = $dishId;
                                $menuDish->ingestion = $ingestionId;
                                $menuDish->ingestion_type = (new Dish())->getIngestionTypeByName($ingestionType);
                                $menuDish->date = $date;

                                $dishList[] = $menuDish;
                            }

                        }
                    }
                } else {
                    foreach ($ingestion as $ingestionId => $dishId) {
                        if ($dishId) {
                            $menuDish = new MenuDish();

                            $menuDish->dish_id = $dishId;
                            $menuDish->ingestion = $ingestionId;
                            $menuDish->ingestion_type = (new Dish())->getIngestionTypeByName($ingestionType);
                            $menuDish->date = $date;

                            $dishList[] = $menuDish;
                        }
                    }
                }
            }
        }
        $this->setDishes($dishList);

        return $this;
    }

    /**
     * @return bool
     */
    public function saveAll(): bool
    {
        $isValidated = $this->validate();
        if (!$isValidated) {
            return false;
        }

        $event = new \app\events\LinkOrderDishes();
        $transaction = \Yii::$app->db->beginTransaction();

        if (!$this->save()) {
            $transaction->rollBack();
            return false;
        }

        MenuDish::deleteAll(['menu_id' => $this->id]);

        $event->setMenuID($this->id);
        foreach ($this->dishes as $dish) {
            $dish->menu_id = $this->id;
            if (!$dish->validate() || !$dish->save()) {
                $transaction->rollBack();
                return false;
            }
        }

        try {
            $transaction->commit();
        } catch (\yii\db\Exception $e) {
            return false;
        }

        $event->prepareEvent();
        \Yii::$app->trigger(\app\events\LinkOrderDishes::EVENT_MENU_CREATED, $event);
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDishes()
    {
        return $this->hasMany(MenuDish::class, ['menu_id' => 'id']);
    }

    /**
     * @param int $ingestionID
     * @param string $date
     * @param string $type
     * @param int $ingestionType
     * @return int
     */
    public function getDishIDByParams(
        int $ingestionID = 0,
        string $date = '',
        string $type = '',
        int $ingestionType = 0
    ): int {
        foreach ($this->dishes as $dish) {
            if ($dish->ingestion == $ingestionID && $dish->date == $date) {
                if ($type == 'breakfast' && $dish->dish->is_breakfast) {
                    return $dish->dish_id;
                }
                if ($type == 'lunch' && $dish->dish->is_lunch) {
                    return $dish->dish_id;
                }
                if ($type == 'dinner' && $dish->dish->is_dinner && $ingestionType == $dish->dish->type) {
                    return $dish->dish_id;
                }
                if ($type == 'supper' && $dish->dish->is_supper && $ingestionType == $dish->dish->type) {
                    return $dish->dish_id;
                }
            }
        }

        return 0;
    }
}