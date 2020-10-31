<?php

use app\models\Incoming;
use yii\helpers\Html;

/* @var $searchModel app\models\IncomingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\Incoming */
/* @var $params array */

$this->title = 'Печать списка приемок';
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
            <th>Оплата</th>
            <th class="date">Создано</th>
            <th class="date">Изменено</th>
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
                <td><?= $model->device->department->name ?></td>
                <td><?= $model->device->name ?></td>
                <td><?= $model->device->number ?></td>
                <td><?php
                    if ($model->status == Incoming::INCOMING) {
                        print 'Принят';
                    } elseif ($model->status == Incoming::READY) {
                        print 'Готов';
                    } elseif ($model->status == Incoming::OUTGOING) {
                        print 'Выдан';
                    }
                ?></td>
                <td><?php
                    if ($model->payment == Incoming::PAID) {
                        print 'Оплачен';
                    }
                ?></td>
                <td><?= Yii::$app->formatter->asDate($model->created_at) ?></td>
                <td><?= Yii::$app->formatter->asDate($model->updated_at) ?></td>
                <td><?= $model->creator->username ?></td>
                <td><?= $model->updater->username ?></td>

            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
<?php $this->endPage() ?>
