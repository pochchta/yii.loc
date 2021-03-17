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
 * @property int|null $name_id                  // из словаря
 * @property int|null $type_id                  //
 * @property int|null $department_id            //
 * @property int|null $crew_id                  //
 * @property string|null $position
 * @property string|null $number
 * @property string|null $description
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $deleted
 *
 * @property User|null $creator magic property
 * @property User|null $updater magic property
 * @property Word|null $wordName magic property
 * @property Word|null $wordType magic property
 * @property Word|null $wordDepartment magic property
 * @property Word|null $wordCrew magic property
 * @property Verification|null $activeVerification magic property
 * @property Verification[] $verifications magic property
 */
class Device extends ActiveRecord
{
    public $name, $type, $department, $crew;
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
            [['name', 'type', 'department', 'crew', 'number'], 'required'],
            [['description'], 'string'],
            [['number'], 'string', 'max' => 30],
            [['name', 'type', 'department', 'crew', 'position'], 'string', 'max' => 30],
            [['name', 'type', 'department', 'crew'], 'validateId', 'skipOnEmpty' => false],
        ];
    }

    /** Присваивание $attribute . '_id' = Word::findOne(['name' => $attribute])->id
     * @param $attribute
     */
    public function validateId($attribute)
    {
        if (!$this->hasErrors()) {
            $attributeId = $attribute . '_id';
            $word = Word::findOne(['name' => $this->$attribute]);
            if (isset($word)) {
                $this->$attributeId = $word->id;
                if (Word::FIELD_WORD[ucfirst($attribute)] !== Word::getParentByLevel($word, 0)->id) {
                    $this->addError($attribute, 'Значение не из списка');
                }
            } else {
                $this->addError($attribute, 'Значение не из списка');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_id' => 'Имя', 'name' => 'Имя',
            'type_id' => 'Тип', 'type' => 'Тип',
            'department_id' => 'Цех', 'department' => 'Цех',
            'crew_id' => 'Бригада', 'crew' => 'Бригада',
            'position' => 'Позиция',
            'number' => 'Номер',
            'description' => 'Описание',
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

    public function getWordName()
    {
        return $this->hasOne(Word::class, ['id' => 'name_id']);
    }

    public function getWordType()
    {
        return $this->hasOne(Word::class, ['id' => 'type_id']);
    }

    public function getWordDepartment()
    {
        return $this->hasOne(Word::class, ['id' => 'department_id']);
    }

    public function getWordCrew()
    {
        return $this->hasOne(Word::class, ['id' => 'crew_id']);
    }

    public function formName()
    {
        return '';
    }
}
