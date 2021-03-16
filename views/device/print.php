<?php

use app\models\QRImage;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Device */

$this->title = 'Печать паспорта ' . $model->wordName->name;
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
<div class="content view_for_print">
    <?= Html::a('Назад', ['view', 'id' => $model->id], ['class' => 'hide']) ?>

    <table class="department">
        <tr>
            <td><?= Html::encode($model->wordDepartment->name) ?></td>
            <td><?= Html::encode($model->wordDepartment->value) ?></td>
            <td><?= Html::encode($model->name) ?></td>
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
            <td><?= Html::encode($model->wordType->name) ?></td>
            <td><?= Html::encode($model->number) ?></td>
            <td><?= /*Html::encode($model->wordScale->name)*/'scale' ?></td>
            <td><?= /*Html::encode($model->wordAccuracy->name)*/'accuracy' ?></td>
        </tr>
        <tr>
            <td colspan="4">
                Изменения_______________________________________________________________________________________
                ________________________________________________________________________________________________
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
            <td><?= Yii::$app->formatter->asDate($model->activeVerification->last_date) ?></td>
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
            <td><?= Yii::$app->formatter->asDate($model->activeVerification->next_date) ?></td>
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