<?php

use yii2mod\rbac\migrations\Migration;

class m211106_131456_add_user_delete_rule extends Migration
{
    public function safeUp()
    {
        $this->addChild('manager', '/user/delete');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/user/delete');
    }
}