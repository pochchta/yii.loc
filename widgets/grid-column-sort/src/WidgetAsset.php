<?php


namespace app\widgets\gcs;


use yii\web\AssetBundle;

class WidgetAsset extends AssetBundle
{
    public $depends = [
        'app\widgets\csc\WidgetAsset',
    ];
}