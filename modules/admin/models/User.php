<?php

namespace app\modules\admin\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string|null $auth_key
 */

class User extends ActiveRecord
{
    public $newPass;                   // новый пароль
    public $newPassRepeat;             // подтверждение нового пароля
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['username', 'newPass', 'newPassRepeat'], 'required'],
            [['username', 'newPass', 'newPassRepeat'], 'string', 'max' => 64],
            ['username', 'unique'],
            ['username', 'trim'],
            ['username', 'match', 'pattern' => '/^[\w- ]+$/i'],
            ['newPassRepeat', 'compare', 'compareAttribute' => 'newPass', 'message' => 'Пароль и повторный пароль не совпадают'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'auth_key' => 'Ключ идентификации',
        ];
    }

    public function getRoles()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }
}
