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
        'not' => 0,
        'kind' => -11,
//        'group' => -12,
//        'type' => -13,
        'name' => -14,
        'state' => -15,
        'department' => -16,
        'crew' => -17,

        's_scale' => -18,
        's_accuracy' => -19,
        's_type' => -20,    // шкала квадратичная
        'v_kind' => -21,    // поверка поверка
    ];

    const LABEL_FIELD_WORD = [
        self::FIELD_WORD['not'] => 'нет',   // TODO: ключи с маленькой буквы
        self::FIELD_WORD['kind'] => 'Вид СИ',
        self::FIELD_WORD['name'] => 'Названия приборов',
        self::FIELD_WORD['state'] => 'Состояние',
        self::FIELD_WORD['department'] => 'Цеха',
        self::FIELD_WORD['crew'] => 'Бригада',

        self::FIELD_WORD['s_scale'] => 'Шкалы',
        self::FIELD_WORD['s_accuracy'] => 'Точность',
        self::FIELD_WORD['s_type'] => 'Тип',
        self::FIELD_WORD['v_kind'] => 'Вид поверки',
    ];

    public $category_name, $parent_name;

    public static function getFieldWord($name)
    {
        if (isset(self::FIELD_WORD[$name])) {
            return self::FIELD_WORD[$name];
        }
        return NULL;
    }

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
            [['name', 'value', 'category_name', 'parent_name'], 'string', 'max' => 40],
            [['name'], 'unique', 'when' => function($model){return $model->isAttributeChanged('name');}],   // создан или изменен
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
                    if (Word::getParentByLevel($parent, 0, 3)->id !== self::getFieldWord($this->category_name)) {
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
                $this->parent_id = self::getFieldWord($this->category_name);
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
                $depth = Word::getDepth($this);
                if (isset($this->id)) {             // не для новых записей
                    for ($i = 1; $i <= self::MAX_NUMBER_PARENTS; $i++) {
                        if ($depth <= self::MAX_NUMBER_PARENTS) {
                            list('condition' => $condition, 'bind' => $bind) = Word::getConditionByParent([
                                'parents' => [$this->id],
                                'depth' => $i,
                            ]);
                            if (self::find()->andOnCondition($condition, $bind)->andFilterWhere(['deleted' => Status::NOT_DELETED])->one() !== NULL) {
                                $depth++;
                            } else {
                                break;
                            }
                        } else {
                            break;
                        }
                    }
                }

                if ($depth > self::MAX_NUMBER_PARENTS) {
                    $this->addError('parent_name', 'Превышена глубина вложенности');
                }
            }
        }
    }

    /**
     * @param array $params str columnName = parent_id, int parent = 0, int depth = 1, bool withParent = false
     * @return array
     */
    public static function getConditionByParentId($params = [])
    {
        $parent_id = 0;
        $columnName = 'parent_id';
        $depth = 1;
        $withParent = false;

        $arrayCondition = [];
        $bindValues = [];

        $withParent = isset($params['withParent']) ? filter_var($params['withParent'], FILTER_VALIDATE_BOOLEAN) : $withParent;
        foreach (['columnName', 'parent_id', 'depth'] as $item) {
            if (isset($params[$item])) {
                $$item = $params[$item];
            }
        }
        $hashColumnName = ':' . md5($columnName);
        $arrayCondition[0] = " = $hashColumnName";
        for ($i = 1; $i < $depth; $i++) {
            $arrayCondition[$i] = "IN (SELECT id FROM word WHERE parent_id {$arrayCondition[$i-1]} AND deleted = :deleted)";
        }

        $arrayCondition[0] = $columnName . $arrayCondition[0];
        $condition = end($arrayCondition);
        if ($withParent) {
            $condition = implode(" OR $columnName ", $arrayCondition);
        }

        $bindValues[$hashColumnName] = $parent_id;
        if ($depth > 1) {
            $bindValues['deleted'] = Status::NOT_DELETED;
        }

        return [
            'condition' => preg_replace('/[\s]+/', ' ', $condition),
            'bind' => $bindValues
        ];

    }

    /** Глубина считается от 1 элемента $parents
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

        $parentsKeyLast = 0;     // ключ последнего значащего родителя
        foreach ($parents as $key => $item) {
            if ($key >= $depth) {
                break;
            }
            $parentsKeyLast = $key;
        }
        $previousKey = 0;                               // если $item == 'not', то $previousKey - ключ последнего значащего родителя
        foreach ($parents as $key => $item) {
            if ($item == array_search(Status::NOT_CATEGORY, Word::FIELD_WORD)) {    // =='not'
                if ($key > 0 && $depth > $key) {
                    $depth = $key;      // ограничение глубины для NOT_CATEGORY
                    $parentsKeyLast = $previousKey;
                }
            }
            $previousKey = $key;
        }
        $conditionError = ['condition' => '0=1', 'bind' => []];
        if (is_array($parents) == false || empty($columnName)) {
            return $conditionError;
        }

        if (isset($parents[0]) && isset(Word::FIELD_WORD[$parents[0]])) {   // поиск по категории Word::FIELD_WORD
            $parents[0] = Word::getFieldWord($parents[0]);
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
            $deleted = 'deleted = :not_del';
            if ($parentExpression || $likeExpression) {
                $deleted = 'AND ' . $deleted;
            }
            $arrayCondition[$i] = "IN (SELECT id FROM word WHERE $parentExpression $likeExpression $deleted)";
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
     * @param Word $model
     * @return int 1 ... 1 + MAX_NUMBER_PARENTS
     */
    public static function getDepth($model)
    {
        for ($i = 1; $i <= 1 + self::MAX_NUMBER_PARENTS; $i++) {
            if ($model->parent_id <= 0) {
                break;
            }
            $model = $model->parent;
        }
        return $i;
    }

    /**
     * @param Word $model
     * @param $level = Status::ALL
     * @param int $maxNumberParents = self::MAX_NUMBER_PARENTS
     * @return Word|Word[]|null
     */
    public static function getParentByLevel($model, $level = Status::ALL, $maxNumberParents = self::MAX_NUMBER_PARENTS)
    {
        $parents = [];
        for ($i = 1; $i <= $maxNumberParents; $i++) {
            if ((int)$model->parent_id <= 0) {   // || parent_id == NULL
                $category = new Word();
                $category->id = (int)$model->parent_id;
                if (isset(self::LABEL_FIELD_WORD[$category->id])) {
                    $category->name = self::LABEL_FIELD_WORD[$category->id];
                }
                $parents[] = $category;
                break;
            }
            $model = $model->parent;
            $parents[] = $model;
        }
        $parents = array_reverse($parents);
        if ($level == Status::ALL) {
            return $parents;
        }
        return isset($parents[$level]) ? $parents[$level] : NULL;
    }

    /**
     * @param Word $model
     * @param Word $parent
     * @param int $maxNumberParents = self::MAX_NUMBER_PARENTS - 1
     * @return bool
     */
    public static function checkIsParent($model, $parent, $maxNumberParents = self::MAX_NUMBER_PARENTS - 1)
    {
        if (isset($model)) {
            $currentParent = $model->parent;
            for ($i = 1; $i <= $maxNumberParents; $i++) {
                if (isset($currentParent)) {
                    if ($currentParent->id === $parent->id) {
                        return true;
                    }
                    $currentParent = $currentParent->parent;
                } else {
                    break;
                }
            }
        }
        return false;
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
            'parent' => 'Родительская категория',
            'category1' => 'Раздел',
            'category2' => 'Папка 1',
            'category3' => 'Папка 2',
            'category4' => 'Папка 3',
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

    /** Запросы для получения дочерних элементов
     * @param $condition int|string|array $condition = 1 === ['id' = 1]
     * @param $level int глубина поиска
     * @param $deleted int
     * @return array Query [0 => parent, 1 => children, 2 => grandchildren]
     */
    public static function getQueriesToGetChildren($condition, $level = 1, $deleted = Status::NOT_DELETED)
    {
        $queries = [];
        $arrayDeleted = [];

        if (! is_array($condition)) {
            $condition = ['id' => $condition];
        }

        if ($deleted === Status::NOT_DELETED || $deleted === Status::DELETED) {
            $arrayDeleted['deleted'] = $deleted;
        }

        $queries[0] = self::find()->select('id')->where($condition + $arrayDeleted);
        for ($currentLevel = 1; $currentLevel <= $level; $currentLevel++) {
            $queries[$currentLevel] = self::find()->select('id')->where(['parent_id' => $queries[$currentLevel - 1]] + $arrayDeleted);
        }

        return $queries;
    }

    /** Запросы для получения дочерних элементов, если родитель не в базе, а в Word::FIELD_WORD
     * @param $condition int|string|array $condition = 1 === ['id' = 1]
     * @param $level int глубина поиска
     * @param $deleted int
     * @return array Query [0 => [номера] НЕ ЗАПРОС!, 1 => children, 2 => grandchildren]
     */
    public static function getQueriesToGetChildrenIfParentIsVirtual($condition, $level = 1, $deleted = Status::NOT_DELETED)
    {
        $queries = [];
        $arrayDeleted = [];

        if (! is_array($condition)) {
            $condition = ['id' => $condition];
        }

        if ($deleted === Status::NOT_DELETED || $deleted === Status::DELETED) {
            $arrayDeleted['deleted'] = $deleted;
        }

        $numbers = [];
        if (strlen($condition['id'])) {
            $numbers[] = $condition['id'];
        } elseif ($condition[0] === 'like' && $condition[1] === 'name') {
            $numbers = Word::getNumbersBySimilarLabel($condition[2]);
        } else {
            return $queries;
        }

        $queries[0] = $numbers;
        for ($currentLevel = 1; $currentLevel <= $level; $currentLevel++) {
            $queries[$currentLevel] = self::find()->select('id')->where(['parent_id' => $queries[$currentLevel - 1]] + $arrayDeleted);
        }

        return $queries;
    }

    /** Нечеткий поиск в Word::LABEL_FIELD_WORD
     * @param $name
     * @return array номера похожих labels
     */
    public static function getNumbersBySimilarLabel($name)
    {
        $numbers = [];

        if (mb_strlen($name) === 0) {
            return array_keys(Word::LABEL_FIELD_WORD, $name);
        }
        if (preg_match('/^%+$/', $name)) {
            return array_keys(Word::LABEL_FIELD_WORD);
        }

        $hasPercentAtStart = false;
        $hasPercentAtEnd = false;
        if (mb_strpos($name, '%') === 0) {
            $name = mb_substr($name, 1);
            $hasPercentAtStart = true;
        }
        if (mb_strpos($name, '%') === mb_strlen($name) - 1) {
            $name = mb_substr($name, 0, mb_strlen($name) - 1);
            $hasPercentAtEnd = true;
        }

        foreach (Word::LABEL_FIELD_WORD as $key => $label) {
            if ($hasPercentAtStart && $hasPercentAtEnd) {
                if (mb_strpos($label, $name) !== false) {
                    $numbers[] = $key;
                }
            } elseif ($hasPercentAtStart) {
                if (mb_strpos($label, $name) === mb_strlen($label) - mb_strlen($name)) {
                    $numbers[] = $key;
                }
            } elseif ($hasPercentAtEnd) {
                if (mb_strpos($label, $name) === 0) {
                    $numbers[] = $key;
                }
            } else {
                if (
                    (mb_strpos($label, $name) === 0)
                    && mb_strpos($label, $name) === mb_strlen($label) - mb_strlen($name)
                ) {
                    $numbers[] = $key;
                }
            }
        }
        return $numbers;
    }
}
