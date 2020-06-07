<?php declare(strict_types=1);
namespace app\models\Common;

use app\models\Helper\Phone;
use app\models\Repository\Dish;
use yii\base\Model;

/**
 * Class Route - Маршрут
 *
 * @package app\models\Common
 */
class MarriageDish extends Model
{
    /** @var string */
    private $time;
    /** @var string */
    private $type;
    /** @var string */
    private $dishName;
    /** @var string */
    private $rating;
    /** @var string */
    private $result;
    /** @var int */
    private $weight;
    /** @var string */
    private $signature;

    public function __construct(
        string $time,
        string $type,
        string $dishName,
        array $config = []
    ) {
        $this->time = $time;
        $this->type = $type;
        $this->dishName = $dishName;
        parent::__construct($config);
    }

    /**
     * @param string $rating
     */
    public function setRating(string $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @param string $result
     */
    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    /**
     * @param string $signature
     */
    public function setSignature(string $signature): void
    {
        $this->signature = $signature;
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getDishName(): string
    {
        return $this->dishName;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight ?? 0;
    }

    /**
     * @return string
     */
    public function getRating(): string
    {
        return (string) $this->rating;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return (string) $this->result;
    }

    /**
     * @return string
     */
    public function getQuality(): string
    {
        return '+';
    }

    /**
     * @return string
     */
    public function getSignature(): string
    {
        return (string) $this->signature;
    }

    /**
     * @param int $weight
     */
    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }
}