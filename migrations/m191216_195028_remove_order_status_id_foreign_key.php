<?php

use yii\db\Migration;

/**
 * Class m191216_195028_remove_order_status_id_foreign_key
 */
class m191216_195028_remove_order_status_id_foreign_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_order_status_id', '{{%order}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addForeignKey('fk_order_status_id', '{{%order}}', 'status_id', '{{%order_status}}', 'id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191216_195028_remove_order_status_id_foreign_key cannot be reverted.\n";

        return false;
    }
    */
}
