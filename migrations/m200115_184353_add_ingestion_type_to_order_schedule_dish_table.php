<?php

use yii\db\Migration;

/**
 * Class m200115_184353_add_ingestion_type_to_order_schedule_dish_table
 */
class m200115_184353_add_ingestion_type_to_order_schedule_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_schedule_dish}}', 'ingestion_type', $this->integer()->after('dish_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_schedule_dish}}', 'ingestion_type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200115_184353_add_ingestion_type_to_order_schedule_dish_table cannot be reverted.\n";

        return false;
    }
    */
}
