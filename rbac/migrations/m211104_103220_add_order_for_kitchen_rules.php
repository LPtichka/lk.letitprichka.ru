<?php

use yii2mod\rbac\migrations\Migration;

class m211104_103220_add_order_for_kitchen_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/order/get-order-for-kitchen');
        $this->addChild('manager', '/order/get-order-for-kitchen');
        $this->createPermission('/order/save-order-for-kitchen');
        $this->addChild('manager', '/order/save-order-for-kitchen');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/order/get-order-for-kitchen');
        $this->removePermission('/order/get-order-for-kitchen');
        $this->removeChild('manager', '/order/save-order-for-kitchen');
        $this->removePermission('/order/save-order-for-kitchen');
    }
}