<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%address}}`.
 */
class m191017_103409_create_address_table extends Migration
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

        $this->createTable('{{%address}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'city' => $this->string(255)->notNull(),
            'street' => $this->string(255),
            'house' => $this->string(16),
            'housing' => $this->string(16),
            'building' => $this->string(16),
            'flat' => $this->string(16),
            'postcode' => $this->string(6),
            'description' => $this->text(),
            'full_address' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_address_customer_id', '{{%address}}', 'customer_id', '{{%customer}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_address_customer_id', '{{%address}}');
        $this->dropTable('{{%address}}');
    }
}
