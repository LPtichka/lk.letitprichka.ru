<?php
namespace app\models\Helper;


use app\models\Repository\Order;

class Status
{
    /** @var int */
    private $statusID;

    /**
     * @param int $statusID
     */
    public function __construct(int $statusID)
    {
        $this->statusID = $statusID;
    }

    /**
     * @return string
     */
    public function getStatusKey(): string
    {
        $statuses = [
            Order::STATUS_NEW => 'new',
            Order::STATUS_COMPLETED => 'completed',
            Order::STATUS_DEFERRED => 'deffered',
            Order::STATUS_PROCESSED => 'processed',
            Order::STATUS_CANCELED => 'canceled',
            Order::STATUS_ARCHIVED => 'deleted',
        ];
        return $statuses[$this->statusID];
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return \Yii::t('status', $this->getStatusKey());
    }

    /**
     * @return string
     */
    public function getStatusActionName(): string
    {
        return \Yii::t('status', 'to ' . $this->getStatusKey());
    }

    /**
     * @return bool
     */
    public function isGreenFlowStatus(): bool
    {
        return in_array($this->statusID, [
            Order::STATUS_PROCESSED,
            Order::STATUS_COMPLETED,
        ]);
    }
}