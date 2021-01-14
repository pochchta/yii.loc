<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "device".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $number
 * @property string|null $type
 * @property string|null $description
 * @property int|null $department_id
 * @property int|null $scale_id
 * @property string|null $accuracy
 * @property string|null $position
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $deleted
 *
 * @property User|null $creator magic property
 * @property User|null $updater magic property
 * @property Word|null $department magic property
 * @property Word|null $scale magic property
 * @property Verification|null $activeVerification magic property
 * @property Verification[] $verifications magic property
 */
class Device extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device';
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
            [['name', 'department_id', 'scale_id'], 'required'],
            [['description'], 'string'],
            [['name', 'type', 'number', 'accuracy', 'position'], 'string', 'max' => 255],
            [['department_id', 'scale_id'], 'integer'],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Word::class, 'targetAttribute' => ['department_id' => 'id']],
            [['scale_id'], 'exist', 'skipOnError' => true, 'targetClass' => Word::class, 'targetAttribute' => ['scale_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'number' => 'Номер',
            'type' => 'Тип',
            'description' => 'Описание',
            'department_id' => 'Цех',
            'scale_id' => 'Шкала',
            'accuracy' => 'Класс точности',
            'position' => 'Позиция',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'created_by' => 'Создал',
            'updated_by' => 'Обновил',
            'deleted' => 'Удален',
        ];
    }

    /**
     * Gets query for [[Verifications]].
     *
     * @return ActiveQuery
     */
    public function getVerifications()
    {
        return $this->hasMany(Verification::class, ['device_id' => 'id']);
    }

    public function getActiveVerification()
    {
        return $this->hasOne(Verification::class, ['device_id' => 'id'])->where(['status' => Verification::STATUS_ON]);
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdater()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getDepartment()
    {
        return $this->hasOne(Word::class, ['id' => 'department_id']);
    }

    public function getScale()
    {
        return $this->hasOne(Word::class, ['id' => 'scale_id']);
    }
}
