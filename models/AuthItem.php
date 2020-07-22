<?php

namespace app\models;

use Yii;
use yii\rbac\Permission;
use yii\rbac\Role;

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
 * @property Role|Permission $_item
 */
class AuthItem extends \yii\db\ActiveRecord
{
    public static $ROLE = 1;
    public static $PERMIT = 2;

    private $_item;

    public static function tableName()
    {
        return 'auth_item';
    }

    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['name'], 'compare', 'compareValue' => '0', 'operator' => '!=='],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'unique', 'on' => 'create'],
            [['name'], 'validateName', 'on' => 'update'],
            [['type'], 'validateType', 'on' => 'update'],
//            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
        ];
    }

    public function validateName($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->_item == NULL) {
                $this->addError($attribute, 'Элемент не найден');
            }
        }
    }

    public function validateType($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->_item == NULL) {
                $this->addError($attribute, 'Элемент не найден');
            }
            if ($this->_item->type != $this->type) {
                $this->addError($attribute, 'Тип изменить нельзя');
            }
        }
    }

    public function findItem($id)
    {
        if ($item = Yii::$app->authManager->getRole($id)) {
        } else {
            $item = Yii::$app->authManager->getPermission($id);
        }
        if ($item) {
            $this->name = $item->name;
            $this->description = $item->description;
            $this->type = $item->type;
            $this->_item = $item;
            return $item;
        }
    }

    public function createItem()
    {
        $this->scenario = 'create';
        if ($this->validate()) {
            if ($this->type == self::$ROLE) {
                $role = Yii::$app->authManager->createRole($this->name);
                $role->description = $this->description;
                return Yii::$app->authManager->add($role);
            } else {
                $permit = Yii::$app->authManager->createPermission($this->name);
                $permit->description = $this->description;
                return Yii::$app->authManager->add($permit);
            }
        }
        return false;
    }

    public function updateItem($id)
    {
        $this->scenario = 'update';
        if ($this->validate()) {
            if ($this->_item == NULL) {
                $this->findItem($id);
            }
            if ($this->_item == NULL) {
                return false;
            }
            $originalName = $this->_item->name;
            $this->_item->name = $this->name;
            $this->_item->description = $this->description;
            return Yii::$app->authManager->update($originalName, $this->_item);
        }
        return false;
    }

    public function deleteItem($id)
    {
        if ($this->_item == NULL) {
            $this->findItem($id);
        }
        if ($this->_item == NULL) {
            return false;
        }

        return Yii::$app->authManager->remove($this->_item);
    }

    public function getAllRoles() {
        $arrayRoles = AuthItem::find()->select(['name'])->where(['type' => self::$ROLE])->asArray()->all();
        $allRoles = array();
        foreach($arrayRoles as $key => $item) {
            $allRoles[$item['name']] = $item['name'];
        }
        return $allRoles;
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
}
