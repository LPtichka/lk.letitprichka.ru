<?php

use yii2mod\rbac\migrations\Migration;

class m200902_063935_add_settings_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/setting/index');
        $this->addChild('manager', '/setting/index');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/setting/index');
        $this->removePermission('/setting/index');
    }
}