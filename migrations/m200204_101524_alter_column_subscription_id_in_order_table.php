<?php

use yii\db\Migration;

/**
 * Class m200204_101524_alter_column_subscription_id_in_order_table
 */
class m200204_101524_alter_column_subscription_id_in_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%order}}', 'subscription_id', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200204_101524_alter_column_subscription_id_in_order_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200204_101524_alter_column_subscription_id_in_order_table cannot be reverted.\n";

        return false;
    }
    */
}
