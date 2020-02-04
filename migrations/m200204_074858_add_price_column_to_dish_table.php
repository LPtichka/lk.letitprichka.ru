<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%dish}}`.
 */
class m200204_074858_add_price_column_to_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%dish}}', 'price', $this->integer()->after('name')->null());
        $this->addColumn('{{%order_schedule_dish}}', 'price', $this->integer()->after('dish_id')->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%dish}}', 'price');
        $this->dropColumn('{{%order_schedule_dish}}', 'price');
    }
}
