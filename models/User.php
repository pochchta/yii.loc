<?php

namespace app\models;

use app\modules\admin\models\AuthAssignment;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\Exception;
use yii\base\InvalidArgumentException;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string|null $auth_key
 * @property string|null $profile_view
 */

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'user';
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @param null $type
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @param $password
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password', 'profile_view'], 'string', 'max' => 64],
            ['username', 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'auth_key' => 'Ключ идентификации',
            'profile_view' => 'Профиль вида',
        ];
    }

    public function getRoles()
    {
        return $this->hasMany(AuthAssignment::class, ['user_id' => 'id']);
    }

    /**Если профиль не задан, то берем первую роль.
     * Если профиль есть в назначенных ролях, то берем его.
     * Иначе 'default'.
     * @return mixed|string|null
     */
    public function getProfileView()
    {
        $rolesByUser = array_keys(Yii::$app->authManager->getRolesByUser($this->id));
        if (! isset($this->profile_view)) {
            if (count($rolesByUser) > 0) {
                return $rolesByUser[0];
            }
        }
        if (in_array($this->profile_view, $rolesByUser)) {
            return $this->profile_view;
        }
        return 'default';
    }
}