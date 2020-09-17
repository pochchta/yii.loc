<?php

namespace app\modules\admin\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property int $type
 * @property string|null $description
 * @property string|null $rule_name
 * @property resource|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 * @method touch(string $string) Method TimestampBehavior
 */
class AuthItem extends \yii\db\ActiveRecord
{
    public static $ROLE = 1;
    public static $PERMIT = 2;

    public static function tableName()
    {
        return 'auth_item';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'unique'],
            ['name', 'trim'],
            ['name', 'string', 'max' => 64],
//            ['name', 'compare', 'compareValue' => '0', 'operator' => '!=='],

            ['type', 'required'],
            ['type', 'in', 'range' => [AuthItem::$ROLE, AuthItem::$PERMIT]],

            ['description', 'string', 'max' => 256],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'type' => 'Тип',
            'description' => 'Описание',
            'rule_name' => 'Rule Name',
            'data' => 'Data',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    public static function getNamesAllRoles() {
        $findRoles = AuthItem::find()->select(['name'])->where(['type' => self::$ROLE])->asArray()->all();
        $outArray = array();
        foreach($findRoles as $key => $item) {
            $outArray[$item['name']] = $item['name'];
        }
        return $outArray;
    }

    public static function getNamesAllPermits()
    {
        $findPermits = AuthItem::find()->select(['name'])->where(['type' => self::$PERMIT])->asArray()->all();
        $outArray = array();
        foreach ($findPermits as $key => $item) {
            $outArray[$item['name']] = $item['name'];
        }
        return $outArray;
    }

    /**
     * Gets query for [[ItemChild]]
     * для получения названий разрешений, которые являются дочерними для роли
     * @return \yii\db\ActiveQuery
     */
    public function getPermits()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }
}
