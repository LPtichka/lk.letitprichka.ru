<?php

use yii\db\Migration;

/**
 * Class m200112_183243_rename_day_column_in_menu_dish_table
 */
class m200112_183243_rename_day_column_in_menu_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%menu_dish}}', 'day', 'date');
        $this->alterColumn('{{%menu_dish}}', 'date', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%menu_dish}}', 'date', $this->integer());
        $this->renameColumn('{{%menu_dish}}', 'date', 'day');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200112_183243_rename_day_column_in_menu_dish_table cannot be reverted.\n";

        return false;
    }
    */
}
