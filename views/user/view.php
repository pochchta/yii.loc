<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelAssign app\models\AuthAssignment */
/* @var array $allRoles app\models\User */
/* @var array $allRolesByUser app\models\User */
/* @var array $allPermsByUser app\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<!--< class="user-view">-->

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?/*= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
//            'password',
            'auth_key',
        ],
    ]) */?>

    <?php
        $dataProvider->sort = [
            'attributes' => [
                'item.type' => [
                    'asc' => ['type' => SORT_ASC],
                    'desc' => ['type' => SORT_DESC],
                ],
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC]
                ],
                'item_name' => [
                    'asc' => ['item_name' => SORT_ASC],
                    'desc' => ['item_name' => SORT_DESC]
                ]
            ],
            'defaultOrder' => ['item.type'=> SORT_ASC],
        ];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'item_name',
            [
                'attribute' => 'item_name',
                'value' => function ($data) {
                    $ret = "Роли:";
                    $arrRoles = Yii::$app->authManager->getChildRoles($data->item_name);
                    foreach ($arrRoles as $item) {
                        $ret .= ' '.$item->name.',';
                    }
                    $ret = rtrim($ret, ',');
                    $ret .= "\nРазрешения:";
                    $arrPerms = Yii::$app->authManager->getPermissionsByRole($data->item_name);
                    foreach ($arrPerms as $item) {
                        $ret .= ' '.$item->name.',';
                    }
                    $ret = rtrim($ret, ',');
                    return $ret;
                },
                'format' => 'ntext',
                'header' => 'Включает в себя'
            ],
            [
                'attribute' => 'item.type',
                'value' => function ($data) {
                    return $data->item->type == \app\models\AuthItem::$ROLE ? 'Роль' : 'Разрешение';
                }
            ],
             'item.description',
            [
                'attribute' => 'created_at',
                'format' => 'date'
            ],

            ['class' => 'yii\grid\ActionColumn', 'controller' => 'auth-assignment'],
        ],
    ]); ?>

    <div class="one-button-form">
        <?php $form = ActiveForm::begin();
            $button = Html::submitButton('Добавить', ['class' => 'btn btn-success']);
            $span = "<span class='input-group-addon' id='basic-addon'>{$button}</span>";
            $formGroup = "<div class='input-group'>{input}{$span}</div>";
        ?>

        <?= $form->field($modelAssign, 'user_id', [
            "template" => "{input}"
        ])->hiddenInput(['value' => $model->id])->label() ?>

        <?= $form->field($modelAssign, 'item_name', [
                "template" => "{label}\n{$formGroup}\n{error}"
        ])->dropDownList($allRoles, ['value' => 'guest', 'class' => 'form-control', 'aria-describedby' => 'basic-addon']) ?>

        <?php ActiveForm::end(); ?>
    </div>




<!--<pre>-->
<?php
//    var_dump(Yii::$app->authManager->getRolesByUser(17));
//?>
<!--</pre>-->
<!--    <pre>
        <?php /*var_dump($allRolesByUser); */?>
        <?php /*var_dump($allPermsByUser); */?>
    </pre>-->

<!--    <div class="allRolesByUser">
        <?php /*foreach($allRolesByUser as $key => $item): */?>
            <p>
                <?/*= $item->name */?>
            </p>
        <?php /*endforeach */?>
    </div>

    <div class="allPermsByUser">

    </div>-->

<!--    <pre>
    <?php
/*        $admin = Yii::$app->authManager->getRole('admin');
        $guest = Yii::$app->authManager->getRole('guest');
        $perm = Yii::$app->authManager->getPermission('createPost');
        try {
//            var_dump(Yii::$app->authManager->addChild($admin, $guest));
//            var_dump(Yii::$app->authManager->addChild($guest, $admin));
            var_dump(Yii::$app->authManager->addChild($guest, $perm));
        } catch (Exception $e) {
            echo "Такую связь добавить нельзя. Код ошибки:\n";
            echo $e->getMessage();
        }
    */?>
    </pre>-->

</div>
