<?php

namespace app\models;

use DateInterval;
use DateTime;
use Exception;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "verification".
 *
 * @property int $id
 * @property int $device_id
 * @property string|null $name
 * @property string|null $type
 * @property string|null $description
 * @property int|null $last_date
 * @property int|null $next_date
 * @property int|null $period
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $status
 * @property int $deleted
 *
 * @property Device $device
 * @property User|null $creator magic property
 * @property User|null $updater magic property
 */
class Verification extends ActiveRecord
{
    const NOT_DELETED = 0;
    const DELETED = 1;
    const ALL = -1;

    const STATUS_OFF = 0;
    const STATUS_ON = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'verification';
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
            [['name', 'device_id'], 'required'],
            [['device_id'], 'integer'],
            [['device_id'], 'exist', 'skipOnError' => true, 'targetClass' => Device::class, 'targetAttribute' => ['device_id' => 'id']],
            [['period'], 'integer', 'max' => 255],
            [['last_date'], 'date', 'format' => 'php:Y-m-d', 'timestampAttribute' => 'last_date'],
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
            'device_id' => '№ прибора',
            'name' => 'Имя',
            'type' => 'Тип',
            'description' => 'Описание',
            'last_date' => 'Дата пов.',
            'next_date' => 'Дата след. пов.',
            'period' => 'Период пов.',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'created_by' => 'Создал',
            'updated_by' => 'Обновил',
            'status' => 'Статус',
            'deleted' => 'Удален'
        ];
    }

    /** Вычисление next_date
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $newDate = new DateTime();
            $newDate->setTimestamp($this->last_date);
            try {
                $newDate->add(new DateInterval('P' . $this->period . 'Y'));
            } catch (Exception $e) {
                return false;
            }
            $this->next_date = $newDate->getTimestamp();

            return true;
        }
        return false;
    }

    /**
     * Установка флагов status для verifications с одинаковым device_id
     * @return bool
     */
    public function checkLastVerification()
    {
        $arrVerifications = Verification::find()->where(['device_id' => $this->device_id])->all();
        $arrDate = [];
        foreach ($arrVerifications as $key => $item) {   /** @var $item Verification */
            $item->status = self::STATUS_OFF;
            if (empty($item->next_date) || $item->deleted != self::NOT_DELETED) {
                continue;
            }
            $arrDate[$item->next_date] = $key;
        }
        if (krsort($arrDate) == false) {
            return false;
        }
        $keyLastVerification = reset($arrDate);
        foreach ($arrVerifications as $key => $item) {   /** @var $item Verification */
            if ($keyLastVerification === $key) {
                $item->status = self::STATUS_ON;
            }
            if ($item->getAttribute('status') !== $item->getOldAttribute('status')) {
                if ($item->save() == false) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Gets query for [[Device]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Device::class, ['id' => 'device_id']);
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
