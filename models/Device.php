<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "device".
 *
 * @property int $id
 * @property int|null $kind_id                  // вид (эталон, ...) - из словаря
 * @property int|null $name_id                  // название
 * @property int|null $state_id                 // состояние (списан)
 * @property int|null $department_id            // цех
 * @property int|null $crew_id                  // бригада
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
 * @property Word|null $wordKind magic property
 * @property Word|null $wordName magic property
 * @property Word|null $wordState magic property
 * @property Word|null $wordDepartment magic property
 * @property Word|null $wordCrew magic property
 * @property Verification|null $activeVerification magic property
 * @property Verification[] $verifications magic property
 */
class Device extends ActiveRecord
{
    public $kind, $group, $type, $name, $state, $department, $crew;
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
            [['kind', 'name', 'state', 'department', 'crew', 'number'], 'required'],
            [['description'], 'string'],
            [['number'], 'string', 'max' => Yii::$app->params['maxLengthTextField']],
            [['kind', 'name', 'state', 'department', 'crew', 'position'], 'string', 'max' => Yii::$app->params['maxLengthTextField']],
            [['kind', 'name', 'state', 'department', 'crew'], 'validateId', 'skipOnEmpty' => false],
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
                $parents = Word::getParentByLevel($word);
                if (Word::getFieldWord($attribute) !== $parents[0]->id) {
                    $this->addError($attribute, 'Значение не из нужной категории');
                }
                if ($attribute == 'name') {
                    if (! isset($parents[2])) {
                        $this->addError($attribute, 'Значение неверной глубины');
                    }
                }
                $this->$attributeId = $word->id;
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
            'kind_id' => 'Вид', 'kind' => 'Вид',
            'group' => 'Группа',
            'type' => 'Тип',
            'name_id' => 'Имя', 'name' => 'Имя',
            'state_id' => 'Состояние', 'state' => 'Состояние',
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

    public function getWordKind()
    {
        return $this->hasOne(Word::class, ['id' => 'kind_id']);
    }

    public function getWordName()
    {
        return $this->hasOne(Word::class, ['id' => 'name_id']);
    }

    public function getWordState()
    {
        return $this->hasOne(Word::class, ['id' => 'state_id']);
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
