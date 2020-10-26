<?php

use app\models\Department;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Department */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Цеха', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

if ($model->deleted == Department::NOT_DELETED) {
    $deleteMessage = 'Вы уверены, что хотите удалить этот элемент?';
    $deleteTitle = 'Удалить';
    $deleteText = '';
} else {
    $deleteMessage = 'Вы уверены, что хотите восстановить этот элемент';
    $deleteTitle = 'Восстановить';
    $deleteText = ' (удален)';
}
?>
<div class="department-view">

    <h1><?= Html::encode($this->title) . $deleteText?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a($deleteTitle, ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => $deleteMessage,
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'phone',
            'description:ntext',
            'created_at:date',
            'updated_at:date',
            [
                'attribute' => 'created_by',
                'value' => $model->creator->username,
            ],
            [
                'attribute' => 'updated_by',
                'value' => $model->updater->username,
            ],
        ],
    ]) ?>

</div>
