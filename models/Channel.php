<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
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

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
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
            'number' => 'Номер',
            'io' => 'Вх/вых',
            'parent_id' => 'ID входа',
            'device_id' => 'ID прибора',
            'type_id' => 'Тип',
            'accuracy_id' => 'Точность',
            'scale_id' => 'Шкала',
            'range' => 'Диапазон',
            'description' => 'Описание',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'created_by' => 'Создал',
            'updated_by' => 'Обновил',
            'deleted' => 'Удален',
        ];
    }
}
