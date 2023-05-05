<?php

use yii\db\Migration;

/**
 * Class m221129_061122_drop_column_gcs
 */
class m221129_061122_drop_column_gcs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn(
            '{{%grid_column_sort}}',
            'user_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn(
            '{{%grid_column_sort}}',
            'user_id',
            $this->bigInteger()->unsigned()
        );
    }
}
