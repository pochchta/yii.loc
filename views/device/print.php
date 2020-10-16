<?php

/* @var $this yii\web\View */

use app\models\QRImage;
use yii\helpers\Html;

/* @var $model app\models\Device */

$this->title = 'Печать паспорта ' . $model->name;
$this->registerCssFile('@web/css/passport-print.css');
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
<!--    <meta http-equiv="X-UA-Compatible" content="IE=edge">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1">-->
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
<div class="view_for_print">
    <table class="department">
        <tr>
            <td><?= Html::encode($model->department->name) ?></td>
            <td><?= Html::encode($model->department->phone) ?></td>
            <td><?= Html::encode($model->position) ?></td>
        </tr>
    </table>
    <div class="qr_image" style="background-image: url('<?= QRImage::getUrl() ?>')"></div>
    <table class="device">
        <tr>
            <td colspan="4" class="center">Паспорт</td>
        </tr>
        <tr>
            <td colspan="4"><?= Html::encode($model->name) ?></td>
        </tr>
        <tr>
            <td>Тип</td>
            <td>Номер</td>
            <td>Пределы</td>
            <td>Класс точности</td>
        </tr>
        <tr>
            <td><?= Html::encode($model->type) ?></td>
            <td><?= Html::encode($model->number) ?></td>
            <td><?= Html::encode($model->scale->value) ?></td>
            <td><?= Html::encode($model->accuracy) ?></td>
        </tr>
        <tr>
            <td colspan="4">
                Изменения_________________________________________________________________________________________________
                __________________________________________________________________________________________________________
            </td>
        </tr>
    </table>
    <table class="verification">
        <tr>
            <td colspan="9" class="center">Результаты п-п</td>
        </tr>
        <tr>
            <td>Дата</td>
            <td>Заключение</td>
            <td>Подпись</td>
            <td>Дата</td>
            <td>Заключение</td>
            <td>Подпись</td>
            <td>Дата</td>
            <td>Заключение</td>
            <td>Подпись</td>
        </tr>
        <tr>
            <td><?= Yii::$app->formatter->asDate($model->last_date) ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><?= Yii::$app->formatter->asDate($model->next_date) ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="9" class="right">Дата___________подпись____________</td>
        </tr>
    </table>
</div>
</body>
</html>
<?php $this->endPage() ?>