<?php

use yii\helpers\Html;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Device */
/* @var $params array */

$this->title = 'Печать списка приборов';
$this->registerCssFile('@web/css/user-print.css');
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
<div class="content print-table-device">
    <?= Html::a('Назад', array_merge(['index'], $params), ['class' => 'hide']) ?>

    <table>
        <thead>
        <tr>
            <th>№</th>
            <th>Название</th>
            <th>Номер</th>
            <th>След. пов.</th>
        </tr>
        </thead>
        <?php
        $n = 1;
        foreach($dataProvider->models as $model):
            ?>
            <tr>
                <td><?= $n++ ?></td>
                <td><?= $model->name ?></td>
                <td><?= $model->number ?></td>
                <td><?= Yii::$app->formatter->asDate($model->next_date) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
<?php $this->endPage() ?>
