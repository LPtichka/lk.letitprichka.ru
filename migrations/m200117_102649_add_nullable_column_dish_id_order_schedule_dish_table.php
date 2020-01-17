<?php

use yii\db\Migration;

/**
 * Class m200117_102649_add_nullable_column_dish_id_order_schedule_dish_table
 */
class m200117_102649_add_nullable_column_dish_id_order_schedule_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%order_schedule_dish}}', 'dish_id', $this->integer()->null());
        $this->alterColumn('{{%order_schedule_dish}}', 'manufactured_at', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200117_102649_add_nullable_column_dish_id_order_schedule_dish_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200117_102649_add_nullable_column_dish_id_order_schedule_dish_table cannot be reverted.\n";

        return false;
    }
    */
}
