<?php

use app\modules\admin\models\AuthItem;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Роли и разрешения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
        $dataProvider->sort = ['defaultOrder' => ['type'=> SORT_ASC]];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name',
                'value' => function ($data) {
                    return Html::a($data->name, ['view', 'id' => $data->name]);
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'type',
                'value' => function ($data) {
                    return $data->type == AuthItem::$ROLE ? 'Роль' : 'Разрешение';
                }
            ],
            'description:ntext',
            [
                'label' => 'Разрешения',
                'value' => function ($data) {
                    $ret = '';
                    $lineBreak = Yii::$app->formatter->asNtext(",\n");
                    foreach ($data->permits as $item) {
                        $ret .= Html::a($item->child, ['view', 'id' => $item->child]).$lineBreak;
                    }
                    $ret = rtrim($ret, $lineBreak);
                    return $ret;
                },
                'format' => 'html',
            ],
//            'rule_name',
//            'data',
            [
                'attribute' => 'created_at',
                'format' => 'date'
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'date'
            ],
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
