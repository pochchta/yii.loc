<?php


namespace app\widgets\sort;


use yii\web\AssetBundle;

class WidgetAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/grid-column-sort/src/assets';
    public $css = [
        'css/grid-column-sort.css',
    ];
    public $js = [
        'js/grid-column-sort.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\jui\JuiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}