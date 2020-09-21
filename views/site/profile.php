<?php

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Профиль';
$this->params['breadcrumbs'][] = $this->title;
?>

<div>
    <h1>Профиль</h1>
    <p>
        Информация о пользователе <?= \yii::$app->user->identity->username ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'item_name',
                'label' => 'Роль',
                'value' => function ($data) {
                    return Html::a(
                        $data->item_name,
                        ['/admin/auth/view', 'id' => $data->item_name],
                        ['title' => $data->item->description]
                    );
                },
                'format' => 'html'
            ],
            'item.description',
            [
                'label' => 'Разрешения',
                'value' => function ($data) {
                    $ret = '';
                    $lineBreak = Yii::$app->formatter->asNtext(",\n");
                    foreach ($data->permits as $item) {
                        $ret .= Html::a(
                                $item->child,
                                ['/admin/auth/view', 'id' => $item->child],
                                ['title' => $item->itemChild->description]
                            ).$lineBreak;
                    }
                    $ret = rtrim($ret, $lineBreak);
                    return $ret;
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Назначено',
                'format' => 'date'
            ],
        ],
    ]); ?>
</div>