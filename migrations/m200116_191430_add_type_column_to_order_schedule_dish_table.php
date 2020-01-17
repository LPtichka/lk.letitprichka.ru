<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order_schedule_dish}}`.
 */
class m200116_191430_add_type_column_to_order_schedule_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_schedule_dish}}', 'type', $this->integer()->after('dish_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_schedule_dish}}', 'type');
    }
}
