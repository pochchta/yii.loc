<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "t_verification".
 *
 * @property int $id
 * @property int $device_id
 * @property int $name
 * @property int $type
 * @property int $description
 * @property int $created_at
 * @property int $updated_at
 * @property int $author_creating
 * @property int $author_updating
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
        return 't_verification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['device_id', 'name', 'type', 'description', 'created_at', 'updated_at', 'author_creating', 'author_updating'], 'required'],
            [['device_id', 'name', 'type', 'description', 'created_at', 'updated_at', 'author_creating', 'author_updating'], 'integer'],
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
            'name' => 'Имя',
            'type' => 'Тип',
            'description' => 'Описание',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'author_creating' => 'Автор создания',
            'author_updating' => 'Автор обновления',
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
