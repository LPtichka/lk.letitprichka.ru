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
        $result = ['' => \Yii::t('app', 'Choose element')];
        return $result + $this->data;
    }
}