<?php

use yii2mod\rbac\migrations\Migration;

class m210830_121248_add_delete_dish_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/order/delete-dish-for-inventory');
        $this->addChild('manager', '/order/delete-dish-for-inventory');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/order/delete-dish-for-inventory');
        $this->removePermission('/order/delete-dish-for-inventory');
    }
}