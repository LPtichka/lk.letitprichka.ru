<?php

use yii2mod\rbac\migrations\Migration;

class m210906_065836_add_warehouse_accounting_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/product/get-warehouse-accounting');
        $this->addChild('manager', '/product/get-warehouse-accounting');
        $this->createPermission('/product/save-warehouse-accounting');
        $this->addChild('manager', '/product/save-warehouse-accounting');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/product/get-warehouse-accounting');
        $this->removePermission('/product/get-warehouse-accounting');
        $this->removeChild('manager', '/product/save-warehouse-accounting');
        $this->removePermission('/product/save-warehouse-accounting');
    }
}