<?php

use yii2mod\rbac\migrations\Migration;

class m200324_143729_add_get_dishes_for_inventory_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/order/get-dishes-for-inventory');
        $this->addChild('manager', '/order/get-dishes-for-inventory');
        $this->createPermission('/order/add-dish-for-inventory');
        $this->addChild('manager', '/order/add-dish-for-inventory');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/order/get-dishes-for-inventory');
        $this->removePermission('/order/get-dishes-for-inventory');
        $this->removeChild('manager', '/order/add-dish-for-inventory');
        $this->removePermission('/order/add-dish-for-inventory');
    }
}