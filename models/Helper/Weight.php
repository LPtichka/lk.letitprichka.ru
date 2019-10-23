<?php
namespace app\models\Helper;

class Weight
{
    const UNIT_KG = 'кг.';
    const UNIT_GR = 'гр.';

    const UNITS = [
        self::UNIT_GR => 1,
        self::UNIT_KG => 1000,
    ];

    private $unit = self::UNIT_GR;

    /**
     * @param string $unit
     * @return Weight
     */
    public function setUnit(string $unit): Weight
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @param float $value
     * @param string $from
     * @return float
     */
    public function convert(float $value, string $from): float
    {
        return $value * (self::UNITS[$from] / self::UNITS[$this->unit]);
    }

    /**
     * @param float $value
     * @param string $to
     * @return string
     */
    public function format(float $value, string $to): string
    {
        return $value * (self::UNITS[$this->unit] / self::UNITS[$to]) . ' ' . $to;
    }
}
