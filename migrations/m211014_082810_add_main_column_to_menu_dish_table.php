<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%menu_dish}}`.
 */
class m211014_082810_add_main_column_to_menu_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%menu_dish}}', 'is_main', $this->boolean()->defaultValue(true)->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%menu_dish}}', 'is_main');
    }
}
