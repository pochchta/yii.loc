<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "verification".
 *
 * @property int $id
 * @property int $device_id
 * @property int|null $name
 * @property int|null $type
 * @property int|null $description
 * @property int|null $verif_date
 * @property int|null $verif_period
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $creator
 * @property int|null $updater
 *
 * @property Device $device
 */
class Verification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'verification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['device_id'], 'required'],
            [['device_id', 'name', 'type', 'description', 'verif_date', 'verif_period', 'created_at', 'updated_at', 'creator', 'updater'], 'integer'],
            [['device_id'], 'exist', 'skipOnError' => true, 'targetClass' => Device::className(), 'targetAttribute' => ['device_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'device_id' => 'Device ID',
            'name' => 'Name',
            'type' => 'Type',
            'description' => 'Description',
            'verif_date' => 'Verif Date',
            'verif_period' => 'Verif Period',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'creator' => 'Creator',
            'updater' => 'Updater',
        ];
    }

    /**
     * Gets query for [[Device]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Device::className(), ['id' => 'device_id']);
    }
}
