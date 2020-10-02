<?php


namespace app\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class ProfileForm  extends Model
{
    public $username;
    public $oldPass;                   // старый пароль
    public $newPass;                   // новый пароль
    public $newPassRepeat;             // подтверждение нового пароля

    private $_userByName = false;
    private $_userByAuthorization = false;

    public function rules() // TODO trim, unique, pattern
    {
        return [
            [['username', 'oldPass'], 'required'],
            [['username', 'oldPass', 'newPass', 'newPassRepeat'], 'string', 'max' => 64],
            ['username', 'unique'],
            ['username', 'trim'],
            ['username', 'match', 'pattern' => '/^[\w- ]+$/i'],
            [['oldPass'], 'validateOldPass'],
            [['newPass', 'newPassRepeat'], 'validateNewPass'],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @throws NotFoundHttpException
     */
    public function validateOldPass($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByAuthorization();
            if ($user->validatePassword($this->oldPass) == false) {
                $this->addError('oldPass', 'Пароль не верен');
            }
        }
    }

    public function validateNewPass($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->newPass != '' || $this->newPassRepeat != '') {
                if ($this->newPass !== $this->newPassRepeat) {
                    $this->addError($attribute, 'Новый пароль и повторный новый пароль не совпадают');
                }
            }
        }
    }

    public function getUserByUsername()
    {
        if ($this->_userByName === false) {
            $this->_userByName = User::findByUsername($this->username);
        }
        return $this->_userByName;
    }

    public function getUserByAuthorization()
    {
        if ($this->_userByAuthorization === false) {
            $this->_userByAuthorization = User::findOne(Yii::$app->user->identity->id);
        }
        return $this->_userByAuthorization;
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $user = $this->getUserByAuthorization();
        $this->username = $user->username;
    }

    /**
     * Обновление данных пользователя
     * @throws NotFoundHttpException
     */
    public function updateUser()
    {
        if ($this->validate()) {
            $user = $this->getUserByAuthorization();
            if ($user === NULL) {
                throw new NotFoundHttpException('Запрошенная страница не существует.');
            }

            $user->username = $this->username;
            $attributes = NULL;                     // полное обновление данных
            if ($this->newPass == '' && $this->newPassRepeat == '') {
                $attributes = ['username'];         // или частичное обновление данных
            }
            try {
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($this->newPass);
            } catch (Exception $e) {
                throw new NotFoundHttpException('Неподходящий пароль.');
            }
            if ($user->save(true, $attributes)) {
                return true;
            }
        }
        return false;
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




}