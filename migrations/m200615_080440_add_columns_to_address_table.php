<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%address}}`.
 */
class m200615_080440_add_columns_to_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%address}}', 'floor', $this->string(32)->after('flat')->null());
        $this->addColumn('{{%address}}', 'porch', $this->string(32)->after('floor')->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%address}}', 'floor');
        $this->dropColumn('{{%address}}', 'porch');
    }
}
