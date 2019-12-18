<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%exception}}`.
 */
class m191210_190809_add_status_column_to_exception_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%exception}}', 'status', $this->integer()->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%exception}}', 'status');
    }
}
