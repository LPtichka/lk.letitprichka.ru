<?php
namespace app\assets;

use yii\web\AssetBundle;

class OrderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/auto-complete.css',
    ];
    public $js = [
        'js/auto-complete.js',
        'js/common.js',
        'js/order.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}