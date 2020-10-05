<?php


namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\web\NotFoundHttpException;

class ProfileForm  extends Model
{
    public $username;
    public $oldPass;                   // старый пароль
    public $newPass;                   // новый пароль
    public $newPassRepeat;             // подтверждение нового пароля

    private $_userByName = false;
    private $_userById = false;

    public function rules()
    {
        return [
            [['username', 'oldPass'], 'required'],
            [['username', 'oldPass', 'newPass', 'newPassRepeat'], 'string', 'max' => 64],
            ['username', 'validateUsername'],
            ['username', 'trim'],
            ['username', 'match', 'pattern' => '/^[\w- ]+$/iu', 'message' => 'Только буквы, цифры, тире и пробелы'],
            [['oldPass'], 'validateOldPass'],
            [['newPass', 'newPassRepeat'], 'validateNewPass'],
        ];
    }

    public function validateUsername($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByName();

            if ($user) {
                if ($user->username !== $this->getUserById()->username) {
                    $this->addError($attribute, 'Это имя пользователя занято');
                }
            }
        }
    }

    /**
     * @param $attribute
     * @throws InvalidArgumentException
     */
    public function validateOldPass($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserById();
            if ($user->validatePassword($this->oldPass) == false) {
                $this->addError($attribute, 'Старый пароль не верен');
            }
        }
    }

    public function validateNewPass($attribute)
    {
        if (!$this->hasErrors()) {
            if ($this->newPass != '' || $this->newPassRepeat != '') {
                if ($this->newPass !== $this->newPassRepeat) {
                    $this->addError($attribute, 'Новый пароль и повторный новый пароль не совпадают');
                }
            }
        }
    }

    public function getUserByName()
    {
        if ($this->_userByName === false) {
            $this->_userByName = User::findByUsername($this->username);
        }
        return $this->_userByName;
    }

    public function getUserById()
    {
        if ($this->_userById === false) {
            $this->_userById = User::findOne(Yii::$app->user->identity->id);
        }
        return $this->_userById;
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $user = $this->getUserById();
        $this->username = $user->username;
    }

    /**
     * Обновление данных пользователя
     * @throws Exception
     */
    public function updateUser()
    {
        if ($this->validate()) {
            $user = $this->getUserById();
            if ($user === NULL) {
                throw new NotFoundHttpException('Запрошенная страница не существует.');
            }

            $user->username = $this->username;
            $attributes = NULL;                     // полное обновление данных
            if ($this->newPass == '' && $this->newPassRepeat == '') {
                $attributes = ['username'];         // или частичное обновление данных
            }
            $user->password = Yii::$app->getSecurity()->generatePasswordHash($this->newPass);
            if ($user->save(true, $attributes)) {
                return true;
            }
        }
        return false;
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'oldPass' => 'Старый пароль',
            'newPass' => 'Новый пароль',
            'newPassRepeat' => 'Повтор нового пароля',
        ];
    }
}