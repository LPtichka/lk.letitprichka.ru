<?php
namespace app\widgets\Grid;

class GridView extends \yii\grid\GridView
{
    public $tableOptions = ['class' => 'table'];

    public $dataColumnClass = 'app\widgets\Grid\DataColumn';

    public $emptyTextOptions = ['class' => 'empty text-center'];

    public $pager = ['class' => 'app\widgets\LinkPager'];

    public $layout = "<div class='box-body'>{items}</div>\n<div class='box-footer'>{pager}</div>";

    public $summaryOptions = ['class' => 'summary pull-right'];
}