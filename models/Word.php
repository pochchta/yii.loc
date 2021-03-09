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
        $bindName = ':' . $columnName . (int)$depth;

        if ($parentId == Status::ALL) {
            $parentId = 0;
            $arrayCondition[0] = "<= $bindName";
        } else {
            $arrayCondition[0] = "= $bindName";
        }
        for ($i = 1; $i < $depth; $i++) {
            $arrayCondition[] = "IN (SELECT id FROM word WHERE parent_id {$arrayCondition[$i-1]} AND deleted = :not_del)";
        }
        foreach ($arrayCondition as &$item) {
            $item = "$columnName $item";
        }

        $bind = [$bindName => $parentId];
        if ($depth > 1) {
            $bind += [':not_del' => Status::NOT_DELETED];
        }

        return [
            'condition' => $withParent ? implode(' OR ', $arrayCondition) : end($arrayCondition),
            'bind' => $bind
        ];
    }

    public static function getConditionLikeName($columnName, $parentName, $depth = 1, $withParent = false)
    {
        if (isset(Word::FIELD_WORD[ucfirst($parentName)])) {
            $parentId = Word::FIELD_WORD[ucfirst($parentName)];
            return self::getConditionById($columnName, $parentId, $depth, $withParent);
        } else {
            $bindName = ':' . $columnName . (int)$depth;
            $arrayCondition[0] = "IN (SELECT id FROM word WHERE name LIKE $bindName AND deleted = :not_del)";
            for ($i = 1; $i < $depth; $i++) {
                $arrayCondition[] = "IN (SELECT id FROM word WHERE parent_id {$arrayCondition[$i-1]} AND deleted = :not_del)";
            }
            foreach ($arrayCondition as &$item) {
                $item = "$columnName $item";
            }

            return [
                'condition' => $withParent ? implode(' OR ', $arrayCondition) : end($arrayCondition),
                'bind' => [$bindName => $parentName . '%', ':not_del' => Status::NOT_DELETED]
            ];
        }
    }

    /**
     * @param array $params array parents, str columnName = parent_id, int depth = 1, bool withParent = false
     * @return array|string[]
     */
    public static function getConditionByParent($params = [])
    {
        $parents = NULL;
        $columnName = 'parent_id';
        $depth = 1;
        $withParent = false;

        $arrayCondition = [];
        $bindNames = [];
        $bindValues = [];

        $withParent = isset($params['withParent']) ? filter_var($params['withParent'], FILTER_VALIDATE_BOOLEAN) : $withParent;
        foreach (['columnName', 'parents', 'depth'] as $item) {
            if (isset($params[$item])) {
                $$item = $params[$item];
            }
        }

        ksort($parents);
        foreach ($parents as $key => $item) {
            if (strlen($item) == 0) {
                unset($parents[$key]);   // удаление пустых parents[$key]
            }
        }
        $depth += (isset($parents[0]) ? 0 : 1);     // если не задана не абсолютная категория, то увеличиваем глубину

        $parentsKeyLast = array_key_last($parents);     // ключ последнего значащего родителя
        $previousKey = 0;                               // если $item == 'not', то $previousKey - ключ последнего значащего родителя
        foreach ($parents as $key => $item) {
            if (ucfirst($item) == array_search(Status::NOT_CATEGORY, Word::FIELD_WORD)) {    // =='not'
                if ($key > 0 && $depth > $key) {
                    $depth = $key;      // ограничение глубины для NOT_CATEGORY
                    $parentsKeyLast = $previousKey;
                }
            }
            $previousKey = $key;
        }
        $conditionError = ['condition' => '0=1', 'bind' => []];
        if ((isset($parents[0]) || isset($parents[1])) == false || empty($columnName)) {
            return $conditionError;
        }

        if (isset($parents[0]) && isset(Word::FIELD_WORD[$parents[0]])) {   // поиск по категории Word::FIELD_WORD
            $parents[0] = Word::FIELD_WORD[$parents[0]];
        }

        foreach ($parents as $key => $item) {   // дальше возможно изменение $parents[0] = 0, но $bindNames[0] останется прежним
            $bindNames[$key] = ':' . md5($key . $item . $columnName . $depth);      // генерация имен для подстановки
        }

        if (isset($parents[0])) {
            $arrayCondition[0] = "= $bindNames[0]";
            if ($parents[0] == Status::ALL) {
                $parents[0] = 0;
                $arrayCondition[0] = "<= $bindNames[0]";
            }
        }

        for ($i = 1; $i < $depth; $i++) {   // составление вложенных запросов
            $parentExpression = strlen($arrayCondition[$i-1]) ? "parent_id {$arrayCondition[$i-1]}" : '';
            $likeExpression = isset($parents[$i]) ? "name LIKE {$bindNames[$i]}" : '';
            if ($parentExpression && $likeExpression) {
                $parentExpression .= ' AND ';
            }
            $arrayCondition[$i] = "IN (SELECT id FROM word WHERE $parentExpression $likeExpression AND deleted = :not_del)";
        }

        foreach ($arrayCondition as $key => $item) {    // присоединение columnName к запросам
            $arrayCondition[$key] = "$columnName $item";
        }

        $condition = end($arrayCondition);
        if ($withParent) {
            foreach($arrayCondition as $key => $item) {
                if ($key < $parentsKeyLast) {
                    unset($arrayCondition[$key]);    // удаление "лишних" родительских категорий
                }
            }
            $condition = implode(' OR ', $arrayCondition);
        }

        if (strlen($condition) == 0) {  // # parents[0] = NULL, parents[1] = 'not'
            return $conditionError;
        }

        foreach ($bindNames as $key => $item) {     // формирование значений для подстановки
            if ($key < $depth) {
                $bindValues[$item] = $parents[$key];
                if ($key > 0) {
                    $bindValues[$item] .= '%';
                }
            }
        }
        if (strpos($condition, ':not_del') !== false) {
            $bindValues[':not_del'] = Status::NOT_DELETED;
        }

        return [
            'condition' => preg_replace('/[\s]+/', ' ', $condition),
            'bind' => $bindValues
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
