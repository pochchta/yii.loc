<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "device".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $type
 * @property string|null $description
 * @property int|null $last_date
 * @property int|null $next_date
 * @property int|null $period
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Verification[] $verifications
 * @method touch(string $string) Method TimestampBehavior
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
            [['description'], 'string'],
            [['name', 'type'], 'string', 'max' => 255],
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
            'type' => 'Тип',
            'description' => 'Описание',
            'last_date' => 'Дата последней поверки',
            'next_date' => 'Дата следующей поверки',
            'period' => 'Период поверки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'created_by' => 'Создал',
            'updated_by' => 'Обновил',
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

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdater()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
