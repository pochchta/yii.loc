<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "incoming".
 *
 * @property int $id
 * @property int|null $device_id
 * @property string|null $description
 * @property int $status
 * @property int $payment
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $deleted
 *
 * @property Device $device
 * @property User $creator
 * @property User $updater
 */
class Incoming extends ActiveRecord
{
    const NOT_PAID = 0;
    const PAID = 1;

    const INCOMING = 0;
    const READY = 1;
    const OUTGOING = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'incoming';
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
            [['device_id', 'status', 'payment'], 'required'],
            [['device_id', 'status', 'payment'], 'integer'],
            [['status', 'payment'], 'integer', 'max' => 255],
            [['description'], 'string'],
            [['device_id'], 'exist', 'skipOnError' => true, 'targetClass' => Device::class, 'targetAttribute' => ['device_id' => 'id']],
            [['device_id'], 'validateDeviceId']
        ];
    }

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
            'device_id' => 'ID прибора',
            'description' => 'Описание',
            'status' => 'Статус',
            'payment' => 'Оплачен',
            'created_by' => 'Создал',
            'updated_by' => 'Обновил',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'deleted' => 'Удален',
        ];
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

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return ActiveQuery
     */
    public function getUpdater()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}