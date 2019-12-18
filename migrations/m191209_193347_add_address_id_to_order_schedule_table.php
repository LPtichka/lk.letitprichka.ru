<?php

use yii\db\Migration;

/**
 * Class m191209_193347_add_address_id_to_order_schedule_table
 */
class m191209_193347_add_address_id_to_order_schedule_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_schedule}}', 'address_id', $this->integer()->after('cost'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_schedule}}', 'address_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191209_193347_add_address_id_to_order_schedule_table cannot be reverted.\n";

        return false;
    }
    */
}
