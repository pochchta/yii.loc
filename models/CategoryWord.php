<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "CategoryWord".
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
 * @property CategoryWord $parent magic property
 * @property User|null $creator magic property
 * @property User|null $updater magic property
 */
class CategoryWord extends ActiveRecord
{
    const ALL = -1;                     // для всех свойств

    const NOT_CATEGORY = 0;

    const NOT_DELETED = 0;              // по умолчанию CategoryWord->deleted
    const DELETED = 1;

    const MAX_NUMBER_PARENTS = 3;       // максимальный уровень вложенности

    const FIELD_WORD = [
        'Scale' => '-2',
        'Department' => '-3',
        'Device_type' => '-4',
        'Device_name' => '-5',
    ];

    const LABEL_FIELD_WORD = [
        self::FIELD_WORD['Scale'] => 'Шкалы',
        self::FIELD_WORD['Department'] => 'Цеха',
        self::FIELD_WORD['Device_type'] => 'Типы приборов',
        self::FIELD_WORD['Device_name'] => 'Названия приборов',
    ];

    public $firstCategory, $secondCategory;

    public static function tableName()
    {
        return 'category_word';
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
            [['description'], 'string'],
            [['name', 'value'], 'string', 'max' => 255],
            [['firstCategory', 'secondCategory', 'parent_id'], 'integer'],
            [['parent_id'], 'validateParent']
        ];
    }

    public function validateParent()
    {
        if (!$this->hasErrors()) {
            $parentAttribute = 'firstCategory';
            if ($this->parent_id < 0) {             // корневой раздел
                if (in_array($this->parent_id, array_keys(self::LABEL_FIELD_WORD)) == false) {
                    $this->addError($parentAttribute, 'Такого корневого раздела нет');
                }
            } elseif ($this->parent_id > 0) {       // есть родительская категория
                $parentAttribute = 'secondCategory';

                $parent = $this->parent;
                if ($parent === NULL) {
                    $this->addError($parentAttribute, 'Родительская категория не найдена');
                }
                if (in_array($parent->parent_id, array_keys(self::LABEL_FIELD_WORD)) == false) {
                    $this->addError($parentAttribute, 'Родительский раздел не корневой');
                }

                $child = self::findOne(['parent_id' => $this->id, 'deleted' => self::NOT_DELETED]);
                if ($child !== NULL) {
                    $this->addError($parentAttribute, 'Категория уже содержит дочерние категории');
                }
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
            'secondCategory' => 'Категория'
        ];
    }

    /**
     * Gets arr[id] = names
     * @param int $parent_id
     * @param int $depth без родительских категорий
     * @param int $pass_id пропускаемый id
     * @return array
     */
    public static function getAllNames($parent_id = self::NOT_CATEGORY, $depth = 1, $pass_id = NULL)
    {
        $query = self::find()->select(['id', 'name', 'parent_id'])->where(['deleted' => self::NOT_DELETED])->limit(Yii::$app->params['maxLinesView']);

        if ($parent_id == self::NOT_CATEGORY) {
            if ($depth == 3) {
                $query->andOnCondition(
                    'parent_id IN (SELECT id FROM category_word WHERE deleted = :del AND parent_id IN (SELECT id FROM category_word WHERE deleted = :del AND parent_id < :id))',
                    [':id' => $parent_id, ':del' => self::NOT_DELETED]
                );
            } elseif ($depth == 2) {
                $query->andOnCondition(
                    'parent_id IN (SELECT id FROM category_word WHERE parent_id < :id AND deleted = :del)',
                    [':id' => $parent_id, ':del' => self::NOT_DELETED]
                );
            } else {
                $query->andOnCondition(
                    'parent_id < :id',
                    [':id' => $parent_id]
                );
            }
        } else {
            if ($depth == 2) {
                $query->andOnCondition(
                    'parent_id IN (SELECT id FROM category_word WHERE parent_id = :id AND deleted = :del)',
                    [':id' => $parent_id, ':del' => self::NOT_DELETED]
                );
            } else {
                $query->andOnCondition(
                    'parent_id = :id',
                    [':id' => $parent_id]
                );
            }
        }

        $query = $query->asArray()->all();

        $outArray = array();

        foreach ($query as $key => $item) {
            if ($item['id'] === $pass_id) {
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
        $arrFirstCategory = [self::ALL => 'все', self::NOT_CATEGORY => 'нет'];
        $arrSecondCategory = [];
        $arrThirdCategory = [];

        if (in_array($category, self::FIELD_WORD)) {

            // получение массивов фильтров
            $categoryName = array_search($category, self::FIELD_WORD);

            $firstCategory = & $params['first' . $categoryName];
            $secondCategory = & $params['second' . $categoryName];
            $thirdCategory = & $params['third' . $categoryName];

            $firstCategory = $firstCategory ?? self::ALL;
            $secondCategory = $secondCategory ?? self::ALL;
            $thirdCategory = $thirdCategory ?? self::ALL;

            $firstCategory = (int) $firstCategory;
            $secondCategory = (int) $secondCategory;
            $thirdCategory = (int) $thirdCategory;

            $arrFirstCategory += self::getAllNames($category);

            if ($firstCategory == self::NOT_CATEGORY) {
                $arrThirdCategory = Word::getAllNames($category, 1);
            } elseif ($firstCategory == self::ALL) {
                $arrSecondCategory = self::getAllNames($category, 2);
                $arrThirdCategory = Word::getAllNames($category, 3);
            } else {
                $arrSecondCategory = self::getAllNames($firstCategory);
                if (isset($arrSecondCategory[$secondCategory]) == false) {
                    $secondCategory = self::ALL;
                }
                if ($secondCategory == self::NOT_CATEGORY) {
                    $arrThirdCategory = Word::getAllNames($firstCategory, 1);
                } elseif ($secondCategory == self::ALL) {
                    $arrThirdCategory = Word::getAllNames($firstCategory, 2);
                } else {
                    $arrThirdCategory = Word::getAllNames($secondCategory);
                    if (isset($arrThirdCategory[$thirdCategory]) == false) {
                        $thirdCategory = self::ALL;
                    }
                }
            }
            if (empty($arrSecondCategory) == false) {
                $arrSecondCategory = [self::NOT_CATEGORY => 'нет'] + $arrSecondCategory;
            }
            $arrSecondCategory = [self::ALL => 'все'] + $arrSecondCategory;
            $arrThirdCategory = [self::ALL => 'все'] + $arrThirdCategory;

            // вычисление parentId для фильтра
            $conditionDepth = NULL;
            $conditionParentId = NULL;
            if ($thirdCategory != self::ALL) {
                $conditionParentId = $thirdCategory;
                $conditionDepth = 0;
            } elseif ($secondCategory != self::ALL) {
                if ($secondCategory == self::NOT_CATEGORY) {
                    $conditionParentId = $firstCategory;
                    $conditionDepth = 1;
                } else {
                    $conditionParentId = $secondCategory;
                    $conditionDepth = 2;
                }
            } elseif ($firstCategory != self::ALL) {
                $conditionParentId = $firstCategory;
                if ($firstCategory == self::NOT_CATEGORY) {
                    $conditionDepth = 1;
                } else {
                    $conditionDepth = 3;
                }
            }

            // подготовка фильтров
            $condition = NULL;
            $bind = [":{$categoryName}" => $conditionParentId, ':del' => self::NOT_DELETED];
            $columnName = strtolower($categoryName) . '_id';    // categoryName проверяется по списку self::FIELD_WORD
            $condition1 = "{$columnName} IN (SELECT id FROM word WHERE parent_id = :{$categoryName} AND deleted = :del) AND deleted = :del";
            $condition2 = "{$columnName} IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM category_word WHERE parent_id = :{$categoryName} AND deleted = :del) AND deleted = :del) AND deleted = :del";
            $condition3 = "{$columnName} IN (SELECT id FROM word WHERE parent_id IN (SELECT id FROM category_word WHERE parent_id IN (SELECT id FROM category_word WHERE parent_id = :{$categoryName} AND deleted = :del) AND deleted = :del) AND deleted = :del) AND deleted = :del";
            if ($conditionDepth === 0) {            // word
                $condition = "{$columnName} = :{$categoryName} AND deleted = :del";
            }
            elseif ($conditionDepth === 1) {        // category_word
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

    public static function getParentName ($model, $n = 0) {
        $parentNames = [];
        for ($i = 1; $i <= self::MAX_NUMBER_PARENTS; $i++) {
            if ($model->parent_id <= 0) {
                $parentNames[] = self::LABEL_FIELD_WORD[$model->parent_id];
                break;
            }
            $model = $model->parent;
            $parentNames[] = $model->name;
        }
        return $parentNames[count($parentNames) - $n - 1];
    }

/*    public function getDevices()
    {
        return $this->hasMany(Device::class, ['id_department' => 'id']);
    }*/

    public function getParent()
    {
        return $this->hasOne(self::class, ['id' => 'parent_id']);
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
