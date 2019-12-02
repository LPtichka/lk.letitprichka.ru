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
    const LUNCH = 2;
    const DINNER = 3;
    const SUPPER = 4;

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
}