<?php

use yii\db\Migration;

/**
 * Class m200119_135331_add_dish_type_to_menu_dish_table
 */
class m200119_135331_add_dish_type_to_menu_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%menu_dish}}', 'dish_type', $this->integer()->after('dish_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%menu_dish}}', 'dish_type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200119_135331_add_dish_type_to_menu_dish_table cannot be reverted.\n";

        return false;
    }
    */
}
