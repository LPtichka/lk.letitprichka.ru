<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%is_garnish_column_in_dish}}`.
 */
class m191124_114837_drop_is_garnish_column_in_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%dish}}', 'is_garnish');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%dish}}', 'is_garnish', $this->boolean()->defaultValue(false)->after('is_supper'));
    }
}
