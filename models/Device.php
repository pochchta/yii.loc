<?php

namespace app\models;

use DateInterval;
use DateTime;
use Exception;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "device".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $number
 * @property string|null $type
 * @property string|null $description
 * @property int|null $last_date
 * @property int|null $next_date
 * @property int|null $period
 * @property int|null $id_department
 * @property int|null $id_scale
 * @property string|null $accuracy
 * @property string|null $position
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $deleted
 *
 * @property User|null $creator magic property
 * @property User|null $updater magic property
 * @property Department|null $department magic property
 * @property Scale|null $scale magic property
 *
 * @property Verification[] $verifications
 * @method touch(string $string) Method TimestampBehavior
 */
class Device extends ActiveRecord
{
    const NOT_DELETED = 0;
    const DELETED = 1;
    const ALL = -1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device';
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
            [['description'], 'string'],
            [['name', 'type', 'number', 'accuracy', 'position'], 'string', 'max' => 255],
            [['id_department', 'id_scale'], 'integer'],
            [['id_department'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['id_department' => 'id']],
            [['id_scale'], 'exist', 'skipOnError' => true, 'targetClass' => Scale::class, 'targetAttribute' => ['id_scale' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'number' => 'Номер',
            'type' => 'Тип',
            'description' => 'Описание',
            'last_date' => 'Дата пред. пов.',
            'next_date' => 'Дата след. пов.',
            'period' => 'Период пов.',
            'id_department' => 'Цех',
            'id_scale' => 'Шкала',
            'accuracy' => 'Класс точности',
            'position' => 'Позиция',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'created_by' => 'Создал',
            'updated_by' => 'Обновил',
            'deleted' => 'Удален'
        ];
    }

    /**
     * Обновление дат device исходя из позднейшей даты verifications
     * return bool
     */
    public function updateDate()
    {
        $arrVerifications = Verification::find()->where(['device_id' => $this->id])->asArray()->all();
        $lastVerification = [];
        foreach ($arrVerifications as $item) {
            if (empty($item['last_date']) || empty($item['period']) || $item['deleted'] != self::NOT_DELETED) {
                continue;
            }
            try {
                $newDate = new DateTime();
                $newDate->setTimestamp($item['last_date']);
                $newDate->add(new DateInterval('P'.$item['period'].'Y'));
                $item['next_date'] = $newDate->getTimestamp();
            } catch (Exception $e) {
                continue;
            }
            if (empty($lastVerification)) {
                $lastVerification = $item;
                continue;
            }
            if ($item['next_date'] > $lastVerification['next_date']) {
                $lastVerification = $item;
            }
        }
        if (empty($lastVerification)) {
            $this->last_date = NULL;
            $this->next_date = NULL;
            $this->period = NULL;
        } else {
            $this->last_date = $lastVerification['last_date'];
            $this->next_date = $lastVerification['next_date'];
            $this->period = $lastVerification['period'];
        }
        return $this->save();
    }

    /**
     * Gets query for [[Verifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVerifications()
    {
        return $this->hasMany(Verification::class, ['device_id' => 'id']);
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdater()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getDepartment()
    {
        return $this->hasOne(Department::class, ['id' => 'id_department']);
    }

    public function getScale()
    {
        return $this->hasOne(Scale::class, ['id' => 'id_scale']);
    }
}
