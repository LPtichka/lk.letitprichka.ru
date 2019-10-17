<?php

use yii2mod\rbac\migrations\Migration;

class m191016_142254_create_manager_permission_main extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/main/index');
        $this->addChild('manager', '/main/index');
        $this->createPermission('/main/logout');
        $this->addChild('manager', '/main/logout');
        $this->createPermission('/payment-type/index');
        $this->addChild('manager', '/payment-type/index');
        $this->createPermission('/*');
        $this->addChild('root', '/*');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/payment-type/index');
        $this->removePermission('/payment-type/index');
        $this->removeChild('manager', '/main/index');
        $this->removePermission('/main/index');
        $this->removeChild('manager', '/main/logout');
        $this->removePermission('/main/logout');
        $this->removeChild('root', '/*');
        $this->removePermission('/*');
    }
}