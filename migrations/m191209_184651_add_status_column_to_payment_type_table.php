<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%payment_type}}`.
 */
class m191209_184651_add_status_column_to_payment_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%payment_type}}', 'status', $this->integer()->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%payment_type}}', 'status');
    }
}
