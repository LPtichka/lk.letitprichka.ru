<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%settings}}`.
 */
class m210624_110846_add_status_column_to_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%settings}}', 'status', $this->integer()->defaultValue(1)->after('franchise_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%settings}}', 'status');
    }
}
