<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%dish}}`.
 */
class m210921_130337_add_status_column_to_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%dish}}', 'status', $this->integer()->after('id')->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%dish}}', 'status');
    }
}
