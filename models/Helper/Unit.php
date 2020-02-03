<?php
namespace app\models\Helper;

class Unit
{
    const UNIT_KG = 'кг';
    const UNIT_LITER = 'л';
    const UNIT_COUNT = 'шт';

    const UNITS = [
        self::UNIT_COUNT => self::UNIT_COUNT,
        self::UNIT_KG => self::UNIT_KG,
        self::UNIT_LITER => self::UNIT_LITER,
    ];

    /** @var string */
    private $unit;

    /**
     * @param string $unit
     */
    public function __construct(string $unit)
    {
        $this->unit = $unit;
    }

    /**
     * @param int $count
     * @return string
     */
    public function format(int $count): string
    {
        switch ($this->unit) {
            case self::UNIT_LITER:
                return ($count / 1000) . ' ' . self::UNIT_LITER . '.';
            case self::UNIT_KG:
                return ($count / 1000) . ' ' . self::UNIT_KG . '.';
            case self::UNIT_COUNT:
                return $count  . ' ' . self::UNIT_COUNT . '.';
        }
    }
}
