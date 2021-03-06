<?php
namespace app\widgets\Grid;

class CheckboxColumn extends \yii\grid\CheckboxColumn
{
    public $filter = '<i class="fa fa-search"></i>';

    protected function renderFilterCellContent()
    {
        if (is_string($this->filter)) {
            return $this->filter;
        }
    }
}