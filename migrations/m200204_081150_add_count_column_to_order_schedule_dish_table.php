<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order_schedule_dish}}`.
 */
class m200204_081150_add_count_column_to_order_schedule_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_schedule_dish}}', 'count', $this->integer()->after('name')->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_schedule_dish}}', 'count');
    }
}
