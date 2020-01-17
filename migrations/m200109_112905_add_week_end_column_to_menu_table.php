<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%menu}}`.
 */
class m200109_112905_add_week_end_column_to_menu_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%menu}}', 'week', 'menu_start_date');
        $this->addColumn('{{%menu}}', 'menu_end_date', $this->string()->after('menu_start_date'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('{{%menu}}', 'menu_start_date', 'week');
        $this->dropColumn('{{%menu}}', 'menu_end_date');
    }
}
