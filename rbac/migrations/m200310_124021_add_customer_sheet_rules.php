<?php

use yii2mod\rbac\migrations\Migration;

class m200310_124021_add_customer_sheet_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/order/get-customer-sheet');
        $this->addChild('manager', '/order/get-customer-sheet');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/order/get-customer-sheet');
        $this->removePermission('/order/get-customer-sheet');
    }
}