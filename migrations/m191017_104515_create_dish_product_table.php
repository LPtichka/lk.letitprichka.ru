<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dish_product}}`.
 */
class m191017_104515_create_dish_product_table extends Migration
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

        $this->createTable('{{%dish_product}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'dish_id' => $this->integer()->notNull(),
            'weight' => $this->integer()->notNull(),
            'brutto' => $this->integer()->notNull(),
            'netto' => $this->integer()->notNull(),
            'kkal' => $this->integer()->notNull(),
            'proteins' => $this->integer()->notNull(),
            'fat' => $this->integer()->notNull(),
            'carbohydrates' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_dish_product_product_id', '{{%dish_product}}', 'product_id', '{{%product}}', 'id');
        $this->addForeignKey('fk_dish_product_dish_id', '{{%dish_product}}', 'dish_id', '{{%dish}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_dish_product_product_id', '{{%dish_product}}');
        $this->dropForeignKey('fk_dish_product_dish_id', '{{%dish_product}}');
        $this->dropTable('{{%dish_product}}');
    }
}
