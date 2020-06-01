<?php
namespace app\models\Helper;


class Arrays
{
    /** @var array */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $placeholder
     * @return array
     */
    public function getSelectOptions(string $placeholder = ''): array
    {
        $result = ['' => $placeholder];
        return $result + $this->data;
    }
}