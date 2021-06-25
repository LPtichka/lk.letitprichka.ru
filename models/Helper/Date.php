<?php

namespace app\models\Helper;

use app\models\Repository\Settings;

class Date
{
    const WEEKDAYS = [
        0 => 'воскресенье',
        1 => 'понедельник',
        2 => 'вторник',
        3 => 'среду',
        4 => 'четверг',
        5 => 'пятницу',
        6 => 'субботу',
    ];

    const SECONDS_IN_DAY = 86400;

    const MONDAY = 'monday';
    const TUESDAY = 'tuesday';
    const WEDNESDAY = 'wednesday';
    const THURSDAY = 'thursday';
    const FRIDAY = 'friday';
    const SATURDAY = 'saturday';
    const SUNDAY = 'sunday';

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

    /**
     * @param int $date
     * @return int
     */
    public function getNextWorkDateTime(int $date): int
    {
        $workDays = (new Settings())->getWorkDays();
        $days = [];
        foreach ($workDays as $day => $isEnabled) {
            switch ($day) {
                case self::MONDAY:
                    $days[1] = $isEnabled;
                    break;
                case self::TUESDAY:
                    $days[2] = $isEnabled;
                    break;
                case self::WEDNESDAY:
                    $days[3] = $isEnabled;
                    break;
                case self::THURSDAY:
                    $days[4] = $isEnabled;
                    break;
                case self::FRIDAY:
                    $days[5] = $isEnabled;
                    break;
                case self::SATURDAY:
                    $days[6] = $isEnabled;
                    break;
                case self::SUNDAY:
                    $days[7] = $isEnabled;
                    break;
            }
        }

        $day = $date + self::SECONDS_IN_DAY;
        $isEnabled = 1;
        while ($isEnabled > 0) {
            $dayOfWeek = date('N', $day);
            if ($days[$dayOfWeek]) {
                $isEnabled = 0;
            } else {
                $day += self::SECONDS_IN_DAY;
            }
        }

        return $day;
    }

    /**
     * @param int $date
     * @return bool
     */
    public function isWorkDay(int $date): bool
    {
        $workDays = (new Settings())->getWorkDays();
        $days = [];
        foreach ($workDays as $day => $isEnabled) {
            switch ($day) {
                case self::MONDAY:
                    $days[1] = $isEnabled;
                    break;
                case self::TUESDAY:
                    $days[2] = $isEnabled;
                    break;
                case self::WEDNESDAY:
                    $days[3] = $isEnabled;
                    break;
                case self::THURSDAY:
                    $days[4] = $isEnabled;
                    break;
                case self::FRIDAY:
                    $days[5] = $isEnabled;
                    break;
                case self::SATURDAY:
                    $days[6] = $isEnabled;
                    break;
                case self::SUNDAY:
                    $days[7] = $isEnabled;
                    break;
            }
        }

        $dayOfWeek = date('N', $date);
        return (bool) $days[$dayOfWeek];
    }
}