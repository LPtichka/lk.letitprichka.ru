<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%exception}}`.
 */
class m200621_150610_add_with_comment_column_to_exception_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%exception}}', 'with_comment', $this->boolean()->after('status')->defaultValue(false));
        $this->addColumn('{{%order_exception}}', 'comment', $this->string(255)->after('exception_id')->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%exception}}', 'with_comment');
        $this->dropColumn('{{%order_exception}}', 'comment');
    }
}
