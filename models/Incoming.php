<?php

namespace app\models;

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
 * @property User $createdBy
 * @property User $updatedBy
 */
class Incoming extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'incoming';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['device_id', 'status', 'payment', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['description'], 'string'],
            [['device_id'], 'exist', 'skipOnError' => true, 'targetClass' => Device::class, 'targetAttribute' => ['device_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
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
            'description' => 'Описание',
            'status' => 'Статус',
            'payment' => 'Оплата',
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
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}