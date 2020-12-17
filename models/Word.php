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
 * @property int $parent_type тип записи: элемент или категория с определенным содержимым
 * @property int $parent_id
 *
 * @property Device[] $devices magic property
 * @property User|null $creator magic property
 * @property User|null $updater magic property
 */
class Word extends ActiveRecord
{
    const MAX_LINES_IN_LIST = 100;      // максимум строк по умолчанию в getAllNames()

    const ALL = -1;                     // для всех свойств

    const NOT_DELETED = 0;              // по умолчанию word->deleted
    const DELETED = 1;

    const ELEMENT = 0;                      // word->parent_type
    const CATEGORY_OF_ELEMENTS = 1;
    const CATEGORY_OF_CATEGORIES = 2;
    const CATEGORY_OF_ALL = 3;

    const DEVICE_NAME = 'device_name_id';       // названия внешних полей (разделов)
    const DEVICE_TYPE = 'device_type_id';

    const LABELS_TYPE = [
        self::ELEMENT => 'Элемент',
        self::CATEGORY_OF_ELEMENTS => 'Категория элементов',
        self::CATEGORY_OF_CATEGORIES => 'Категория категорий',
        self::CATEGORY_OF_ALL => 'Категория для всего',
    ];

    public $firstCategory, $secondCategory;

    private $_parent;

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
            [['parent_type'], 'validateParentId'],
            [['parent_type'], 'validateParentType'],
            [['firstCategory', 'secondCategory', 'parent_id'], 'integer']
        ];
    }

    public function getParentRecord() {                  // вспомогательный метод для валидации
        if ($this->_parent === NULL) {
            $this->_parent = self::findOne(['id' => $this->parent_id, 'deleted' => self::NOT_DELETED]);
        }
        return $this->_parent;
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
                $parent = $this->getParentRecord();
                $parentAttribute = 'firstCategory';
                if ($parent === NULL) {
                    $this->addError($parentAttribute, 'Родительская категория не найдена');
                } else {
                    if ($parent->parent_id != 0) {
                        $parentAttribute = 'secondCategory';
                    }
                    if ($this->id == $this->parent_id) {
                        $this->addError($parentAttribute, 'Родительская категория совпадает с редактируемой категорией');
                    }
                    if ($parent->parent_type == self::ELEMENT) {
                        $this->addError($parentAttribute, 'Родительская категория - не категория');
                    }
                    if ($parent->parent_type == self::CATEGORY_OF_ELEMENTS) {
                        if ($this->parent_type != self::ELEMENT) {
                            $this->addError($parentAttribute, 'Родительская категория не может иметь вложенных категорий');
                        }
                    }
                    if ($parent->parent_type == self::CATEGORY_OF_CATEGORIES) {
                        if ($this->parent_type != self::CATEGORY_OF_ELEMENTS) {
                            $this->addError($parentAttribute, 'Родительская категория может иметь только вложенные категории');
                        }
                    }
                }
            }
        }
    }

    public function validateParentType($attribute)          // проверка при изменении type или deleted, изменение названия раздела не проверяется
    {
        if (!$this->hasErrors()) {
            $old_parent_type = $this->getOldAttribute('parent_type');
            $old_deleted = $this->getOldAttribute('deleted');
            if (
                $old_parent_type !== NULL &&                                                    // обновление записи
                ($this->parent_type != $old_parent_type || $this->deleted != $old_deleted)      // изменение type или deleted
            ) {

                // если элемент не позволяет иметь дочерние элементы, то проверяем есть ли они
                if (
                    $this->parent_type == self::ELEMENT ||
                    $this->parent_type == self::CATEGORY_OF_CATEGORIES ||
                    $this->deleted == self::DELETED
                ) {
                    $child = self::findOne(['parent_id' => $this->id, 'parent_type' => self::ELEMENT, 'deleted' => self::NOT_DELETED]);
                    if ($child !== NULL) {
                        $this->addError($attribute, 'Категория уже содержит элементы');
                    }
                }

                // если элемент не позволяет иметь дочерние категории, то проверяем есть ли они
                if (
                    $this->parent_type == self::ELEMENT ||
                    $this->parent_type == self::CATEGORY_OF_ELEMENTS ||
                    $this->deleted == self::DELETED
                ) {                                                       // дочерняя категория не может содержать категории, поэтому проверяем только категорию элементов
                    $child = self::findOne(['parent_id' => $this->id, 'parent_type' => self::CATEGORY_OF_ELEMENTS, 'deleted' => self::NOT_DELETED]);
                    if ($child !== NULL) {
                        $this->addError($attribute, 'Категория уже содержит категории');
                    }
                }
                // TODO закоментированный фрагмент не работает:
                // 1) валидатор работает только при изменении типа или deleted

                // если элемент не позволяет использование в других таблицах, ищем в таблице [$this->value]
/*                if ($this->parent_type != self::ELEMENT || $this->deleted == self::DELETED) {
                    $category = $this->value;              // раздел, который предназначен для соответствующей таблицы
                    if ($this->parent_id != 0) {
                        $parent = $this->getParentRecord();
                        $category = $parent->value;
                        if ($parent->parent_id != 0) {
                            $parentOfParent = self::findOne(['id' => $parent->parent_id, 'deleted' => self::NOT_DELETED]);
                            $category = $parentOfParent->value;
                        }
                    }
                    switch ($category) {
                        case self::DEVICE_NAME: $record = Device::findOne([self::DEVICE_NAME => $this->id, 'deleted' => self::NOT_DELETED]);
                        break;
                        case self::DEVICE_TYPE: $record = Device::findOne([self::DEVICE_TYPE => $this->id, 'deleted' => self::NOT_DELETED]);
                        break;
                    }
                    if (isset($record)) {
                        $this->addError($attribute, 'Элемент используется в ' . get_class($record));
                    }
                }*/
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
     * @param int $pass_id
     * @return array
     */
    public static function getAllNames($parent_type = self::ALL, $parent_id = self::ALL, $pass_id = NULL)
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

        $query = self::find()->select(['id', 'name', 'parent_type', 'parent_id'])->where($arrWhere)->limit(Yii::$app->params['maxLinesView'])->asArray()->all();
        $outArray = array();

        foreach ($query as $key => $item) {
            if ($item['id'] === $pass_id) {
                continue;
            }
            $outArray[$item['id']] = $item['name'];
        }
        return $outArray;
    }

/*    public function getDevices()
    {
        return $this->hasMany(Device::class, ['id_department' => 'id']);
    }*/

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
