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

        $this->createPermission('/product/index');
        $this->addChild('manager', '/product/index');
        $this->createPermission('/product/create');
        $this->addChild('manager', '/product/create');
        $this->createPermission('/product/view');
        $this->addChild('manager', '/product/view');
        $this->createPermission('/product/import');
        $this->addChild('manager', '/product/import');
        $this->createPermission('/product/export');
        $this->addChild('manager', '/product/export');
        $this->createPermission('/product/delete');
        $this->addChild('manager', '/product/delete');
        $this->createPermission('/product/search');
        $this->addChild('manager', '/product/search');
        $this->createPermission('/product/get-row');
        $this->addChild('manager', '/product/get-row');

        $this->createPermission('/exception/index');
        $this->addChild('manager', '/exception/index');
        $this->createPermission('/exception/create');
        $this->addChild('manager', '/exception/create');
        $this->createPermission('/exception/view');
        $this->addChild('manager', '/exception/view');
        $this->createPermission('/exception/import');
        $this->addChild('manager', '/exception/import');
        $this->createPermission('/exception/export');
        $this->addChild('manager', '/exception/export');
        $this->createPermission('/exception/delete');
        $this->addChild('manager', '/exception/delete');

        $this->createPermission('/dish/index');
        $this->addChild('manager', '/dish/index');
        $this->createPermission('/dish/create');
        $this->addChild('manager', '/dish/create');
        $this->createPermission('/dish/view');
        $this->addChild('manager', '/dish/view');
        $this->createPermission('/dish/import');
        $this->addChild('manager', '/dish/import');
        $this->createPermission('/dish/export');
        $this->addChild('manager', '/dish/export');
        $this->createPermission('/dish/delete');
        $this->addChild('manager', '/dish/delete');

        $this->createPermission('/user/index');
        $this->addChild('manager', '/user/index');
        $this->createPermission('/user/create');
        $this->addChild('manager', '/user/create');
        $this->createPermission('/user/view');
        $this->addChild('manager', '/user/view');
        $this->createPermission('/user/export');
        $this->addChild('manager', '/user/export');

        $this->createPermission('/user/block');
        $this->addChild('root', '/user/block');
        $this->createPermission('/user/grant-privilege');
        $this->addChild('root', '/user/grant-privilege');
        $this->createPermission('/user/delete');
        $this->addChild('root', '/user/delete');

        $this->createPermission('/*');
        $this->addChild('root', '/*');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/main/index');
        $this->removePermission('/main/index');
        $this->removeChild('manager', '/main/logout');
        $this->removePermission('/main/logout');

        $this->removeChild('manager', '/payment-type/index');
        $this->removePermission('/payment-type/index');
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

        $this->removeChild('manager', '/product/index');
        $this->removePermission('/product/index');
        $this->removeChild('manager', '/product/create');
        $this->removePermission('/product/create');
        $this->removeChild('manager', '/product/delete');
        $this->removePermission('/product/delete');
        $this->removeChild('manager', '/product/view');
        $this->removePermission('/product/view');
        $this->removeChild('manager', '/product/export');
        $this->removePermission('/product/export');
        $this->removeChild('manager', '/product/import');
        $this->removePermission('/product/import');
        $this->removeChild('manager', '/product/search');
        $this->removePermission('/product/search');
        $this->removeChild('manager', '/product/get-row');
        $this->removePermission('/product/get-row');

        $this->removeChild('manager', '/exception/index');
        $this->removePermission('/exception/index');
        $this->removeChild('manager', '/exception/create');
        $this->removePermission('/exception/create');
        $this->removeChild('manager', '/exception/delete');
        $this->removePermission('/exception/delete');
        $this->removeChild('manager', '/exception/view');
        $this->removePermission('/exception/view');
        $this->removeChild('manager', '/exception/export');
        $this->removePermission('/exception/export');
        $this->removeChild('manager', '/exception/import');
        $this->removePermission('/exception/import');

        $this->removeChild('manager', '/dish/index');
        $this->removePermission('/dish/index');
        $this->removeChild('manager', '/dish/create');
        $this->removePermission('/dish/create');
        $this->removeChild('manager', '/dish/delete');
        $this->removePermission('/dish/delete');
        $this->removeChild('manager', '/dish/view');
        $this->removePermission('/dish/view');
        $this->removeChild('manager', '/dish/export');
        $this->removePermission('/dish/export');
        $this->removeChild('manager', '/dish/import');
        $this->removePermission('/dish/import');

        $this->removeChild('manager', '/user/index');
        $this->removePermission('/user/index');
        $this->removeChild('manager', '/user/view');
        $this->removePermission('/user/view');
        $this->removeChild('manager', '/user/create');
        $this->removePermission('/user/create');
        $this->removeChild('manager', '/user/export');
        $this->removePermission('/user/export');

        $this->removeChild('root', '/user/delete');
        $this->removePermission('/user/delete');
        $this->removeChild('root', '/user/block');
        $this->removePermission('/user/block');
        $this->removeChild('root', '/user/grant-privilege');
        $this->removePermission('/user/grant-privilege');

        $this->removeChild('root', '/*');
        $this->removePermission('/*');
    }
}