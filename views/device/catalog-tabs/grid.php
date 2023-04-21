<?php

use app\models\Status;
use yii\helpers\Html;

/* @var $menu app\models\FilterMenu */
?>

<form id="filters-form">
    <div class="catalogTabs">
        <div class="tabs_title" id="tabs">
            <ul>
                <?php foreach ($menu->getMenu() as $key => $tab): ?>
                    <li><a data-value="<?=$tab['id']?>" data-name="<?=$tab['name']?>"><span><?=$tab['label']?></span></a></li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="tabs_content hide absolute" id="tabs_content1">

            <?php foreach ($menu->getMenu() as $key => $tab): ?>
                <div id="tab<?=$tab['id']?>" data-name="<?=$key?>" class="hide">
                    <?php if($tab['source'] === 'number'): ?>
                        <?= Html::input('text', $key, '', ['class' => 'ui-autocomplete-input', 'data' => ['parent' => 'device']]) ?>
                        <?= Html::button('Применить', ['class' => 'filter_button']) ?>
                    <?php elseif($tab['source'] === 'manual'): ?>
                        <?= Html::input('text', $key, '', ['class' => 'hide']) ?>
                        <div class="checkboxList">
                            <span class="checkbox filter-checkbox" data-source=<?=$key?> data-value="<?=Status::NOT_DELETED?>">Действующие</span>
                            <span class="checkbox filter-checkbox" data-source=<?=$key?> data-value="<?=Status::DELETED?>">Удаленные</span>
                            <span class="checkbox filter-checkbox" data-source=<?=$key?> data-value="<?=Status::ALL?>">Все</span>
                        </div>
                    <?php elseif($tab['source'] === 'date'): ?>
                        <?= Html::input('date', $key . '_start') ?>
                        <?= Html::input('date', $key . '_end') ?>
                        <?= Html::button('Применить', ['class' => 'filter_button']) ?>
                    <?php else: ?>
                        <?= Html::input('text', $key, '', ['class' => 'ui-autocomplete-input', 'data' => ['parent' => 'device']]) ?>
                        <?= Html::input('text', $key . '_id', '', ['class' => 'hide']) ?>
                        <?= Html::button('Применить', ['class' => 'filter_button']) ?>
                        <div class="checkboxList"></div>
                    <?php endif ?>
                </div>
            <?php endforeach ?>

            <div id="block_arrow1" class="block_arrow glyphicon glyphicon-menu-down hide"></div>
            <div class="tabs_content hide absolute" id="tabs_content2">
                <div id="block_arrow2" class="block_arrow glyphicon glyphicon-menu-down hide"></div>
                <div class="tabs_content hide absolute" id="tabs_content3"></div>
            </div>
        </div>
        <div class="tabsFilterParams hide">
            <div class="callOffAll filtersReset"><a title="Отменить все фильтры" id="filters-reset"><span>Отменить все фильтры</span></a></div>
            <div class="filterItemsList" id="filters-active"></div>
        </div>
    </div>
</form>