<?php

use app\models\Incoming;
use app\models\Status;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Incoming */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Приемки', 'url' => ['index']];
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
<div class="incoming-view">

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
                    $model->device->wordName->name . ', №' . $model->device->number . ($model->device->deleted == Status::DELETED ? ' (удален)' : ''),
                    ['device/view', 'id' => $model->device_id]
                ),
                'label' => 'Относится к прибору',
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->status == Incoming::INCOMING) {
                        return '<span class="glyphicon glyphicon-log-in color-err" title="Принят"></span>';
                    } elseif ($model->status == Incoming::READY) {
                        return '<span class="glyphicon glyphicon-ok-circle color-war" title="Готов"></span>';
                    } elseif ($model->status == Incoming::OUTGOING) {
                        return '<span class="glyphicon glyphicon-log-out color-ok" title="Выдан"></span>';
                    }
                    return '';
                }
            ],
            [
                'attribute' => 'payment',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->payment == Incoming::PAID) {
                        return '<span class="glyphicon glyphicon-ok-circle color-ok" title="Оплачен"></span>';
                    }
                    return '';
                },
            ],
            'description:ntext',
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
