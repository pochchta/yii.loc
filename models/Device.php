<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $type
 * @property string|null $description
 * @property int|null $verif_next_date
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $creator
 * @property int|null $updater
 *
 * @property Verification[] $verifications
 */
class Device extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['verif_next_date', 'created_at', 'updated_at', 'creator', 'updater'], 'integer'],
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
            'name' => 'Name',
            'type' => 'Type',
            'description' => 'Description',
            'verif_next_date' => 'Verif Next Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'creator' => 'Creator',
            'updater' => 'Updater',
        ];
    }

    /**
     * Gets query for [[Verifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVerifications()
    {
        return $this->hasMany(Verification::className(), ['device_id' => 'id']);
    }
}
