<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%dish}}`.
 */
class m191116_135925_add_brutto_on_1_kg_column_to_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%dish_product}}', 'brutto_on_1_kg', $this->integer()->after('weight'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%dish_product}}', 'brutto_on_1_kg');
    }
}
