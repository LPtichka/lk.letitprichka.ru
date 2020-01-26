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
     * @return array
     */
    public function getSelectOptions(): array
    {
        $result = ['' => ''];
        return $result + $this->data;
    }
}