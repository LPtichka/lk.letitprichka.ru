<?php declare(strict_types = 1);
namespace app\models\Common;

use app\models\Helper\Phone;
use app\models\Repository\Dish;
use app\models\Repository\Franchise;
use app\models\Repository\OrderSchedule;
use app\models\Repository\OrderScheduleDish;
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
    private $addressComment;
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
    /** @var OrderScheduleDish[] */
    private $dishes;
    /** @var Franchise */
    private $franchise;
    /** @var bool */
    private $hasBreakfast;
    /** @var bool */
    private $hasDinner;
    /** @var bool */
    private $hasLunch;
    /** @var bool */
    private $hasSupper;

    /**
     * @return bool
     */
    public function isHasBreakfast(): bool
    {
        return $this->hasBreakfast;
    }

    /**
     * @param bool $hasBreakfast
     * @return CustomerSheet
     */
    public function setHasBreakfast(bool $hasBreakfast): CustomerSheet
    {
        $this->hasBreakfast = $hasBreakfast;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasDinner(): bool
    {
        return $this->hasDinner;
    }

    /**
     * @param bool $hasDinner
     * @return CustomerSheet
     */
    public function setHasDinner(bool $hasDinner): CustomerSheet
    {
        $this->hasDinner = $hasDinner;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasLunch(): bool
    {
        return $this->hasLunch;
    }

    /**
     * @param bool $hasLunch
     * @return CustomerSheet
     */
    public function setHasLunch(bool $hasLunch): CustomerSheet
    {
        $this->hasLunch = $hasLunch;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasSupper(): bool
    {
        return $this->hasSupper;
    }

    /**
     * @param bool $hasSupper
     * @return CustomerSheet
     */
    public function setHasSupper(bool $hasSupper): CustomerSheet
    {
        $this->hasSupper = $hasSupper;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddressComment(): ?string
    {
        return $this->addressComment;
    }

    /**
     * @param string $addressComment
     * @return CustomerSheet
     */
    public function setAddressComment(string $addressComment): CustomerSheet
    {
        $this->addressComment = $addressComment;
        return $this;
    }

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
     * @param int $cutlery
     * @return CustomerSheet
     */
    public function setCutlery(int $cutlery): CustomerSheet
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
     * @param OrderScheduleDish[] $dishes
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
        return $this->phone
            ? (new Phone($this->phone))->getHumanView()
            : '';
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
     * @return OrderScheduleDish[]
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

    /**
     * @return int
     */
    public function getTotalKkal(): int
    {
        $result = 0;

        foreach ($this->dishes as $dish) {
            $result += $dish->dish->kkal;
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getTotalFat(): int
    {
        $result = 0;

        foreach ($this->dishes as $dish) {
            $result += $dish->dish->fat;
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getTotalProteins(): int
    {
        $result = 0;

        foreach ($this->dishes as $dish) {
            $result += $dish->dish->proteins;
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getTotalCarbohydrates(): int
    {
        $result = 0;

        foreach ($this->dishes as $dish) {
            $result += $dish->dish->carbohydrates;
        }

        return $result;
    }

    /**
     * @param OrderSchedule[] $orderSchedules
     * @return array
     */
    public function getAllCustomerSheets(array $orderSchedules): array
    {
        foreach ($orderSchedules as $orderSchedule) {
            $order = $orderSchedule->order;
            $dishes         = [];
            $manufacturedAt = 0;
            foreach ($orderSchedule->dishes as $scheduleDish) {
                $dishes[] = $scheduleDish;
                if ($scheduleDish->manufactured_at > $manufacturedAt) {
                    $manufacturedAt = $scheduleDish->manufactured_at;
                }
            }

            $dayBalance = OrderSchedule::find()
                ->where(['status' => OrderSchedule::EDITABLE_STATUSES])
                ->andWhere(['order_id' => $order->id])
                ->count();

            $customerSheet = (new CustomerSheet())
                ->setFio($order->customer->fio)
                ->setPhone($order->customer->phone)
                ->setAddress($order->address->full_address)
                ->setFranchise($order->franchise)
                ->setManufacturedAt($manufacturedAt)
                ->setCutlery($order->cutlery)
                ->setSubscriptionId($order->subscription_id)
                ->setSubscriptionName($order->subscription->name)
                ->setSubscriptionDayCount($order->count)
                ->setSubscriptionDayBalance($dayBalance - 1)
                ->setExceptions($order->getExceptionNames())
                ->setDeliveryTime($orderSchedule->interval)
                ->setDishes($dishes)
                ->setHasBreakfast((bool) $order->subscription->has_breakfast)
                ->setHasDinner((bool) $order->subscription->has_dinner)
                ->setHasLunch((bool) $order->subscription->has_lunch)
                ->setHasSupper((bool) $order->subscription->has_supper);

            $result[] = $customerSheet;
        }

        return $result;
    }
}