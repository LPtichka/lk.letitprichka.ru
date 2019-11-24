<?php
namespace app\models\Helper;

class ExcelParser
{
    const MODEL_PRODUCT = 'product';
    const MODEL_CUSTOMER = 'customer';
    const MODEL_ADDRESS = 'address';

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
        $this->data  = $data;
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
            case self::MODEL_CUSTOMER:
                return $this->parseCustomer();
            case self::MODEL_ADDRESS:
                return $this->parseAddress();
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
            'id'     => $this->data[0] ?? null,
            'name'   => $this->data[1] ?? null,
            'count'  => $this->data[2] ?? null,
            'weight' => $this->data[3] ?? null,
        ];
    }

    /**
     * @return array
     */
    private function parseCustomer(): array
    {
        return [
            'fio'          => $this->data[0] ?? null,
            'email'        => $this->data[1] ?? null,
            'phone'        => $this->data[2] ?? null,
            'full_address' => $this->data[3] ?? null,
            'description'  => $this->data[4] ?? null,
        ];
    }

    /**
     * @return array
     */
    private function parseAddress(): array
    {
        return [
            'customer_id'  => $this->data[0] ?? null,
            'full_address' => $this->data[1] ?? null,
            'description'  => $this->data[2] ?? null,
        ];
    }
}