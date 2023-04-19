<?php
namespace app\assets;

use yii\web\AssetBundle;

class FormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/catalog-tabs/main.css',
    ];
    public $js = [
        'js/init-grid.js',
        'js/catalog-tabs/main.js',
        'js/catalog-tabs/form.js',
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}