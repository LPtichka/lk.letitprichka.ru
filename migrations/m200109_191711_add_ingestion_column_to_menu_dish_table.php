<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%menu_dish}}`.
 */
class m200109_191711_add_ingestion_column_to_menu_dish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%menu_dish}}', 'ingestion', $this->integer()->after('dish_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%menu_dish}}', 'ingestion');
    }
}
