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
class Route extends Model
{
    /** @var string */
    private $fio;
    /** @var string */
    private $address;
    /** @var string */
    private $interval;
    /** @var string */
    private $comment;
    /** @var string */
    private $phone;
    /** @var string */
    private $payment;

    /**
     * @param string $fio
     * @param string $address
     * @param string $phone
     * @param array $config
     */
    public function __construct(string $fio, string $address, string $phone, array $config = [])
    {
        $this->fio = $fio;
        $this->address = $address;
        $this->phone = $phone;

        parent::__construct($config);
    }

    /**
     * @param string $interval
     */
    public function setInterval(string $interval): void
    {
        $this->interval = $interval;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @param string $payment
     */
    public function setPayment(string $payment): void
    {
        $this->payment = $payment;
    }

    /**
     * @return string
     */
    public function getFio(): string
    {
        return $this->fio;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getInterval(): string
    {
        return $this->interval;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment ?? '';
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return (new Phone($this->phone))->getHumanView();
    }

    /**
     * @return string
     */
    public function getPayment(): string
    {
        return $this->payment ?? '';
    }
}