<?php

use yii2mod\rbac\migrations\Migration;

class m200523_085810_add_get_menu_additionals_rule extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/menu/get-menu-additionals');
        $this->addChild('manager', '/menu/get-menu-additionals');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/menu/get-menu-additionals');
        $this->removePermission('/menu/get-menu-additionals');
    }
}