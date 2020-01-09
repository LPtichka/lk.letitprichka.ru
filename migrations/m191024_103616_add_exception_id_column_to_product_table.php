<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%product}}`.
 */
class m191024_103616_add_exception_id_column_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'exception_id', $this->integer()->after('id'));
        $this->addForeignKey('fk_product_exception_id', '{{%product}}', 'exception_id', '{{%exception}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_product_exception_id', '{{%product}}');
        $this->dropColumn('{{%product}}', 'exception_id');
    }
}
