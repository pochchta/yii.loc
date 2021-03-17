<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "channel".
 *
 * @property int $id
 * @property int $number
 * @property int $io
 * @property int|null $parent_id
 * @property int $device_id
 * @property int $type_id
 * @property int $accuracy_id
 * @property int $scale_id
 * @property string $range
 * @property string|null $description
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $deleted
 */
class Channel extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'channel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'io', 'device_id', 'type_id', 'accuracy_id', 'scale_id', 'range'], 'required'],
            [['number', 'io', 'parent_id', 'device_id', 'type_id', 'accuracy_id', 'scale_id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted'], 'integer'],
            [['description'], 'string'],
            [['range'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'io' => 'Io',
            'parent_id' => 'Parent ID',
            'device_id' => 'Device ID',
            'type_id' => 'Type ID',
            'accuracy_id' => 'Accuracy ID',
            'scale_id' => 'Scale ID',
            'range' => 'Range',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'deleted' => 'Deleted',
        ];
    }
}
