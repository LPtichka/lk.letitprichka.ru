<?php
namespace app\models\Helper;

class Params
{
    /**
     * Получить отформатированное значение
     *
     * @param string $type
     * @param int $index
     * @param array $data
     * @return float|int|string
     */
    public function getArrayParam(string $type, int $index, array $data)
    {
        switch ($type) {
            case 'string':
                return !empty($data[$index]) ? (string)$data[$index] : '';
            case 'int':
            case 'integer':
                return !empty($data[$index]) ? (int)$data[$index] : 0;
            case 'float':
                return !empty($data[$index]) ? (float)$data[$index] : 0;
            default:
                return null;
        }
    }

    /**
     * @param string $param
     * @param $data
     * @param array $params
     * @return false|int|mixed|string
     */
    public function getRealValueParam(string $param, $data = '', $params = [])
    {
        switch ($param) {
            default:
                $value = $data;
        }

        return $value;
    }

    /**
     * @param array $array
     * @return array
     */
    public function getClearedParamsArray(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $subArray = $this->getClearedParamsArray($value);
                if (!empty($subArray)) {
                    $result[$key] = $subArray;
                }
            } elseif (is_numeric($value)) {
                $result[$key] = $value;
            } elseif (is_bool($value)) {
                $result[$key] = $value;
            } elseif (is_string($value) && !empty($value)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}