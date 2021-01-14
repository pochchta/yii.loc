<?php

use app\models\Status;
use app\models\Verification;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Verification */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => $model->device->name, 'url' => ['device/view', 'id' => $model->device_id]];
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
<div class="verification-view">

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
            [
                'format' => 'html',
                'value' => Html::a(
                    $model->device->name . ', №' . $model->device->number . ($model->device->deleted == Status::DELETED ? ' (удален)' : ''),
                    ['device/view', 'id' => $model->device_id]
                ),
                'label' => 'Относится к прибору',
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->status == Verification::STATUS_ON) {
                        return '<span class="glyphicon glyphicon-ok-circle color-ok" title="Последняя поверка"></span>';
                    } else {
                        return '';
                    }
                },
            ],
            'name',
            'type',
            'description:ntext',
            'last_date:date',
            'next_date:date',
            'period',
            'created_at:date',
            'updated_at:date',
            [
                'attribute' => 'created_by',
                'value' => $model->creator->username
            ],
                        [
                'attribute' => 'updated_by',
                'value' => $model->updater->username
            ],
        ],
    ]) ?>

</div>
