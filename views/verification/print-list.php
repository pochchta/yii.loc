<?php

use app\models\Verification;
use yii\helpers\Html;

/* @var $searchModel app\models\VerificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Verification */
/* @var $params array */

$this->title = 'Печать списка поверок';
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
            <th>Цех</th>
            <th>Прибор</th>
            <th>Номер</th>
            <th>Статус</th>
            <th class="date">Дата пов.</th>
            <th class="date">Дата сл. пов.</th>
            <th>П-д</th>
            <th>Создал</th>
            <th>Обновил</th>
        </tr>
        </thead>
        <?php
        $n = 1;
        foreach($dataProvider->models as $model):
            ?>
            <tr>
                <td><?= $n++ ?></td>
                <td><?= $model->device->wordDepartment->name ?></td>
                <td><?= $model->device->wordName->name ?></td>
                <td><?= $model->device->number ?></td>
                <td><?php
                    if ($model->status == Verification::STATUS_ON) {
                        print 'Действующая';
                    }
                ?></td>
                <td><?= Yii::$app->formatter->asDate($model->last_date) ?></td>
                <td><?= Yii::$app->formatter->asDate($model->next_date) ?></td>
                <td><?= $model->period ?></td>
                <td><?= $model->creator->username ?></td>
                <td><?= $model->updater->username ?></td>

            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
<?php $this->endPage() ?>
