<?php

use yii\db\Migration;

/**
 * Class m200117_105036_alter_column_cost_in_order_schedule_table
 */
class m200117_105036_alter_column_cost_in_order_schedule_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%order_schedule}}', 'cost', $this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%order_schedule}}', 'cost', $this->integer());
    }
}
