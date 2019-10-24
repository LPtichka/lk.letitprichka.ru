<?php declare(strict_types=1);
namespace app\models\Helper;

class Excel
{
    const AVAILABLE_TYPES = ['Excel2007'];
    const MODEL_PRODUCT = 'product';
    const MODEL_PAYMENT = 'payment';
    const MODEL_EXCEPTION = 'exception';
    const MODEL_USER = 'user';

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
                        if (!empty($value) && (substr((string)$value, 0, 1) === '=') && (strlen((string)$value) > 1)) {
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
     * @return bool
     * @throws \PHPExcel_Exception
     */
    public function prepare(iterable $data, string $model): bool
    {
        $objWorksheet = $this->fileName->getActiveSheet();

        $iRow = 1;
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