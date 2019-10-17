<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_schedule}}`.
 */
class m191017_105629_create_order_schedule_table extends Migration
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

        $this->createTable('{{%order_schedule}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'date' => $this->string(10)->notNull(),
            'status' => $this->tinyInteger()->notNull(),
            'interval' => $this->string()->notNull(),
            'cost' => $this->float()->notNull(),
            'comment' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_order_schedule_order_id', '{{%order_schedule}}', 'order_id', '{{%order}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_order_schedule_order_id', '{{%order_schedule}}');
        $this->dropTable('{{%order_schedule}}');
    }
}
