<?php
namespace app\models\Helper;

class Date
{
    const WEEKDAYS = [
        0 => 'воскресенье',
        1 => 'понедельник',
        2 => 'вторник',
        3 => 'среда',
        4 => 'четверг',
        5 => 'пятница',
        6 => 'суббота',
    ];

    /** @var int */
    private $time;

    /**
     * @param string $date
     */
    public function __construct(string $date)
    {
        $this->time = strtotime($date);
    }

    /**
     * @return string
     */
    public function getWeekdayName(): string
    {
        return self::WEEKDAYS[date('w', $this->time)];
    }

    /**
     * @return string
     */
    public function getFormattedDate(): string
    {
        return date('d.m.Y', $this->time);
    }
}