<?php

use yii\db\Migration;

/**
 * Class m200203_135337_change_weight_column_in_product_table
 */
class m200203_135337_change_weight_column_in_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%product}}', 'weight');
        $this->addColumn('{{%product}}', 'unit', $this->string(2)->after('count'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'unit');
        $this->addColumn('{{%product}}', 'weight', $this->integer()->after('count'));
    }
}
