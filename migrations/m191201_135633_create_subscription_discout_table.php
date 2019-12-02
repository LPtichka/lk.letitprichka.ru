<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscription_discout}}`.
 */
class m191201_135633_create_subscription_discout_table extends Migration
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

        $this->createTable('{{%subscription_discount}}', [
            'id' => $this->primaryKey(),
            'subscription_id' => $this->integer()->notNull(),
            'count' => $this->integer()->notNull(),
            'price' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_subscription_discount_subscription_id', '{{%subscription_discount}}', 'subscription_id', '{{%subscription}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_subscription_discount_subscription_id', '{{%subscription_discount}}');
        $this->dropTable('{{%subscription_discount}}');
    }
}
