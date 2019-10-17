<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_exception}}`.
 */
class m191017_105417_create_order_exception_table extends Migration
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

        $this->createTable('{{%order_exception}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'exception_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_order_exception_order_id', '{{%order_exception}}', 'order_id', '{{%order}}', 'id');
        $this->addForeignKey('fk_order_exception_exception_id', '{{%order_exception}}', 'exception_id', '{{%exception}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_order_exception_order_id', '{{%order}}');
        $this->dropForeignKey('fk_order_exception_exception_id', '{{%order}}');
        $this->dropTable('{{%order_exception}}');
    }
}
