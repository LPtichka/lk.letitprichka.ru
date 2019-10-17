<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dish}}`.
 */
class m191017_083739_create_dish_table extends Migration
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

        $this->createTable('{{%dish}}', [
            'id' => $this->primaryKey(),
            'type' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'process' => $this->text(),
            'weight' => $this->integer()->notNull(),
            'kkal' => $this->integer()->notNull(),
            'proteins' => $this->integer()->notNull(),
            'fat' => $this->integer()->notNull(),
            'carbohydrates' => $this->integer()->notNull(),
            'storage_condition' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%dish}}');
    }
}
