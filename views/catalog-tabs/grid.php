<?php

use app\models\Status;
use app\models\Word;
use yii\helpers\Html;

$menu = $catalogTabsSort->getMenu();
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
                    <?php if($tab['source'] === 'word'): ?>
                        <?= Html::input('text', $key, '', $tab['autoComplete']) ?>
                        <?= Html::input('text', $key . '_id', '', ['class' => 'hide']) ?>
                        <?= Html::button('Применить', ['class' => 'filter_button']) ?>
                        <div class="checkboxList"></div>
                    <?php elseif($tab['source'] === 'category'): ?>
                        <?= Html::input('text', $key, '', $tab['autoComplete']) ?>
                        <?= Html::input('text', $key . '_id', '', ['class' => 'hide']) ?>
                        <?= Html::button('Применить', ['class' => 'filter_button']) ?>
                        <div class="checkboxList">
                            <?php foreach (Word::FIELD_WORD as $name => $number):?>
                                <span class="checkbox filter-checkbox" data-value=<?=$number?>><?=Word::LABEL_FIELD_WORD[$number]?></span>
                            <?php endforeach ?>
                        </div>
                    <?php elseif($tab['source'] === 'date'): ?>
                        <?= Html::input('date', $key . '_start') ?>
                        <?= Html::input('date', $key . '_end') ?>
                        <?= Html::button('Применить', ['class' => 'filter_button']) ?>
                    <?php elseif($tab['source'] === 'text'): ?>
                        <?= Html::input('text', $key, '', $tab['autoComplete']) ?>
                        <?= Html::button('Применить', ['class' => 'filter_button']) ?>
                    <?php elseif($tab['source'] === 'deleted'): ?>
                        <?= Html::input('text', $key, '', ['class' => 'hide']) ?>
                        <div class="checkboxList">
                            <span class="checkbox filter-checkbox" data-source=<?=$key?> data-value="<?=Status::NOT_DELETED?>">Действующие</span>
                            <span class="checkbox filter-checkbox" data-source=<?=$key?> data-value="<?=Status::DELETED?>">Удаленные</span>
                            <span class="checkbox filter-checkbox" data-source=<?=$key?> data-value="<?=Status::ALL?>">Все</span>
                        </div>
                    <?php elseif($tab['source'] === 'settingsButton'): ?>
                        <?= Html::a(
                            '<span class="glyphicon glyphicon-cog a-action" data-toggle-id="catalog_tabs_sort"></span>',
                            null,
                            [
                                'title' => 'Настроить меню',
                            ]
                        ) ?>
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
<?php if (Yii::$app->user->can('ChangingCatalogTabsSort')) : ?>
    <?= $catalogTabsSort->runWidget() ?>
<?php endif?>
