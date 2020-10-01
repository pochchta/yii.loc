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
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $deleted
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
            [['name', 'type'], 'string', 'max' => 255],
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
}
