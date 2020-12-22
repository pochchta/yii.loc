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
        'scale' => '-2',
        'department' => '-3',
        'deviceType' => '-4',
        'deviceName' => '-5',
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
            [['firstCategory', 'secondCategory', 'parent_id'], 'integer']
        ];
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
        $arrWhere = ['deleted' => CategoryWord::NOT_DELETED];
        if ($parent_id == 0) {
            $arrWhere = ['and', 'deleted='.CategoryWord::NOT_DELETED, ['<', 'parent_id', 0]];     // перезапись
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

    public static function getParentN ($model, $n = 0) {
        $parents[1] = $model->parent;
        for ($i = 1; $i <= self::MAX_NUMBER_PARENTS; $i++) {
            if ($parents[$i]->parent_id < 0) {
                break;
            }
            $parents[$i+1] = $parents[$i]->parent;
        }
        return $parents[count($parents) - $n]->name;
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
