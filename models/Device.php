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
            [['name', 'type', 'department', 'scale', 'accuracy'], 'required'],
            [['description'], 'string'],
            [['number'], 'string', 'max' => 30],
            [['name', 'type', 'department', 'scale', 'position', 'accuracy'], 'string', 'max' => 20],
            [['name', 'type', 'department', 'scale', 'position', 'accuracy'], 'validateId', 'skipOnEmpty' => false],
        ];
    }

    public function validateId($attribute)
    {
        if (!$this->hasErrors()) {
            if ($attribute == 'position') {
                if (strlen($this->position) == 0) {     // position можно оставить пустым
                    $this->position_id = 0;
                    return;
                }
            }

            $attributeId = $attribute . '_id';
            $word = Word::findOne(['name' => $this->$attribute]);
            if ($word) {
                $this->$attributeId = $word->id;

                if ($attribute == 'position') {
                    $departmentFromModel = mb_strtolower($this->department);
                    $departmentFromParent = mb_strtolower($word->parent->name);
                    if (strcasecmp($departmentFromModel, $departmentFromParent) != 0) {     // позиция не принадлежит цеху
                        $this->addError($attribute, 'Значение не из списка');
                    }
                } else {
                    $wordParentId = 0;
                    if ($word->parent_id <= 0) {
                        $wordParentId = $word->parent_id;
                    } elseif ($word->parent->parent_id <= 0) {
                        $wordParentId = $word->parent->parent_id;
                    } elseif ($word->parent->parent->parent_id <= 0) {
                        $wordParentId = $word->parent->parent->parent_id;
                    }
                    if (Word::FIELD_WORD[ucfirst($attribute)] != $wordParentId) {
                        $this->addError($attribute, 'Значение не из списка');
                    }
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

    public function formName()
    {
        return '';
    }
}
