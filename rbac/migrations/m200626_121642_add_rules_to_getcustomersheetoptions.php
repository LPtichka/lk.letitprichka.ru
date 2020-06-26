<?php

use yii2mod\rbac\migrations\Migration;

class m200626_121642_add_rules_to_getcustomersheetoptions extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/order/get-customer-sheet-options');
        $this->addChild('manager', '/order/get-customer-sheet-options');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/order/get-customer-sheet-options');
        $this->removePermission('/order/get-customer-sheet-options');
    }
}