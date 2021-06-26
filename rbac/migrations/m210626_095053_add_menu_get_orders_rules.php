<?php

use yii2mod\rbac\migrations\Migration;

class m210626_095053_add_menu_get_orders_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/menu/get-orders');
        $this->addChild('manager', '/menu/get-orders');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/menu/get-orders');
        $this->removePermission('/menu/get-orders');
    }
}