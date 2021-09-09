<?php

use app\models\DeviceSearch;
use app\models\Status;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\AutoComplete;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeviceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Device */
/* @var $params array */

$this->title = 'Приборы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="device-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin([
        'id' => 'my-pjax-container',
        'timeout' => Yii::$app->params['pjaxTimeout'],
    ]) ?>

    <p>
        <?= Html::a('Печать списка', array_merge(['print-list'], $params), [
            'class' => 'btn btn-warning',
            'data' => ['pjax' => 0]
        ]) ?>
        <?= Html::a('Создать новую запись', ['create'], [
            'class' => 'btn btn-success',
            'data' => ['pjax' => 0]
        ]) ?>
    </p>

    <?/*= GridView::widget([
        'id' => 'grid_id',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => ($attribute = 'kind') . '_id',
                'value' => function ($model) use ($attribute) {
                    return $model->{'word' . ucfirst($attribute)}->name;
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + DeviceSearch::getAutoCompleteOptions($attribute, '', true))
            ],
            [
                'attribute' => ($attribute = 'group'),
                'value' => function ($model) use ($attribute) {
                    return $model->wordName->parent->parent->name;
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + DeviceSearch::getAutoCompleteOptions($attribute, '', true))
            ],
            [
                'attribute' => ($attribute = 'type'),
                'value' => function ($model) use ($attribute) {
                    return $model->wordName->parent->name;
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + DeviceSearch::getAutoCompleteOptions($attribute, '', true))
            ],
            [
                'attribute' => ($attribute = 'name') . '_id',
                'value' => function ($model) use ($attribute) {
                    return $model->{'word' . ucfirst($attribute)}->name;
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + DeviceSearch::getAutoCompleteOptions($attribute, '', true))
            ],
            [
                'attribute' => ($attribute = 'state') . '_id',
                'value' => function ($model) use ($attribute) {
                    return $model->{'word' . ucfirst($attribute)}->name;
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + DeviceSearch::getAutoCompleteOptions($attribute, '', true))
            ],
            [
                'attribute' => ($attribute = 'department') . '_id',
                'value' => function ($model) use ($attribute) {
                    return $model->{'word' . ucfirst($attribute)}->name;
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + DeviceSearch::getAutoCompleteOptions($attribute, '', true))
            ],
            [
                'attribute' => $attribute = 'position',
                'value' => function ($model) {
                    return $model->position;
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + DeviceSearch::getAutoCompleteOptions($attribute, '', true))
            ],
            [
                'attribute' => ($attribute = 'crew') . '_id',
                'value' => function ($model) use ($attribute) {
                    return $model->{'word' . ucfirst($attribute)}->name;
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + DeviceSearch::getAutoCompleteOptions($attribute, '', true))
            ],
            [
                'attribute' => $attribute = 'number',
                'value' => function ($model) {
                    return $model->number;
                },
                'filter' => AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => $attribute,
                ] + DeviceSearch::getAutoCompleteOptions($attribute, '', true))
            ],
            [
                'attribute' => 'deleted',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->deleted == Status::NOT_DELETED) {
                        return '';
                    } else {
                        return '<span class="glyphicon glyphicon-remove-sign color-err" title="Удален"></span>';
                    }
                },
                'filter' => Html::activeDropDownList($searchModel, 'deleted', [
                    Status::NOT_DELETED => 'нет',
                    Status::DELETED => 'да',
                    Status::ALL => 'все'
                ])
            ],
            [
                'format' => 'raw',
                'filter' => Html::a(
                    '<span class="glyphicon glyphicon-remove a-action"></span>',
                    ['index'],
                    ['title' => 'Очистить все фильтры']
                ),
                'value' => function ($model) {
                    return
                        Html::a(
                            '<span class="glyphicon glyphicon-eye-open a-action"></span>',
                            ['view', 'id' => $model->id],
                            ['title' => 'Просмотр', 'data' => ['pjax' => 0]]
                        )
                        . Html::a(
                            '<span class="glyphicon glyphicon-log-in a-action"></span>',
                            ['incoming/create', 'device_id' => $model->id],
                            ['title' => 'Новая приемка', 'data' => ['pjax' => 0]]
                        )
                        . Html::a(
                            '<span class="glyphicon glyphicon-scale a-action"></span>',
                            ['verification/create', 'device_id' => $model->id],
                            ['title' => 'Новая поверка', 'data' => ['pjax' => 0]]
                        );
                }
            ],
        ],
    ]); */?>

    <?php Pjax::end() ?>

    <form id="filters-form" action="" method="get">
        <div class="catalogTabs">
            <div class="tabs_title" id="tabs">
                <ul>
                    <li><a href="#tab14" id="filter-title-14" class="rfalse"><span>Корпус</span></a></li>
                    <li><a href="#tab600" id="filter-title-600" class="rfalse"><span>Распиновка</span></a></li>
                    <li><a href="#tab601" id="filter-title-601" class="rfalse"><span>Мощность</span><i>, Вт</i></a></li>
                    <li><a href="#tab189" id="filter-title-189" class="rfalse"><span>Ток</span><i>, А</i></a></li>
                    <li><a href="#tab192" id="filter-title-192" class="rfalse"><span>Напр. ЭБ</span><i>, В</i></a></li>
                    <li><a href="#tab191" id="filter-title-191" class="rfalse"><span>Напр. КЭ</span><i>, В</i></a></li>
                    <li><a href="#tab190" id="filter-title-190" class="rfalse"><span>Напр. КБ</span><i>, В</i></a></li>
                </ul>
            </div>
            <div id="block_arrow1" class="block_arrow glyphicon glyphicon-scale"></div>
            <div class="tabs_content hide" id="tabs_content1">
                <div id="tab14" class="hide">
                    <div class="checkboxList ">
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="DFN1006-3H" data-id="filter[14][]_DFN1006-3H" type="checkbox" value="1" name="filter[14][]">
                            <span class="checkboxIn">DFN1006-3H <small>15</small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-23" data-id="filter[14][]_SOT-23" type="checkbox" value="2" name="filter[14][]">
                            <span class="checkboxIn">SOT-23 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-26" data-id="filter[14][]_SOT-26" type="checkbox" value="3" name="filter[14][]">
                            <span class="checkboxIn">SOT-26 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-323" data-id="filter[14][]_SOT-323" type="checkbox" value="4" name="filter[14][]">
                            <span class="checkboxIn">SOT-323 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-363" data-id="filter[14][]_SOT-363" type="checkbox" value="5" name="filter[14][]">
                            <span class="checkboxIn">SOT-363 <small>15</small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-523" data-id="filter[14][]_SOT-523" type="checkbox" value="6" name="filter[14][]">
                            <span class="checkboxIn">SOT-523 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-89" data-id="filter[14][]_SOT-89" type="checkbox" value="7" name="filter[14][]">
                            <span class="checkboxIn">SOT-89 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="TO-92" data-id="filter[14][]_TO-92" type="checkbox" value="8" name="filter[14][]">
                            <span class="checkboxIn">TO-92 <small>32</small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="TO-93" data-id="filter[14][]_TO-93" type="checkbox" value="9" name="filter[14][]">
                            <span class="checkboxIn">TO-93 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="DFN1006-3H" data-id="filter[14][]_DFN1006-3H" type="checkbox" value="1" name="filter[14][]">
                            <span class="checkboxIn">DFN1006-3H <small>15</small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-23" data-id="filter[14][]_SOT-23" type="checkbox" value="2" name="filter[14][]">
                            <span class="checkboxIn">SOT-23 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-26" data-id="filter[14][]_SOT-26" type="checkbox" value="3" name="filter[14][]">
                            <span class="checkboxIn">SOT-26 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-323" data-id="filter[14][]_SOT-323" type="checkbox" value="4" name="filter[14][]">
                            <span class="checkboxIn">SOT-323 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-363" data-id="filter[14][]_SOT-363" type="checkbox" value="5" name="filter[14][]">
                            <span class="checkboxIn">SOT-363 <small>15</small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-523" data-id="filter[14][]_SOT-523" type="checkbox" value="6" name="filter[14][]">
                            <span class="checkboxIn">SOT-523 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-89" data-id="filter[14][]_SOT-89" type="checkbox" value="7" name="filter[14][]">
                            <span class="checkboxIn">SOT-89 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="TO-92" data-id="filter[14][]_TO-92" type="checkbox" value="8" name="filter[14][]">
                            <span class="checkboxIn">TO-92 <small>32</small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="TO-93" data-id="filter[14][]_TO-93" type="checkbox" value="9" name="filter[14][]">
                            <span class="checkboxIn">TO-93 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="DFN1006-3H" data-id="filter[14][]_DFN1006-3H" type="checkbox" value="1" name="filter[14][]">
                            <span class="checkboxIn">DFN1006-3H <small>15</small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-23" data-id="filter[14][]_SOT-23" type="checkbox" value="2" name="filter[14][]">
                            <span class="checkboxIn">SOT-23 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-26" data-id="filter[14][]_SOT-26" type="checkbox" value="3" name="filter[14][]">
                            <span class="checkboxIn">SOT-26 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-323" data-id="filter[14][]_SOT-323" type="checkbox" value="4" name="filter[14][]">
                            <span class="checkboxIn">SOT-323 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-363" data-id="filter[14][]_SOT-363" type="checkbox" value="5" name="filter[14][]">
                            <span class="checkboxIn">SOT-363 <small>15</small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-523" data-id="filter[14][]_SOT-523" type="checkbox" value="6" name="filter[14][]">
                            <span class="checkboxIn">SOT-523 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="SOT-89" data-id="filter[14][]_SOT-89" type="checkbox" value="7" name="filter[14][]">
                            <span class="checkboxIn">SOT-89 <small></small></span>
                        </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="TO-92" data-id="filter[14][]_TO-92" type="checkbox" value="8" name="filter[14][]">
                            <span class="checkboxIn">TO-92 <small>32</small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="TO-93" data-id="filter[14][]_TO-93" type="checkbox" value="9" name="filter[14][]">
                            <span class="checkboxIn">TO-93 <small></small></span>
                        </span>
                    </div>
                </div>
                <div id="tab600" class="hide" >
                    <div class="checkboxList ">
                                <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="600" data-value-title="BCE" data-id="filter[600][]_BCE" type="checkbox" value="BCE" name="filter[600][]">
                    <span class="checkboxIn">BCE <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="600" data-value-title="BEC" data-id="filter[600][]_BEC" type="checkbox" value="BEC" name="filter[600][]">
                    <span class="checkboxIn">BEC <small>15</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="600" data-value-title="CBE" data-id="filter[600][]_CBE" type="checkbox" value="CBE" name="filter[600][]">
                    <span class="checkboxIn">CBE <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="600" data-value-title="EBC" data-id="filter[600][]_EBC" type="checkbox" value="EBC" name="filter[600][]">
                    <span class="checkboxIn">EBC <small>18</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="600" data-value-title="EBCEBC" data-id="filter[600][]_EBCEBC" type="checkbox" value="EBCEBC" name="filter[600][]">
                    <span class="checkboxIn">EBCEBC <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="600" data-value-title="ECB" data-id="filter[600][]_ECB" type="checkbox" value="ECB" name="filter[600][]">
                    <span class="checkboxIn">ECB <small>29</small></span>
                </span>
                    </div>
                    <div class="paramsNotice">Распиновка (Цоколевка)</div>        </div>
                <div id="tab601" class="hide">
                    <div class="checkboxList ">
                                <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.1" data-id="filter[601][]_0.1" type="checkbox" value="0.1" name="filter[601][]">
                    <span class="checkboxIn">0.1 <small>13</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.125" data-id="filter[601][]_0.125" type="checkbox" value="0.125" name="filter[601][]">
                    <span class="checkboxIn">0.125 <small>3</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.15" data-id="filter[601][]_0.15" type="checkbox" value="0.15" name="filter[601][]">
                    <span class="checkboxIn">0.15 <small>50</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.2" data-id="filter[601][]_0.2" type="checkbox" value="0.2" name="filter[601][]">
                    <span class="checkboxIn">0.2 <small>236</small></span>
                </span>
                        <span class="checkbox filter-checkbox  checked" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.25" data-id="filter[601][]_0.25" type="checkbox" value="0.25" name="filter[601][]" checked="checked">
                    <span class="checkboxIn">0.25 <small>62</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.3" data-id="filter[601][]_0.3" type="checkbox" value="0.3" name="filter[601][]">
                    <span class="checkboxIn">0.3 <small>34</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.35" data-id="filter[601][]_0.35" type="checkbox" value="0.35" name="filter[601][]">
                    <span class="checkboxIn">0.35 <small>38</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.4" data-id="filter[601][]_0.4" type="checkbox" value="0.4" name="filter[601][]">
                    <span class="checkboxIn">0.4 <small>51</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.45" data-id="filter[601][]_0.45" type="checkbox" value="0.45" name="filter[601][]">
                    <span class="checkboxIn">0.45 <small>4</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.5" data-id="filter[601][]_0.5" type="checkbox" value="0.5" name="filter[601][]">
                    <span class="checkboxIn">0.5 <small>42</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.6" data-id="filter[601][]_0.6" type="checkbox" value="0.6" name="filter[601][]">
                    <span class="checkboxIn">0.6 <small>25</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.625" data-id="filter[601][]_0.625" type="checkbox" value="0.625" name="filter[601][]">
                    <span class="checkboxIn">0.625 <small>45</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.75" data-id="filter[601][]_0.75" type="checkbox" value="0.75" name="filter[601][]">
                    <span class="checkboxIn">0.75 <small>20</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.8" data-id="filter[601][]_0.8" type="checkbox" value="0.8" name="filter[601][]">
                    <span class="checkboxIn">0.8 <small>5</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.83" data-id="filter[601][]_0.83" type="checkbox" value="0.83" name="filter[601][]">
                    <span class="checkboxIn">0.83 <small>3</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.85" data-id="filter[601][]_0.85" type="checkbox" value="0.85" name="filter[601][]">
                    <span class="checkboxIn">0.85 <small>1</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.9" data-id="filter[601][]_0.9" type="checkbox" value="0.9" name="filter[601][]">
                    <span class="checkboxIn">0.9 <small>7</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.95" data-id="filter[601][]_0.95" type="checkbox" value="0.95" name="filter[601][]">
                    <span class="checkboxIn">0.95 <small>1</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="1" data-id="filter[601][]_1" type="checkbox" value="1" name="filter[601][]">
                    <span class="checkboxIn">1 <small>19</small></span>
                </span>
                    </div>
                    <div class="paramsNotice">Суммарная рассеиваемая мощность</div>        </div>
                <div id="tab189" class="hide">
                    <div class="checkboxList ">
                                <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.015" data-id="filter[189][]_0.015" type="checkbox" value="0.015" name="filter[189][]">
                    <span class="checkboxIn">0.015 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.02" data-id="filter[189][]_0.02" type="checkbox" value="0.02" name="filter[189][]">
                    <span class="checkboxIn">0.02 <small>9</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.025" data-id="filter[189][]_0.025" type="checkbox" value="0.025" name="filter[189][]">
                    <span class="checkboxIn">0.025 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.03" data-id="filter[189][]_0.03" type="checkbox" value="0.03" name="filter[189][]">
                    <span class="checkboxIn">0.03 <small>7</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.05" data-id="filter[189][]_0.05" type="checkbox" value="0.05" name="filter[189][]">
                    <span class="checkboxIn">0.05 <small>3</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.08" data-id="filter[189][]_0.08" type="checkbox" value="0.08" name="filter[189][]">
                    <span class="checkboxIn">0.08 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.1" data-id="filter[189][]_0.1" type="checkbox" value="0.1" name="filter[189][]">
                    <span class="checkboxIn">0.1 <small>31</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.15" data-id="filter[189][]_0.15" type="checkbox" value="0.15" name="filter[189][]">
                    <span class="checkboxIn">0.15 <small>9</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.2" data-id="filter[189][]_0.2" type="checkbox" value="0.2" name="filter[189][]">
                    <span class="checkboxIn">0.2 <small>3</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.3" data-id="filter[189][]_0.3" type="checkbox" value="0.3" name="filter[189][]">
                    <span class="checkboxIn">0.3 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.5" data-id="filter[189][]_0.5" type="checkbox" value="0.5" name="filter[189][]">
                    <span class="checkboxIn">0.5 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.6" data-id="filter[189][]_0.6" type="checkbox" value="0.6" name="filter[189][]">
                    <span class="checkboxIn">0.6 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.7" data-id="filter[189][]_0.7" type="checkbox" value="0.7" name="filter[189][]">
                    <span class="checkboxIn">0.7 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.75" data-id="filter[189][]_0.75" type="checkbox" value="0.75" name="filter[189][]">
                    <span class="checkboxIn">0.75 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.8" data-id="filter[189][]_0.8" type="checkbox" value="0.8" name="filter[189][]">
                    <span class="checkboxIn">0.8 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="1" data-id="filter[189][]_1" type="checkbox" value="1" name="filter[189][]">
                    <span class="checkboxIn">1 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="1.25" data-id="filter[189][]_1.25" type="checkbox" value="1.25" name="filter[189][]">
                    <span class="checkboxIn">1.25 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="1.5" data-id="filter[189][]_1.5" type="checkbox" value="1.5" name="filter[189][]">
                    <span class="checkboxIn">1.5 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="2" data-id="filter[189][]_2" type="checkbox" value="2" name="filter[189][]">
                    <span class="checkboxIn">2 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="3" data-id="filter[189][]_3" type="checkbox" value="3" name="filter[189][]">
                    <span class="checkboxIn">3 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="5" data-id="filter[189][]_5" type="checkbox" value="5" name="filter[189][]">
                    <span class="checkboxIn">5 <small></small></span>
                </span>
                    </div>
                    <div class="paramsNotice">Ток коллектора</div>        </div>
                <div id="tab192" class="hide">
                    <div class="rangeSliderWrapper mainFilter">
                        <span class="irs js-irs-0"><span class="irs" style=""><span class="irs-line" tabindex="-1"><span class="irs-line-left"></span><span class="irs-line-mid"></span><span class="irs-line-right"></span></span><span class="irs-min" style="visibility: hidden;">2</span><span class="irs-max" style="visibility: hidden;">25</span><span class="irs-from" style="visibility: visible; left: -0.875%;">2</span><span class="irs-to" style="visibility: visible; left: 95.875%;">25</span><span class="irs-single" style="visibility: hidden; left: 44.375%;">2 — 25</span></span><span class="irs-grid"></span><span class="irs-bar" style="left: 1.25%; width: 97.5%;"></span><span class="irs-shadow shadow-from" style="display: none;"></span><span class="irs-shadow shadow-to" style="display: none;"></span><span class="irs-slider from" style="left: 0%;"></span><span class="irs-slider to" style="left: 97.5%;"></span></span><div id="rangeSlider_192" class="irs-hidden-input"></div>
                        <input class="minCost rangeInputStart" type="hidden" data-filter-id="192" data-type="range" name="filter[192][min]" value="">
                        <input class="maxCost" type="hidden" data-filter-id="192" data-type="range" name="filter[192][max]" value="">
                    </div>
                    <div class="paramsNotice">Напряжение эмиттер-база</div>        </div>
                <div id="tab191" class="hide">
                    <div class="rangeSliderWrapper mainFilter">
                        <span class="irs js-irs-1"><span class="irs" style=""><span class="irs-line" tabindex="-1"><span class="irs-line-left"></span><span class="irs-line-mid"></span><span class="irs-line-right"></span></span><span class="irs-min" style="">5</span><span class="irs-max" style="">450</span><span class="irs-from">0</span><span class="irs-to">0</span><span class="irs-single">0</span></span><span class="irs-grid"></span><span class="irs-bar"></span><span class="irs-shadow shadow-from"></span><span class="irs-shadow shadow-to"></span><span class="irs-slider from" style=""></span><span class="irs-slider to"></span></span><div id="rangeSlider_191" class="irs-hidden-input"></div>
                        <input class="minCost rangeInputStart" type="hidden" data-filter-id="191" data-type="range" name="filter[191][min]" value="">
                        <input class="maxCost" type="hidden" data-filter-id="191" data-type="range" name="filter[191][max]" value="">
                    </div>
                    <div class="paramsNotice">Напряжение коллектор-эмиттер</div>        </div>
                <div id="tab190" class="hide">
                    <div class="rangeSliderWrapper mainFilter">
                        <span class="irs js-irs-2"><span class="irs" style=""><span class="irs-line" tabindex="-1"><span class="irs-line-left"></span><span class="irs-line-mid"></span><span class="irs-line-right"></span></span><span class="irs-min" style="">15</span><span class="irs-max" style="">700</span><span class="irs-from">0</span><span class="irs-to">0</span><span class="irs-single">0</span></span><span class="irs-grid"></span><span class="irs-bar"></span><span class="irs-shadow shadow-from"></span><span class="irs-shadow shadow-to"></span><span class="irs-slider from" style=""></span><span class="irs-slider to"></span></span><div id="rangeSlider_190" class="irs-hidden-input"></div>
                        <input class="minCost rangeInputStart" type="hidden" data-filter-id="190" data-type="range" name="filter[190][min]" value="">
                        <input class="maxCost" type="hidden" data-filter-id="190" data-type="range" name="filter[190][max]" value="">
                    </div>
                    <div class="paramsNotice">Напряжение коллектор-база</div>        </div>
            </div>
            <div class="tabs_content hide" id="tabs_content2">
                <div id="tab1" class="hide">
                    <div class="checkboxList ">
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="DFN1006-3H" data-id="filter[14][]_DFN1006-3H" type="checkbox" value="DFN1006-3H" name="filter[14][]">
                            <span class="checkboxIn">DFN1006-3H <small>15</small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-23" data-id="filter[14][]_SOT-23" type="checkbox" value="SOT-23" name="filter[14][]">
                    <span class="checkboxIn">SOT-23 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-26" data-id="filter[14][]_SOT-26" type="checkbox" value="SOT-26" name="filter[14][]">
                    <span class="checkboxIn">SOT-26 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-323" data-id="filter[14][]_SOT-323" type="checkbox" value="SOT-323" name="filter[14][]">
                    <span class="checkboxIn">SOT-323 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-363" data-id="filter[14][]_SOT-363" type="checkbox" value="SOT-363" name="filter[14][]">
                    <span class="checkboxIn">SOT-363 <small>15</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-523" data-id="filter[14][]_SOT-523" type="checkbox" value="SOT-523" name="filter[14][]">
                    <span class="checkboxIn">SOT-523 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-89" data-id="filter[14][]_SOT-89" type="checkbox" value="SOT-89" name="filter[14][]">
                    <span class="checkboxIn">SOT-89 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="TO-92" data-id="filter[14][]_TO-92" type="checkbox" value="TO-92" name="filter[14][]">
                    <span class="checkboxIn">TO-92 <small>32</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="TO-93" data-id="filter[14][]_TO-93" type="checkbox" value="TO-93" name="filter[14][]">
                    <span class="checkboxIn">TO-93 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="DFN1006-3H" data-id="filter[14][]_DFN1006-3H" type="checkbox" value="DFN1006-3H" name="filter[14][]">
                            <span class="checkboxIn">DFN1006-3H <small>15</small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-23" data-id="filter[14][]_SOT-23" type="checkbox" value="SOT-23" name="filter[14][]">
                    <span class="checkboxIn">SOT-23 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-26" data-id="filter[14][]_SOT-26" type="checkbox" value="SOT-26" name="filter[14][]">
                    <span class="checkboxIn">SOT-26 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-323" data-id="filter[14][]_SOT-323" type="checkbox" value="SOT-323" name="filter[14][]">
                    <span class="checkboxIn">SOT-323 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-363" data-id="filter[14][]_SOT-363" type="checkbox" value="SOT-363" name="filter[14][]">
                    <span class="checkboxIn">SOT-363 <small>15</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-523" data-id="filter[14][]_SOT-523" type="checkbox" value="SOT-523" name="filter[14][]">
                    <span class="checkboxIn">SOT-523 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-89" data-id="filter[14][]_SOT-89" type="checkbox" value="SOT-89" name="filter[14][]">
                    <span class="checkboxIn">SOT-89 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="TO-92" data-id="filter[14][]_TO-92" type="checkbox" value="TO-92" name="filter[14][]">
                    <span class="checkboxIn">TO-92 <small>32</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="TO-93" data-id="filter[14][]_TO-93" type="checkbox" value="TO-93" name="filter[14][]">
                    <span class="checkboxIn">TO-93 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                            <span class="hidden"></span>
                            <input data-filter-id="14" data-value-title="DFN1006-3H" data-id="filter[14][]_DFN1006-3H" type="checkbox" value="DFN1006-3H" name="filter[14][]">
                            <span class="checkboxIn">DFN1006-3H <small>15</small></span>
                        </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-23" data-id="filter[14][]_SOT-23" type="checkbox" value="SOT-23" name="filter[14][]">
                    <span class="checkboxIn">SOT-23 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-26" data-id="filter[14][]_SOT-26" type="checkbox" value="SOT-26" name="filter[14][]">
                    <span class="checkboxIn">SOT-26 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-323" data-id="filter[14][]_SOT-323" type="checkbox" value="SOT-323" name="filter[14][]">
                    <span class="checkboxIn">SOT-323 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-363" data-id="filter[14][]_SOT-363" type="checkbox" value="SOT-363" name="filter[14][]">
                    <span class="checkboxIn">SOT-363 <small>15</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-523" data-id="filter[14][]_SOT-523" type="checkbox" value="SOT-523" name="filter[14][]">
                    <span class="checkboxIn">SOT-523 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="SOT-89" data-id="filter[14][]_SOT-89" type="checkbox" value="SOT-89" name="filter[14][]">
                    <span class="checkboxIn">SOT-89 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="TO-92" data-id="filter[14][]_TO-92" type="checkbox" value="TO-92" name="filter[14][]">
                    <span class="checkboxIn">TO-92 <small>32</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="14" data-value-title="TO-93" data-id="filter[14][]_TO-93" type="checkbox" value="TO-93" name="filter[14][]">
                    <span class="checkboxIn">TO-93 <small></small></span>
                </span>
                    </div>
                </div>
                <div id="tab2" class="hide" >
                    <div class="checkboxList ">
                                <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="600" data-value-title="BCE" data-id="filter[600][]_BCE" type="checkbox" value="BCE" name="filter[600][]">
                    <span class="checkboxIn">BCE <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="600" data-value-title="BEC" data-id="filter[600][]_BEC" type="checkbox" value="BEC" name="filter[600][]">
                    <span class="checkboxIn">BEC <small>15</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="600" data-value-title="CBE" data-id="filter[600][]_CBE" type="checkbox" value="CBE" name="filter[600][]">
                    <span class="checkboxIn">CBE <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="600" data-value-title="EBC" data-id="filter[600][]_EBC" type="checkbox" value="EBC" name="filter[600][]">
                    <span class="checkboxIn">EBC <small>18</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="600" data-value-title="EBCEBC" data-id="filter[600][]_EBCEBC" type="checkbox" value="EBCEBC" name="filter[600][]">
                    <span class="checkboxIn">EBCEBC <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="600" data-value-title="ECB" data-id="filter[600][]_ECB" type="checkbox" value="ECB" name="filter[600][]">
                    <span class="checkboxIn">ECB <small>29</small></span>
                </span>
                    </div>
                    <div class="paramsNotice">Распиновка (Цоколевка)</div>        </div>
                <div id="tab3" class="hide">
                    <div class="checkboxList ">
                                <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.1" data-id="filter[601][]_0.1" type="checkbox" value="0.1" name="filter[601][]">
                    <span class="checkboxIn">0.1 <small>13</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.125" data-id="filter[601][]_0.125" type="checkbox" value="0.125" name="filter[601][]">
                    <span class="checkboxIn">0.125 <small>3</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.15" data-id="filter[601][]_0.15" type="checkbox" value="0.15" name="filter[601][]">
                    <span class="checkboxIn">0.15 <small>50</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.2" data-id="filter[601][]_0.2" type="checkbox" value="0.2" name="filter[601][]">
                    <span class="checkboxIn">0.2 <small>236</small></span>
                </span>
                        <span class="checkbox filter-checkbox  checked" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.25" data-id="filter[601][]_0.25" type="checkbox" value="0.25" name="filter[601][]" checked="checked">
                    <span class="checkboxIn">0.25 <small>62</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.3" data-id="filter[601][]_0.3" type="checkbox" value="0.3" name="filter[601][]">
                    <span class="checkboxIn">0.3 <small>34</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.35" data-id="filter[601][]_0.35" type="checkbox" value="0.35" name="filter[601][]">
                    <span class="checkboxIn">0.35 <small>38</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.4" data-id="filter[601][]_0.4" type="checkbox" value="0.4" name="filter[601][]">
                    <span class="checkboxIn">0.4 <small>51</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.45" data-id="filter[601][]_0.45" type="checkbox" value="0.45" name="filter[601][]">
                    <span class="checkboxIn">0.45 <small>4</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.5" data-id="filter[601][]_0.5" type="checkbox" value="0.5" name="filter[601][]">
                    <span class="checkboxIn">0.5 <small>42</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.6" data-id="filter[601][]_0.6" type="checkbox" value="0.6" name="filter[601][]">
                    <span class="checkboxIn">0.6 <small>25</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.625" data-id="filter[601][]_0.625" type="checkbox" value="0.625" name="filter[601][]">
                    <span class="checkboxIn">0.625 <small>45</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.75" data-id="filter[601][]_0.75" type="checkbox" value="0.75" name="filter[601][]">
                    <span class="checkboxIn">0.75 <small>20</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.8" data-id="filter[601][]_0.8" type="checkbox" value="0.8" name="filter[601][]">
                    <span class="checkboxIn">0.8 <small>5</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.83" data-id="filter[601][]_0.83" type="checkbox" value="0.83" name="filter[601][]">
                    <span class="checkboxIn">0.83 <small>3</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.85" data-id="filter[601][]_0.85" type="checkbox" value="0.85" name="filter[601][]">
                    <span class="checkboxIn">0.85 <small>1</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.9" data-id="filter[601][]_0.9" type="checkbox" value="0.9" name="filter[601][]">
                    <span class="checkboxIn">0.9 <small>7</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="0.95" data-id="filter[601][]_0.95" type="checkbox" value="0.95" name="filter[601][]">
                    <span class="checkboxIn">0.95 <small>1</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="601" data-value-title="1" data-id="filter[601][]_1" type="checkbox" value="1" name="filter[601][]">
                    <span class="checkboxIn">1 <small>19</small></span>
                </span>
                    </div>
                    <div class="paramsNotice">Суммарная рассеиваемая мощность</div>        </div>
                <div id="tab4" class="hide">
                    <div class="checkboxList ">
                                <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.015" data-id="filter[189][]_0.015" type="checkbox" value="0.015" name="filter[189][]">
                    <span class="checkboxIn">0.015 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.02" data-id="filter[189][]_0.02" type="checkbox" value="0.02" name="filter[189][]">
                    <span class="checkboxIn">0.02 <small>9</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.025" data-id="filter[189][]_0.025" type="checkbox" value="0.025" name="filter[189][]">
                    <span class="checkboxIn">0.025 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.03" data-id="filter[189][]_0.03" type="checkbox" value="0.03" name="filter[189][]">
                    <span class="checkboxIn">0.03 <small>7</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.05" data-id="filter[189][]_0.05" type="checkbox" value="0.05" name="filter[189][]">
                    <span class="checkboxIn">0.05 <small>3</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.08" data-id="filter[189][]_0.08" type="checkbox" value="0.08" name="filter[189][]">
                    <span class="checkboxIn">0.08 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.1" data-id="filter[189][]_0.1" type="checkbox" value="0.1" name="filter[189][]">
                    <span class="checkboxIn">0.1 <small>31</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.15" data-id="filter[189][]_0.15" type="checkbox" value="0.15" name="filter[189][]">
                    <span class="checkboxIn">0.15 <small>9</small></span>
                </span>
                        <span class="checkbox filter-checkbox" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.2" data-id="filter[189][]_0.2" type="checkbox" value="0.2" name="filter[189][]">
                    <span class="checkboxIn">0.2 <small>3</small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.3" data-id="filter[189][]_0.3" type="checkbox" value="0.3" name="filter[189][]">
                    <span class="checkboxIn">0.3 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.5" data-id="filter[189][]_0.5" type="checkbox" value="0.5" name="filter[189][]">
                    <span class="checkboxIn">0.5 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.6" data-id="filter[189][]_0.6" type="checkbox" value="0.6" name="filter[189][]">
                    <span class="checkboxIn">0.6 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.7" data-id="filter[189][]_0.7" type="checkbox" value="0.7" name="filter[189][]">
                    <span class="checkboxIn">0.7 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.75" data-id="filter[189][]_0.75" type="checkbox" value="0.75" name="filter[189][]">
                    <span class="checkboxIn">0.75 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="0.8" data-id="filter[189][]_0.8" type="checkbox" value="0.8" name="filter[189][]">
                    <span class="checkboxIn">0.8 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="1" data-id="filter[189][]_1" type="checkbox" value="1" name="filter[189][]">
                    <span class="checkboxIn">1 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="1.25" data-id="filter[189][]_1.25" type="checkbox" value="1.25" name="filter[189][]">
                    <span class="checkboxIn">1.25 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="1.5" data-id="filter[189][]_1.5" type="checkbox" value="1.5" name="filter[189][]">
                    <span class="checkboxIn">1.5 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="2" data-id="filter[189][]_2" type="checkbox" value="2" name="filter[189][]">
                    <span class="checkboxIn">2 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="3" data-id="filter[189][]_3" type="checkbox" value="3" name="filter[189][]">
                    <span class="checkboxIn">3 <small></small></span>
                </span>
                        <span class="checkbox filter-checkbox disabled" unselectable="on" style="user-select: none;">
                    <span class="hidden"></span>
                    <input data-filter-id="189" data-value-title="5" data-id="filter[189][]_5" type="checkbox" value="5" name="filter[189][]">
                    <span class="checkboxIn">5 <small></small></span>
                </span>
                    </div>
                    <div class="paramsNotice">Ток коллектора</div>        </div>
                <div id="tab5" class="hide">
                    <div class="rangeSliderWrapper mainFilter">
                        <span class="irs js-irs-0"><span class="irs" style=""><span class="irs-line" tabindex="-1"><span class="irs-line-left"></span><span class="irs-line-mid"></span><span class="irs-line-right"></span></span><span class="irs-min" style="visibility: hidden;">2</span><span class="irs-max" style="visibility: hidden;">25</span><span class="irs-from" style="visibility: visible; left: -0.875%;">2</span><span class="irs-to" style="visibility: visible; left: 95.875%;">25</span><span class="irs-single" style="visibility: hidden; left: 44.375%;">2 — 25</span></span><span class="irs-grid"></span><span class="irs-bar" style="left: 1.25%; width: 97.5%;"></span><span class="irs-shadow shadow-from" style="display: none;"></span><span class="irs-shadow shadow-to" style="display: none;"></span><span class="irs-slider from" style="left: 0%;"></span><span class="irs-slider to" style="left: 97.5%;"></span></span><div id="rangeSlider_192" class="irs-hidden-input"></div>
                        <input class="minCost rangeInputStart" type="hidden" data-filter-id="192" data-type="range" name="filter[192][min]" value="">
                        <input class="maxCost" type="hidden" data-filter-id="192" data-type="range" name="filter[192][max]" value="">
                    </div>
                    <div class="paramsNotice">Напряжение эмиттер-база</div>        </div>
                <div id="tab6" class="hide">
                    <div class="rangeSliderWrapper mainFilter">
                        <span class="irs js-irs-1"><span class="irs" style=""><span class="irs-line" tabindex="-1"><span class="irs-line-left"></span><span class="irs-line-mid"></span><span class="irs-line-right"></span></span><span class="irs-min" style="">5</span><span class="irs-max" style="">450</span><span class="irs-from">0</span><span class="irs-to">0</span><span class="irs-single">0</span></span><span class="irs-grid"></span><span class="irs-bar"></span><span class="irs-shadow shadow-from"></span><span class="irs-shadow shadow-to"></span><span class="irs-slider from" style=""></span><span class="irs-slider to"></span></span><div id="rangeSlider_191" class="irs-hidden-input"></div>
                        <input class="minCost rangeInputStart" type="hidden" data-filter-id="191" data-type="range" name="filter[191][min]" value="">
                        <input class="maxCost" type="hidden" data-filter-id="191" data-type="range" name="filter[191][max]" value="">
                    </div>
                    <div class="paramsNotice">Напряжение коллектор-эмиттер</div>        </div>
                <div id="tab7" class="hide">
                    <div class="rangeSliderWrapper mainFilter">
                        <span class="irs js-irs-2"><span class="irs" style=""><span class="irs-line" tabindex="-1"><span class="irs-line-left"></span><span class="irs-line-mid"></span><span class="irs-line-right"></span></span><span class="irs-min" style="">15</span><span class="irs-max" style="">700</span><span class="irs-from">0</span><span class="irs-to">0</span><span class="irs-single">0</span></span><span class="irs-grid"></span><span class="irs-bar"></span><span class="irs-shadow shadow-from"></span><span class="irs-shadow shadow-to"></span><span class="irs-slider from" style=""></span><span class="irs-slider to"></span></span><div id="rangeSlider_190" class="irs-hidden-input"></div>
                        <input class="minCost rangeInputStart" type="hidden" data-filter-id="190" data-type="range" name="filter[190][min]" value="">
                        <input class="maxCost" type="hidden" data-filter-id="190" data-type="range" name="filter[190][max]" value="">
                    </div>
                    <div class="paramsNotice">Напряжение коллектор-база</div>        </div>
            </div>
            <div class="tabsFilterParams ">
                <!--<a class="fullFilterButton" href="/catalog/npn-small-signal-transistor/all-filters/"><span>Открыть подробный фильтр</span></a>-->
                <div class="callOffAll filtersReset"><a title="Отменить все фильтры" id="filters-reset" href="#"><span>Отменить все фильтры</span></a></div>
                <div class="filterItemsList" id="filters-active"><span class="showOnly">Выводятся только:</span>   <span class="showGroup"> <span class="first">Мощность, Вт</span>   <span><a href="#" class="reset-filter" title="Отменить фильтр" data-type="" data-filter-id="601" data-value="0.25">0.25</a>,</span>  </span></div>
            </div>
        </div>
    </form>




</div>

<script>
    let filters = {
        1: {2: 'Два'},
        2: {3: 'Три', 4: 'Четыре'},
        3: {5: 'Пять'},
        4: {6: 'Шесть'},
    };
</script>