<?php


namespace app\models\profile;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

class ChangePassForm  extends Model
{
    public $oldPass;                   // старый пароль
    public $newPass;                   // новый пароль
    public $newPassRepeat;             // подтверждение нового пароля

    private $_userById = false;

    public function rules()
    {
        return [
            [['oldPass', 'newPass', 'newPassRepeat'], 'required'],
            [['oldPass', 'newPass', 'newPassRepeat'], 'string', 'min' => 1, 'max' => 64],
            [['newPass'], 'validateChangedPass'],
            [['newPassRepeat'], 'validatePassRepeat'],
            [['oldPass'], 'validateOldPass'],
        ];
    }

    public function validateOldPass($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserById();
            if ($user->validatePassword($this->oldPass) == false) {
                $this->addError($attribute, 'Старый пароль не верен');
            }
        }
    }

    public function validateChangedPass($attribute)
    {
        if (!$this->hasErrors()) {
            if ($this->oldPass === $this->newPass) {
                $this->addError($attribute, 'Старый и новый пароль одинаковы');
            }
        }
    }

    public function validatePassRepeat($attribute)
    {
        if (!$this->hasErrors()) {
            if ($this->newPass !== $this->newPassRepeat) {
                $this->addError($attribute, 'Новый пароль и повторный новый пароль не совпадают');
            }
        }
    }

    public function getUserById()
    {
        if ($this->_userById === false) {
            $this->_userById = User::findOne(Yii::$app->user->identity->id);
        }
        return $this->_userById;
    }

    /**
     * Обновление данных пользователя
     * @throws Exception
     */
    public function updatePass()
    {
        if ($this->validate()) {
            $user = $this->getUserById();
            if ($user === NULL) {
                throw new NotFoundHttpException('Запрошенная страница не существует.');
            }

            $user->password = Yii::$app->getSecurity()->generatePasswordHash($this->newPass);
            if ($user->save()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Очистка полей
     */
    public function clearFields()
    {
        $this->oldPass = '';
        $this->newPass = '';
        $this->newPassRepeat = '';
    }

    public function attributeLabels()
    {
        return [
            'oldPass' => 'Старый пароль',
            'newPass' => 'Новый пароль',
            'newPassRepeat' => 'Повтор нового пароля',
        ];
    }
}