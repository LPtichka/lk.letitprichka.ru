<?php

use yii\db\Migration;

/**
 * Class m210929_125501_add_status_to_product_table
 */
class m210929_125501_add_status_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'status', $this->integer()->defaultValue(10)->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210929_125501_add_status_to_product_table cannot be reverted.\n";

        return false;
    }
    */
}
