<?php

use yii\db\Migration;

/**
 * Class m210926_142840_update_count_type_in_product_table
 */
class m210926_142840_update_count_type_in_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%product}}', 'count', $this->float()->defaultValue(0));
        $this->alterColumn('{{%dish_product}}', 'weight', $this->float()->defaultValue(0));
        $this->alterColumn('{{%dish_product}}', 'netto', $this->float()->defaultValue(0));
        $this->alterColumn('{{%dish_product}}', 'brutto', $this->float()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%product}}', 'count', $this->integer()->defaultValue(0));
        $this->alterColumn('{{%dish_product}}', 'weight', $this->integer()->defaultValue(0));
        $this->alterColumn('{{%dish_product}}', 'netto', $this->integer()->defaultValue(0));
        $this->alterColumn('{{%dish_product}}', 'brutto', $this->integer()->defaultValue(0));
    }
}
