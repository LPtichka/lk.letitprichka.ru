<?php declare(strict_types=1);
namespace app\models\Common;

use app\models\Helper\Phone;
use app\models\Repository\Dish;
use app\models\Repository\Franchise;
use yii\base\Model;

/**
 * Class CustomerSheet - Бланк клиента
 *
 * @package app\models\Common
 */
class CustomerSheet extends Model
{
    /** @var string */
    private $fio;
    /** @var string */
    private $phone;
    /** @var string */
    private $address;
    /** @var string */
    private $deliveryTime;
    /** @var int */
    private $cutlery;
    /** @var int */
    private $subscriptionId;
    /** @var string */
    private $subscriptionName;
    /** @var int */
    private $subscriptionDayCount;
    /** @var int */
    private $subscriptionDayBalance;
    /** @var int */
    private $manufacturedAt;
    /** @var string[] */
    private $exceptions;
    /** @var Dish[] */
    private $dishes;
    /** @var Franchise */
    private $franchise;

    /**
     * @param string $fio
     * @return CustomerSheet
     */
    public function setFio(string $fio): CustomerSheet
    {
        $this->fio = $fio;
        return $this;
    }

    /**
     * @param string $phone
     * @return CustomerSheet
     */
    public function setPhone(string $phone): CustomerSheet
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @param string $address
     * @return CustomerSheet
     */
    public function setAddress(string $address): CustomerSheet
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @param string $deliveryTime
     * @return CustomerSheet
     */
    public function setDeliveryTime(string $deliveryTime): CustomerSheet
    {
        $this->deliveryTime = $deliveryTime;
        return $this;
    }

    /**
     * @param string $cutlery
     * @return CustomerSheet
     */
    public function setCutlery(string $cutlery): CustomerSheet
    {
        $this->cutlery = $cutlery;
        return $this;
    }

    /**
     * @param int $subscriptionId
     * @return CustomerSheet
     */
    public function setSubscriptionId(int $subscriptionId): CustomerSheet
    {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }

    /**
     * @param string $subscriptionName
     * @return CustomerSheet
     */
    public function setSubscriptionName(string $subscriptionName): CustomerSheet
    {
        $this->subscriptionName = $subscriptionName;
        return $this;
    }

    /**
     * @param int $subscriptionDayCount
     * @return CustomerSheet
     */
    public function setSubscriptionDayCount(int $subscriptionDayCount): CustomerSheet
    {
        $this->subscriptionDayCount = $subscriptionDayCount;
        return $this;
    }

    /**
     * @param int $subscriptionDayBalance
     * @return CustomerSheet
     */
    public function setSubscriptionDayBalance(int $subscriptionDayBalance): CustomerSheet
    {
        $this->subscriptionDayBalance = $subscriptionDayBalance;
        return $this;
    }

    /**
     * @param int $manufacturedAt
     * @return CustomerSheet
     */
    public function setManufacturedAt(int $manufacturedAt): CustomerSheet
    {
        $this->manufacturedAt = $manufacturedAt;
        return $this;
    }

    /**
     * @param string[] $exceptions
     * @return CustomerSheet
     */
    public function setExceptions(array $exceptions): CustomerSheet
    {
        $this->exceptions = $exceptions;
        return $this;
    }

    /**
     * @param Dish[] $dishes
     * @return CustomerSheet
     */
    public function setDishes(array $dishes): CustomerSheet
    {
        $this->dishes = $dishes;
        return $this;
    }

    /**
     * @param Franchise $franchise
     * @return CustomerSheet
     */
    public function setFranchise(Franchise $franchise): CustomerSheet
    {
        $this->franchise = $franchise;
        return $this;
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
    public function getPhone(): string
    {
        return $this->phone;
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
    public function getDeliveryTime(): string
    {
        return $this->deliveryTime;
    }

    /**
     * @return int
     */
    public function getCutlery(): int
    {
        return $this->cutlery;
    }

    /**
     * @return int
     */
    public function getSubscriptionId(): int
    {
        return $this->subscriptionId;
    }

    /**
     * @return string
     */
    public function getSubscriptionName(): string
    {
        return $this->subscriptionName;
    }

    /**
     * @return int
     */
    public function getSubscriptionDayCount(): int
    {
        return $this->subscriptionDayCount;
    }

    /**
     * @return int
     */
    public function getSubscriptionDayBalance(): int
    {
        return $this->subscriptionDayBalance;
    }

    /**
     * @return int
     */
    public function getManufacturedAt(): int
    {
        return $this->manufacturedAt;
    }

    /**
     * @return string[]
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }

    /**
     * @return Dish[]
     */
    public function getDishes(): array
    {
        return $this->dishes;
    }

    /**
     * @return Franchise
     */
    public function getFranchise(): Franchise
    {
        return $this->franchise;
    }
}