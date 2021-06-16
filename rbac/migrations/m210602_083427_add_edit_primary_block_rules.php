<?php

use yii2mod\rbac\migrations\Migration;

class m210602_083427_add_edit_primary_block_rules extends Migration
{
    public function safeUp()
    {
        $this->createPermission('/order/get-edit-primary-block');
        $this->addChild('manager', '/order/get-edit-primary-block');
        $this->createPermission('/order/edit-primary-block');
        $this->addChild('manager', '/order/edit-primary-block');
    }

    public function safeDown()
    {
        $this->removeChild('manager', '/order/get-edit-primary-block');
        $this->removePermission('/order/get-edit-primary-block');
        $this->removeChild('manager', '/order/edit-primary-block');
        $this->removePermission('/order/edit-primary-block');

    }
}