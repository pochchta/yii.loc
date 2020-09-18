<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\AuthItem */

$this->title = 'Создание роли или разрешения';
$this->params['breadcrumbs'][] = ['label' => 'Роли и разрешения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', compact(
        'model'
    )) ?>

</div>
