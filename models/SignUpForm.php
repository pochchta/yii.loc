<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SignUpForm extends Model
{
    public $username;
    public $password;

    private $_user = false;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password'], 'string', 'max' => 64],
            ['username', 'validateUsername'],
            ['username', 'trim'],
            ['username', 'match', 'pattern' => '/^[\w- ]+$/iu', 'message' => 'Только буквы, цифры, тире и пробелы'],
        ];
    }

    public function validateUsername($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if ($user) {
                $this->addError($attribute, 'Это имя пользователя занято');
            }
        }
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }

    public function signUp()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            if ($user->save()) {
                return true;
            }
        }
        return false;
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя',
            'password' => 'Пароль',
        ];
    }
}