<?php

use yii2mod\rbac\migrations\Migration;

class m200621_161050_add_report_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/report/index');
        $this->addChild('manager', '/report/index');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/report/index');
        $this->removePermission('/report/index');
    }
}