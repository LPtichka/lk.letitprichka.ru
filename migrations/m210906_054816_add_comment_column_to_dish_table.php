<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%dish}}`.
 */
class m210906_054816_add_comment_column_to_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%dish}}', 'comment', $this->text()->after('storage_condition')->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%dish}}', 'comment');
    }
}
