<?php

use yii\db\Migration;

/**
 * Class m200219_072315_alter_column_type_in_dish_table
 */
class m200219_072315_alter_column_type_in_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%dish}}', 'type', $this->tinyInteger()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200219_072315_alter_column_type_in_dish_table cannot be reverted.\n";

        return false;
    }
}
