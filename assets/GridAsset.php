<?php
namespace app\assets;

use yii\web\AssetBundle;

class GridAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [
        'js/init-grid.js',
        'js/filter-tabs.js',
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
