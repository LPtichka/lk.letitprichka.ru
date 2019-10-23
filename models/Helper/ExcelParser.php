<?php
namespace app\models\Helper;

class ExcelParser
{
    const MODEL_PRODUCT = 'product';

    /** @var array */
    private $data;

    /** @var string */
    private $model;

    /**
     * ExcelParser constructor.
     *
     * @param array $data
     * @param string $model
     */
    public function __construct(array $data, string $model)
    {
        $this->data = $data;
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function getParsedArray(): array
    {
        switch ($this->model) {
            case self::MODEL_PRODUCT:
                return $this->parseProduct();
            default:
                return [];
        }
    }

    /**
     * @return array
     */
    private function parseProduct(): array
    {
        return [
            'id' => $this->data[0] ?? null,
            'name' => $this->data[1] ?? null,
            'count' => $this->data[2] ?? null,
            'weight' => $this->data[3] ?? null,
        ];
    }
}