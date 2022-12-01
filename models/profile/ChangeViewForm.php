<?php


namespace app\models\profile;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class ChangeViewForm  extends Model
{
    public $profileView;

    private $_userById = false;

    public function rules()
    {
        return [
            ['profileView', 'trim'],
            [['profileView'], 'required'],
            [['profileView'], 'string', 'min' => 1, 'max' => 64],
            ['profileView', 'validateExist'],
        ];
    }

    public function validateExist($attribute)
    {
        if (!$this->hasErrors()) {
            if (key_exists($this->profileView, self::getListProfileView()) == false) {
                $this->addError($attribute, 'Профиль вида не найден');
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

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->profileView = Yii::$app->user->identity->getProfileView();
    }

    public function updateProfileView()
    {
        if ($this->validate()) {
            $user = $this->getUserById();
            if ($user === NULL) {
                throw new NotFoundHttpException('Запрошенная страница не существует.');
            }

            $user->profile_view = $this->profileView;
            if ($user->save()) {
                return true;
            }
        }
        return false;
    }

    public function attributeLabels()
    {
        return [
            'profileView' => 'Профиль вида',
        ];
    }

    public static function getListProfileView()
    {
        $keys = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()));
        $roles = array_combine($keys, $keys);
        return array_merge(
            ['default' => 'По умолчанию'],
            $roles
        );
    }
}