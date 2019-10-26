<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%dish}}`.
 */
class m191025_075452_add_columns_to_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%dish}}', 'is_breakfast', $this->boolean()->defaultValue(false)->after('type'));
        $this->addColumn('{{%dish}}', 'is_dinner', $this->boolean()->defaultValue(false)->after('is_breakfast'));
        $this->addColumn('{{%dish}}', 'is_lunch', $this->boolean()->defaultValue(false)->after('is_dinner'));
        $this->addColumn('{{%dish}}', 'is_supper', $this->boolean()->defaultValue(false)->after('is_lunch'));
        $this->addColumn('{{%dish}}', 'is_garnish', $this->boolean()->defaultValue(false)->after('is_supper'));
        $this->addColumn('{{%dish}}', 'with_garnish', $this->boolean()->defaultValue(false)->after('is_garnish'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%dish}}', 'is_breakfast');
        $this->dropColumn('{{%dish}}', 'is_dinner');
        $this->dropColumn('{{%dish}}', 'is_lunch');
        $this->dropColumn('{{%dish}}', 'is_supper');
        $this->dropColumn('{{%dish}}', 'is_garnish');
        $this->dropColumn('{{%dish}}', 'with_garnish');
    }
}
