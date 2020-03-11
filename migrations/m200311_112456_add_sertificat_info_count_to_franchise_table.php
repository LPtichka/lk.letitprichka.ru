<?php

use yii\db\Migration;

/**
 * Class m200311_112456_add_sertificat_info_count_to_franchise_table
 */
class m200311_112456_add_sertificat_info_count_to_franchise_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%franchise}}', 'sertificat_info', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%franchise}}', 'sertificat_info');
    }
}
