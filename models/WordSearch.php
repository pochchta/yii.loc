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
    public $limit;
    public $category1, $category2, $category3, $category4;
    public $term, $term_name, $term_p1, $term_p2, $term_p3;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted', 'parent_id'], 'integer'],
            [['name', 'value', 'description'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['deleted'], 'default', 'value' => Status::NOT_DELETED],
            [['category1', 'category2', 'category3', 'category4'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['term', 'term_name', 'term_p1', 'term_p2', 'term_p3'], 'string', 'max' => Yii::$app->params['maxLengthSearchParam']],
            [['category1'], 'default', 'value' => Status::ALL],
            [['category1'], 'validateCategoryName'],
        ];
    }

    public function validateCategoryName($attribute)
    {
        if (!$this->hasErrors()) {
            if ($this->category1 != Status::ALL) {
                if (isset(Word::FIELD_WORD[$this->category1]) == false) {
                    $this->addError($attribute, 'Первая категория не найдена');
                    return;
                }
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Word::find()->with('parent.parent');   // TODO зависит от глубины

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize = $this->limit;

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

        if ($this->category1 != Status::ALL || strlen($this->category2) || strlen($this->category3) || strlen($this->category4)) {
            list('condition' => $condition, 'bind' => $bind) = Word::getConditionByParent([
                'parents' => [$this->category1, $this->category2, $this->category3, $this->category4],
                'depth' => Word::MAX_NUMBER_PARENTS,
                'withParent' => true
            ]);
            $query->andOnCondition($condition, $bind);
        }
        return $dataProvider;
    }

    public function formName()
    {
        return '';
    }

    /**
     * @param $attribute
     * @return array
     */
    public static function getAutoCompleteOptions($attribute)
    {
        return [
            'clientOptions' => [
                'source' => new JsExpression("function(request, response) {
                    $.getJSON('" . Url::to('/word/list-auto-complete') . "', {
                        term: request.term,
                        term_name: '{$attribute}',
                        term_p1: $('#category1').val(),
                        term_p2: $('#category2').val(),
                        term_p3: $('#category3').val(),
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

    /**
     * @param array $params Массив массивов параметров для Word::getConditionByParent
     * @return false|string
     */
    public function findNamesByParents($params)
    {
        $query = Word::find()
            ->select(['name as value'])
            ->where(['deleted' => Status::NOT_DELETED])
            ->andFilterWhere(['like', 'name', $this->term . '%', false])
            ->orderBy('name')
            ->limit(Yii::$app->params['maxLinesAutoComplete'])
//            ->distinct()
            ->asArray();

        foreach ($params as $item) {
            list('condition' => $condition, 'bind' => $bind) = Word::getConditionByParent($item);
            $query->andOnCondition($condition, $bind);
        }

        return json_encode($query->all());
    }

    /** Поиск по значению и названию поля из массива self::COLUMN_SEARCH
     * @return false|string
     */
    public function findNamesByFieldName()
    {
        $data = [];
        if (in_array($this->term_name, self::COLUMN_SEARCH)) {
            $data = Word::find()
                ->select(["$this->term_name as value"])
                ->where(['deleted' => Status::NOT_DELETED])
                ->andFilterWhere(['like', $this->term_name, $this->term . '%', false])
                ->orderBy($this->term_name)
                ->limit(Yii::$app->params['maxLinesAutoComplete'])
                ->distinct()    // value может быть неуникальным
                ->asArray()
                ->all();
        }

        return json_encode($data);
    }

    /** Поиск элементов по родительскому id (parent_id)
     * @param array $params queryParams
     * @return array [['id' => 1, 'value' => 'Название'], []]
     */
    public static function findNamesByParentId($params)
    {
        $names = [];
        $wordSearch = new WordSearch();
        $wordSearch->load($params);
        if ($wordSearch->validate()) {
            $query = Word::find()
                ->select(['id', 'name'])
                ->where(['deleted' => Status::NOT_DELETED])
                ->andFilterWhere(['parent_id' => $wordSearch->parent_id])
                ->orderBy('name')
                ->limit(Yii::$app->params['maxElementsTabMenu'])
//            ->distinct()
                ->asArray();
            $names = $query->all();
        }

        return $names;
    }
}