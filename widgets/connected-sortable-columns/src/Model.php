<?php

namespace app\widgets\csc;


use yii\db\ActiveRecord;

/**
 * This is the model class for table "grid_column_sort".
 *
 * @property int $id
 * @property string|null $role
 * @property string|null $name
 * @property string|null $widget_name
 * @property string|null $col
 */
class Model extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grid_column_sort';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['col'], 'string'],
            [['role', 'name', 'widget_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role' => 'Role',
            'name' => 'Name',
            'col' => 'Col',
            'widget_name' => 'widgetName',
        ];
    }

    public function formName()
    {
        return '';
    }
}
