<?php

use yii\db\Migration;

/**
 * Class m200208_072941_add_customer_exception_table
 */
class m200208_072941_add_customer_exception_table extends Migration
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

        $this->createTable('{{%customer_exception}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'exception_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_customer_exception_customer_id', '{{%customer_exception}}', 'customer_id', '{{%customer}}', 'id');
        $this->addForeignKey('fk_customer_exception_exception_id', '{{%customer_exception}}', 'exception_id', '{{%exception}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_customer_exception_customer_id', '{{%customer_exception}}');
        $this->dropForeignKey('fk_customer_exception_exception_id', '{{%customer_exception}}');
        $this->dropTable('{{%customer_exception}}');
    }
}
