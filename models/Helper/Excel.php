<?php declare(strict_types = 1);
namespace app\models\Helper;

use app\models\Common\Route;
use app\models\Repository\Dish;

class Excel
{
    const AVAILABLE_TYPES = ['Excel2007'];
    const MODEL_PRODUCT = 'product';
    const MODEL_PAYMENT = 'payment';
    const MODEL_DISH = 'dish';
    const MODEL_ADDRESS = 'address';
    const MODEL_EXCEPTION = 'exception';
    const MODEL_USER = 'user';
    const MODEL_CUSTOMER = 'customer';
    const MODEL_ROUTE_SHEET = 'route_sheet';

    const COLUMN_NAMES = [
        1  => 'А',
        2  => 'B',
        3  => 'C',
        4  => 'D',
        5  => 'E',
        6  => 'F',
        7  => 'G',
        8  => 'H',
        9  => 'I',
        10 => 'J',
        11 => 'K',
    ];

    /** @var array */
    private $file;

    /** @var string */
    private $fileType;

    /** @var array */
    private $headerRow = [];

    /** @var string */
    private $url;

    /** @var \PHPExcel */
    private $fileName;

    /** @var array */
    private $borderAll = [
        'borders' => [
            'allborders' => [
                'style' => \PHPExcel_Style_Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ]
        ]
    ];

    /** @var array */
    private $fillGreen = [
        'fill' => [
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'color' => ['rgb' => '39B739'],
        ]
    ];

    /**
     * Загрузка файла
     *
     * @param array $file
     */
    public function load(array $file)
    {
        $this->file = $file;
        try {
            $this->fileType = \PHPExcel_IOFactory::identify($this->file['tmp_name']);
        } catch (\Exception $e) {
            $this->fileType = 'unknown';
        }
    }

    /**
     * Загрузить шаблон файла для дальнейшего наполенения
     *
     * @param string $template
     * @throws \PHPExcel_Reader_Exception
     */
    public function loadFromTemplate(string $template)
    {
        $this->fileName = \PHPExcel_IOFactory::load(dirname(dirname(dirname(__FILE__))) . '/web/' . $template);
    }

    /**
     * Парсинг excel файла
     *
     * @return array
     * @throws \PHPExcel_Exception
     */
    public function parse(): array
    {
        try {
            $objReader   = \PHPExcel_IOFactory::createReader($this->fileType);
            $objPhpExcel = $objReader->load($this->file['tmp_name']);
        } catch (\Exception $e) {
            return [];
        }

        $sheet = $objPhpExcel->getSheet(0);
        $data  = [];

        $nColumn = \PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
        for ($header = 0; $header < $nColumn; $header++) {
            $cell  = $sheet->getCellByColumnAndRow($header, 1);
            $value = $cell->getValue();
            if (!empty($value)) $this->headerRow[] = trim($value);
        }

        for ($i = 2; $i <= $sheet->getHighestRow(); $i++) {
            $nColumn = \PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
            for ($j = 0; $j < $nColumn; $j++) {
                $cell = $sheet->getCellByColumnAndRow($j, $i);
                if (\PHPExcel_Shared_Date::isDateTime($cell)) {
                    try {
                        $value = date('Y-m-d H:i:s', \PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()));
                    } catch (\Exception $e) {
                        $value = '';
                    }

                } else {
                    $value = $cell->getValue();

                    if (!is_numeric($value)) {
                        if (!empty($value) && (substr((string) $value, 0, 1) === '=') && (strlen((string) $value) > 1)) {
                            $value = trim($cell->getOldCalculatedValue());
                        }
                    }
                }
                $data[$i][$j] = $value;
            }
        }
        return $data ?? [];
    }

    /**
     * Проверка файла на соответсвие
     *
     * @return bool
     */
    public function validate(): bool
    {
        return in_array($this->fileType, self::AVAILABLE_TYPES);
    }

    /**
     * Получить массив шапки таблицы
     *
     * @return array
     */
    public function getHeaderRow(): array
    {
        return $this->headerRow;
    }

    /**
     * Загружаем данные в файл
     *
     * @param iterable $data
     * @param string $model
     * @param array $params
     * @return bool
     * @throws \PHPExcel_Exception
     */
    public function prepare(iterable $data, string $model, array $params = []): bool
    {
        if ($model === self::MODEL_DISH) {
            return $this->prepareDish($data);
        } elseif ($model === self::MODEL_ROUTE_SHEET) {
            return $this->prepareRouteSheet($data, $params);
        }

        $objWorksheet = $this->fileName->getActiveSheet();
        $iRow         = 1;
        foreach ($data as $row) {
            $col = 0;

            if ($iRow === 1) {
                $keys = array_keys($row);
                foreach ($keys as $item) {
                    $value = \Yii::t($model, $item);
                    $objWorksheet->getCellByColumnAndRow($col, $iRow)->setValue($value);
                    unset($value);
                    $col++;
                }
                $iRow++;
                $col = 0;
            }

            foreach ($row as $key => $item) {
                $value = (new Params())->getRealValueParam($key, $item, []);
                $objWorksheet->getCellByColumnAndRow($col, $iRow)->setValue($value);
                $cell = $objWorksheet->getCellByColumnAndRow($col, $iRow);
                $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                unset($value);
                $col++;
            }

            $iRow++;
        }

        return true;
    }

    /**
     * @param Dish[]|iterable $dishes
     * @return bool
     * @throws \PHPExcel_Exception
     */
    public function prepareDish(iterable $dishes): bool
    {
        foreach ($dishes as $key => $dish) {
            if (!$key) {
                $objWorksheet = $this->fileName->getActiveSheet();
            } else {
                $objWorksheet = $this->fileName->createSheet();
            }

            $objWorksheet->setTitle($dish->name);
            $this->makeDishTemplatePage($objWorksheet, 1);
            $objWorksheet->getCellByColumnAndRow(0, 1)->setValue($dish->name);
            $productCount = count($dish->dishProducts);

            $objWorksheet->getCellByColumnAndRow(9, 4)->setValue('Технология приготовления:');

            $lastCell = 4 + $productCount;
            $objWorksheet->mergeCells("J5:J" . $lastCell);
            $objWorksheet->mergeCells("J" . ($lastCell + 2) . ":J" . ($lastCell + 3));
            $objWorksheet->getCellByColumnAndRow(0, 1)->setValue($dish->name);

            $objWorksheet->getColumnDimension('J')->setWidth(50);
            $objWorksheet->getCellByColumnAndRow(9, 5)->setValue($dish->process);
            $objWorksheet->getCellByColumnAndRow(9, $lastCell + 1)->setValue('Условия и сроки хранения:');
            $objWorksheet->getCellByColumnAndRow(9, $lastCell + 2)->setValue($dish->storage_condition);

            $weight = 0;
            foreach ($dish->dishProducts as $key => $dishProduct) {
                $objWorksheet->getCellByColumnAndRow(0, $key + 4)->setValue($dishProduct->name);
                $objWorksheet->getCellByColumnAndRow(1, $key + 4)->setValue((new Weight())->convert($dishProduct->brutto, Weight::UNIT_KG));
                $objWorksheet->getCellByColumnAndRow(2, $key + 4)->setValue((new Weight())->convert($dishProduct->netto, Weight::UNIT_KG));
                $objWorksheet->getCellByColumnAndRow(3, $key + 4)->setValue((new Weight())->convert($dishProduct->weight, Weight::UNIT_KG));
                $objWorksheet->getCellByColumnAndRow(4, $key + 4)->setValue((new Weight())->convert($dishProduct->brutto_on_1_kg, Weight::UNIT_KG));
                $objWorksheet->getCellByColumnAndRow(5, $key + 4)->setValue($dishProduct->kkal);
                $objWorksheet->getCellByColumnAndRow(6, $key + 4)->setValue($dishProduct->proteins);
                $objWorksheet->getCellByColumnAndRow(7, $key + 4)->setValue($dishProduct->fat);
                $objWorksheet->getCellByColumnAndRow(8, $key + 4)->setValue($dishProduct->carbohydrates);

                $weight += $dishProduct->weight;
            }

            $objWorksheet->getCellByColumnAndRow(0, $key + 5)->setValue('ВЫХОД на 1 порцию');
            $objWorksheet->getCellByColumnAndRow(3, $key + 5)->setValue((new Weight())->convert($weight, Weight::UNIT_KG));
            $objWorksheet->getCellByColumnAndRow(0, $key + 6)->setValue('ВЫХОД');
            $objWorksheet->getCellByColumnAndRow(1, $key + 6)->setValue('Ккал');
            $objWorksheet->getCellByColumnAndRow(2, $key + 6)->setValue('Белки, г');
            $objWorksheet->getCellByColumnAndRow(3, $key + 6)->setValue('Жиры, г');
            $objWorksheet->getCellByColumnAndRow(4, $key + 6)->setValue('Углеводы, г');
            $objWorksheet->getCellByColumnAndRow(0, $key + 7)->setValue('Информация о пищевой ценности на 1 порцию');
            $objWorksheet->getCellByColumnAndRow(1, $key + 7)->setValue($dish->getKkalFoodValue());
            $objWorksheet->getCellByColumnAndRow(2, $key + 7)->setValue($dish->getProteinsFoodValue());
            $objWorksheet->getCellByColumnAndRow(3, $key + 7)->setValue($dish->getFatFoodValue());
            $objWorksheet->getCellByColumnAndRow(4, $key + 7)->setValue($dish->getCarbohydratesFoodValue());
            $objWorksheet->getCellByColumnAndRow(0, $key + 8)->setValue('% содержания ');

            $objWorksheet->getStyle('A' . 4 . ':J' . ($key + 8))->applyFromArray($this->borderAll);
            $objWorksheet->getStyle('A' . 4 . ':J' . ($key + 8))
                ->getAlignment()
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setWrapText(true);

            $highestRow = $objWorksheet->getHighestRow();
            for ($i = 1; $i <= $highestRow; $i++) {
                $objWorksheet->getRowDimension($i)->setRowHeight(30);
            }
        }
        return true;
    }

    /**
     * @param \PHPExcel_Worksheet $objWorksheet
     * @param int $row
     * @throws \PHPExcel_Exception
     */
    private function makeDishTemplatePage(\PHPExcel_Worksheet $objWorksheet, int $row): void
    {
        $objWorksheet->mergeCells('A' . $row . ':J' . $row);

        $columnWidths = [
            0 => 25,
            1 => 25,
            2 => 25,
            3 => 25,
            4 => 25,
            5 => 10,
            6 => 10,
            7 => 10,
            8 => 10,
            9 => 50,
        ];

        for ($i = 0; $i < 10; $i++) {
            $objWorksheet->getColumnDimensionByColumn($i)->setWidth($columnWidths[$i]);
        }

        $objWorksheet->getStyle('A' . $row . ':J' . ($row + 2))->applyFromArray($this->borderAll);
        $objWorksheet->getStyle('A' . $row . ':J' . ($row + 2))->getFont()->setSize(12)->setBold(true);
        $objWorksheet->getStyle('A' . $row . ':J' . ($row + 2))
            ->getAlignment()
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);

        $objWorksheet->getStyle('A' . $row)->applyFromArray([
            'fill' => [
                'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'f1ee3b']
            ]
        ]);

        $objWorksheet->mergeCells('A' . ($row + 1) . ':A' . ($row + 2));
        $objWorksheet->setCellValue('A' . ($row + 1), 'Наименование сырья, пищевых');

        $objWorksheet->mergeCells('B' . ($row + 1) . ':B' . ($row + 2));
        $objWorksheet->setCellValue('B' . ($row + 1), 'Масса брутто, г.');

        $objWorksheet->mergeCells('C' . ($row + 1) . ':C' . ($row + 2));
        $objWorksheet->setCellValue('C' . ($row + 1), 'Масса нетто, г.');

        $objWorksheet->mergeCells('D' . ($row + 1) . ':D' . ($row + 2));
        $objWorksheet->setCellValue('D' . ($row + 1), 'Масса готового продукта, г.');

        $objWorksheet->mergeCells('E' . ($row + 1) . ':E' . ($row + 2));
        $objWorksheet->setCellValue('E' . ($row + 1), 'Масса брутто (г.) на 1кг готового продукта');

        $objWorksheet->setCellValue('F' . ($row + 1), 'К');
        $objWorksheet->setCellValue('G' . ($row + 1), 'Б');
        $objWorksheet->setCellValue('H' . ($row + 1), 'Ж');
        $objWorksheet->setCellValue('I' . ($row + 1), 'У');

        $objWorksheet->mergeCells('F' . ($row + 2) . ':I' . ($row + 2));
        $objWorksheet->setCellValue('F' . ($row + 2), 'на 100г готового продукта');

        $objWorksheet->mergeCells('J' . ($row + 1) . ':J' . ($row + 2));
        $objWorksheet->setCellValue('J' . ($row + 1), 'Технологический процесс изготовления, оформления и подачи блюда (изделия), условия и сроки реализации');
    }

    /**
     * @param Route[]|iterable $routes
     * @param array $params
     * @return bool
     */
    public function prepareRouteSheet(iterable $routes, array $params): bool
    {
        $objWorksheet = $this->fileName->getActiveSheet();
        $objWorksheet->setTitle('Маршрутный лист');

        $objWorksheet->getCellByColumnAndRow(0, 1)->setValue('Дата');
        $objWorksheet->getCellByColumnAndRow(1, 1)->setValue($params['date'] ?? '');

        $objWorksheet->getCellByColumnAndRow(0, 3)->setValue('Клиент');
        $objWorksheet->getCellByColumnAndRow(1, 3)->setValue('Адрес');
        $objWorksheet->getCellByColumnAndRow(2, 3)->setValue('Время');
        $objWorksheet->getCellByColumnAndRow(3, 3)->setValue('Комментарий');
        $objWorksheet->getCellByColumnAndRow(4, 3)->setValue('Телефон');
        $objWorksheet->getCellByColumnAndRow(5, 3)->setValue('Оплата');

        $i = 4;
        foreach ($routes as $key => $route) {
            $objWorksheet->getCellByColumnAndRow(0, ($i + $key))->setValue($route->getFio());
            $cell = $objWorksheet->getCellByColumnAndRow(0, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(1, ($i + $key))->setValue($route->getAddress());
            $cell = $objWorksheet->getCellByColumnAndRow(1, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(2, ($i + $key))->setValue($route->getInterval());
            $cell = $objWorksheet->getCellByColumnAndRow(2, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(3, ($i + $key))->setValue($route->getComment());
            $cell = $objWorksheet->getCellByColumnAndRow(3, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(4, ($i + $key))->setValue($route->getPhone());
            $cell = $objWorksheet->getCellByColumnAndRow(4, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(5, ($i + $key))->setValue($route->getPayment());
            $cell = $objWorksheet->getCellByColumnAndRow(5, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }

        $objWorksheet->getStyle('A3' . ':F' . (count($routes) + 3))->applyFromArray($this->borderAll);
        $objWorksheet->getStyle('A1:B1')->applyFromArray($this->fillGreen);
        $objWorksheet->getStyle('A3:F3')->applyFromArray($this->fillGreen);

        return true;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Сохраняем файл
     *
     * @param string $name
     * @param string $path
     * @return bool
     */
    public function save(string $name, string $path = ''): bool
    {
        try {
            $objWriter = \PHPExcel_IOFactory::createWriter($this->fileName, 'Excel2007');
            if (!file_exists(dirname(dirname(dirname(__FILE__))) . '/web/' . $path)) {
                mkdir(dirname(dirname(dirname(__FILE__))) . '/web/' . $path, 0777, true);
            }
            $this->url = $path . '/' . $name;
            $objWriter->save(dirname(dirname(dirname(__FILE__))) . '/web/' . $this->url);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}