<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%menu_dish}}`.
 */
class m191017_103857_create_menu_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%menu_dish}}', [
            'id' => $this->primaryKey(),
            'menu_id' => $this->integer()->notNull(),
            'dish_id' => $this->integer()->notNull(),
            'day' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_menu_dish_menu_id', '{{%menu_dish}}', 'menu_id', '{{%menu}}', 'id');
        $this->addForeignKey('fk_menu_dish_dish_id', '{{%menu_dish}}', 'dish_id', '{{%dish}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_menu_dish_menu_id', '{{%menu_dish}}');
        $this->dropForeignKey('fk_menu_dish_dish_id', '{{%menu_dish}}');
        $this->dropTable('{{%menu_dish}}');
    }
}
