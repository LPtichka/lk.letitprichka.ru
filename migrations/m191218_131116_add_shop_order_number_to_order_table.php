<?php

use yii\db\Migration;

/**
 * Class m191218_131116_add_shop_order_number_to_order_table
 */
class m191218_131116_add_shop_order_number_to_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'shop_order_number', $this->string(255)->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'shop_order_number');
    }
}
