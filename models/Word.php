<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
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
 * @property int $parent_type тип записи: элемент или категория с определенным содержимым
 * @property int $parent_id
 *
 * @property Device[] $devices magic property
 * @property User|null $creator magic property
 * @property User|null $updater magic property
 */
class Word extends ActiveRecord
{
    const MAX_LINES_IN_LIST = 100;      // максимум строк в getAllNames()

    const ALL = -1;                     // для всех свойств

    const NOT_DELETED = 0;              // по умолчанию word->deleted
    const DELETED = 1;

    const DEPARTMENT = 'department';        // word->name
    const SCALE = 'scale';
    const DEVICE_TYPE = 'device_type';
    const DEVICE_NAME = 'device_name';

    const ELEMENT = 0;                      // word->parent_type
    const CATEGORY_OF_ELEMENTS = 1;
    const CATEGORY_OF_CATEGORIES = 2;
    const CATEGORY_OF_ALL = 3;

    const LABELS_TYPE = [
        self::ELEMENT => 'Элемент',
        self::CATEGORY_OF_ELEMENTS => 'Категория элементов',
        self::CATEGORY_OF_CATEGORIES => 'Категория категорий',
        self::CATEGORY_OF_ALL => 'Категория для элементов и категорий',
    ];

    public $firstCategory, $secondCategory;

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
            [['name', 'parent_type', 'parent_id'], 'required'],
            [['description'], 'string'],
            [['name', 'value'], 'string', 'max' => 255],
            [['parent_type'], 'integer', 'min' => 0, 'max' => 3],
            [['parent_id'], 'validateParentId'],
            [['parent_type'], 'validateParentType'],
            [['firstCategory', 'secondCategory'], 'safe']
        ];
    }

    public function validateParentId($attribute)        // проверка соответствия id, type
    {
        if (!$this->hasErrors()) {
            if ($this->parent_id == 0) {                        // если выбрана корневая категория
                if ($this->parent_type == self::ELEMENT) {
                    $this->addError($attribute, 'Элемент не может быть корневым');
                }
            } else {                                            // если вложенная категория или элемент
                if (
                    $this->parent_type == self::CATEGORY_OF_CATEGORIES ||
                    $this->parent_type == self::CATEGORY_OF_ALL
                ) {
                    $this->addError($attribute, 'Элемент не может содержать категории');
                }
                $parent = self::findOne($this->parent_id);
                if ($parent === NULL) {
                    $this->addError($attribute, 'Категория не найдена');
                } else {
                    if ($this->parent_type == self::CATEGORY_OF_ELEMENTS) {
                        if (
                            $parent->parent_type == self::ELEMENT ||
                            $parent->parent_type == self::CATEGORY_OF_ELEMENTS
                        ) {
                            $this->addError($attribute, 'Категория не подходит');
                        }
                    }
                    if ($this->parent_type == self::ELEMENT) {
                        if ($parent->parent_type == self::ELEMENT) {
                            $this->addError($attribute, 'Категория не подходит');
                        }
                    }
                }
            }
        }
    }

    public function validateParentType($attribute)          // проверка изменения type
    {
        if (!$this->hasErrors()) {
            $old_parent_type = $this->getOldAttribute('parent_type');
            $old_deleted = $this->getOldAttribute('deleted');
            if ($old_parent_type !== NULL) {                        // обновление записи

                // если новый тип не позволяет иметь дочерние элементы, то проверяем есть ли они
                if ($this->parent_type == self::ELEMENT) {
                    $child = self::findOne(['parent_id' => $this->id, 'parent_type' => self::ELEMENT]);
                    if ($child !== NULL) {
                        $this->addError($attribute, 'Категория уже содержит элементы');
                    }
                }

                // если новый тип не позволяет иметь дочерние категории, то проверяем есть ли они
                if ($this->parent_type == self::ELEMENT && $this->parent_type == self::CATEGORY_OF_ELEMENTS) {
                    $child = self::findOne(['parent_id' => $this->id, 'parent_type' => [
                        self::CATEGORY_OF_ELEMENTS, self::CATEGORY_OF_CATEGORIES, self::CATEGORY_OF_ALL
                    ]]);
                    if ($child !== NULL) {
                        $this->addError($attribute, 'Категория уже содержит категории');
                    }
                }

                // если новый тип не позволяет использование в других таблицах, ищем в таблице [$this->value]
                if ($this->parent_type != self::ELEMENT) {
                    $category = $this->value;              // раздел, который предназначен для соответствующей таблицы
                    if ($this->parent_id != 0) {
                        $parent = self::findOne($this->parent_id);
                        $category = $parent->value;
                        if ($parent->parent_id != 0) {
                            $parentOfParent = self::findOne($parent->parent_id);
                            $category = $parentOfParent->value;
                        }
                    }
                    switch ($this->value) {
                        case 'device': $record = Device::findOne(['parent_id' => $this->id, 'parent_type' => [
                            self::CATEGORY_OF_ELEMENTS, self::CATEGORY_OF_CATEGORIES, self::CATEGORY_OF_ALL
                        ]]);
                        break;
                    }
                    if (isset($record)) {
                        $this->addError($attribute, 'Элемент используется в ' . get_class($record));
                    }
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
            'parent_type' => 'Тип',
            'parent_id' => 'Родительская категория',
            'firstCategory' => 'Раздел',
            'secondCategory' => 'Категория'
        ];
    }

    /**
     * Gets arr[id] = names
     * @param int $parent_type
     * @param int $parent_id
     * @param int $limit
     * @return array
     */
    public static function getAllNames($parent_type = self::ALL, $parent_id = self::ALL, $limit = self::MAX_LINES_IN_LIST)
    {
        $arrWhere = ['deleted' => Department::NOT_DELETED];
        if ($parent_type != self::ALL) {
            if ($parent_type == self::CATEGORY_OF_ALL) {
                $arrWhere['parent_type'] = [self::CATEGORY_OF_ELEMENTS, self::CATEGORY_OF_CATEGORIES, self::CATEGORY_OF_ALL];
            } else {
                $arrWhere['parent_type'] = $parent_type;
            }
        }
        if ($parent_id != self::ALL) {
            $arrWhere['parent_id'] = $parent_id;
        }

        $query = self::find()->select(['id', 'name', 'parent_type', 'parent_id'])->where($arrWhere)->limit($limit)->asArray()->all();
        $outArray = array();

        foreach ($query as $key => $item) {
            $outArray[$item['id']] = $item['name'];
        }
        return $outArray;
    }

    /**
     * Gets query for [[Devices]].
     *
     * @return ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(Device::class, ['id_department' => 'id']);
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
