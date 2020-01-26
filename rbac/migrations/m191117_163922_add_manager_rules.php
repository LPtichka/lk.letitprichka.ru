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
        $this->createPermission('/customer/get-customer-by-fio');
        $this->addChild('manager', '/customer/get-customer-by-fio');

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

        $this->createPermission('/franchise/index');
        $this->addChild('manager', '/franchise/index');
        $this->createPermission('/franchise/create');
        $this->addChild('manager', '/franchise/create');
        $this->createPermission('/franchise/delete');
        $this->addChild('manager', '/franchise/delete');
        $this->createPermission('/franchise/view');
        $this->addChild('manager', '/franchise/view');

        $this->createPermission('/menu/index');
        $this->addChild('manager', '/menu/index');
        $this->createPermission('/menu/create');
        $this->addChild('manager', '/menu/create');
        $this->createPermission('/menu/delete');
        $this->addChild('manager', '/menu/delete');
        $this->createPermission('/menu/view');
        $this->addChild('manager', '/menu/view');
        $this->createPermission('/menu/get-day-blocks');
        $this->addChild('manager', '/menu/get-day-blocks');

        $this->createPermission('/order/index');
        $this->addChild('manager', '/order/index');
        $this->createPermission('/order/create');
        $this->addChild('manager', '/order/create');
        $this->createPermission('/order/delete');
        $this->addChild('manager', '/order/delete');
        $this->createPermission('/order/view');
        $this->addChild('manager', '/order/view');
        $this->createPermission('/order/export');
        $this->addChild('manager', '/order/export');
        $this->createPermission('/order/import');
        $this->addChild('manager', '/order/import');
        $this->createPermission('/order/add-exception');
        $this->addChild('manager', '/order/add-exception');
        $this->createPermission('/order/get-address');
        $this->addChild('manager', '/order/get-address');
        $this->createPermission('/order/get-menu');
        $this->addChild('manager', '/order/get-menu');
        $this->createPermission('/order/set-status');
        $this->addChild('manager', '/order/set-status');
        $this->createPermission('/order/deffer-request');
        $this->addChild('manager', '/order/deffer-request');
        $this->createPermission('/order/deffer');
        $this->addChild('manager', '/order/deffer');

        $this->createPermission('/api/order/create');
        $this->addChild('manager', '/api/order/create');
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
        $this->removeChild('manager', '/customer/get-customer-by-fio');
        $this->removePermission('/customer/get-customer-by-fio');

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

        $this->removeChild('manager', '/franchise/index');
        $this->removePermission('/franchise/index');
        $this->removeChild('manager', '/franchise/create');
        $this->removePermission('/franchise/create');
        $this->removeChild('manager', '/franchise/delete');
        $this->removePermission('/franchise/delete');
        $this->removeChild('manager', '/franchise/view');
        $this->removePermission('/franchise/view');

//        $this->removeChild('manager', '/menu/index');
//        $this->removePermission('/menu/index');
//        $this->removeChild('manager', '/menu/create');
//        $this->removePermission('/menu/create');
//        $this->removeChild('manager', '/menu/delete');
//        $this->removePermission('/menu/delete');
//        $this->removeChild('manager', '/menu/view');
//        $this->removePermission('/menu/view');
//        $this->removeChild('manager', '/menu/get-day-blocks');
//        $this->removePermission('/menu/get-day-blocks');

        $this->removeChild('manager', '/order/index');
        $this->removePermission('/order/index');
        $this->removeChild('manager', '/order/create');
        $this->removePermission('/order/create');
        $this->removeChild('manager', '/order/delete');
        $this->removePermission('/order/delete');
        $this->removeChild('manager', '/order/view');
        $this->removePermission('/order/view');
        $this->removeChild('manager', '/order/export');
        $this->removePermission('/order/export');
        $this->removeChild('manager', '/order/import');
        $this->removePermission('/order/import');
        $this->removeChild('manager', '/order/add-exception');
        $this->removePermission('/order/add-exception');
        $this->removeChild('manager', '/order/get-address');
        $this->removePermission('/order/get-address');
        $this->removeChild('manager', '/order/get-menu');
        $this->removePermission('/order/get-menu');
        $this->removeChild('manager', '/order/set-status');
        $this->removePermission('/order/set-status');
        $this->removeChild('manager', '/order/deffer-request');
        $this->removePermission('/order/deffer-request');
        $this->removeChild('manager', '/order/deffer');
        $this->removePermission('/order/deffer');

        $this->removeChild('manager', '/api/order/create');
        $this->removePermission('/api/order/create');
    }
}