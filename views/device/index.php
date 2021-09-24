<?php

use app\models\DeviceSearch;
use app\models\Status;
use app\models\Word;
use app\models\WordSearch;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\AutoComplete;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeviceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Device */
/* @var $params array */
/* @var $menu array */

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
                    <?php foreach ($menu as $key => $tab): ?>
                    <li><a data-value="tab<?=$tab['id']?>"><span><?=$tab['name']?></span></a></li>
                    <?php endforeach ?>
                </ul>
            </div>
            <div class="tabs_content hide" id="tabs_content1">

                <?php foreach ($menu as $key => $tab): ?>
                    <div id="tab<?=$tab['id']?>" class="hide">
                        <div class="checkboxList">
                            <?php foreach ($tab as $elem): if (is_array($elem) == false) continue;?>
                            <span class="checkbox filter-checkbox" data-value=<?=$elem['id']?>>
                                <?=$elem['name']?>
                            </span>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endforeach ?>

                <div id="block_arrow1" class="block_arrow glyphicon glyphicon-menu-down hide"></div>
                <div class="tabs_content hide" id="tabs_content2">
                    <div id="block_arrow2" class="block_arrow glyphicon glyphicon-menu-down hide"></div>
                    <div class="tabs_content hide" id="tabs_content3"></div>
                </div>
            </div>
            <div class="tabsFilterParams ">
                <!--<a class="fullFilterButton" href="/catalog/npn-small-signal-transistor/all-filters/"><span>Открыть подробный фильтр</span></a>-->
                <div class="callOffAll filtersReset"><a title="Отменить все фильтры" id="filters-reset" href="#"><span>Отменить все фильтры</span></a></div>
                <div class="filterItemsList" id="filters-active"><span class="showOnly">Выводятся только:</span>   <span class="showGroup"> <span class="first">Мощность, Вт</span>   <span><a href="#" class="reset-filter" title="Отменить фильтр" data-type="" data-filter-id="601" data-value="0.25">0.25</a>,</span>  </span></div>
            </div>
        </div>
    </form>
</div>