<?php

use yii\db\Migration;

/**
 * Class m210531_123544_add_individual_menu_field_in_order_table
 */
class m210531_123544_add_individual_menu_field_in_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'individual_menu', $this->integer()->after('without_soup')->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'individual_menu');
    }
}
