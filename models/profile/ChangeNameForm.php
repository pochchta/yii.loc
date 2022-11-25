<?php


namespace app\models\profile;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

class ChangeNameForm  extends Model
{
    public $username;
    public $oldPass;

    private $_userByName = false;
    private $_userById = false;

    public function rules()
    {
        return [
            ['username', 'trim'],
            [['username', 'oldPass'], 'required'],
            [['username'], 'string', 'min' => 3, 'max' => 64],
            ['username', 'match', 'pattern' => '/^[\w- ]+$/iu', 'message' => 'Только буквы, цифры, тире и пробелы'],
            ['username', 'validateChangedName'],
            ['username', 'validateUniqueName'],
            [['oldPass'], 'string', 'max' => 64],
            [['oldPass'], 'validateOldPass'],
        ];
    }

    public function validateChangedName($attribute)
    {
        if (!$this->hasErrors()) {
            if ($this->username === Yii::$app->user->identity->username) {
                $this->addError($attribute, 'Новое имя пользователя совпадает со старым');
            }
        }
    }

    public function validateUniqueName($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByName();
            if ($user) {
                $this->addError($attribute, 'Это имя пользователя занято');
            }
        }
    }

    public function validateOldPass($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserById();
            if ($user->validatePassword($this->oldPass) == false) {
                $this->addError($attribute, 'Пароль не верен');
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
        $this->username = Yii::$app->user->identity->username;
    }

    /**
     * Обновление данных пользователя
     * @throws Exception
     */
    public function updateName()
    {
        if ($this->validate()) {
            $user = $this->getUserById();
            if ($user === NULL) {
                throw new NotFoundHttpException('Запрошенная страница не существует.');
            }

            $user->username = $this->username;
            $user->password = Yii::$app->getSecurity()->generatePasswordHash($this->oldPass);
            if ($user->save()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Очистка полей
     */
    public function clearPassFields()
    {
        $this->oldPass = '';
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'oldPass' => 'Пароль',
        ];
    }
}