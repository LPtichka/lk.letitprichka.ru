<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%franchise}}`.
 */
class m200108_141205_add_status_column_to_franchise_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%franchise}}', 'status', $this->integer()->defaultValue(\app\models\Repository\Franchise::STATUS_ACTIVE)->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%franchise}}', 'status');
    }
}
