<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%subscription}}`.
 */
class m191210_191631_add_status_column_to_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%subscription}}', 'status', $this->integer()->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%subscription}}', 'status');
    }
}
