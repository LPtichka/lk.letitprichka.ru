<?php

use yii2mod\rbac\migrations\Migration;

class m200208_162359_add_customer_add_exception_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/customer/add-exception');
        $this->addChild('manager', '/customer/add-exception');
        $this->createPermission('/order/get-exception');
        $this->addChild('manager', '/order/get-exception');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/customer/add-exception');
        $this->removePermission('/customer/add-exception');
        $this->removeChild('manager', '/order/get-exception');
        $this->removePermission('/order/get-exception');
    }
}