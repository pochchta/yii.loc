<?php

use app\models\Status;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Device */

$this->title = $model->wordName->name;
$this->params['breadcrumbs'][] = ['label' => 'Приборы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

if ($model->deleted_id == Status::NOT_DELETED) {
    $deleteMessage = 'Вы уверены, что хотите удалить этот элемент?';
    $deleteTitle = 'Удалить';
    $deleteText = '';
} else {
    $deleteMessage = 'Вы уверены, что хотите восстановить этот элемент';
    $deleteTitle = 'Восстановить';
    $deleteText = ' (удален)';

}
?>
<div class="device-view">

    <h1><?= Html::encode($this->title) . $deleteText?></h1>

    <p>
        <?= Html::a('Печать', ['print', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Новая приемка', ['incoming/create', 'device_id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Приемки', ['incoming/index', 'device_id' => $model->id], ['class' => 'btn btn-info']) ?>
        <?= Html::a('Новая поверка', ['verification/create', 'device_id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Поверки', ['verification/index', 'device_id' => $model->id], ['class' => 'btn btn-info']) ?>
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
            [
                'attribute' => ($attribute = 'kind') . '_id',
                'value' => $model->{'word' . ucfirst($attribute)}->name
            ],
            [
                'attribute' => ($attribute = 'group'),
                'value' => $model->wordName->parent->parent->name
            ],
            [
                'attribute' => ($attribute = 'type'),
                'value' => $model->wordName->parent->name
            ],
            [
                'attribute' => ($attribute = 'name') . '_id',
                'value' => $model->{'word' . ucfirst($attribute)}->name
            ],
            [
                'attribute' => ($attribute = 'state') . '_id',
                'value' => $model->{'word' . ucfirst($attribute)}->name
            ],
            [
                'attribute' => ($attribute = 'department') . '_id',
                'value' => $model->{'word' . ucfirst($attribute)}->name
            ],
            [
                'attribute' => 'position',
                'value' => $model->position
            ],
            [
                'attribute' => ($attribute = 'crew') . '_id',
                'value' => $model->{'word' . ucfirst($attribute)}->name
            ],
            'number',
            'description:ntext',
            [
                'value' => $model->activeVerification->last_date,
                'label' => 'Дата поверки',
                'format' => 'date',
            ],
            [
                'value' => $model->activeVerification->next_date,
                'label' => 'Дата cледующей поверки',
                'format' => 'date',
            ],
            [
                'value' => $model->activeVerification->period,
                'label' => 'Межповерочный интервал',
            ],
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
