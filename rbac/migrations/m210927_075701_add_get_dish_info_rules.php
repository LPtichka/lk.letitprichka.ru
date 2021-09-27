<?php

use yii2mod\rbac\migrations\Migration;

class m210927_075701_add_get_dish_info_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/dish/get-info');
        $this->addChild('manager', '/dish/get-info');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/dish/get-info');
        $this->removePermission('/dish/get-info');
    }
}