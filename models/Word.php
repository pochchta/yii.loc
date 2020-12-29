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
 * @property CategoryWord $parent magic property
 * @property User|null $creator magic property
 * @property User|null $updater magic property
 */
class Word extends ActiveRecord
{
    const ALL = -1;                     // для всех свойств

    const NOT_DELETED = 0;              // по умолчанию word->deleted
    const DELETED = 1;

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
            [['description'], 'string'],
            [['name', 'value'], 'string', 'max' => 255],
            [['firstCategory', 'secondCategory', 'thirdCategory', 'parent_id'], 'integer'],
            [['parent_id'], 'validateParent']
        ];
    }

    public function validateParent()
    {
        if (!$this->hasErrors()) {
            $parent = $this->parent;
            $parentAttribute = 'firstCategory';
            if ($parent->parent_id != 0) {
                $parentAttribute = 'secondCategory';
            }
            if ($parent === NULL) {
                $this->addError($parentAttribute, 'Родительская категория не найдена');
            }
        }
    }

    /**
     * Gets arr[id] = names
     * @param int $parent_id
     * @param int $depth Глубина поиска word.parent_id
     * @return array
     */
    public static function getAllNames($parent_id = self::ALL, $depth = 1)
    {
        $query = self::find()->select(['id', 'name', 'parent_id'])->limit(Yii::$app->params['maxLinesView']);

        $arrWhere = ['deleted' => self::NOT_DELETED];
        if ($parent_id != self::ALL) {
            if ($depth == 3) {
                $query->andOnCondition(
                    'parent_id = :id OR parent_id IN (SELECT id FROM category_word WHERE category_word.parent_id = :id)'
                    .'OR parent_id IN (SELECT id FROM category_word WHERE category_word.parent_id IN (SELECT id FROM category_word WHERE category_word.parent_id = :id))',
                    [':id' => $parent_id]
                );
            } elseif ($depth == 2) {
                $query->andOnCondition(
                    'parent_id = :id OR parent_id IN (SELECT id FROM category_word WHERE category_word.parent_id = :id)',
                    [':id' => $parent_id]
                );
            } else {
                $arrWhere['parent_id'] = $parent_id;
            }
        }

        $query = $query->where($arrWhere)->asArray()->all();
        $outArray = array();

        foreach ($query as $key => $item) {
            $outArray[$item['id']] = $item['name'];
        }
        return $outArray;
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

/*    public function getDevices()
    {
        return $this->hasMany(Device::class, ['id_department' => 'id']);
    }*/

    public function getParent()
    {
        return $this->hasOne(CategoryWord::class, ['id' => 'parent_id']);
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
