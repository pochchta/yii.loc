<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AuthItem */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Auth Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auth-item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->name], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'type',
                'value' => $model->type == $model::$ROLE ? 'Роль' : 'Разрешение'
            ],
            'description:ntext',
            [
                'attribute' => 'created_at',
                'format' => 'date'
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'date'
            ],
//            'rule_name',
//            'data',
//            'created_at',
//            'updated_at',
        ],
    ]) ?>

    <?php foreach($allRoles as $permit): ?>
        <?= "$permit<br>" ?>
    <?php endforeach ?>

</div>
