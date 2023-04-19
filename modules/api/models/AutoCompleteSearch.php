<?php

namespace app\modules\api\models;

use app\models\Device;
use app\models\Status;
use app\models\Word;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class AutoCompleteSearch extends ActiveRecord
{
    const RULES_AUTO_COMPLETE = [
        'word' => [
            'virtualParent' => 1,
            'levels' => [1, 2, 3],
            'parent_name' => [
                'levels' => [1,2],
            ],
            'parent' => [
                'levels' => [1,2],
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
            'number' => [
                'source' => 1
            ]
        ]
    ];

    public $name, $field, $parent, $deleted;

    public function rules()
    {
        return [
            [['name', 'field', 'parent'], 'string', 'min' => 1, 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['deleted'], 'default', 'value' => Status::NOT_DELETED],
            ['parent', 'validateParentName'],
            ['field', 'validateFieldName'],
        ];
    }

    public function validateParentName($attribute)
    {
        if (!$this->hasErrors()) {
            if (key_exists($this->parent, $this::RULES_AUTO_COMPLETE) == false) {
                $this->addError($attribute, 'Недопустимое имя родителя');
            }
            if (key_exists($this->parent, ['virtualParent', 'levels', 'source'])) {
                $this->addError($attribute, 'Недопустимое имя родителя');
            }
        }
    }

    public function validateFieldName($attribute)
    {
        if (!$this->hasErrors()) {
            if (key_exists($this->field, ['virtualParent', 'levels', 'source'])) {
                $this->addError($attribute, 'Недопустимое имя поля');
            }
        }
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function formName()
    {
        return '';
    }

    /**
     * @return array
     */
    public function findAutoComplete()
    {
        $names = [];
        if ($this->validate()) {
            $limit = Yii::$app->params['maxLinesAutoComplete'];

            $levels = $this::RULES_AUTO_COMPLETE[$this->parent]['levels'] ?? null;
            $levels = $this::RULES_AUTO_COMPLETE[$this->parent][$this->field]['levels'] ?? $levels;

            $source = $this::RULES_AUTO_COMPLETE[$this->parent]['source'] ?? null;
            $source = $this::RULES_AUTO_COMPLETE[$this->parent][$this->field]['source'] ?? $source;

            $virtualParent = $this::RULES_AUTO_COMPLETE[$this->parent]['virtualParent'] ?? null;
            $virtualParent = $this::RULES_AUTO_COMPLETE[$this->parent][$this->field]['virtualParent'] ?? $virtualParent;
            $virtualParent = isset($source) ? null : $virtualParent;    // сброс $virtualParent

            if ($virtualParent) {
                $names = array_map(function ($item) {
                    return ['value' => Word::LABEL_FIELD_WORD[$item]];
                }, Word::getNumbersBySimilarLabel($this->name . '%'));

                $names = array_slice($names, 0, $limit);  // обрезка если уже слишком много элементов
                $limit = $limit - count($names);
            }

            if ($limit > 0) {
                $query = Word::find();
                $name = 'name';
                if ($source) {
                    if ($this->parent === 'device') {
                        $query = Device::find();
                        if (key_exists($this->field, $this::RULES_AUTO_COMPLETE[$this->parent])) {
                            $name = $this->field;
                        }
                    }
                } else {
                    $queries = Word::getQueriesToGetChildrenIfDepthIsAbsolute();
                    $query->andFilterWhere(Word::mergeQueriesOr($queries, 'id', $levels));
                }
                $query
                    ->distinct()
                    ->select(["$name as value"])
                    ->orderBy($name)
                    ->andFilterWhere(['like', $name, $this->$name . '%', false])
                    ->limit($limit)
                    ->asArray();

                if ($this->deleted == Status::NOT_DELETED || $this->deleted == Status::DELETED) {
                    $query->andFilterWhere(['deleted' => $this->deleted]);
                }

                $names = array_merge($names, $query->all());
            }
        }

        return $names;
    }
}