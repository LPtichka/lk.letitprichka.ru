<?php

use yii2mod\rbac\migrations\Migration;

class m200214_072020_add_procurement_sheet_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/product/get-procurement-sheet');
        $this->addChild('manager', '/product/get-procurement-sheet');
        $this->createPermission('/product/save-procurement-sheet');
        $this->addChild('manager', '/product/save-procurement-sheet');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/product/get-procurement-sheet');
        $this->removePermission('/product/get-procurement-sheet');
        $this->removeChild('manager', '/product/save-procurement-sheet');
        $this->removePermission('/product/save-procurement-sheet');
    }
}