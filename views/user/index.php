<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'username',
                'value' => function ($data) {
                    return Html::a($data->username, ['view', 'id' => $data->id]);
                },
                'format' => 'html'
            ],
            [
                'value' => function ($data) {
                    $ret = '';
                    $arrRoles = Yii::$app->authManager->getRolesByUser($data->id);
                    foreach ($arrRoles as $item) {
                        $ret .= Html::a($item->name, ['/auth/view', 'id' => $item->name]);
                        $ret .= ', ';
                    }
                    $ret = rtrim($ret, ', ');
                    return $ret;
                },
                'label' => 'Роли',
                'format' => 'html'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
