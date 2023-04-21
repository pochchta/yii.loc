<?php

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
                    <div class="checkboxList"></div>
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