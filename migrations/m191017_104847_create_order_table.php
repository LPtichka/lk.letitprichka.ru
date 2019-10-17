<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order}}`.
 */
class m191017_104847_create_order_table extends Migration
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

        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
            'subscription_id' => $this->integer()->notNull(),
            'customer_id' => $this->integer()->notNull(),
            'address_id' => $this->integer()->notNull(),
            'payment_type' => $this->integer()->notNull(),
            'status_id' => $this->integer()->notNull(),
            'count' => $this->integer()->notNull(),
            'cutlery' => $this->integer()->notNull(),
            'total' => $this->float()->notNull(),
            'cash_machine' => $this->boolean()->notNull(),
            'comment' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_order_subscription_id', '{{%order}}', 'subscription_id', '{{%subscription}}', 'id');
        $this->addForeignKey('fk_order_customer_id', '{{%order}}', 'customer_id', '{{%customer}}', 'id');
        $this->addForeignKey('fk_order_address_id', '{{%order}}', 'address_id', '{{%address}}', 'id');
        $this->addForeignKey('fk_order_status_id', '{{%order}}', 'status_id', '{{%order_status}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_order_subscription_id', '{{%order}}');
        $this->dropForeignKey('fk_order_customer_id', '{{%order}}');
        $this->dropForeignKey('fk_order_address_id', '{{%order}}');
        $this->dropForeignKey('fk_order_status_id', '{{%order}}');
        $this->dropTable('{{%dish_product}}');
    }
}
