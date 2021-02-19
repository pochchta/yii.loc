<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "word".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $value
 * @property string|null $description
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $deleted
 * @property int $parent_id
 *
 * @property Device[] $devices magic property
 * @property Word $parent magic property
 * @property User|null $creator magic property
 * @property User|null $updater magic property
 */
class Word extends ActiveRecord
{
    const MAX_NUMBER_PARENTS = 3;       // максимальный уровень вложенности

    const FIELD_WORD = [
        'Not' => 0,
        'Scale' => -11,
        'Department' => -12,
        'Type' => -13,
        'Name' => -14,
        'Crew' => -15,
        'Accuracy' => -16,
    ];

    const LABEL_FIELD_WORD = [
        self::FIELD_WORD['Not'] => 'нет',
        self::FIELD_WORD['Scale'] => 'Шкалы',
        self::FIELD_WORD['Department'] => 'Цеха',
        self::FIELD_WORD['Type'] => 'Типы приборов',
        self::FIELD_WORD['Name'] => 'Названия приборов',
        self::FIELD_WORD['Crew'] => 'Бригада',
        self::FIELD_WORD['Accuracy'] => 'Точность',
    ];

    public $category_name, $parent_name;

    public static function tableName()
    {
        return 'word';
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

    public function rules()
    {
        return [
            [['name', 'category_name'], 'required'],
            [['name', 'value', 'category_name', 'parent_name'], 'string', 'max' => 20],
            [['name'], 'unique', 'when' => function($model){return $model->isAttributeChanged('name');}],
            [['description'], 'string'],
            [['category_name'], 'validateCategoryName'],     // сначала присваивание parent_id
            [['parent_id'], 'validateDepth'],               // затем его валидация
        ];
    }

    /** Валидация category_name, parent_name, присваивание parent_id
     * @param $attribute
     */
    public function validateCategoryName($attribute)
    {
        if (!$this->hasErrors()) {
            if (isset(self::FIELD_WORD[$this->category_name]) == false) {
                $this->addError($attribute, 'Категория не найдена');
                return;
            }
            if (strlen($this->parent_name)) {        // задан промежуточный родитель
                $parent = self::findOne(['name' => $this->parent_name]);
                if ($parent) {
                    if (
                        $parent->parent_id !== self::FIELD_WORD[$this->category_name] &&
                        $parent->parent->parent_id !== self::FIELD_WORD[$this->category_name]
                    ) {
                        $this->addError('parent_name', 'Категория не принадлежит разделу или превышена вложенность');
                    }
                    if ($this->id !== $parent->id) {
                        $this->parent_id = $parent->id;
                    } else {
                        $this->addError('parent_name', 'Выбрана та же категория');
                    }
                } else {
                    $this->addError('parent_name', 'Родительская категория не найдена');
                }
            } else {
                $this->parent_id = self::FIELD_WORD[$this->category_name];
            }
        }
    }

    /** Не только ограничивает глубину, но и запрещает циклические назначения родителей
     *
     */
    public function validateDepth()
    {
        if (!$this->hasErrors()) {
            if ($this->deleted == Status::NOT_DELETED) {
                $depthAttribute = 'firstCategory';
                $depth = 1;             // вложена в "виртуальную" категорию
                $parent = $this->parent;
                if (isset($parent) && $parent->deleted == Status::NOT_DELETED) {
                    $depth = 2;
                    $parent = $parent->parent;
                    if (isset($parent) && $parent->deleted == Status::NOT_DELETED) {
                        $depth = 3;
                        if ($parent->parent_id > 0) {
                            $depth = 4;
                        }
                    }
                }

                if ($depth <= self::MAX_NUMBER_PARENTS) {
                    if (self::find()->where(['parent_id' => $this->id, 'deleted' => Status::NOT_DELETED])->one() !== NULL) {
                        $depth++;
                    }
                }
                if ($depth <= self::MAX_NUMBER_PARENTS) {
                    if (self::find()->andOnCondition(
                        'parent_id IN (SELECT id FROM word WHERE parent_id = :id AND deleted = :not_del) AND deleted = :not_del',
                        [':id' => $this->id, ':not_del' => Status::NOT_DELETED]
                    )->one() !== NULL) {
                        $depth++;
                    }
                }
                if ($depth <= self::MAX_NUMBER_PARENTS) {
                    if (self::find()->andOnCondition(
                        'parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id = :id AND deleted = :not_del) AND deleted = :not_del) AND deleted = :not_del',
                        [':id' => $this->id, ':not_del' => Status::NOT_DELETED])->one() !== NULL) {
                        $depth++;
                    }
                }
                if ($depth > self::MAX_NUMBER_PARENTS) {
                    $this->addError($depthAttribute, 'Превышена глубина вложенности');
                }
            }
        }
    }

    /** Получение условия по id родителя для запроса дочерних элементов
     * @param $columnName
     * @param $parentId
     * @param int $depth
     * @param bool $withParent
     * @return array
     */
    public static function getConditionById($columnName, $parentId, $depth = 1, $withParent = false)
    {
        $condition = NULL;
        $bindName = ':' . $columnName . (int)$depth;
        if ($parentId == Status::ALL) {
            $parentId = 0;
            $condition1 = "<= $bindName";
            $condition2 = "IN (SELECT id FROM word WHERE parent_id $condition1 AND deleted = :not_del)";
            $condition3 = "IN (SELECT id FROM word WHERE parent_id $condition2 AND deleted = :not_del)";
        } else {
            $condition1 = "= $bindName";
            $condition2 = "IN (SELECT id FROM word WHERE parent_id $condition1 AND deleted = :not_del)";
            $condition3 = "IN (SELECT id FROM word WHERE parent_id $condition2 AND deleted = :not_del)";
        }
        $condition1 = "$columnName $condition1";
        $condition2 = "$columnName $condition2";
        $condition3 = "$columnName $condition3";
        if ($depth == 3) {
            $condition = $condition3;
            if ($withParent) {
                $condition = $condition1 . ' OR ' . $condition2 . ' OR ' . $condition3;
            }
        } elseif ($depth == 2) {
            $condition = $condition2;
            if ($withParent) {
                $condition = $condition1 . ' OR ' . $condition2;
            }
        } else {
            $condition = $condition1;
        }
        $bind = [$bindName => $parentId];
        if ($depth != 1) {
            $bind += [':not_del' => Status::NOT_DELETED];
        }

        return [
            'condition' => $condition,
            'bind' => $bind
        ];
    }

    public static function getConditionLikeName($columnName, $parentName, $depth = 1, $withParent = false)
    {
        if (isset(Word::FIELD_WORD[ucfirst($parentName)])) {
            $parentId = Word::FIELD_WORD[ucfirst($parentName)];
            return self::getConditionById($columnName, $parentId, $depth, $withParent);
        } else {
            $condition = NULL;
            $bind = [];
            $bindName = ':' . $columnName . (int)$depth;
            if (empty($parentName) == false) {
                $condition1 = "IN (SELECT id FROM word WHERE name LIKE $bindName AND deleted = :not_del)";
                $condition2 = "IN (SELECT id FROM word WHERE parent_id $condition1 AND deleted = :not_del)";
                $condition3 = "IN (SELECT id FROM word WHERE parent_id $condition2 AND deleted = :not_del)";
                $condition1 = "$columnName $condition1";
                $condition2 = "$columnName $condition2";
                $condition3 = "$columnName $condition3";
                if ($depth == 3) {
                    $condition = $condition3;
                    if ($withParent) {
                        $condition = $condition1 . ' OR ' . $condition2 . ' OR ' . $condition3;
                    }
                } elseif ($depth == 2) {
                    $condition = $condition2;
                    if ($withParent) {
                        $condition = $condition1 . ' OR ' . $condition2;
                    }
                } else {
                    $condition = $condition1;
                }
                $bind = [$bindName => $parentName . '%', ':not_del' => Status::NOT_DELETED];
            }
        }

        return [
            'condition' => $condition,
            'bind' => $bind
        ];
    }

    public static function getParentName ($model, $n = 0) {
        $parentNames = [];
        $parentIds = [];
        for ($i = 1; $i <= self::MAX_NUMBER_PARENTS; $i++) {
            if ($model->parent_id <= 0) {
                $parentNames[] = self::LABEL_FIELD_WORD[$model->parent_id];
                $parentIds[] = $model->parent_id;
                break;
            }
            $model = $model->parent;
            $parentNames[] = $model->name;
            $parentIds[] = $model->id;
        }
        return array('name' => $parentNames[count($parentNames) - $n - 1], 'id' => $parentIds[count($parentIds) - $n - 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'value' => 'Значение',
            'description' => 'Описание',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'created_by' => 'Создал',
            'updated_by' => 'Обновил',
            'deleted' => 'Удален',
            'parent_id' => 'Родительская категория',
            'first_category' => 'Раздел',
            'second_category' => 'Категория',
            'third_category' => 'Папка',
            'category_name' => 'Раздел',
            'parent_name' => 'Папка'
        ];
    }

    public function getParent()
    {
        return $this->hasOne(Word::class, ['id' => 'parent_id']);
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdater()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function formName()
    {
        return '';
    }
}
