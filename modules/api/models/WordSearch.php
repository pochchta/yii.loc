<?php

namespace app\modules\api\models;

use app\models\Status;
use app\models\Word;
use Yii;
use yii\base\Model;

/**
 * WordSearch represents the model behind the search form of `app\models\Word`.
 */
class WordSearch extends Word
{
    const REPLACE_NAMES = ['value'];

    public $limit;
    public $parent, $parent_v;
    public $name_v;
    public $replace_name;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted', 'parent_id'], 'integer'],
            [['name', 'name_v', 'value', 'description'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['deleted'], 'default', 'value' => Status::NOT_DELETED],
            [['parent', 'parent_v'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['limit'], 'integer', 'min' => 0, 'max' => Yii::$app->params['maxLines']],
            [['replace_name'], 'validateReplaceName'],
        ];
    }

    public function validateReplaceName($attribute)
    {
        if (!$this->hasErrors()) {
            if (in_array($this->replace_name, $this::REPLACE_NAMES) == false) {
                $this->addError($attribute, 'Недопустимое имя для замены');
                    return;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->limit = Yii::$app->params['maxLinesIndex'];
    }

    /** Поиск имен в базе и массиве Word::LABEL_FIELD_WORD по name_id, name (по базе), name_v (включая массив).
     * replace_name - замена ключа,
     * limit - лимит
     * @return array
     */
    public function findNames()
    {
        $names = [];
        if ($this->validate()) {
            if (mb_strlen($this->name_v)) {
                $names = array_map(function ($item) {
                    $name = $this->replace_name ?? 'name';
                    return [$name => Word::LABEL_FIELD_WORD[$item]];
                }, Word::getNumbersBySimilarLabel($this->name_v . '%'));
            }
            $names = array_slice($names, 0, $this->limit);  // обрезка если уже слишком много элементов

            $limit = $this->limit - count($names);
            if ($limit > 0) {
                $query = Word::find()
//                    ->where(['deleted' => Status::NOT_DELETED])
//                    ->distinct()
                    ->andFilterWhere(['id' => $this->id])
                    ->orderBy('name')
                    ->limit($limit)
                    ->asArray();

                if (mb_strlen($this->replace_name)) {
                    $query->select(['name as ' . $this->replace_name]);
                } else {
                    $query->select(['name']);
                }

                if (mb_strlen($this->name)) {
                    $query->andFilterWhere(['like', 'name', $this->name . '%', false]);
                }
                if (mb_strlen($this->name_v)) {
                    $query->andFilterWhere(['like', 'name', $this->name_v . '%', false]);
                }

                $names = array_merge($names, $query->all());
            }

        }
        return $names;
    }

    /** Поиск элементов по parent_id, parent, parent_v
     * @return array [['id' => 1, 'value' => 'Название'], []]
     */
    public function findNamesByParent()
    {
        $names = [];
        if ($this->validate()) {
            $query = Word::find()
                ->select(['id', 'name'])
                ->orderBy('name')
//            ->distinct()
                ->asArray()
                ->limit($this->limit)
                ->where(['deleted' => Status::NOT_DELETED])
                ->andFilterWhere([
                    'parent_id' => $this->parent_id,
                ]);
            if (strlen($this->parent)) {
                $query->andFilterWhere([
                    'id' => Word::getQueriesToGetChildren(['like', 'name', $this->parent . '%', false])[1]
                ]);
            }
            if (strlen($this->parent_v)) {
                $subQuery = ['or'];
                $subQuery[]['id'] = Word::getQueriesToGetChildren(['like', 'name', $this->parent_v . '%', false])[1];
                $subQuery[]['id'] = Word::getQueriesToGetChildrenIfParentIsVirtual(['like', 'name', $this->parent_v . '%', false])[1];
                $query->andFilterWhere($subQuery);
            }
            $names = $query->all();
        }

        return $names;
    }
}