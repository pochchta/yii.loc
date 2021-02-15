<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * WordSearch represents the model behind the search form of `app\models\Word`.
 */
class WordSearch extends Word
{
    const COLUMN_SEARCH = ['id', 'name', 'value'];
    public $first_category, $second_category, $third_category;
    public $term, $term_name, $term_parent;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted', 'parent_id'], 'integer'],
            [['name', 'value', 'description'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['deleted'], 'default', 'value' => Status::NOT_DELETED],
            [['first_category', 'second_category', 'third_category'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['term', 'term_name', 'term_parent'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Word::find()->with('parent.parent');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        if (strlen($this->name)) {
            $query->andFilterWhere(['like', 'name', $this->name . '%', false]);
        }
        if (strlen($this->value)) {
            $query->andFilterWhere(['like', 'value', $this->value . '%', false]);
        }

        if ($this->deleted == Status::NOT_DELETED || $this->deleted == Status::DELETED) {
            $query->andFilterWhere(['deleted' => $this->deleted]);
        }

        if (strlen($this->first_category)) {
            $depth = 3;
            if (ucfirst($this->second_category) == array_search(Status::NOT_CATEGORY, Word::FIELD_WORD)) {
                $depth = 1;
            } elseif (ucfirst($this->third_category) == array_search(Status::NOT_CATEGORY, Word::FIELD_WORD)) {
                $depth = 2;
            }
            if ($this->first_category == Status::ALL) {
                if ($depth != 3) {
                    list('condition' => $condition, 'bind' => $bind) =
                        Word::getConditionById('parent_id', $this->first_category, $depth, false);
                    $query->andOnCondition($condition, $bind);
                }
            } else {
                list('condition' => $condition, 'bind' => $bind) =
                    Word::getConditionLikeName('parent_id', $this->first_category, $depth, false);
                $query->andOnCondition($condition, $bind);
            }
        }

/*        foreach(['second_category', 'third_category'] as $item) {
            if ($item != 'first_category' || $this->$item != Status::ALL) {
                $depth = 3;
                if ($item == 'second_category') {
                    $depth = 2;
                } elseif ($item == 'third_category') {
                    $depth = 1;
                }
                if (strlen($this->$item)) {
                    list('condition' => $condition, 'bind' => $bind) =
                        Word::getConditionLikeName('parent_id', $this->$item, $depth, true);
                    $query->andOnCondition($condition, $bind);
                }
            }
        }*/

        return $dataProvider;
    }

    public function formName()
    {
        return '';
    }

    public static function getAutoCompleteOptions($attribute)
    {
        $parent = "''";
        if ($attribute === 'second_category') {
            $status = Status::ALL;
            $parent = "$('#first_category').val()!= $status ? $('#first_category').val() : ''";
        } elseif ($attribute === 'third_category') {
            $status = Status::ALL;
            $parent = "$('#first_category').val()!= $status ? $('#first_category').val() : ''";
            $parent = "$('#second_category').val()!= '' ? $('#second_category').val() : $parent";
        }
        return [
            'clientOptions' => [
                'source' => new JsExpression("function(request, response) {
                    $.getJSON('" . Url::to('list-auto-complete') . "', {
                        term: request.term,
                        term_name: '{$attribute}',
                        term_parent: {$parent},
                    }, response);
                }"),
                'select' => new JsExpression("function(event, ui) {
                    selectAutoComplete(event, ui, '{$attribute}');
                }"),
                'minLength' => Yii::$app->params['minSymbolsAutoComplete'],
                'delay' => Yii::$app->params['delayAutoComplete']
            ],
            'options' => [
                'class' => 'form-control',
            ]
        ];
    }

    /** Поиск по словарю для AutoComplete
     * @param int $depth
     * @param bool $withParent
     * @return false|string
     */
    public function findNames($depth = 0, $withParent = false)
    {
        $data = [];
        if ($depth) {               // поиск по полю name
            if ($this->term_parent) {
                list('condition' => $condition, 'bind' => $bind) =
                    Word::getConditionLikeName('parent_id', $this->term_parent, $depth, $withParent);
            } else {
                list('condition' => $condition, 'bind' => $bind) =
                    Word::getConditionById('parent_id', Status::ALL, $depth, $withParent);
            }

            if (isset($condition)) {
                $data = Word::find()
                    ->select(['name as value'])
                    ->where(['deleted' => Status::NOT_DELETED])
                    ->andFilterWhere(['like', 'name', $this->term . '%', false])
                    ->andOnCondition($condition, $bind)
                    ->orderBy('name')
                    ->limit(Yii::$app->params['maxLinesAutoComplete'])
                    ->distinct()
                    ->asArray()
                    ->all();
            }
        } else {                    // поиск по произвольному полю
            if (in_array($this->term_name, self::COLUMN_SEARCH)) {
                $data = Word::find()
                    ->select(["$this->term_name as value"])
                    ->where(['deleted' => Status::NOT_DELETED])
                    ->andFilterWhere(['like', $this->term_name, $this->term . '%', false])
                    ->orderBy($this->term_name)
                    ->limit(Yii::$app->params['maxLinesAutoComplete'])
                    ->distinct()
                    ->asArray()
                    ->all();
            }
        }

        return json_encode($data);

    }
}
// TODO  $condition == ?? в других поисках
// TODO andFilterWhere % x % и в других тоже
// TODO findNames использовалась в word/form, device/index и была изменена
// TODO bind_name опять не уникальный
