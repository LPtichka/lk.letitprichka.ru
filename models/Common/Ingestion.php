<?php declare(strict_types=1);
namespace app\models\Common;

use app\models\Repository\Dish;
use yii\base\Model;

/**
 * Class Ingestion - Прием пищи
 *
 * @package app\models\Common
 */
class Ingestion extends Model
{
    const BREAKFAST = 1;
    const LUNCH = 3;
    const DINNER = 2;
    const SUPPER = 4;

    const FIRST = 1;
    const SECOND = 2;

    const BREAKFAST_NAME = 'завтрак';
    const LUNCH_NAME = 'перекус';
    const DINNER_NAME = 'обед';
    const SUPPER_NAME = 'ужин';

    const DINNER_FIRST_NAME = 'первое';
    const DINNER_SECOND_NAME = 'второе';

    /** @var Dish */
    private $dish;

    /** @var string */
    private $eatingName;

    /** @var int */
    private $eatingType;

    /**
     * @param Dish $dish
     */
    public function setDish(Dish $dish): void
    {
        $this->dish = $dish;
    }

    /**
     * @param string $eatingName
     * @return Ingestion
     */
    public function setEatingName(string $eatingName): Ingestion
    {
        $this->eatingName = mb_strtolower(trim($eatingName));
        switch ($this->eatingName) {
            case 'завтрак':
                $this->eatingType = self::BREAKFAST;
                break;
            case 'обед':
                $this->eatingType = self::DINNER;
                break;
            case 'перекус':
            case 'ланч':
                $this->eatingType = self::LUNCH;
                break;
            case 'ужин':
                $this->eatingType = self::SUPPER;
                break;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isBreakfast(): bool
    {
        return $this->eatingType === self::BREAKFAST;
    }

    /**
     * @return bool
     */
    public function isLunch(): bool
    {
        return $this->eatingType === self::LUNCH;
    }

    /**
     * @return bool
     */
    public function isDinner(): bool
    {
        return $this->eatingType === self::DINNER;
    }

    /**
     * @return bool
     */
    public function isSupper(): bool
    {
        return $this->eatingType === self::SUPPER;
    }

    /**
     * @param int $ingestionType
     * @param int $type
     * @return string
     */
    public function getIngestionName(int $ingestionType, int $type = 0): string
    {
        $name = '';

        switch ($ingestionType) {
            case self::BREAKFAST:
                $name = self::BREAKFAST_NAME;
                break;
            case self::DINNER:
                $name = self::DINNER_NAME;
                break;
            case self::LUNCH:
                $name = self::LUNCH_NAME;
                break;
            case self::SUPPER:
                $name = self::SUPPER_NAME;
                break;
        }

        if (!empty($type)) {
            switch ($type) {
                case self::FIRST:
                    $name .= ' ' . self::DINNER_FIRST_NAME;
                    break;
                case self::SECOND:
                    $name .= ' ' . self::DINNER_SECOND_NAME;
                    break;
            }
        }

        return $name;
    }
}