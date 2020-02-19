<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%columns_form_dish_product}}`.
 */
class m200219_074101_drop_columns_form_dish_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%dish_product}}', 'brutto_on_1_kg');
        $this->dropColumn('{{%dish_product}}', 'fat');
        $this->dropColumn('{{%dish_product}}', 'proteins');
        $this->dropColumn('{{%dish_product}}', 'kkal');
        $this->dropColumn('{{%dish_product}}', 'carbohydrates');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%dish_product}}', 'brutto_on_1_kg', $this->integer());
        $this->addColumn('{{%dish_product}}', 'fat', $this->integer());
        $this->addColumn('{{%dish_product}}', 'proteins', $this->integer());
        $this->addColumn('{{%dish_product}}', 'kkal', $this->integer());
        $this->addColumn('{{%dish_product}}', 'carbohydrates', $this->integer());
    }
}
