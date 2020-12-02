<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "department".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $description
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $deleted
 * @property int $parent_type
 * @property int $parent_id
 *
 * @property Device[] $devices magic property
 * @property User|null $creator magic property
 * @property User|null $updater magic property
 */
class Department extends ActiveRecord
{
    const ALL = -1;                     // для всех свойств

    const NOT_DELETED = 0;              // по умолчанию для свойства deleted
    const DELETED = 1;

    const ONLY_DEVICES = 0;             // по умолчанию для свойства parent_type [что может содержать цех]
    const ONLY_DEPARTMENT = 1;
//    const DEVICE_AND_DEPARTMENT = 2;

    const LABELS_TYPE = [
        self::ONLY_DEVICES => 'Цех',
        self::ONLY_DEPARTMENT => 'Категория',
//        self::DEVICE_AND_DEPARTMENT => 'Приборы и цеха',
    ];

    const MAX_LINES_IN_LIST = 100;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department';
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
            [['name', 'parent_type', 'parent_id'], 'required'],
            [['description'], 'string'],
            [['name', 'phone'], 'string', 'max' => 255],
            [['parent_type'], 'integer', 'min' => 0, 'max' => 1],
            [['parent_id'], 'validateParentId'],
            [['parent_type'], 'validateParentType'],
        ];
    }

    public function validateParentId($attribute)
    {
        if (!$this->hasErrors()) {                          // проверка соответствия id, type
            if ($this->parent_id != 0) {                                // если выбрана категория
                if ($this->parent_type == Department::ONLY_DEVICES) {       // категория только для приборов
                    $parent = Department::findOne($this->parent_id);
                    if ($parent === NULL || $parent->parent_type == Department::ONLY_DEVICES) {
                        $this->addError($attribute, 'Категория не найдена');
                    }
                } else {                                                    // остальные категории
                    $this->addError($attribute, 'Нельзя вложить категорию в категорию');
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

                // department_parent to department_child (child не может содержать department)
                if (
                    (
                        $old_parent_type != Department::ONLY_DEVICES &&         // была категория НЕ (только для приборов)
                        $this->parent_type == Department::ONLY_DEVICES          // теперь категория (только для приборов)
                    ) || (
                        $old_parent_type != Department::ONLY_DEVICES &&         // была категория НЕ (только для приборов)
                        $old_deleted == Department::NOT_DELETED &&              // попытка удаления
                        $this->deleted == Department::DELETED
                    )
                ) {
                    $child = Department::findOne(['parent_id' => $this->id]);
                    if ($child !== NULL) {
                        $this->addError($attribute, 'Категория содержит цеха');
                    }
                }

                // department_child to department_parent (parent не может содержать device)
                if (
                    (
                        $old_parent_type == Department::ONLY_DEVICES &&         // была категория (только для приборов)
                        $this->parent_type != Department::ONLY_DEVICES          // теперь категория НЕ (только для приборов)
                    ) || (
                        $old_parent_type == Department::ONLY_DEVICES &&         // была категория (только для приборов)
                        $old_deleted == Department::NOT_DELETED &&              // попытка удаления
                        $this->deleted == Department::DELETED
                    )

                ) {
                    $child = Device::findOne(['id_department' => $this->id]);
                    if ($child !== NULL) {
                        $this->addError($attribute, 'Категория содержит приборы');
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
            'name' => 'Цех',
            'phone' => 'Телефон',
            'description' => 'Описание',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'created_by' => 'Создал',
            'updated_by' => 'Обновил',
            'deleted' => 'Удален',
            'parent_type' => 'Тип',
            'parent_id' => 'Категория',
        ];
    }

    /**
     * Gets arr[id] = names of departments
     * @param int $type
     * @param int $limit
     * @return array
     */
    public static function getAllNames($type = self::ALL, $limit = self::MAX_LINES_IN_LIST)
    {
        $arrWhere = ['deleted' => Department::NOT_DELETED];
        if ($type != self::ALL) {
            $arrWhere['parent_type'] = $type;
        }
        $query = self::find()->select(['id', 'name', 'parent_type', 'parent_id'])->where($arrWhere)->limit($limit)->asArray()->all();
        $outArray = array();
        if ($type == self::ALL) {                   // двухуровневый список
            foreach ($query as $key => $item) {         // сначала группы
                if ($item['parent_type'] == self::ONLY_DEPARTMENT) {
                    $outArray[$item['id']] = $item['name'];
                    foreach ($query as $keyChild => $itemChild) {
                        if ($itemChild['parent_id'] == $item['id']) {
                            $outArray[$itemChild['id']] = Yii::$app->formatter->asHtml('&nbsp;&nbsp;') . $itemChild['name'];
                        }
                    }
                }
            }
            foreach ($query as $key => $item) {         // затем элементы без группы
                if ($item['parent_type'] == self::ONLY_DEVICES && $item['parent_id'] == 0) {
                    $outArray[$item['id']] = $item['name'];
                }
            }
        } else {                                // одноуровневый список
            foreach ($query as $key => $item) {
                $outArray[$item['id']] = $item['name'];
            }
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
