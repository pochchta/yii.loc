<?php


namespace app\widgets\csc;


use yii\web\AssetBundle;

class WidgetAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/connected-sortable-columns/src/assets';
    public $css = [
        'css/column-sort.css',
    ];
    public $js = [
        'js/column-sort.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\jui\JuiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}