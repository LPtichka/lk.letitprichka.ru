<?php

use yii\db\Migration;

/**
 * Class m200201_150724_add_migration_data
 */
class m200201_150724_add_migration_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $this->execute(file_get_contents(__DIR__ . '/lk.letitptichka.ru_2020-02-01.sql'));
        $this->execute('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m200201_150724_add_migration_data cannot be reverted.\n";

        return false;
    }
}
