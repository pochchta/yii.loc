<?php
namespace app\assets;

use yii\web\AssetBundle;

class GridAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/catalog-tabs/main.css',
        'css/catalog-tabs/grid.css',
    ];
    public $js = [
        'js/init-grid.js',
        'js/catalog-tabs/main.js',
        'js/catalog-tabs/grid.js',
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
