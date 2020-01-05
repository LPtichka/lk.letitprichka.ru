<?php

use yii\db\Migration;

/**
 * Class m200103_131829_add_cash_machine_to_payment_table
 */
class m200103_131829_add_cash_machine_to_payment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%payment_type}}', 'cash_machine', $this->boolean()->after('name')->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%payment_type}}', 'cash_machine');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200103_131829_add_cash_machine_to_payment_table cannot be reverted.\n";

        return false;
    }
    */
}
