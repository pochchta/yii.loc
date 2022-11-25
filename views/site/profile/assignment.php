<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Профиль';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>Профиль</h1>

<div class="row">

    <div class="col-xs-4">
        <?= $this->render('menu') ?>
    </div>

    <div class="col-xs-8">
        <h4>Разрешения</h4>

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
</div>