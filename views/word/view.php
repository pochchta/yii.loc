<?php

use app\models\Status;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Word */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Словарь', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

if ($model->deleted == Status::NOT_DELETED) {
    $deleteMessage = 'Вы уверены, что хотите удалить этот элемент?';
    $deleteTitle = 'Удалить';
    $deleteText = '';
} else {
    $deleteMessage = 'Вы уверены, что хотите восстановить этот элемент';
    $deleteTitle = 'Восстановить';
    $deleteText = ' (удален)';
}
?>
<div class="word-view">

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
//            'id',
            'name',
            'value',
            'description:ntext',
            'created_at:date',
            'updated_at:date',
            [
                'attribute' => 'created_by',
                'value' => $model->creator->username
            ],
            [
                'attribute' => 'created_by',
                'value' => $model->updater->username
            ],
            [
                'attribute' => 'parent_id',
                'value' => in_array($model->parent_id, $model::FIELD_WORD) ? $model::LABEL_FIELD_WORD[$model->parent_id] : $model->parent->name
            ],
        ],
    ]) ?>

</div>
