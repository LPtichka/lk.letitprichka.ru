<?php

use yii\db\Migration;

/**
 * Class m200531_181321_add_garnish_column
 */
class m200531_181321_add_garnish_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order_schedule_dish}}', 'with_garnish', $this->boolean()->after('type')->defaultValue(false));
        $this->addColumn('{{%order_schedule_dish}}', 'garnish_id', $this->integer()->after('with_garnish')->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_schedule_dish}}', 'with_garnish');
        $this->dropColumn('{{%order_schedule_dish}}', 'garnish_id');
    }
}
