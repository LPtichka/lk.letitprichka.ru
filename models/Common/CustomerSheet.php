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
    /** @var string */
    private $cutlery;
    /** @var int */
    private $subscriptionId;
    /** @var string */
    private $subscriptionName;
    /** @var int */
    private $subscriptionDayCount;
    /** @var string[] */
    private $exceptions;
    /** @var Dish[] */
    private $dishes;
    /** @var Franchise */
    private $franchise;
}