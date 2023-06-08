<?php

use app\widgets\csc\CatalogTabsSort;
use yii\helpers\Html;

$options = json_encode($columns['params']);
$this->registerJs("$('#save_catalog_tabs_sort').on('click', $options, csc.save);");
$this->registerJs("$('#load_catalog_tabs_sort').on('change', $options, csc.load);");
?>

<div id='catalog_tabs_sort' class='connected-sortable-columns clearfix absolute'>
    <h3>Настройка полей фильтра</h3>
    <ul class='connected-sortable sortable1'>
        <? foreach($columns['enabled'] as $value) echo "<li>$value</li>" ?>
    </ul>
    <ul class='connected-sortable sortable2'>
        <? foreach($columns['disabled'] as $value) echo "<li>$value</li>" ?>
    </ul>

    <div class="control">
        <?= Html::dropDownList(
            'profileView',
            Yii::$app->user->identity->getProfileView(),
            CatalogTabsSort::getListProfileView(),
            [
                'id' => 'load_catalog_tabs_sort',
                'class' => 'form-control'
            ]
        ) ?>
        <?= Html::a('Сохранить', null, [
            'id' => 'save_catalog_tabs_sort',
            'class' => 'btn btn-success',
        ]) ?>
        <?= Html::a('Закрыть', null, [
            'id' => 'hide_catalog_tabs_sort',
            'class' => 'btn btn-warning toggle-connected-sortable-columns',
        ]) ?>
    </div>
</div>