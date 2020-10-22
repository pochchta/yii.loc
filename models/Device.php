<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "device".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $number
 * @property string|null $type
 * @property string|null $description
 * @property int|null $id_department
 * @property int|null $id_scale
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
 * @property Department|null $department magic property
 * @property Scale|null $scale magic property
 * @property Verification|null $activeVerification magic property
 * @property Verification[] $verifications magic property
 * @method touch(string $string) Method TimestampBehavior
 */
class Device extends ActiveRecord
{
    const NOT_DELETED = 0;
    const DELETED = 1;
    const ALL = -1;
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
            [['description'], 'string'],
            [['name', 'type', 'number', 'accuracy', 'position'], 'string', 'max' => 255],
            [['id_department', 'id_scale'], 'integer'],
            [['id_department'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['id_department' => 'id']],
            [['id_scale'], 'exist', 'skipOnError' => true, 'targetClass' => Scale::class, 'targetAttribute' => ['id_scale' => 'id']],
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
            'id_department' => 'Цех',
            'id_scale' => 'Шкала',
            'accuracy' => 'Класс точности',
            'position' => 'Позиция',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'created_by' => 'Создал',
            'updated_by' => 'Обновил',
            'deleted' => 'Удален'
        ];
    }

    /**
     * Gets query for [[Verifications]].
     *
     * @return \yii\db\ActiveQuery
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
        return $this->hasOne(Department::class, ['id' => 'id_department']);
    }

    public function getScale()
    {
        return $this->hasOne(Scale::class, ['id' => 'id_scale']);
    }
}
