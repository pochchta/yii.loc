<?php

use yii\db\Migration;

/**
 * Class m230505_044718_add_column_csc
 */
class m230505_044718_add_column_csc extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%grid_column_sort}}',
            'widget_name',
            $this->string()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(
            '{{%grid_column_sort}}',
            'widget_name'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230505_044718_add_column_csc cannot be reverted.\n";

        return false;
    }
    */
}
