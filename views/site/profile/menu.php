<?php

use yii\helpers\Html;
?>
<h4>Меню</h4>
<p>
    <?= Html::a('Сменить имя', ['change-name']); ?>
</p>
<p>
    <?= Html::a('Сменить пароль', ['change-pass']); ?>
</p>
<p>
    <?= Html::a('Разрешения', ['assignment']); ?>
</p>
<p>
    <?= Html::a(
        'Выбор вида таблиц',
        ['change-view'],
        ['title' => 'Профиль для отображения и сортировки колонок таблиц']);
    ?>
</p>
