<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%grid_column_sort}}`.
 */
class m220823_020848_create_grid_column_sort_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%grid_column_sort}}', [
            'id' => $this->primaryKey()->unsigned(),
            'role' => $this->string(),
            'user_id' => $this->bigInteger()->unsigned(),
            'name' => $this->string(),
            'col' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%grid_column_sort}}');
    }
}
