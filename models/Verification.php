<?php

namespace app\models;

use DateInterval;
use DateTime;
use Exception;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "verification".
 *
 * @property int $id
 * @property int $device_id
 * @property string|null $name
 * @property int $type_id
 * @property string|null $description
 * @property int|null $last_date
 * @property int|null $next_date
 * @property int|null $period
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $status_id
 * @property int $deleted_id
 *
 * @property Device $device
 * @property User|null $creator magic property
 * @property User|null $updater magic property
 */
class Verification extends ActiveRecord
{
    const STATUS_OFF = 0;
    const STATUS_ON = 1;
    const STATUS_ALL = -1;

    const PERIOD_BY_DEFAULT = '1';

    public $type;

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
            [['type', 'device_id', 'last_date', 'period'], 'required'],
            [['type'], 'string', 'max' => Yii::$app->params['maxLengthTextField']],
            [['type'], 'validateId', 'skipOnEmpty' => false],
            [['description'], 'string'],
            [['device_id'], 'integer'],
            [['device_id'], 'validateDeviceId'],
            [['device_id'], 'exist', 'skipOnError' => true, 'targetClass' => Device::class, 'targetAttribute' => ['device_id' => 'id']],
            [['period'], 'integer', 'min' => 1, 'max' => 20],
            [['last_date'], 'date', 'format' => 'php:Y-m-d', 'timestampAttribute' => 'last_date'],
        ];
    }

    /** Присваивание $attribute . '_id' = Word::findOne(['name' => $attribute])->id
     * @param $attribute
     */
    public function validateId($attribute)
    {
        if (!$this->hasErrors()) {
            $attributeId = $attribute . '_id';
            $word = Word::findOne(['name' => $this->$attribute]);
            if (isset($word)) {
                $parents = Word::getParentByLevel($word);
                if (Word::getFieldWord('v_' . $attribute) !== $parents[0]->id) {
                    $this->addError($attribute, 'Значение не из нужной категории');
                }
                $this->$attributeId = $word->id;
            } else {
                $this->addError($attribute, 'Значение не из списка');
            }
        }
    }

    /** Проверка: device_id нельзя менять
     * @param $attribute
     */
    public function validateDeviceId($attribute)
    {
        if (!$this->hasErrors()) {
            $old_device_id = $this->getOldAttribute('device_id');
            if ($old_device_id !== NULL) {                        // обновление записи
                if ($this->device_id != $old_device_id) {
                    $this->addError($attribute, 'Прибор менять нельзя');
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
            'device_id' => '№ прибора',
            'name' => 'Имя',
            'type_id' => 'Тип', 'type' => 'Тип',
            'description' => 'Описание',
            'last_date' => 'Дата пов.',
            'next_date' => 'Дата след. пов.',
            'period' => 'Период пов.',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'created_by' => 'Создал',
            'updated_by' => 'Обновил',
            'status_id' => 'Последняя',
            'deleted_id' => 'Удален'
        ];
    }

    /** Вычисление next_date при изменении period, last_date
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (
                $this->period != $this->getOldAttribute('period') ||
                $this->last_date != $this->getOldAttribute('last_date')
            ) {
                $newDate = new DateTime();
                $newDate->setTimestamp($this->last_date);
                try {
                    $newDate->add(new DateInterval('P' . $this->period . 'Y'));
                } catch (Exception $e) {
                    return false;
                }
                $this->next_date = $newDate->getTimestamp();
            }
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
            $item->status_id = self::STATUS_OFF;
            if (empty($item->next_date) || $item->deleted_id != Status::NOT_DELETED) {
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
                $item->status_id = self::STATUS_ON;
            }
            if ($item->status_id !== $item->getOldAttribute('status')) {
                if ($item->save(false) == false) {        // validation == false, т.к. в валидаторе преобразуется дата из php:Y-m-d в TimeStamp и
                    return false;                                      // вообще здесь нечего валидировать
                }
            }
        }
        return true;
    }

    /**
     * Gets query for [[Device]].
     *
     * @return ActiveQuery
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

    public function getVtype()
    {
        return $this->hasOne(Word::class, ['id' => 'type_id']);
    }

    public function formName()
    {
        return '';
    }
}