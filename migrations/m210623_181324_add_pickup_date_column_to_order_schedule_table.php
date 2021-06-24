<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order_schedule}}`.
 */
class m210623_181324_add_pickup_date_column_to_order_schedule_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_schedule}}', 'pickup_date', $this->string(10)->after('date'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_schedule}}', 'pickup_date');
    }
}
