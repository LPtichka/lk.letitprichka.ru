<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_and_order}}`.
 */
class m200107_122125_add_franchize_id_column_to_user_and_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'franchise_id', $this->integer()->after('status'));
        $this->addColumn('{{%order}}', 'franchise_id', $this->integer()->after('id'));

        $this->addForeignKey('fk_user_franchise_id', '{{%user}}', 'franchise_id', '{{%franchise}}', 'id');
        $this->addForeignKey('fk_order_franchise_id', '{{%order}}', 'franchise_id', '{{%franchise}}', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'franchise_id');
        $this->dropColumn('{{%order}}', 'franchise_id');
    }
}
