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
    const COLUMN_NAMES = ['id'];

    const RULES_AUTO_COMPLETE = [
        'word' => [
            'levels' => [1, 2, 3],
            'parent_name' => [
                'virtualParent' => 'yes'
            ],
        ],
        'device' => [
            'levels' => [1, 2, 3],
            'kind' => [
                'levels' => [1],
            ],
            'group' => [
                'levels' => [2],
            ],
            'type' => [
                'levels' => [3],
            ],
        ]
    ];

    public $limit;
    public $parent, $parent_v;
    public $name_v;
    public $replace_name, $column_name;

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
            [['column_name'], 'validateColumnName'],
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

    public function validateColumnName($attribute)
    {
        if (!$this->hasErrors()) {
            if (in_array($this->column_name, $this::COLUMN_NAMES) == false) {
                $this->addError($attribute, 'Недопустимое имя для столбца');
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
//                    ->distinct()
                    ->andFilterWhere([
                        'id' => $this->id,
                        'parent_id' => $this->parent_id,
                    ])
                    ->orderBy('name')
                    ->limit($limit)
                    ->asArray();

                $select = [];
                if (mb_strlen($this->column_name)) {
                    $select[] = $this->column_name;
                }
                if (mb_strlen($this->replace_name)) {
                    $select[] = 'name as ' . $this->replace_name;
                } else {
                    $select[] = 'name';
                }
                $query->select($select);

                if (mb_strlen($this->name)) {
                    $query->andFilterWhere(['like', 'name', $this->name . '%', false]);
                }

                if (mb_strlen($this->name_v)) {
                    $query->andFilterWhere(['like', 'name', $this->name_v . '%', false]);
                }

                if (mb_strlen($this->parent)) {
                    $query->andFilterWhere([
                        'id' => Word::getQueriesToGetChildren(['like', 'name', $this->parent . '%', false])[1]
                    ]);
                }

                if (mb_strlen($this->parent_v)) {
                    $subQuery = ['or'];
                    $subQuery[]['id'] = Word::getQueriesToGetChildren(['like', 'name', $this->parent_v . '%', false])[1];
                    $subQuery[]['id'] = Word::getQueriesToGetChildrenIfParentIsVirtual(['like', 'name', $this->parent_v . '%', false])[1];
                    $query->andFilterWhere($subQuery);
                }

                if ($this->deleted == Status::NOT_DELETED || $this->deleted == Status::DELETED) {
                    $query->andFilterWhere(['deleted' => $this->deleted]);
                }

                $names = array_merge($names, $query->all());
            }

        }
        return $names;
    }

    /** Поиск имен в базе и массиве Word::LABEL_FIELD_WORD по name_id, name.
     * @return array
     */
    public function findAutoComplete()  // TODO name в массиве должен искать!
    {
        $names = [];
        $limit = Yii::$app->params['maxLinesAutoComplete'];

        if (
            $this->validate() === false
            || mb_strlen($this->parent) === 0
            || mb_strlen($this->name) === 0
        ) {
            return $names;
        }

        $levels = $this::RULES_AUTO_COMPLETE[$this->parent]['levels'];
        $virtualParent = $this::RULES_AUTO_COMPLETE[$this->parent]['virtualParent'];
        if (isset($this::RULES_AUTO_COMPLETE[$this->parent][$this->name]['levels'])) {
            $levels = $this::RULES_AUTO_COMPLETE[$this->parent][$this->name]['levels'];
        }
        if (isset($this::RULES_AUTO_COMPLETE[$this->parent][$this->name]['virtualParent'])) {
            $virtualParent = $this::RULES_AUTO_COMPLETE[$this->parent][$this->name]['virtualParent'];
        }
        $virtualParent = Word::FIELD_WORD[$virtualParent];  // получаем id по названию

        if ($virtualParent) {
            $names = array_map(function ($item) {
                return ['name' => Word::LABEL_FIELD_WORD[$item]];
            }, Word::getNumbersBySimilarLabel($this->name . '%'));
        }
        $names = array_slice($names, 0, $limit);  // обрезка если уже слишком много элементов

        $limit = $limit - count($names);
        if ($limit > 0) {
            $query = Word::find()
//                ->distinct()
                ->select(['name as value'])
                ->orderBy('name')
                ->andFilterWhere(['like', 'name', $this->name . '%', false])
                ->limit($limit)
                ->asArray();

            if ($virtualParent) {
                $queries = Word::getQueriesToGetChildrenIfParentIsVirtual(['id', $virtualParent]);
            } else {
                $queries = Word::getQueriesToGetChildrenIfDepthIsAbsolute();
            }

            if ($levels) {
                $query->andFilterWhere(['id' => Word::mergeQueriesOr($queries, $levels)]);
            }

            if ($this->deleted == Status::NOT_DELETED || $this->deleted == Status::DELETED) {
                $query->andFilterWhere(['deleted' => $this->deleted]);
            }

            $names = array_merge($names, $query->all());
        }

        return $names;
    }
}