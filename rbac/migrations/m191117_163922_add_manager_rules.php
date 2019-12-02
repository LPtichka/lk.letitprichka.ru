<?php

use yii2mod\rbac\migrations\Migration;

class m191117_163922_add_manager_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/address/index');
        $this->addChild('manager', '/address/index');
        $this->createPermission('/address/create');
        $this->addChild('manager', '/address/create');
        $this->createPermission('/address/delete');
        $this->addChild('manager', '/address/delete');
        $this->createPermission('/address/view');
        $this->addChild('manager', '/address/view');
        $this->createPermission('/address/export');
        $this->addChild('manager', '/address/export');
        $this->createPermission('/address/import');
        $this->addChild('manager', '/address/import');
        $this->createPermission('/address/get-by-query');
        $this->addChild('manager', '/address/get-by-query');
        $this->createPermission('/address/get-row');
        $this->addChild('manager', '/address/get-row');

        $this->createPermission('/customer/index');
        $this->addChild('manager', '/customer/index');
        $this->createPermission('/customer/create');
        $this->addChild('manager', '/customer/create');
        $this->createPermission('/customer/delete');
        $this->addChild('manager', '/customer/delete');
        $this->createPermission('/customer/view');
        $this->addChild('manager', '/customer/view');
        $this->createPermission('/customer/export');
        $this->addChild('manager', '/customer/export');
        $this->createPermission('/customer/import');
        $this->addChild('manager', '/customer/import');
        $this->createPermission('/customer/get-by-query');
        $this->addChild('manager', '/customer/get-by-query');

        $this->createPermission('/subscription/index');
        $this->addChild('manager', '/subscription/index');
        $this->createPermission('/subscription/create');
        $this->addChild('manager', '/subscription/create');
        $this->createPermission('/subscription/delete');
        $this->addChild('manager', '/subscription/delete');
        $this->createPermission('/subscription/view');
        $this->addChild('manager', '/subscription/view');
        $this->createPermission('/subscription/export');
        $this->addChild('manager', '/subscription/export');
        $this->createPermission('/subscription/import');
        $this->addChild('manager', '/subscription/import');
        $this->createPermission('/subscription/add-discount');
        $this->addChild('manager', '/subscription/add-discount');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/address/index');
        $this->removePermission('/address/index');
        $this->removeChild('manager', '/address/create');
        $this->removePermission('/address/create');
        $this->removeChild('manager', '/address/delete');
        $this->removePermission('/address/delete');
        $this->removeChild('manager', '/address/view');
        $this->removePermission('/address/view');
        $this->removeChild('manager', '/address/export');
        $this->removePermission('/address/export');
        $this->removeChild('manager', '/address/import');
        $this->removePermission('/address/import');
        $this->removeChild('manager', '/address/get-by-query');
        $this->removePermission('/address/get-by-query');
        $this->removeChild('manager', '/address/get-row');
        $this->removePermission('/address/get-row');

        $this->removeChild('manager', '/customer/index');
        $this->removePermission('/customer/index');
        $this->removeChild('manager', '/customer/create');
        $this->removePermission('/customer/create');
        $this->removeChild('manager', '/customer/delete');
        $this->removePermission('/customer/delete');
        $this->removeChild('manager', '/customer/view');
        $this->removePermission('/customer/view');
        $this->removeChild('manager', '/customer/export');
        $this->removePermission('/customer/export');
        $this->removeChild('manager', '/customer/import');
        $this->removePermission('/customer/import');
        $this->removeChild('manager', '/customer/get-by-query');
        $this->removePermission('/customer/get-by-query');

        $this->removeChild('manager', '/subscription/index');
        $this->removePermission('/subscription/index');
        $this->removeChild('manager', '/subscription/create');
        $this->removePermission('/subscription/create');
        $this->removeChild('manager', '/subscription/delete');
        $this->removePermission('/subscription/delete');
        $this->removeChild('manager', '/subscription/view');
        $this->removePermission('/subscription/view');
        $this->removeChild('manager', '/subscription/export');
        $this->removePermission('/subscription/export');
        $this->removeChild('manager', '/subscription/import');
        $this->removePermission('/subscription/import');
        $this->removeChild('manager', '/subscription/add-discount');
        $this->removePermission('/subscription/add-discount');
    }
}