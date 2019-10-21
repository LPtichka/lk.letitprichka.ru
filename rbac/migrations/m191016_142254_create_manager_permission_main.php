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
        $this->createPermission('/payment-type/create');
        $this->addChild('manager', '/payment-type/create');
        $this->createPermission('/payment-type/delete');
        $this->addChild('manager', '/payment-type/delete');
        $this->createPermission('/payment-type/view');
        $this->addChild('manager', '/payment-type/view');
        $this->createPermission('/payment-type/export');
        $this->addChild('manager', '/payment-type/export');
        $this->createPermission('/payment-type/import');
        $this->addChild('manager', '/payment-type/import');
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
        $this->removeChild('manager', '/payment-type/create');
        $this->removePermission('/payment-type/create');
        $this->removeChild('manager', '/payment-type/delete');
        $this->removePermission('/payment-type/delete');
        $this->removeChild('manager', '/payment-type/view');
        $this->removePermission('/payment-type/view');
        $this->removeChild('manager', '/payment-type/export');
        $this->removePermission('/payment-type/export');
        $this->removeChild('manager', '/payment-type/import');
        $this->removePermission('/payment-type/import');
        $this->removeChild('root', '/*');
        $this->removePermission('/*');
    }
}