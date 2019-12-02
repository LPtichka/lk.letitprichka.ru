<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%subscription}}`.
 */
class m191201_152247_add_price_column_to_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%subscription}}', 'price', $this->integer()->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%subscription}}', 'price');
    }
}
