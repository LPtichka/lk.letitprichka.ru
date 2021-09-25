<?php declare(strict_types = 1);
namespace app\models\Helper;

use app\models\Common\CustomerSheet;
use app\models\Common\Ingestion;
use app\models\Common\MarriageDish;
use app\models\Common\Route;
use app\models\Repository\Dish;
use app\models\Repository\Settings;
use app\models\Repository\Subscription;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use yii\helpers\ArrayHelper;

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
    const MODEL_CUSTOMER_SHEET = 'customer_sheet';
    const MODEL_MARRIAGE_SHEET = 'marriage_sheet';
    const MODEL_PROCUREMENT_SHEET = 'procurement_sheet';

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

    /** @var Spreadsheet */
    private $fileName;

    /** @var array */
    private $borderAll = [
        'borders' => [
            'allborders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ]
        ]
    ];

    /** @var array */
    private $fillGreen = [
        'fill' => [
            'fillType'  => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['argb' => 'FF39B739'],
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
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function loadFromTemplate(string $template): void
    {
        $this->fileName = \PhpOffice\PhpSpreadsheet\IOFactory::load((dirname(dirname(dirname(__FILE__))) . '/web/' . $template));
    }

    /**
     * Парсинг excel файла
     *
     * @return array
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
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function prepare(iterable $data, string $model, array $params = []): bool
    {
        if ($model === self::MODEL_DISH) {
            return $this->prepareDish($data);
        } elseif ($model === self::MODEL_ROUTE_SHEET) {
            return $this->prepareRouteSheet($data, $params);
        } elseif ($model === self::MODEL_MARRIAGE_SHEET) {
            return $this->prepareMarriageSheet($data, $params);
        } elseif ($model === self::MODEL_CUSTOMER_SHEET) {
            return $this->prepareCustomerSheet($data, $params);
        }

        $objWorksheet = $this->fileName->getActiveSheet();
        $iRow         = 1;
        foreach ($data as $row) {
            $col = 1;

            if ($iRow === 1) {
                $keys = array_keys($row);
                foreach ($keys as $item) {
                    $value = \Yii::t($model, $item);
                    $objWorksheet->getCellByColumnAndRow($col, $iRow)->setValue($value);
                    unset($value);
                    $col++;
                }
                $iRow++;
                $col = 1;
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
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function prepareDish(iterable $dishes): bool
    {
        foreach ($dishes as $key => $dish) {
            if (!$key) {
                $objWorksheet = $this->fileName->getActiveSheet();
            } else {
                $objWorksheet = $this->fileName->createSheet();
            }

            $title = mb_substr($dish->name, 0, 30);
            $title = preg_replace('/[^ a-zа-яё\d]/ui', '', $title);
            $objWorksheet->setTitle($title);
            $this->makeDishTemplatePage($objWorksheet, 1);
            $objWorksheet->getCellByColumnAndRow(1, 1)->setValue($dish->name);
            $productCount = count($dish->dishProducts);

            $objWorksheet->getCellByColumnAndRow(10, 4)->setValue('Технология приготовления:');

            $lastCell = 4 + ($productCount ? $productCount : 1);
            $objWorksheet->mergeCells("J5:J" . $lastCell);
            $objWorksheet->mergeCells("J" . ($lastCell + 2) . ":J" . ($lastCell + 3));
            $objWorksheet->getCellByColumnAndRow(1, 1)->setValue($dish->name);

            $objWorksheet->getColumnDimension('J')->setWidth(50);
            $objWorksheet->getCellByColumnAndRow(10, 5)->setValue($dish->process);
            $objWorksheet->getCellByColumnAndRow(10, $lastCell + 1)->setValue('Условия и сроки хранения:');
            $objWorksheet->getCellByColumnAndRow(10, $lastCell + 2)->setValue($dish->storage_condition);

            $weight = 0;
            foreach ($dish->dishProducts as $key => $dishProduct) {
                $objWorksheet->getCellByColumnAndRow(1, $key + 4)->setValue($dishProduct->name);
                $objWorksheet->getCellByColumnAndRow(2, $key + 4)->setValue((new Weight())->convert($dishProduct->brutto, Weight::UNIT_GR));
                $objWorksheet->getCellByColumnAndRow(3, $key + 4)->setValue((new Weight())->convert($dishProduct->netto, Weight::UNIT_GR));
                $objWorksheet->getCellByColumnAndRow(4, $key + 4)->setValue((new Weight())->convert($dishProduct->weight, Weight::UNIT_GR));
                $weight += $dishProduct->weight;
            }

            $objWorksheet->getCellByColumnAndRow(1, $key + 5)->setValue('ВЫХОД на 1 порцию');
            $objWorksheet->getCellByColumnAndRow(4, $key + 5)->setValue((new Weight())->convert($weight, Weight::UNIT_GR));
            $objWorksheet->getCellByColumnAndRow(1, $key + 6)->setValue('ВЫХОД');
            $objWorksheet->getCellByColumnAndRow(2, $key + 6)->setValue('Ккал');
            $objWorksheet->getCellByColumnAndRow(3, $key + 6)->setValue('Белки, г');
            $objWorksheet->getCellByColumnAndRow(4, $key + 6)->setValue('Жиры, г');
            $objWorksheet->getCellByColumnAndRow(5, $key + 6)->setValue('Углеводы, г');
            $objWorksheet->getCellByColumnAndRow(1, $key + 7)->setValue('Информация о пищевой ценности на 1 порцию');
            $objWorksheet->getCellByColumnAndRow(2, $key + 7)->setValue($dish->kkal);
            $objWorksheet->getCellByColumnAndRow(3, $key + 7)->setValue($dish->proteins);
            $objWorksheet->getCellByColumnAndRow(4, $key + 7)->setValue($dish->fat);
            $objWorksheet->getCellByColumnAndRow(5, $key + 7)->setValue($dish->carbohydrates);
            $objWorksheet->getCellByColumnAndRow(1, $key + 8)->setValue('% содержания ');

            $objWorksheet->getStyle('A' . 4 . ':J' . ($key + 8))->applyFromArray($this->borderAll);
            $objWorksheet->getStyle('A' . 4 . ':J' . ($key + 8))
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setWrapText(true);
            $objWorksheet->getStyle('A1')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FFFFFF00');
            $objWorksheet->getStyle('A' . 1 . ':J' . ($key + 8))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $highestRow = $objWorksheet->getHighestRow();
            for ($i = 1; $i <= $highestRow; $i++) {
                $objWorksheet->getRowDimension($i)->setRowHeight(30);
            }
        }
        return true;
    }

    /**
     * @param Worksheet $objWorksheet
     * @param int $row
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function makeDishTemplatePage(Worksheet $objWorksheet, int $row): void
    {
        $objWorksheet->mergeCells('A' . $row . ':J' . $row);

        $columnWidths = [
            1 => 25,
            2 => 25,
            3 => 25,
            4 => 25,
            5 => 25,
            6 => 10,
            7 => 10,
            8 => 10,
            9 => 10,
            10 => 50,
        ];

        for ($i = 1; $i <= 10; $i++) {
            $objWorksheet->getColumnDimensionByColumn($i)->setWidth($columnWidths[$i]);
        }

        $objWorksheet->getStyle('A' . $row . ':J' . ($row + 2))->applyFromArray($this->borderAll);
        $objWorksheet->getStyle('A' . $row . ':J' . ($row + 2))->getFont()->setSize(12)->setBold(true);
        $objWorksheet->getStyle('A' . $row . ':J' . ($row + 2))
            ->getAlignment()
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);

        $objWorksheet->getStyle('A' . $row)->applyFromArray([
            'fill' => [
                'type'  => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
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
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function prepareRouteSheet(iterable $routes, array $params): bool
    {
        $objWorksheet = $this->fileName->getActiveSheet();
        $objWorksheet->setTitle('Маршрутный лист');

        $objWorksheet->getCellByColumnAndRow(1, 1)->setValue('Дата');
        $objWorksheet->getCellByColumnAndRow(2, 1)->setValue($params['date'] ?? '');

        $objWorksheet->getCellByColumnAndRow(1, 3)->setValue('Клиент');
        $objWorksheet->getCellByColumnAndRow(2, 3)->setValue('Адрес');
        $objWorksheet->getCellByColumnAndRow(3, 3)->setValue('Время');
        $objWorksheet->getCellByColumnAndRow(4, 3)->setValue('Комментарий');
        $objWorksheet->getCellByColumnAndRow(5, 3)->setValue('Телефон');
        $objWorksheet->getCellByColumnAndRow(6, 3)->setValue('Оплата');

        $i = 4;
        foreach ($routes as $key => $route) {
            $objWorksheet->getCellByColumnAndRow(1, ($i + $key))->setValue($route->getFio());
            $cell = $objWorksheet->getCellByColumnAndRow(0, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(2, ($i + $key))->setValue($route->getAddress());
            $cell = $objWorksheet->getCellByColumnAndRow(1, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(3, ($i + $key))->setValue($route->getInterval());
            $cell = $objWorksheet->getCellByColumnAndRow(2, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(4, ($i + $key))->setValue($route->getComment());
            $cell = $objWorksheet->getCellByColumnAndRow(3, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(5, ($i + $key))->setValue($route->getPhone());
            $cell = $objWorksheet->getCellByColumnAndRow(4, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(6, ($i + $key))->setValue($route->getPayment());
            $cell = $objWorksheet->getCellByColumnAndRow(5, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }

        $objWorksheet->getStyle('A3' . ':F' . (count($routes) + 3))->applyFromArray($this->borderAll);
        $objWorksheet->getStyle('A1:B1')->applyFromArray($this->fillGreen);
        $objWorksheet->getStyle('A3:F3')->applyFromArray($this->fillGreen);

        return true;
    }

    /**
     * @param MarriageDish[]|iterable $marriageDish
     * @param array $params
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function prepareMarriageSheet(iterable $marriageDish, array $params): bool
    {
        $objWorksheet = $this->fileName->getActiveSheet();
        $objWorksheet->setTitle('Лист бракеражной комиссии');

        $objWorksheet->getCellByColumnAndRow(1, 1)->setValue('дата приготовления блюда');
        $objWorksheet->getCellByColumnAndRow(2, 1)->setValue('время бракеража');
        $objWorksheet->getCellByColumnAndRow(3, 1)->setValue('тип');
        $objWorksheet->getCellByColumnAndRow(4, 1)->setValue('наименование изделия');
        $objWorksheet->getCellByColumnAndRow(5, 1)->setValue('выход (г.)');
        $objWorksheet->getCellByColumnAndRow(6, 1)->setValue('результат органолептической оценки и степени готовности');
        $objWorksheet->getCellByColumnAndRow(7, 1)->setValue('качество');
        $objWorksheet->getCellByColumnAndRow(8, 1)->setValue('разрешение к реализации');
        $objWorksheet->getCellByColumnAndRow(9, 1)->setValue('подписи членов бракеражной комиссии');

        $objWorksheet->getColumnDimension('H')->setAutoSize(true);
        $objWorksheet->getColumnDimension('I')->setAutoSize(true);
        $objWorksheet->getColumnDimension('G')->setAutoSize(true);

        $date = $params['date'] ?? '';
        $time = $params['time'] ?? date("H:i", time());

        $i = 2;
        foreach ($marriageDish as $key => $dish) {
            $objWorksheet->getCellByColumnAndRow(1, ($i + $key))->setValue($date);
            $cell = $objWorksheet->getCellByColumnAndRow(0, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(2, ($i + $key))->setValue($time);
            $cell = $objWorksheet->getCellByColumnAndRow(0, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(3, ($i + $key))->setValue($dish->getType());
            $cell = $objWorksheet->getCellByColumnAndRow(1, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(4, ($i + $key))->setValue($dish->getDishName());
            $cell = $objWorksheet->getCellByColumnAndRow(2, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(5, ($i + $key))->setValue($dish->getWeight());
            $cell = $objWorksheet->getCellByColumnAndRow(2, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(6, ($i + $key))->setValue($dish->getRating());
            $cell = $objWorksheet->getCellByColumnAndRow(3, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(7, ($i + $key))->setValue($dish->getQuality());
            $cell = $objWorksheet->getCellByColumnAndRow(4, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(8, ($i + $key))->setValue($dish->getResult());
            $cell = $objWorksheet->getCellByColumnAndRow(4, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

            $objWorksheet->getCellByColumnAndRow(9, ($i + $key))->setValue($dish->getSignature());
            $cell = $objWorksheet->getCellByColumnAndRow(5, ($i + $key));
            $objWorksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }

        $objWorksheet->getStyle('A1:I1')->applyFromArray($this->fillGreen);

        return true;
    }

    /**
     * @param CustomerSheet[] $customerSheets
     * @param array $params
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function prepareCustomerSheet(iterable $customerSheets, array $params): bool
    {
        foreach ($customerSheets as $key => $sheet) {
            if (!$key) {
                $objWorksheet = $this->fileName->getActiveSheet();
            } else {
                $objWorksheet = $this->fileName->createSheet();
            }

            $columnWidths = [
                1 => 25,
                2 => 40,
                3 => 40,
                4 => 10,
                5 => 10,
                6 => 10,
                7 => 10,
                8 => 40,
            ];

            for ($i = 1; $i <= 8; $i++) {
                $objWorksheet->getColumnDimensionByColumn($i)->setWidth($columnWidths[$i]);
            }

            $objWorksheet->setTitle($sheet->getFio());

            $objWorksheet->getCellByColumnAndRow(1, 1)->setValue('Покупатель:');
            $objWorksheet->getCellByColumnAndRow(2, 1)->setValue($sheet->getFio());
            $objWorksheet->getCellByColumnAndRow(3, 1)->setValue($sheet->getPhone());

            $objWorksheet->getCellByColumnAndRow(1, 2)->setValue('Тип рациона:');
            $objWorksheet->getCellByColumnAndRow(2, 2)->setValue($sheet->getSubscriptionName());

            $objWorksheet->getCellByColumnAndRow(1, 3)->setValue('Исключения:');
            $objWorksheet->getCellByColumnAndRow(2, 3)->setValue(!empty($sheet->getExceptions()) ? implode(', ', $sheet->getExceptions()) : 'Нет');

            $objWorksheet->getCellByColumnAndRow(1, 4)->setValue('Адрес:');
            $objWorksheet->getCellByColumnAndRow(2, 4)->setValue($sheet->getAddress());
            $objWorksheet->getCellByColumnAndRow(3, 4)->setValue($sheet->getAddressComment() ?? '');

            $objWorksheet->getCellByColumnAndRow(1, 5)->setValue('Время доставки:');
            $objWorksheet->getCellByColumnAndRow(2, 5)->setValue($sheet->getDeliveryTime());

            $objWorksheet->getCellByColumnAndRow(1, 6)->setValue('Подписка (дней):');
            $objWorksheet->getCellByColumnAndRow(2, 6)->setValue($sheet->getSubscriptionDayCount());
            $objWorksheet->getCellByColumnAndRow(3, 6)->setValue('Остаток дней по подписке:');
            $objWorksheet->getCellByColumnAndRow(4, 6)->setValue($sheet->getSubscriptionDayBalance());

            $objWorksheet->getCellByColumnAndRow(1, 7)->setValue('Приборы:');
            $culteryText = $sheet->getCutlery() ? 'Да' : 'Нет (пожалуйста, сообщите нам, если Вам понадобятся приборы)';
            $objWorksheet->getCellByColumnAndRow(2, 7)->setValue($culteryText);

            $objWorksheet->getCellByColumnAndRow(1, 9)->setValue('Изготовитель:');
            $objWorksheet->getCellByColumnAndRow(2, 9)->setValue($sheet->getFranchise()->name);
            $objWorksheet->getCellByColumnAndRow(3, 9)->setValue((new Phone($sheet->getFranchise()->phone))->getHumanView());

            $objWorksheet->getCellByColumnAndRow(1, 10)->setValue('Изготовлено и упаковано:');
            $objWorksheet->getCellByColumnAndRow(2, 10)->setValue(!empty($sheet->getManufacturedAt()) ? date('H:i', $sheet->getManufacturedAt()): "06:00");
            $objWorksheet->getCellByColumnAndRow(3, 10)->setValue(!empty($sheet->getManufacturedAt()) ? date('d.m.Y', $sheet->getManufacturedAt()) : date('d.m.Y', strtotime($sheet->getDate())));

            $settings = ArrayHelper::map(Settings::find()->asArray()->all(), 'name', 'value');

            $objWorksheet->getCellByColumnAndRow(1, 11)->setValue('Условия хранения:');
            $objWorksheet->getCellByColumnAndRow(2, 11)->setValue($settings['storage_conditions']);
            $objWorksheet->getCellByColumnAndRow(3, 11)->setValue($sheet->getFranchise()->sertificat_info ?? $settings['certificate']);

            $objWorksheet->mergeCells('A13:D13');

            $objWorksheet->getCellByColumnAndRow(1, 13)->setValue("ВАЖНО: Мы используем органическую 100% био-разлагаемую упаковку: ланчбоксы выполнены из сахарного тростника. Они могут незначительно намокать, впитывая влагу блюда, что не сказывается на его качестве - надеемся на Ваше понимание, ведь так мы вместе минимизируем количество пластиковых отходов. Также мы будем признательны, если Вы проинформируете нас в случае, если Вам не нужны столовые приборы - так мы вместе сократим количество отходов.");
            $objWorksheet->getRowDimension(13)->setRowHeight(70);
            $objWorksheet->getCellByColumnAndRow(1, 14)->setValue('');
            $objWorksheet->getCellByColumnAndRow(2, 14)->setValue('Блюдо');
            $objWorksheet->getCellByColumnAndRow(3, 14)->setValue('Состав, выход (гр)');
            $objWorksheet->getCellByColumnAndRow(4, 14)->setValue('К');
            $objWorksheet->getCellByColumnAndRow(5, 14)->setValue('Б');
            $objWorksheet->getCellByColumnAndRow(6, 14)->setValue('Ж');
            $objWorksheet->getCellByColumnAndRow(7, 14)->setValue('У');
            $objWorksheet->getCellByColumnAndRow(8, 14)->setValue('Комментарии');

            $lastID = 15;
            if ($sheet->getSubscriptionId() === Subscription::NO_SUBSCRIPTION_ID) {
                foreach ($sheet->getDishes() as $dish) {
                    $this->setDishToCustomerSheet($objWorksheet, $dish->dish, $lastID, '');
                    $lastID++;
                }
            } else {
                if ($sheet->isHasBreakfast()) {
                    foreach ($sheet->getDishes() as $dish) {
                        if ($dish->ingestion_type == Ingestion::BREAKFAST) {
                            $this->setDishToCustomerSheet($objWorksheet, $dish->dish, $lastID, Dish::INGESTION_TYPE_BREAKFAST_NAME);
                            $lastID++;
                        }
                    }
                }
                if ($sheet->isHasDinner()) {
                    foreach ($sheet->getDishes() as $dish) {
                        if ($dish->ingestion_type == Ingestion::DINNER) {
                            $this->setDishToCustomerSheet($objWorksheet, $dish->dish, $lastID, Dish::INGESTION_TYPE_DINNER_NAME);
                            $lastID++;
                            if ($dish->garnish_id) {
                                $this->setDishToCustomerSheet($objWorksheet, $dish->garnish, $lastID, Dish::INGESTION_TYPE_DINNER_NAME);
                                $lastID++;
                            }
                        }
                    }
                }
                if ($sheet->isHasLunch()) {
                    foreach ($sheet->getDishes() as $dish) {
                        if ($dish->ingestion_type == Ingestion::LUNCH) {
                            $this->setDishToCustomerSheet($objWorksheet, $dish->dish, $lastID, Dish::INGESTION_TYPE_LUNCH_NAME);
                            $lastID++;
                        }
                    }
                }
                if ($sheet->isHasSupper()) {
                    foreach ($sheet->getDishes() as $dish) {
                        if ($dish->ingestion_type == Ingestion::SUPPER) {
                            $this->setDishToCustomerSheet($objWorksheet, $dish->dish, $lastID, Dish::INGESTION_TYPE_SUPPER_NAME);
                            $lastID++;
                            if ($dish->garnish_id) {
                                $this->setDishToCustomerSheet($objWorksheet, $dish->garnish, $lastID, Dish::INGESTION_TYPE_SUPPER_NAME);
                                $lastID++;
                            }
                        }
                    }
                }
            }

            $objWorksheet->getStyle('A' . 1 . ':H' . $lastID)
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setWrapText(true);

            $objWorksheet->getStyle('A13:D13')
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP)
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setWrapText(true);

            $objWorksheet->getStyle('A' . 1 . ':D' . 11)
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $objWorksheet->getStyle('A' . 14 . ':H' . 14)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FFD2D2D2');

            $objWorksheet->getRowDimension(8)->setRowHeight(10);
            $objWorksheet->getStyle('A' . 8 . ':D' . 8)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FFD2D2D2');

            $objWorksheet->getStyle('A' . 14 . ':H' . $lastID)
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $objWorksheet->getCellByColumnAndRow(4, $lastID)->setValue($sheet->getTotalKkal());
            $objWorksheet->getCellByColumnAndRow(5, $lastID)->setValue($sheet->getTotalProteins());
            $objWorksheet->getCellByColumnAndRow(6, $lastID)->setValue($sheet->getTotalFat());
            $objWorksheet->getCellByColumnAndRow(7, $lastID)->setValue($sheet->getTotalCarbohydrates());

        }
        return true;
    }

    /**
     * @param Worksheet $objWorksheet
     * @param Dish $dish
     * @param int $lastID
     * @param string $ingestionName
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function setDishToCustomerSheet($objWorksheet, $dish, $lastID, $ingestionName): void
    {
        $objWorksheet->getCellByColumnAndRow(1, $lastID)->setValue(\Yii::t('dish', $ingestionName));
        $objWorksheet->getCellByColumnAndRow(2, $lastID)->setValue($dish->name);
        $objWorksheet->getCellByColumnAndRow(3, $lastID)->setValue(implode(', ', $dish->getComposition()) . ', ' . $dish->weight . 'г.');
        $objWorksheet->getCellByColumnAndRow(4, $lastID)->setValue($dish->kkal);
        $objWorksheet->getCellByColumnAndRow(5, $lastID)->setValue($dish->proteins);
        $objWorksheet->getCellByColumnAndRow(6, $lastID)->setValue($dish->fat);
        $objWorksheet->getCellByColumnAndRow(7, $lastID)->setValue($dish->carbohydrates);
        $objWorksheet->getCellByColumnAndRow(8, $lastID)->setValue($dish->comment);
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
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function save(string $name, string $path = ''): bool
    {
        try {
            $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->fileName, 'Xlsx');
            if (!file_exists(dirname(dirname(dirname(__FILE__))) . '/web/' . $path)) {
                mkdir(dirname(dirname(dirname(__FILE__))) . '/web/' . $path, 0777, true);
            }
            $this->url = $path . '/' . $name;
            $objWriter->save(dirname(dirname(dirname(__FILE__))) . '/web/' . $this->url);

            return true;
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $exception) {
            \Yii::error([
                'Не удалось создать файл выгрузки',
                $exception
            ]);
        }  catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $exception) {
            \Yii::error([
                'Не удалось сохранить файл выгрузки',
                $exception
            ]);
        }

        return false;
    }
}