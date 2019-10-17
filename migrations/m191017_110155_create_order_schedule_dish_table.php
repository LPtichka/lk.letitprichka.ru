<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_schedule_dish}}`.
 */
class m191017_110155_create_order_schedule_dish_table extends Migration
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

        $this->createTable('{{%order_schedule_dish}}', [
            'id' => $this->primaryKey(),
            'order_schedule_id' => $this->integer()->notNull(),
            'dish_id' => $this->integer()->notNull(),
            'manufactured_at' => $this->integer()->notNull(),
            'storage_condition' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_order_schedule_dish_order_id', '{{%order_schedule_dish}}', 'order_schedule_id', '{{%order_schedule}}', 'id');
        $this->addForeignKey('fk_order_schedule_dish_dish_id', '{{%order_schedule_dish}}', 'dish_id', '{{%dish}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_order_schedule_dish_order_id', '{{%order_schedule_dish}}');
        $this->dropForeignKey('fk_order_schedule_dish_dish_id', '{{%order_schedule_dish}}');
        $this->dropTable('{{%order_schedule_dish}}');
    }
}
