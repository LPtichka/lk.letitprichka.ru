<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%payment_type}}`.
 */
class m200126_185135_add_type_column_to_payment_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%payment_type}}', 'type', $this->string()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%payment_type}}', 'type');
    }
}
