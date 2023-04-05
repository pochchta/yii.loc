<?php

use app\models\Word;

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
                    <?php if($tab['source'] === 'category'): ?>
                        <div class="checkboxList">
                            <?php foreach (Word::FIELD_WORD as $name => $number):?>
                                <span class="checkbox filter-checkbox" data-value=<?=$number?>><?=Word::LABEL_FIELD_WORD[$number]?></span>
                            <?php endforeach ?>
                        </div>

                    <?php endif ?>
                </div>
            <?php endforeach ?>

            <div id="block_arrow1" class="block_arrow glyphicon glyphicon-menu-down hide"></div>
            <div class="tabs_content hide absolute" id="tabs_content2">
                <div id="block_arrow2" class="block_arrow glyphicon glyphicon-menu-down hide"></div>
                <div class="tabs_content hide absolute" id="tabs_content3"></div>
            </div>
        </div>
    </div>
</form>