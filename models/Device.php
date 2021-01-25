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
 * @property int|null $scale_id                 //
 * @property int|null $position_id              //
 * @property string|null $accuracy_id           //
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
 * @property Word|null $wordScale magic property
 * @property Word|null $wordPosition magic property
 * @property Word|null $wordAccuracy magic property
 * @property Verification|null $activeVerification magic property
 * @property Verification[] $verifications magic property
 */
class Device extends ActiveRecord
{
    public $name, $type, $department, $scale, $position, $accuracy;
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
            [['name_id', 'type_id', 'department_id', 'scale_id', 'accuracy_id'], 'required'],
            [['description'], 'string'],
            [['number'], 'string', 'max' => 255],
            [['name_id', 'type_id', 'department_id', 'scale_id', 'position_id', 'accuracy_id'], 'validateId'],
            [['name', 'type', 'department', 'scale', 'position', 'accuracy'], 'string', 'max' => 255]
        ];
    }

    public function validateId($attribute)
    {
        if (!$this->hasErrors()) {
            $attributeName = substr($attribute, 0, -3);    // удаление '_id' на конце
            if ($this->$attribute > 0) {
                $word = Word::find()->where(['id' => $this->$attribute])->one();
                if ($this->$attributeName !== $word->name) {
                    $words = Word::find()->where(['name' => $this->$attributeName])->limit(2)->all();
                    if (count($words) == 1) {   // заполнение атрибута не из списка, если он один и введен точно
                        $this->$attribute = $words[0]->id;
                    } else {
                        $this->addError($attributeName, 'Значение не из списка');
                    }
                }
            } else {
                $this->addError($attributeName, 'Значение не из списка');
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
            'scale_id' => 'Шкала', 'scale' => 'Шкала',
            'position_id' => 'Позиция', 'position' => 'Позиция',
            'accuracy_id' => 'Класс точности', 'accuracy' => 'Класс точности',
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

    public function getWordDepartment()
    {
        return $this->hasOne(Word::class, ['id' => 'department_id']);
    }

    public function getWordScale()
    {
        return $this->hasOne(Word::class, ['id' => 'scale_id']);
    }
    public function getWordName()
    {
        return $this->hasOne(Word::class, ['id' => 'name_id']);
    }

    public function getWordType()
    {
        return $this->hasOne(Word::class, ['id' => 'type_id']);
    }

    public function getWordPosition()
    {
        return $this->hasOne(Word::class, ['id' => 'position_id']);
    }

    public function getWordAccuracy()
    {
        return $this->hasOne(Word::class, ['id' => 'accuracy_id']);
    }
}
