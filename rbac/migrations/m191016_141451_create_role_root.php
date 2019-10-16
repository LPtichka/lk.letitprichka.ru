<?php

use yii2mod\rbac\migrations\Migration;
use yii2mod\rbac\rules\UserRule;

class m191016_141451_create_role_root extends Migration
{
    public function safeUp()
    {
        $this->createRule('manager', UserRule::class);
        $this->createRole('root', 'Admin has all available permissions.');
        $this->createRole('manager', 'Authenticated user.', 'manager');
    }

    public function safeDown()
    {
        $this->removeRule('manager');
        $this->removeRole('root');
        $this->removeRole('manager');
    }
}