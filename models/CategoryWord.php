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
     * @param int $pass_id пропускаемый id
     * @return array
     */
    public static function getAllNames($parent_id = self::ALL, $pass_id = NULL)
    {
        $arrWhere = ['deleted' => self::NOT_DELETED];
        if ($parent_id == 0) {
            $arrWhere = ['and', 'deleted='.self::NOT_DELETED, ['<', 'parent_id', 0]];     // перезапись условия
        } elseif ($parent_id != self::ALL) {
           $arrWhere['parent_id'] = $parent_id;
        }

        $query = self::find()->select(['id', 'name', 'parent_id'])->where($arrWhere)->limit(Yii::$app->params['maxLinesView'])->asArray()->all();
        $outArray = array();

        foreach ($query as $key => $item) {
            if ($item['id'] === $pass_id) {
                continue;
            }
            $outArray[$item['id']] = $item['name'];
        }
        return $outArray;
    }

    public static function getArrFilters(& $params, $category)
    {
        $arrFirstCategory = [self::ALL => 'все', '0' => 'нет'];
        $arrSecondCategory = [];
        $arrThirdCategory = [];

        if (in_array($category, self::FIELD_WORD)) {
            $categoryName = array_search($category, self::FIELD_WORD);

            $arrFirstCategory += self::getAllNames($category);

            if ($params['first' . $categoryName] == self::ALL || $params['first' . $categoryName] == 0) {
                $params['second' . $categoryName] = self::ALL;
                $arrThirdCategory = Word::getAllNames($category, $params['first' . $categoryName] === '0' ? 1 : 3);
            } else {
                $arrSecondCategory = ['0' => 'нет'] + self::getAllNames($params['first' . $categoryName]);

                if ($arrSecondCategory[$params['second' . $categoryName]] === NULL) {
                    $params['second' . $categoryName] = self::ALL;
                }
                if ($params['second' . $categoryName] == self::ALL || $params['second' . $categoryName] == 0) {
                    $arrThirdCategory = Word::getAllNames($params['first' . $categoryName], $params['second' . $categoryName] === '0' ? 1 : 2);
                } else {
                    $arrThirdCategory = Word::getAllNames($params['second' . $categoryName]);
                    if ($arrThirdCategory[$params['third' . $categoryName]] === NULL) {
                        $params['third' . $categoryName] = self::ALL;
                    }
                }
            }
        }
        $arrSecondCategory = [self::ALL => 'все'] + $arrSecondCategory;
        $arrThirdCategory = [self::ALL => 'все'] + $arrThirdCategory;

        return compact('arrFirstCategory', 'arrSecondCategory', 'arrThirdCategory');
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
