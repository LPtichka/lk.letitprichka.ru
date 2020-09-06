<?php

namespace app\models\Repository;

use app\models\Queries\MenuQuery;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property int $id
 * @property int $status
 * @property string $menu_start_date
 * @property string $menu_end_date
 * @property int $created_at
 * @property int $updated_at
 *
 * @property MenuDish[] $dishes
 */
class Menu extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_DELETED = 0;

    /** @var int */
    public $dayCount = 1;
    /** @var array */
    private $ingestionCounts = [];
    /** @var array */
    private $ingestionAvailability = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
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
            [['status'], 'integer'],
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
     * @return string[]
     */
    public function getDisabledDays(): array
    {
        $startDate = date('Y-m-d', time() - 86400 * 50);
        $menu      = Menu::find()->where(['>', 'menu_start_date', $startDate])->asArray()->all();

        $result = [];
        foreach ($menu as $menuItem) {
            $startMenuTimestamp = strtotime($menuItem['menu_start_date']);
            $i                  = 0;
            do {
                $date     = date('Y-m-d', $startMenuTimestamp + 86400 * $i);
                $result[] = $date;
                $i++;
            } while ($date < $menuItem['menu_end_date']);
        }

        return $result;
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

                                $menuDish->dish_id        = $dishId;
                                $menuDish->ingestion      = $ingestionId;
                                $menuDish->dish_type      = (new Dish())->getDishTypeByName($type);
                                $menuDish->ingestion_type = (new Dish())->getIngestionTypeByName($ingestionType);
                                $menuDish->date           = $date;

                                $dishList[] = $menuDish;
                            } else {
                                $this->addError('dishes', \Yii::t('menu', 'Empty dish id for ' . $ingestionType));
                            }
                        }
                    }
                } else {
                    foreach ($ingestion as $ingestionId => $dishId) {
                        if ($dishId) {
                            $menuDish = new MenuDish();

                            $menuDish->dish_id        = $dishId;
                            $menuDish->ingestion      = $ingestionId;
                            $menuDish->dish_type      = null;
                            $menuDish->ingestion_type = (new Dish())->getIngestionTypeByName($ingestionType);
                            $menuDish->date           = $date;

                            $dishList[] = $menuDish;
                        } else {
                            $this->addError('dishes', \Yii::t('menu', 'Empty dish id for ' . $ingestionType));
                        }
                    }
                }
            }
        }
        $this->setDishes($dishList);

        return $this;
    }

    /**
     * @param array $dishes
     */
    public function setDishes(array $dishes): void
    {
        $this->dishes = $dishes;
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

        $event       = new \app\events\MenuCreated();
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
        \Yii::$app->trigger(\app\events\MenuCreated::EVENT_MENU_CREATED, $event);
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
     * @param array $data
     * @return bool
     */
    public function isGarnishNeeded(
        int $ingestionID = 0,
        string $date = '',
        string $type = '',
        int $ingestionType = 0,
        array $data = []
    ): bool{
        $dishId = $this->getDishIDByParams($ingestionID, $date, $type, $ingestionType, $data);

        $dish = Dish::findOne($dishId);
        if (!$dish) {
            return false;
        }

        return (bool) $dish->with_garnish;
    }

    /**
     * @param int $ingestionID
     * @param string $date
     * @param string $type
     * @param int $ingestionType
     * @param array $chosenDishes
     * @return int
     */
    public function getDishIDByParams(
        int $ingestionID = 0,
        string $date = '',
        string $type = '',
        int $ingestionType = 0,
        array $chosenDishes = []
    ): int{
        foreach ($this->dishes as $dish) {
            if ($dish->ingestion == $ingestionID && $dish->date == $date) {
                if ($type == 'breakfast' && $dish->dish->is_breakfast) {
                    return $dish->dish_id;
                }
                if ($type == 'lunch' && $dish->dish->is_lunch) {
                    return $dish->dish_id;
                }
                if ($type == 'dinner'
                    && $dish->dish->is_dinner
                    && $dish->ingestion_type == Dish::INGESTION_TYPE_DINNER
                    && $ingestionType == $dish->dish->type
                ) {
                    return $dish->dish_id;
                }
//                if ($type == 'garnish'
//                    && $dish->ingestion_type == Dish::INGESTION_TYPE_DINNER
//                    && $ingestionType == $dish->dish->type
//                ) {
//                    return $dish->dish_id;
//                }
                if ($type == 'supper' && $dish->dish->is_supper && $dish->ingestion_type == Dish::INGESTION_TYPE_SUPPER && $ingestionType == $dish->dish->type) {
                    return $dish->dish_id;
                }
            }
        }

        if ($type == 'dinner' || $type == 'supper') {
            if ($ingestionType == Dish::TYPE_SECOND) {
                $type = 'second';
            } elseif ($ingestionType == Dish::TYPE_GARNISH) {
                $type = 'garnish';
            } else {
                $type = 'first';
            }
            if (!empty($chosenDishes[$type][$ingestionID])) {
                return $chosenDishes[$type][$ingestionID];
            }
        } else {
            if (!empty($chosenDishes[$ingestionID])) {
                return $chosenDishes[$ingestionID];
            }
        }


        return 0;
    }

    /**
     * @param string $type
     * @return int
     */
    public function getIngestionCountForDay(string $type = ''): int
    {
        if (empty($this->ingestionCounts)) {
            $rows = (new \yii\db\Query())
                ->select(['ingestion_type', 'MAX(ingestion) + 1 as count'])
                ->from('menu_dish')
                ->where(['menu_id' => $this->id])
                ->groupBy('ingestion_type')
                ->all();

            $this->ingestionCounts = ArrayHelper::map($rows, 'ingestion_type', 'count');
        }

        $ingestionId = (new Dish())->getIngestionTypeByName($type);
        if (isset($this->ingestionCounts[$ingestionId])) {
            return $this->ingestionCounts[$ingestionId];
        }

        return 0;
    }

    /**
     * @param string $type
     * @param int $ingestionId
     * @return bool
     */
    public function hasIngestion(string $type = '', int $ingestionId = 0): bool
    {
        if (!isset($this->ingestionAvailability[$type][$ingestionId])) {
            $ingestionType = (new Dish())->getIngestionTypeByName($type);
            $rows = (new \yii\db\Query())
                ->from('menu_dish')
                ->where(['menu_id' => $this->id, 'ingestion' => $ingestionId, 'ingestion_type' => $ingestionType])
                ->all();

            $this->ingestionAvailability[$type][$ingestionId] = !empty($rows);
        }

        return $this->ingestionAvailability[$type][$ingestionId];
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareMenuData(array $data): array
    {
        $result = [];
        foreach ($data['dishes'] as $item) {
            $indexName              = sprintf('%s-%d-%d', $item['date'], $item['ingestion_type'], $item['dish_type']);
            $dish                   = Dish::findOne($item['dish_id']);
            $item['exception_list'] = $dish->getExceptionList();
            $item['name']           = $dish->name;
            $item['with_garnish']   = $dish->with_garnish;

            $result[$indexName][$item['ingestion']] = $item;
        }

        return $result;
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function isEquipped(): bool
    {
        $sql = 'SELECT COUNT(*) as `count` FROM `order_schedule` AS os 
                      LEFT JOIN `order_schedule_dish` AS osd
                        ON os.id = osd.order_schedule_id
                      WHERE os.date >= "' . $this->menu_start_date . '" 
                            AND os.date <= "' . $this->menu_end_date . '" 
                            AND (osd.dish_id IS NULL OR (osd.with_garnish = 1 AND osd.garnish_id is NULL))';

        $result = \Yii::$app->db->createCommand($sql)->queryOne();
        return (bool) $result['count'];
    }

    /**
     * @return Product[]
     */
    public function getProcurementProducts(): array
    {
        $products = [];

        $schedules = OrderSchedule::find()
            ->where(['>=', 'date', $this->menu_start_date])
            ->andWhere(['<=', 'date', $this->menu_end_date])
            ->all();

        foreach ($schedules as $schedule) {
            foreach ($schedule->dishes as $scheduleDish) {
                if (empty($scheduleDish->dish)) {
                    throw new \LogicException('Имеются не назначенные блюда в меню.');
                }
                foreach ($scheduleDish->dish->dishProducts as $dishProduct) {
                    if (empty($products[$dishProduct->product_id])) {
                        $product = $dishProduct->product;
                        $product->setNeedCount($dishProduct->weight);
                        $products[$dishProduct->product_id] = $product;
                    } else {
                        $products[$dishProduct->product_id]->setNeedCount($dishProduct->weight);
                    }
                }
            }
        }

        return $products;
    }
}