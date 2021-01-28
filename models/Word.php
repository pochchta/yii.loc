<?php

namespace app\models;

use Yii;
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
        'Scale' => -11,
        'Department' => -12,
        'Type' => -13,
        'Name' => -14,
        'Position' => -15,
        'Accuracy' => -16,
    ];

    const LABEL_FIELD_WORD = [
        self::FIELD_WORD['Scale'] => 'Шкалы',
        self::FIELD_WORD['Department'] => 'Цеха',
        self::FIELD_WORD['Type'] => 'Типы приборов',
        self::FIELD_WORD['Name'] => 'Названия приборов',
        self::FIELD_WORD['Position'] => 'Позиция',
        self::FIELD_WORD['Accuracy'] => 'Точность',
    ];

    public $firstCategory, $secondCategory, $thirdCategory;

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
            [['name', 'parent_id'], 'required'],
            [['name', 'value'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['description'], 'string'],
            [['firstCategory', 'secondCategory', 'thirdCategory', 'parent_id'], 'integer'],
            [['parent_id'], 'validateParent'],
            [['parent_id'], 'validateDepth'],
        ];
    }

    public function validateParent()
    {
        if (!$this->hasErrors()) {
            $parentAttribute = 'firstCategory';
            if ($this->parent_id < 0) {             // есть "виртуальная" родительская категория
                if (in_array($this->parent_id, self::FIELD_WORD) == false) {
                    $this->addError($parentAttribute, 'Такого корневого раздела нет');
                }
            } elseif ($this->parent_id > 0) {       // есть родительская категория
                $parentAttribute = 'secondCategory';
                $parent = $this->parent;
                if ($parent === NULL) {
                    $this->addError($parentAttribute, 'Родительская категория не найдена');
                }
                if ($parent->parent_id > 0) {
                    $parentAttribute = 'thirdCategory';
                    $parent = $parent->parent;
                    if ($parent === NULL) {
                        $this->addError($parentAttribute, 'Родительская категория не найдена');
                    }
                }
            }
        }
    }
/*
 * Шкала
 * Электрич
 * Ток
 * 0-5 мА
 * */
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
                        'parent_id IN (SELECT id FROM word WHERE parent_id = :id AND deleted = :del) AND deleted = :del',
                        [':id' => $this->id, ':del' => Status::NOT_DELETED]
                    )->one() !== NULL) {
                        $depth++;
                    }
                }
                if ($depth <= self::MAX_NUMBER_PARENTS) {
                    if (self::find()->andOnCondition(
                        'parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id = :id AND deleted = :del) AND deleted = :del) AND deleted = :del',
                        [':id' => $this->id, ':del' => Status::NOT_DELETED])->one() !== NULL) {
                        $depth++;
                    }
                }
                if ($depth > self::MAX_NUMBER_PARENTS) {
                    $this->addError($depthAttribute, 'Превышена глубина вложенности');
                }
            }
        }
    }

    /** Получение условия для запроса дочерних элементов по родительской категории и глубине
     * @param $parentName
     * @param int $depth
     * @param bool $withParent
     * @return array
     */
    public static function getCondition($parentName, $depth = 1, $withParent = false)
    {
        $parentName = ucfirst($parentName);
        $condition = NULL;
        $bind = [];
        $parentId = NULL;
        if (isset(Word::FIELD_WORD[$parentName])) {
            $parentId = Word::FIELD_WORD[$parentName];
        } elseif ($parent = Word::findOne(['name' => $parentName])) {
            $parentId = $parent->id;
        }
        if (isset($parentId)) {
            if ($parentId == Status::NOT_CATEGORY || $parentId == Status::ALL) {
                $condition1 = 'parent_id < :id';
                $condition2 = 'parent_id IN (SELECT id FROM word WHERE parent_id < :id AND deleted = :del)';
                $condition3 = 'parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id < :id AND deleted = :del) AND deleted = :del)';
            } else {
                $condition1 = 'parent_id = :id';
                $condition2 = 'parent_id IN (SELECT id FROM word WHERE parent_id = :id AND deleted = :del)';
                $condition3 = 'parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id = :id AND deleted = :del) AND deleted = :del)';
            }
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
            $bind = [':id' => $parentId];
            if ($depth != 1) {
                $bind += [':del' => Status::NOT_DELETED];
            }
        }

        return [
            'condition' => $condition,
            'bind' => $bind
        ];
    }

    /**
     * Получение названий дочерних элементов
     * @param int $parentId Id родителя
     * @param int $depth Глубина поиска
     * @param bool $withParent Поиск только на указанной глубине (false) или включая всех родителей (true)
     * @param null $passId Пропуск определенного id
     * @return array
     */
    public static function getAllNames($parentId = Status::NOT_CATEGORY, $depth = 1, $withParent = false, $passId = NULL)
    {
        if ($parentId == Status::NOT_CATEGORY || $parentId == Status::ALL) {
            $condition1 = 'parent_id < :id';
            $condition2 = 'parent_id IN (SELECT id FROM word WHERE parent_id < :id AND deleted = :del)';
            $condition3 = 'parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id < :id AND deleted = :del) AND deleted = :del)';
        } else {
            $condition1 = 'parent_id = :id';
            $condition2 = 'parent_id IN (SELECT id FROM word WHERE parent_id = :id AND deleted = :del)';
            $condition3 = 'parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id = :id AND deleted = :del) AND deleted = :del)';
        }

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
        $condition = '(' . $condition . ') AND deleted = :del';

        $query = self::find()->select(['id', 'name', 'parent_id'])->limit(Yii::$app->params['maxLinesView'])
            ->andOnCondition(
                $condition,
                [':id' => $parentId, ':del' => Status::NOT_DELETED]
            )
            ->asArray()->all();

        $outArray = array();
        foreach ($query as $key => $item) {
            if ($item['id'] === $passId) {
                continue;
            }
            $outArray[$item['id']] = $item['name'];
        }
        return $outArray;
    }

    /**
     * Возвращает списки для фильтров словаря и условия для фильтрации;
     * модификация $params[firstCategoryName, ...] (приведение к int или если значения не верны)
     * @param $params array ссылка на массив параметров запроса
     * @param $category int корневой раздел словаря
     * @return array ['condition' => string, 'bind' => array]
     */
    public static function getArrFilters(& $params, $category)
    {
        $arrFirstCategory = [Status::ALL => 'все', Status::NOT_CATEGORY => 'нет'];
        $arrSecondCategory = [];
        $arrThirdCategory = [];

        if (in_array($category, self::FIELD_WORD)) {

            // получение массивов фильтров
            $categoryName = array_search($category, self::FIELD_WORD);

            $firstCategory = & $params['first' . $categoryName];
            $secondCategory = & $params['second' . $categoryName];
            $thirdCategory = & $params['third' . $categoryName];

            $firstCategory = $firstCategory ?? Status::ALL;
            $secondCategory = $secondCategory ?? Status::ALL;
            $thirdCategory = $thirdCategory ?? Status::ALL;

            $firstCategory = (int) $firstCategory;
            $secondCategory = (int) $secondCategory;
            $thirdCategory = (int) $thirdCategory;

            $arrFirstCategory += self::getAllNames($category);
            if ($firstCategory == Status::NOT_CATEGORY) {
                $arrThirdCategory = self::getAllNames($category, 1);
                $secondCategory = Status::ALL;
            } else {
                if ($firstCategory == Status::ALL) {
                    $arrSecondCategory = self::getAllNames($category, 2);
                } else {
                    $arrSecondCategory = self::getAllNames($firstCategory);
                    if (empty($arrSecondCategory) == false) {
                        $arrSecondCategory = [Status::NOT_CATEGORY => 'нет'] + $arrSecondCategory;
                    }
                }
                if (isset($arrSecondCategory[$secondCategory]) == false) {
                    $secondCategory = Status::ALL;
                }
                if ($secondCategory == Status::NOT_CATEGORY) {
                    $arrThirdCategory = self::getAllNames($firstCategory, 1);
                } elseif ($secondCategory == Status::ALL) {
                    if ($firstCategory == Status::ALL) {
                        $arrThirdCategory = self::getAllNames($category, 3, true);
                    } else {
                        $arrThirdCategory = self::getAllNames($firstCategory, 2, true);
                    }
                } else {
                    $arrThirdCategory = self::getAllNames($secondCategory);
                }
            }
            if (isset($arrThirdCategory[$thirdCategory]) == false) {
                $thirdCategory = Status::ALL;
            }
            $arrSecondCategory = [Status::ALL => 'все'] + $arrSecondCategory;
            $arrThirdCategory = [Status::ALL => 'все'] + $arrThirdCategory;

            // вычисление parentId для фильтра
            $conditionDepth = NULL;
            $conditionParentId = NULL;
            if ($thirdCategory != Status::ALL) {
                $conditionParentId = $thirdCategory;
                $conditionDepth = 0;
            } elseif ($secondCategory != Status::ALL) {
                if ($secondCategory == Status::NOT_CATEGORY) {
                    $conditionParentId = $firstCategory;
                    $conditionDepth = 1;
                } else {
                    $conditionParentId = $secondCategory;
                    $conditionDepth = 2;
                }
            } elseif ($firstCategory != Status::ALL) {
                if ($firstCategory == Status::NOT_CATEGORY) {
                    $conditionParentId = $category;
                    $conditionDepth = 1;
                } else {
                    $conditionParentId = $firstCategory;
                    $conditionDepth = 3;
                }
            }

            // подготовка фильтров
            $condition = NULL;
            $bind = [":{$categoryName}" => $conditionParentId, ':del' => Status::NOT_DELETED];
            $columnName = strtolower($categoryName) . '_id';    // categoryName проверяется по списку self::FIELD_WORD
            $condition1 = "{$columnName} IN (SELECT id FROM word WHERE parent_id = :{$categoryName} AND deleted = :del)";
            $condition2 = "{$columnName} IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id = :{$categoryName} AND deleted = :del) AND deleted = :del)";
            $condition3 = "{$columnName} IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM word WHERE parent_id = :{$categoryName} AND deleted = :del) AND deleted = :del) AND deleted = :del)";
            if ($conditionDepth === 0) {            // word
                $condition = "{$columnName} = :{$categoryName}";
                $bind = [":{$categoryName}" => $conditionParentId];     // перезапись bind
            }
            elseif ($conditionDepth === 1) {
                $condition = $condition1;
            } elseif($conditionDepth === 2) {
                $condition = $condition1 . ' OR ' . $condition2;
            } elseif ($conditionDepth === 3) {
                $condition = $condition1 . ' OR ' . $condition2 . ' OR ' . $condition3;
            }

        }

        return [
            'array' => compact('arrFirstCategory', 'arrSecondCategory', 'arrThirdCategory'),
            'condition' => compact('condition', 'bind')
        ];
    }

    /**
     * заполнение параметров из модели
     * @param array $params модифицируемые параметры запроса
     * @param Word $model
     * @param int $category значение из self::FIELD_WORD[]
     */
    public static function setParams(& $params, $model, $category) {
        if (in_array($category, self::FIELD_WORD) && $model !== NULL) {
            $categoryName = array_search($category, self::FIELD_WORD);

            $firstCategory = & $params['first' . $categoryName];
            $secondCategory = & $params['second' . $categoryName];
            $thirdCategory = & $params['third' . $categoryName];

            $thirdCategory = $model->id;
            if ($model->parent === NULL) {                                  //      /слово
                $firstCategory  = Status::NOT_CATEGORY;
                $secondCategory = Status::ALL;
            } elseif ($model->parent->parent === NULL) {                    //      //слово
                $firstCategory = $model->parent_id;
                $secondCategory = Status::NOT_CATEGORY;
            } else {                                                        //      ///слово
                $firstCategory = $model->parent->parent_id;
                $secondCategory = $model->parent_id;
            }
        }
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
            'firstCategory' => 'Раздел',
            'secondCategory' => 'Категория',
            'thirdCategory' => 'Папка',
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
}
