<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\AuthItem */

$this->title = 'Обновление роли или разрешения: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Роли и разрешения', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="auth-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', compact(
        'model'
    )) ?>

</div>
