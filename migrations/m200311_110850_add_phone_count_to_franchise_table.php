<?php

use yii\db\Migration;

/**
 * Class m200311_110850_add_phone_count_to_franchise_table
 */
class m200311_110850_add_phone_count_to_franchise_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%franchise}}', 'phone', $this->string(12));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%franchise}}', 'phone');
    }
}
