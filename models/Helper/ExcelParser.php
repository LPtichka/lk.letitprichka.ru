<?php
namespace app\models\Helper;

use app\models\Common\Ingestion;
use app\models\Repository\Dish;

class ExcelParser
{
    const MODEL_PRODUCT = 'product';
    const MODEL_CUSTOMER = 'customer';
    const MODEL_ADDRESS = 'address';
    const MODEL_DISH = 'dish';

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
            case self::MODEL_DISH:
                return $this->parseDish();
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

    /**
     * @return array
     */
    private function parseDish(): array
    {
        $data = [];

        foreach ($this->data as $item) {
            if (!empty($item[0])) {
                $data[] = $item;
            }
        }

        if (empty($data)) {
            return $data;
        }

        $ingestion            = new Ingestion();
        $ingestionNameStrings = $data[4][10] ?? '';
        $ingestionNames       = explode(',', $ingestionNameStrings);

        $isBreakfast = false;
        $isDinner    = false;
        $isLunch     = false;
        $isSupper    = false;
        foreach ($ingestionNames as $name) {
            $ingestion->setEatingName($name);
            if ($ingestion->isBreakfast()) {
                $isBreakfast = $ingestion->isBreakfast();
            }
            if ($ingestion->isDinner()) {
                $isDinner = $ingestion->isDinner();
            }
            if ($ingestion->isLunch()) {
                $isLunch = $ingestion->isLunch();
            }
            if ($ingestion->isSupper()) {
                $isSupper = $ingestion->isSupper();
            }
        }

        $dish = [
            'name'               => $data[0][0] ?? null,
            'type'               => (new Dish())->getTypeByTitle($data[2][10] ?? ''),
            'with_garnish'       => mb_strtolower(trim($data[6][10] ?? '')) == 'да',
            'is_breakfast'       => $isBreakfast,
            'is_dinner'          => $isDinner,
            'is_lunch'           => $isLunch,
            'is_supper'          => $isSupper,
            'kkal'               => $data[(count($data) - 2)][1] ?? null,
            'proteins'           => $data[(count($data) - 2)][2] ?? null,
            'fat'                => $data[(count($data) - 2)][3] ?? null,
            'carbohydrates'      => $data[(count($data) - 2)][4] ?? null,
            'process'            => $this->data[4][9] ?? null,
            'storage_conditions' => $data[(count($data) - 2)][9] ?? null,
            'weight'             => $data[(count($data) - 4)][3] ?? null,
            'products'           => [],
        ];

        for ($i = 2; $i < (count($data) - 4); $i++) {
            $dish['products'][] = [
                'name'           => $data[$i][0] ?? null,
                'brutto'         => $data[$i][1] ?? null,
                'netto'          => $data[$i][2] ?? null,
                'weight'         => $data[$i][3] ?? null,
                'weight_on_1_kg' => $data[$i][4] ?? null,
                'kkal'           => $data[$i][5] ?? null,
                'proteins'       => $data[$i][6] ?? null,
                'fat'            => $data[$i][7] ?? null,
                'carbohydrates'  => $data[$i][8] ?? null,
            ];
        }

        return $dish;
    }
}